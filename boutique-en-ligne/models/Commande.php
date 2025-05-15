<?php
// models/Commande.php
require_once __DIR__ . '/../config/database.php';

class Commande {
    private $conn;
    private $db;
    
    // Propriétés
    public $id_commande;
    public $id_utilisateur;
    public $date_commande;
    public $statut;
    public $total;
    public $adresse_livraison;
    public $methode_paiement;
    
    public function __construct() {
        $this->db = Database::getInstance();
        $this->conn = $this->db->getConnection();
    }
    
    // Créer une nouvelle commande à partir du panier de l'utilisateur
    public function creerDepuisPanier($id_utilisateur, $adresseLivraison = null, $methodePaiement = 'card') {
        $id_commande = null;
        
        try {
            // Vérifier que l'utilisateur a un panier avec des articles
            $panier = new Panier();
            $id_panier = $panier->getOuCreerPourUtilisateur($id_utilisateur);
            
            if (!$id_panier) {
                error_log("DEBUG [Commande::creerDepuisPanier] - Panier non trouvé pour utilisateur #$id_utilisateur");
                return false;
            }
            
            $contenu_panier = $panier->getContenu();
            
            if (empty($contenu_panier)) {
                error_log("DEBUG [Commande::creerDepuisPanier] - Panier vide pour utilisateur #$id_utilisateur");
                return false;
            }
            
            // N'utilisons pas de transactions pour l'instant pour résoudre le problème
            // Créer la commande - Toujours en état "en_attente" initialement
            error_log("DEBUG [Commande::creerDepuisPanier] - Création de la commande sans transaction");
            $query = "INSERT INTO commandes (id_utilisateur, statut, total) 
                      VALUES (:id_utilisateur, 'en_attente', :total)";
            
            $total = $panier->calculerTotal();
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id_utilisateur', $id_utilisateur);
            $stmt->bindParam(':total', $total);
            
            if (!$stmt->execute()) {
                error_log("DEBUG [Commande::creerDepuisPanier] - Erreur lors de la création de la commande: " . print_r($stmt->errorInfo(), true));
                return false;
            }
            
            $id_commande = $this->conn->lastInsertId();
            error_log("DEBUG [Commande::creerDepuisPanier] - Commande créée avec ID: $id_commande");
            
            // Créer les tables nécessaires si elles n'existent pas
            $this->creerTableAdresseLivraisonSiNecessaire();
            $this->creerTableMethodePaiementSiNecessaire();
            
            $success = true;
            
            // Ajouter les produits à la commande
            foreach ($contenu_panier as $item) {
                $query = "INSERT INTO details_commande (id_commande, id_produit, quantite, prix_unitaire) 
                          VALUES (:id_commande, :id_produit, :quantite, :prix_unitaire)";
                
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':id_commande', $id_commande);
                $stmt->bindParam(':id_produit', $item['id_produit']);
                $stmt->bindParam(':quantite', $item['quantite']);
                $stmt->bindParam(':prix_unitaire', $item['prix_unitaire']);
                
                if (!$stmt->execute()) {
                    error_log("DEBUG [Commande::creerDepuisPanier] - Erreur lors de l'ajout du produit " . $item['id_produit'] . " à la commande: " . print_r($stmt->errorInfo(), true));
                    $success = false;
                    break;
                }
                
                // Mettre à jour le stock
                $query = "UPDATE produits SET stock = stock - :quantite WHERE id_produit = :id_produit";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':quantite', $item['quantite']);
                $stmt->bindParam(':id_produit', $item['id_produit']);
                
                if (!$stmt->execute()) {
                    error_log("DEBUG [Commande::creerDepuisPanier] - Erreur lors de la mise à jour du stock pour le produit " . $item['id_produit'] . ": " . print_r($stmt->errorInfo(), true));
                    $success = false;
                    break;
                }
            }
            
            if (!$success) {
                error_log("DEBUG [Commande::creerDepuisPanier] - Erreur lors de l'ajout des produits à la commande");
                return false;
            }
            
            // Enregistrer les informations de livraison si fournies
            if ($adresseLivraison) {
                if (!$this->sauvegarderInfosLivraison($id_commande, $adresseLivraison)) {
                    error_log("DEBUG [Commande::creerDepuisPanier] - Erreur lors de l'enregistrement des informations de livraison");
                    return false;
                }
            }
            
            // Enregistrer la méthode de paiement
            if (!$this->sauvegarderMethodePaiement($id_commande, $methodePaiement)) {
                error_log("DEBUG [Commande::creerDepuisPanier] - Erreur lors de l'enregistrement de la méthode de paiement");
                return false;
            }
            
            // Ne plus mettre à jour le statut à "validee" comme avant
            // La commande reste "en_attente" jusqu'à ce que l'administrateur la confirme
            
            // Vider le panier de l'utilisateur
            if (!$panier->vider()) {
                error_log("DEBUG [Commande::creerDepuisPanier] - Erreur lors du vidage du panier");
                // Continuer quand même car la commande est créée
            }
            
            $this->id_commande = $id_commande;
            $this->id_utilisateur = $id_utilisateur;
            $this->adresse_livraison = $adresseLivraison;
            $this->methode_paiement = $methodePaiement;
            
            error_log("DEBUG [Commande::creerDepuisPanier] - Commande créée avec succès en statut 'en_attente'");
            return $id_commande;
        } catch(PDOException $e) {
            error_log("DEBUG [Commande::creerDepuisPanier] - Erreur PDO: " . $e->getMessage());
            throw $e;
        } catch(Exception $e) {
            // Capturer d'autres types d'exceptions
            error_log("DEBUG [Commande::creerDepuisPanier] - Erreur générale: " . $e->getMessage());
            throw $e;
        }
    }
    
    // Sauvegarder les informations de livraison
    private function sauvegarderInfosLivraison($id_commande, $adresseLivraison) {
        try {
            // Vérifier si une table d'adresses de livraison existe, sinon la créer
            $this->creerTableAdresseLivraisonSiNecessaire();
            
            // Insérer les informations de livraison
            $query = "INSERT INTO commandes_adresses (
                          id_commande, 
                          nom, 
                          prenom, 
                          adresse, 
                          code_postal, 
                          ville, 
                          telephone
                      ) VALUES (
                          :id_commande, 
                          :nom, 
                          :prenom, 
                          :adresse, 
                          :code_postal, 
                          :ville, 
                          :telephone
                      )";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id_commande', $id_commande);
            $stmt->bindParam(':nom', $adresseLivraison['nom']);
            $stmt->bindParam(':prenom', $adresseLivraison['prenom']);
            $stmt->bindParam(':adresse', $adresseLivraison['adresse']);
            $stmt->bindParam(':code_postal', $adresseLivraison['code_postal']);
            $stmt->bindParam(':ville', $adresseLivraison['ville']);
            $stmt->bindParam(':telephone', $adresseLivraison['telephone']);
            
            return $stmt->execute();
        } catch(PDOException $e) {
            throw $e;
        }
    }
    
    // Sauvegarder la méthode de paiement
    private function sauvegarderMethodePaiement($id_commande, $methodePaiement) {
        try {
            // Vérifier si une table de méthodes de paiement existe, sinon la créer
            $this->creerTableMethodePaiementSiNecessaire();
            
            // Insérer la méthode de paiement
            $query = "INSERT INTO commandes_paiements (
                          id_commande, 
                          methode_paiement, 
                          date_paiement, 
                          statut_paiement
                      ) VALUES (
                          :id_commande, 
                          :methode_paiement, 
                          NOW(), 
                          'completed'
                      )";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id_commande', $id_commande);
            $stmt->bindParam(':methode_paiement', $methodePaiement);
            
            return $stmt->execute();
        } catch(PDOException $e) {
            throw $e;
        }
    }
    
    // Mettre à jour le statut d'une commande
    private function updateStatut($id_commande, $statut) {
        try {
            $query = "UPDATE commandes SET statut = :statut WHERE id_commande = :id_commande";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id_commande', $id_commande);
            $stmt->bindParam(':statut', $statut);
            
            return $stmt->execute();
        } catch(PDOException $e) {
            throw $e;
        }
    }
    
    // Créer la table d'adresses de livraison si elle n'existe pas
    private function creerTableAdresseLivraisonSiNecessaire() {
        try {
            $query = "CREATE TABLE IF NOT EXISTS commandes_adresses (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        id_commande INT NOT NULL,
                        nom VARCHAR(100) NOT NULL,
                        prenom VARCHAR(100) NOT NULL,
                        adresse VARCHAR(255) NOT NULL,
                        code_postal VARCHAR(20) NOT NULL,
                        ville VARCHAR(100) NOT NULL,
                        telephone VARCHAR(20),
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                        FOREIGN KEY (id_commande) REFERENCES commandes(id_commande) ON DELETE CASCADE
                    )";
            
            $this->conn->exec($query);
        } catch(PDOException $e) {
            throw $e;
        }
    }
    
    // Créer la table de méthodes de paiement si elle n'existe pas
    private function creerTableMethodePaiementSiNecessaire() {
        try {
            $query = "CREATE TABLE IF NOT EXISTS commandes_paiements (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        id_commande INT NOT NULL,
                        methode_paiement VARCHAR(50) NOT NULL,
                        date_paiement DATETIME NOT NULL,
                        statut_paiement VARCHAR(50) NOT NULL,
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                        FOREIGN KEY (id_commande) REFERENCES commandes(id_commande) ON DELETE CASCADE
                    )";
            
            $this->conn->exec($query);
        } catch(PDOException $e) {
            throw $e;
        }
    }
    
    // Récupérer les détails d'une commande
    public function getDetailsCommande($id_commande, $id_utilisateur) {
        try {
            // Check if the user is admin
            $isAdmin = false;
            $query = "SELECT est_admin FROM utilisateurs WHERE id_utilisateur = :id_utilisateur LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id_utilisateur', $id_utilisateur);
            $stmt->execute();
            $userData = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($userData && isset($userData['est_admin']) && $userData['est_admin'] == 1) {
                $isAdmin = true;
            }
            
            // Get the order details
            $query = "SELECT c.*, u.nom, u.prenom, u.email, 
                           ca.adresse, ca.code_postal, ca.ville, ca.telephone,
                           cp.methode_paiement
                    FROM commandes c
                    LEFT JOIN utilisateurs u ON c.id_utilisateur = u.id_utilisateur
                    LEFT JOIN commandes_adresses ca ON c.id_commande = ca.id_commande
                    LEFT JOIN commandes_paiements cp ON c.id_commande = cp.id_commande
                    WHERE c.id_commande = :id_commande";
            
            // Only filter by user ID if not admin
            if (!$isAdmin) {
                $query .= " AND c.id_utilisateur = :id_utilisateur";
            }
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id_commande', $id_commande);
            
            if (!$isAdmin) {
                $stmt->bindParam(':id_utilisateur', $id_utilisateur);
            }
            
            $stmt->execute();
            $commande = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$commande) {
                return null;
            }
            
            // Get order items
            $query = "SELECT dc.*, p.nom, p.categorie, p.image_url
                      FROM details_commande dc
                      LEFT JOIN produits p ON dc.id_produit = p.id_produit
                      WHERE dc.id_commande = :id_commande";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id_commande', $id_commande);
            $stmt->execute();
            
            $produits = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Always add products array even if empty to avoid undefined index
            $commande['produits'] = $produits ?: [];
            
            return $commande;
        } catch(PDOException $e) {
            error_log("Error in getDetailsCommande: " . $e->getMessage());
            throw $e;
        }
    }
    
    // Récupérer l'historique des commandes d'un utilisateur
    public function getHistoriqueCommandes($id_utilisateur) {
        try {
            // Méthode directe pour récupérer les commandes, sans utiliser de procédure stockée
            error_log("DEBUG [Commande::getHistoriqueCommandes] - Fetching orders for user #$id_utilisateur");
            
            // Récupérer toutes les commandes de l'utilisateur
            $query = "SELECT c.*, 
                        ca.nom, ca.prenom, ca.adresse, ca.code_postal, ca.ville, ca.telephone,
                        cp.methode_paiement
                      FROM commandes c
                      LEFT JOIN commandes_adresses ca ON c.id_commande = ca.id_commande
                      LEFT JOIN commandes_paiements cp ON c.id_commande = cp.id_commande
                      WHERE c.id_utilisateur = :id_utilisateur
                      ORDER BY c.date_commande DESC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id_utilisateur', $id_utilisateur);
            $stmt->execute();
            
            $commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            error_log("DEBUG [Commande::getHistoriqueCommandes] - Found " . count($commandes) . " orders");
            
            if (empty($commandes)) {
                return [];
            }
            
            return $commandes;
        } catch(PDOException $e) {
            error_log("DEBUG [Commande::getHistoriqueCommandes] - Error: " . $e->getMessage());
            throw $e;
        }
    }
    
    // Annuler une commande
    public function annuler($id_commande, $id_utilisateur, $raison = null) {
        try {
            // Vérifier que la commande appartient à l'utilisateur
            $query = "SELECT * FROM commandes WHERE id_commande = :id_commande AND id_utilisateur = :id_utilisateur LIMIT 1";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id_commande', $id_commande);
            $stmt->bindParam(':id_utilisateur', $id_utilisateur);
            $stmt->execute();
            
            $commande = $stmt->fetch();
            
            if(!$commande) {
                return false;
            }
            
            // Mettre à jour le statut de la commande (le trigger s'occupera du reste)
            $query = "UPDATE commandes SET statut = 'annulee' WHERE id_commande = :id_commande";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id_commande', $id_commande);
            
            $stmt->execute();
            
            // Mettre à jour la raison de l'annulation si fournie
            if($raison) {
                $query = "UPDATE commandes_annulees SET raison = :raison 
                          WHERE id_commande = :id_commande AND id_utilisateur = :id_utilisateur";
                
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':raison', $raison);
                $stmt->bindParam(':id_commande', $id_commande);
                $stmt->bindParam(':id_utilisateur', $id_utilisateur);
                
                $stmt->execute();
            }
            
            return true;
        } catch(PDOException $e) {
            throw $e;
        }
    }
}
?>