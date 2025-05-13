<?php
// models/Panier.php
require_once __DIR__ . '/../config/database.php';

class Panier {
    private $conn;
    
    // Propriétés
    public $id_panier;
    public $id_utilisateur;
    public $date_creation;
    
    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
    }
    
    // Créer ou récupérer le panier d'un utilisateur
    public function getOuCreerPourUtilisateur($id_utilisateur) {
        // Vérifier si l'utilisateur a déjà un panier
        $query = "SELECT * FROM paniers WHERE id_utilisateur = :id_utilisateur LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_utilisateur', $id_utilisateur);
        $stmt->execute();
        
        $panier = $stmt->fetch();
        
        // Si le panier existe, le retourner
        if($panier) {
            $this->id_panier = $panier['id_panier'];
            $this->id_utilisateur = $panier['id_utilisateur'];
            $this->date_creation = $panier['date_creation'];
            
            return $this->id_panier;
        }
        
        // Sinon, créer un nouveau panier
        $query = "INSERT INTO paniers (id_utilisateur) VALUES (:id_utilisateur)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_utilisateur', $id_utilisateur);
        
        if($stmt->execute()) {
            $this->id_panier = $this->conn->lastInsertId();
            $this->id_utilisateur = $id_utilisateur;
            $this->date_creation = date('Y-m-d H:i:s');
            
            return $this->id_panier;
        }
        
        return false;
    }
    
    // Ajouter un produit au panier
    public function ajouterProduit($id_produit, $quantite = 1) {
        if(!$this->id_panier) {
            return false;
        }
        
        // Vérifier si le produit est déjà dans le panier
        $query = "SELECT * FROM elements_panier WHERE id_panier = :id_panier AND id_produit = :id_produit LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_panier', $this->id_panier);
        $stmt->bindParam(':id_produit', $id_produit);
        $stmt->execute();
        
        $element = $stmt->fetch();
        
        // Si le produit existe déjà, mettre à jour la quantité
        if($element) {
            $nouvelle_quantite = $element['quantite'] + $quantite;
            
            $query = "UPDATE elements_panier SET quantite = :quantite WHERE id_element = :id_element";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':quantite', $nouvelle_quantite);
            $stmt->bindParam(':id_element', $element['id_element']);
            
            return $stmt->execute();
        }
        
        // Sinon, ajouter le produit au panier
        $query = "INSERT INTO elements_panier (id_panier, id_produit, quantite) VALUES (:id_panier, :id_produit, :quantite)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_panier', $this->id_panier);
        $stmt->bindParam(':id_produit', $id_produit);
        $stmt->bindParam(':quantite', $quantite);
        
        return $stmt->execute();
    }
    
    // Supprimer un produit du panier
    public function supprimerProduit($id_produit) {
        if(!$this->id_panier) {
            return false;
        }
        
        $query = "DELETE FROM elements_panier WHERE id_panier = :id_panier AND id_produit = :id_produit";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_panier', $this->id_panier);
        $stmt->bindParam(':id_produit', $id_produit);
        
        return $stmt->execute();
    }
    
    // Mettre à jour la quantité d'un produit dans le panier
    public function mettreAJourQuantite($id_produit, $quantite) {
        if(!$this->id_panier) {
            return false;
        }
        
        if($quantite <= 0) {
            return $this->supprimerProduit($id_produit);
        }
        
        $query = "UPDATE elements_panier SET quantite = :quantite WHERE id_panier = :id_panier AND id_produit = :id_produit";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':quantite', $quantite);
        $stmt->bindParam(':id_panier', $this->id_panier);
        $stmt->bindParam(':id_produit', $id_produit);
        
        return $stmt->execute();
    }
    
    // Récupérer le contenu du panier
    public function getContenu() {
        if(!$this->id_panier) {
            return [];
        }
        
        $query = "SELECT ep.id_element, ep.id_produit, ep.quantite, p.nom, p.prix, p.image_url, p.stock, p.categorie,
                  (ep.quantite * p.prix) as sous_total, p.prix as prix_unitaire 
                  FROM elements_panier ep 
                  JOIN produits p ON ep.id_produit = p.id_produit 
                  WHERE ep.id_panier = :id_panier";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_panier', $this->id_panier);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    // Calculer le total du panier
    public function calculerTotal() {
        if(!$this->id_panier) {
            return 0;
        }
        
        $query = "SELECT SUM(ep.quantite * p.prix) as total 
                  FROM elements_panier ep 
                  JOIN produits p ON ep.id_produit = p.id_produit 
                  WHERE ep.id_panier = :id_panier";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_panier', $this->id_panier);
        $stmt->execute();
        
        $resultat = $stmt->fetch();
        
        return $resultat ? $resultat['total'] : 0;
    }
    
    // Vider le panier
    public function vider() {
        if(!$this->id_panier) {
            return false;
        }
        
        $query = "DELETE FROM elements_panier WHERE id_panier = :id_panier";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_panier', $this->id_panier);
        
        return $stmt->execute();
    }
}
?>