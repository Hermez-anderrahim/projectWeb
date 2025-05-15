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
    
    // Méthodes pour la gestion des images en base de données
    
    /**
     * Ajouter une image pour un produit dans la base de données
     * 
     * @param int $id_produit ID du produit
     * @param string $image_data Données binaires de l'image (contenu du fichier)
     * @param string $mime_type Type MIME de l'image
     * @param bool $is_primary Si c'est l'image principale
     * @param string $titre Titre optionnel de l'image
     * @param int $ordre Ordre d'affichage
     * @return int|bool ID de l'image créée ou false en cas d'échec
     */
    public function ajouterImage($id_produit, $image_data, $mime_type, $is_primary = false, $titre = null, $ordre = 0) {
        try {
            // Si c'est l'image principale, d'abord réinitialiser toutes les autres
            if ($is_primary) {
                $query = "UPDATE produit_images SET is_primary = FALSE WHERE id_produit = :id_produit";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':id_produit', $id_produit, PDO::PARAM_INT);
                $stmt->execute();
            }
            
            $query = "INSERT INTO produit_images (id_produit, image_data, mime_type, is_primary, titre, ordre) 
                      VALUES (:id_produit, :image_data, :mime_type, :is_primary, :titre, :ordre)";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id_produit', $id_produit, PDO::PARAM_INT);
            $stmt->bindParam(':image_data', $image_data, PDO::PARAM_LOB);
            $stmt->bindParam(':mime_type', $mime_type);
            $stmt->bindParam(':is_primary', $is_primary, PDO::PARAM_BOOL);
            $stmt->bindParam(':titre', $titre);
            $stmt->bindParam(':ordre', $ordre, PDO::PARAM_INT);
            
            if ($stmt->execute()) {
                return $this->conn->lastInsertId();
            }
            return false;
        } catch (PDOException $e) {
            error_log("Erreur lors de l'ajout d'image: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Récupérer une image par son ID
     * 
     * @param int $id_image ID de l'image
     * @return array|bool Données de l'image ou false si non trouvée
     */
    public function getImageById($id_image) {
        $query = "SELECT * FROM produit_images WHERE id_image = :id_image LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_image', $id_image, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Récupérer toutes les images d'un produit
     * 
     * @param int $id_produit ID du produit
     * @param bool $include_data Si true, inclut les données binaires des images
     * @return array Images du produit
     */
    public function getImagesProduit($id_produit, $include_data = false) {
        $select = $include_data ? "*" : "id_image, id_produit, mime_type, is_primary, titre, ordre, date_ajout";
        $query = "SELECT $select FROM produit_images WHERE id_produit = :id_produit ORDER BY is_primary DESC, ordre ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_produit', $id_produit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Récupérer l'image principale d'un produit
     * 
     * @param int $id_produit ID du produit
     * @param bool $include_data Si true, inclut les données binaires de l'image
     * @return array|bool Données de l'image principale ou false si non trouvée
     */
    public function getImagePrincipale($id_produit, $include_data = false) {
        $select = $include_data ? "*" : "id_image, id_produit, mime_type, is_primary, titre, ordre, date_ajout";
        $query = "SELECT $select FROM produit_images WHERE id_produit = :id_produit AND is_primary = TRUE LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_produit', $id_produit, PDO::PARAM_INT);
        $stmt->execute();
        
        $image = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Si aucune image principale n'est définie, on prend la première
        if (!$image) {
            $select = $include_data ? "*" : "id_image, id_produit, mime_type, is_primary, titre, ordre, date_ajout";
            $query = "SELECT $select FROM produit_images WHERE id_produit = :id_produit ORDER BY ordre ASC LIMIT 1";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id_produit', $id_produit, PDO::PARAM_INT);
            $stmt->execute();
            
            $image = $stmt->fetch(PDO::FETCH_ASSOC);
        }
        
        return $image;
    }
    
    /**
     * Supprimer une image
     * 
     * @param int $id_image ID de l'image
     * @return bool Succès ou échec
     */
    public function supprimerImage($id_image) {
        $query = "DELETE FROM produit_images WHERE id_image = :id_image";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_image', $id_image, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    
    /**
     * Définir une image comme principale
     * 
     * @param int $id_image ID de l'image
     * @return bool Succès ou échec
     */
    public function definirImagePrincipale($id_image) {
        try {
            // Récupérer l'ID du produit pour cette image
            $query = "SELECT id_produit FROM produit_images WHERE id_image = :id_image LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id_image', $id_image, PDO::PARAM_INT);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$result) {
                return false;
            }
            
            $id_produit = $result['id_produit'];
            
            // Retirer le statut principal de toutes les images du produit
            $query = "UPDATE produit_images SET is_primary = FALSE WHERE id_produit = :id_produit";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id_produit', $id_produit, PDO::PARAM_INT);
            $stmt->execute();
            
            // Définir cette image comme principale
            $query = "UPDATE produit_images SET is_primary = TRUE WHERE id_image = :id_image";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id_image', $id_image, PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erreur lors de la définition de l'image principale: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Mettre à jour l'ordre des images
     * 
     * @param int $id_image ID de l'image
     * @param int $ordre Nouvel ordre
     * @return bool Succès ou échec
     */
    public function mettreAJourOrdreImage($id_image, $ordre) {
        $query = "UPDATE produit_images SET ordre = :ordre WHERE id_image = :id_image";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_image', $id_image, PDO::PARAM_INT);
        $stmt->bindParam(':ordre', $ordre, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
}
?>