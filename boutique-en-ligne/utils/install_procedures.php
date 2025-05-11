<?php
// utils/install_procedures.php
require_once __DIR__ . '/../config/database.php';

echo "Starting installation of stored procedures and triggers...\n";

try {
    // Get the connection
    $db = Database::getInstance()->getConnection();
    
    // First, drop any existing procedures and triggers to avoid conflicts
    $db->exec("DROP PROCEDURE IF EXISTS afficher_details_commande");
    $db->exec("DROP PROCEDURE IF EXISTS finaliser_commande");
    $db->exec("DROP PROCEDURE IF EXISTS historique_commandes");
    $db->exec("DROP TRIGGER IF EXISTS after_commande_validee");
    $db->exec("DROP TRIGGER IF EXISTS before_commande_insertion");
    $db->exec("DROP TRIGGER IF EXISTS after_commande_annulee");
    $db->exec("DROP TRIGGER IF EXISTS after_commande_annulation_details");
    
    echo "Dropped existing procedures and triggers.\n";
    
    // Create Stored Procedure: afficher_details_commande
    $sql = "
    CREATE PROCEDURE afficher_details_commande(IN p_id_commande INT, IN p_id_utilisateur INT)
    BEGIN
        DECLARE total_commande DECIMAL(10, 2);
        
        -- Vérifier que la commande appartient à l'utilisateur
        IF EXISTS (SELECT 1 FROM commandes WHERE id_commande = p_id_commande AND id_utilisateur = p_id_utilisateur) THEN
            -- Afficher les détails de la commande
            SELECT 
                c.id_commande, 
                c.date_commande, 
                c.statut,
                p.nom AS nom_produit, 
                dc.quantite, 
                dc.prix_unitaire, 
                (dc.quantite * dc.prix_unitaire) AS sous_total
            FROM commandes c
            JOIN details_commande dc ON c.id_commande = dc.id_commande
            JOIN produits p ON dc.id_produit = p.id_produit
            WHERE c.id_commande = p_id_commande;
            
            -- Calculer et afficher le total
            SELECT SUM(quantite * prix_unitaire) INTO total_commande
            FROM details_commande
            WHERE id_commande = p_id_commande;
            
            SELECT total_commande AS total_a_payer;
        ELSE
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Commande non trouvée pour cet utilisateur';
        END IF;
    END";
    
    echo "Creating afficher_details_commande procedure...\n";
    $db->exec($sql);
    
    // Create Stored Procedure: finaliser_commande
    $sql = "
    CREATE PROCEDURE finaliser_commande(IN p_id_utilisateur INT, OUT p_id_commande INT)
    BEGIN
        DECLARE v_id_panier INT;
        DECLARE v_total DECIMAL(10, 2) DEFAULT 0;
        
        -- Trouver le panier de l'utilisateur
        SELECT id_panier INTO v_id_panier 
        FROM paniers 
        WHERE id_utilisateur = p_id_utilisateur 
        LIMIT 1;
        
        IF v_id_panier IS NULL THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Panier non trouvé pour cet utilisateur';
        ELSE
            -- Calculer le total de la commande
            SELECT SUM(ep.quantite * p.prix) INTO v_total
            FROM elements_panier ep
            JOIN produits p ON ep.id_produit = p.id_produit
            WHERE ep.id_panier = v_id_panier;
            
            IF v_total = 0 OR v_total IS NULL THEN
                SIGNAL SQLSTATE '45000'
                SET MESSAGE_TEXT = 'Le panier est vide';
            ELSE
                -- Créer la commande
                INSERT INTO commandes (id_utilisateur, total, statut)
                VALUES (p_id_utilisateur, v_total, 'validee');
                
                -- Récupérer l'ID de la commande
                SET p_id_commande = LAST_INSERT_ID();
                
                -- Transférer les éléments du panier vers les détails de commande
                INSERT INTO details_commande (id_commande, id_produit, quantite, prix_unitaire)
                SELECT p_id_commande, ep.id_produit, ep.quantite, p.prix
                FROM elements_panier ep
                JOIN produits p ON ep.id_produit = p.id_produit
                WHERE ep.id_panier = v_id_panier;
                
                -- Vider le panier
                DELETE FROM elements_panier WHERE id_panier = v_id_panier;
            END IF;
        END IF;
    END";
    
    echo "Creating finaliser_commande procedure...\n";
    $db->exec($sql);
    
    // Create Stored Procedure: historique_commandes
    $sql = "
    CREATE PROCEDURE historique_commandes(IN p_id_utilisateur INT)
    BEGIN
        -- Afficher les commandes actives
        SELECT 
            c.id_commande,
            c.date_commande,
            c.statut,
            c.total,
            COUNT(dc.id_detail) AS nombre_produits
        FROM commandes c
        LEFT JOIN details_commande dc ON c.id_commande = dc.id_commande
        WHERE c.id_utilisateur = p_id_utilisateur
        GROUP BY c.id_commande
        ORDER BY c.date_commande DESC;
        
        -- Afficher les commandes annulées
        SELECT 
            ca.id_annulation,
            ca.id_commande,
            ca.date_annulation,
            'annulee' AS statut,
            ca.total,
            COUNT(dca.id_detail) AS nombre_produits
        FROM commandes_annulees ca
        LEFT JOIN details_commandes_annulees dca ON ca.id_annulation = dca.id_annulation
        WHERE ca.id_utilisateur = p_id_utilisateur
        GROUP BY ca.id_annulation
        ORDER BY ca.date_annulation DESC;
    END";
    
    echo "Creating historique_commandes procedure...\n";
    $db->exec($sql);
    
    // Create Trigger: after_commande_validee
    $sql = "
    CREATE TRIGGER after_commande_validee
    AFTER INSERT ON details_commande
    FOR EACH ROW
    BEGIN
        -- Mettre à jour le stock
        UPDATE produits
        SET stock = stock - NEW.quantite
        WHERE id_produit = NEW.id_produit;
    END";
    
    echo "Creating after_commande_validee trigger...\n";
    $db->exec($sql);
    
    // Create Trigger: before_commande_insertion
    $sql = "
    CREATE TRIGGER before_commande_insertion
    BEFORE INSERT ON details_commande
    FOR EACH ROW
    BEGIN
        DECLARE stock_disponible INT;
        
        -- Vérifier le stock disponible
        SELECT stock INTO stock_disponible
        FROM produits
        WHERE id_produit = NEW.id_produit;
        
        IF NEW.quantite > stock_disponible THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Stock insuffisant pour ce produit';
        END IF;
    END";
    
    echo "Creating before_commande_insertion trigger...\n";
    $db->exec($sql);
    
    // Create Trigger: after_commande_annulee
    $sql = "
    CREATE TRIGGER after_commande_annulee
    AFTER UPDATE ON commandes
    FOR EACH ROW
    BEGIN
        IF NEW.statut = 'annulee' AND OLD.statut != 'annulee' THEN
            -- Restaurer le stock pour chaque produit dans la commande
            INSERT INTO commandes_annulees (id_commande, id_utilisateur, total)
            VALUES (NEW.id_commande, NEW.id_utilisateur, NEW.total);
            
            -- L'insertion dans details_commandes_annulees et la restauration du stock
            -- sont gérées par un autre trigger
        END IF;
    END";
    
    echo "Creating after_commande_annulee trigger...\n";
    $db->exec($sql);
    
    // Create Trigger: after_commande_annulation_details
    $sql = "
    CREATE TRIGGER after_commande_annulation_details
    AFTER INSERT ON commandes_annulees
    FOR EACH ROW
    BEGIN
        DECLARE v_id_annulation INT;
        SET v_id_annulation = NEW.id_annulation;
        
        -- Copier les détails de la commande dans l'historique des annulations
        INSERT INTO details_commandes_annulees (id_annulation, id_produit, quantite, prix_unitaire)
        SELECT v_id_annulation, id_produit, quantite, prix_unitaire
        FROM details_commande
        WHERE id_commande = NEW.id_commande;
        
        -- Restaurer le stock
        UPDATE produits p
        JOIN details_commande dc ON p.id_produit = dc.id_produit
        SET p.stock = p.stock + dc.quantite
        WHERE dc.id_commande = NEW.id_commande;
    END";
    
    echo "Creating after_commande_annulation_details trigger...\n";
    $db->exec($sql);
    
    echo "All stored procedures and triggers installed successfully!\n";
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
    exit;
}