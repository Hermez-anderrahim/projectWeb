<?php
// models/Produit.php
require_once __DIR__ . '/../config/database.php';

class Produit {
    private $conn;
    
    // Propriétés
    public $id_produit;
    public $nom;
    public $description;
    public $prix;
    public $stock;
    public $categorie;
    public $image_url;
    public $date_ajout;
    
    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
    }
    
    // Créer un nouveau produit
    public function creer($nom, $description, $prix, $stock, $categorie, $image_url = null) {
        $query = "INSERT INTO produits (nom, description, prix, stock, categorie, image_url) 
                  VALUES (:nom, :description, :prix, :stock, :categorie, :image_url)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':prix', $prix);
        $stmt->bindParam(':stock', $stock);
        $stmt->bindParam(':categorie', $categorie);
        $stmt->bindParam(':image_url', $image_url);
        
        if($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }
    
    // Récupérer tous les produits
    public function getTous($limite = 10, $page = 1, $categorie = null) {
        $offset = ($page - 1) * $limite;
        $condition = "";
        $params = [];
        
        if($categorie) {
            $condition = "WHERE categorie = :categorie";
            $params[':categorie'] = $categorie;
        }
        
        $query = "SELECT * FROM produits $condition ORDER BY date_ajout DESC LIMIT :limite OFFSET :offset";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        
        if($categorie) {
            $stmt->bindParam(':categorie', $categorie);
        }
        
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    // Récupérer un produit par son ID
    public function getById($id_produit) {
        $query = "SELECT * FROM produits WHERE id_produit = :id LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id_produit);
        $stmt->execute();
        
        $produit = $stmt->fetch();
        
        if($produit) {
            $this->id_produit = $produit['id_produit'];
            $this->nom = $produit['nom'];
            $this->description = $produit['description'];
            $this->prix = $produit['prix'];
            $this->stock = $produit['stock'];
            $this->categorie = $produit['categorie'];
            $this->image_url = $produit['image_url'];
            $this->date_ajout = $produit['date_ajout'];
            
            return true;
        }
        
        return false;
    }
    
    // Mettre à jour un produit
    public function mettreAJour() {
        $query = "UPDATE produits SET 
                  nom = :nom, 
                  description = :description, 
                  prix = :prix, 
                  stock = :stock, 
                  categorie = :categorie, 
                  image_url = :image_url 
                  WHERE id_produit = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nom', $this->nom);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':prix', $this->prix);
        $stmt->bindParam(':stock', $this->stock);
        $stmt->bindParam(':categorie', $this->categorie);
        $stmt->bindParam(':image_url', $this->image_url);
        $stmt->bindParam(':id', $this->id_produit);
        
        return $stmt->execute();
    }
    
    // Supprimer un produit
    public function supprimer($id_produit) {
        $query = "DELETE FROM produits WHERE id_produit = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id_produit);
        
        return $stmt->execute();
    }
    
    // Rechercher des produits
    public function rechercher($terme, $categorie = null, $prix_min = null, $prix_max = null, $page = 1, $limite = 10) {
        $conditions = [];
        $params = [];
        $offset = ($page - 1) * $limite;
        
        if($terme) {
            $conditions[] = "(nom LIKE :terme OR description LIKE :terme)";
            $params[':terme'] = "%$terme%";
        }
        
        if($categorie) {
            $conditions[] = "categorie = :categorie";
            $params[':categorie'] = $categorie;
        }
        
        if($prix_min) {
            $conditions[] = "prix >= :prix_min";
            $params[':prix_min'] = $prix_min;
        }
        
        if($prix_max) {
            $conditions[] = "prix <= :prix_max";
            $params[':prix_max'] = $prix_max;
        }
        
        $where = count($conditions) > 0 ? "WHERE " . implode(" AND ", $conditions) : "";
        
        $query = "SELECT * FROM produits $where ORDER BY date_ajout DESC LIMIT :limite OFFSET :offset";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        
        foreach($params as $key => $value) {
            if(is_int($value)) {
                $stmt->bindValue($key, $value, PDO::PARAM_INT);
            } else {
                $stmt->bindValue($key, $value);
            }
        }
        
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    // Compter le nombre total de résultats d'une recherche
    public function compterRecherche($terme, $categorie = null, $prix_min = null, $prix_max = null) {
        $conditions = [];
        $params = [];
        
        if($terme) {
            $conditions[] = "(nom LIKE :terme OR description LIKE :terme)";
            $params[':terme'] = "%$terme%";
        }
        
        if($categorie) {
            $conditions[] = "categorie = :categorie";
            $params[':categorie'] = $categorie;
        }
        
        if($prix_min) {
            $conditions[] = "prix >= :prix_min";
            $params[':prix_min'] = $prix_min;
        }
        
        if($prix_max) {
            $conditions[] = "prix <= :prix_max";
            $params[':prix_max'] = $prix_max;
        }
        
        $where = count($conditions) > 0 ? "WHERE " . implode(" AND ", $conditions) : "";
        
        $query = "SELECT COUNT(*) as total FROM produits $where";
        
        $stmt = $this->conn->prepare($query);
        
        foreach($params as $key => $value) {
            if(is_int($value)) {
                $stmt->bindValue($key, $value, PDO::PARAM_INT);
            } else {
                $stmt->bindValue($key, $value);
            }
        }
        
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['total'];
    }
}
?>