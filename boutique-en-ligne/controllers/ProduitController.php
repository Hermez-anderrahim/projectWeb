<?php
// controllers/ProduitController.php
require_once __DIR__ . '/../models/Produit.php';
require_once __DIR__ . '/../utils/auth.php';

class ProduitController {
    private $produit;
    
    public function __construct() {
        $this->produit = new Produit();
    }
    
    // Obtenir tous les produits
    public function getTousProduits($page = 1, $limite = 10, $categorie = null) {
        $produits = $this->produit->getTous($limite, $page, $categorie);
        
        return [
            'success' => true,
            'produits' => $produits,
            'page' => $page,
            'limite' => $limite
        ];
    }
    
    // Obtenir un produit par son ID
    public function getProduitParId($id_produit) {
        if($this->produit->getById($id_produit)) {
            return [
                'success' => true,
                'produit' => [
                    'id_produit' => $this->produit->id_produit,
                    'nom' => $this->produit->nom,
                    'description' => $this->produit->description,
                    'prix' => $this->produit->prix,
                    'stock' => $this->produit->stock,
                    'categorie' => $this->produit->categorie,
                    'image_url' => $this->produit->image_url,
                    'date_ajout' => $this->produit->date_ajout
                ]
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Produit non trouvé.'
            ];
        }
    }
    
    // Rechercher des produits
    public function rechercherProduits($terme, $categorie = null, $prix_min = null, $prix_max = null) {
        $produits = $this->produit->rechercher($terme, $categorie, $prix_min, $prix_max);
        
        return [
            'success' => true,
            'produits' => $produits,
            'nb_resultats' => count($produits)
        ];
    }
    
    /**
     * Gère le téléchargement d'une image et retourne l'URL relative
     * 
     * @param string $image_data Données de l'image au format base64
     * @return string|null L'URL de l'image ou null si erreur
     */
    private function handleImageUpload($image_data) {
        // Vérifier si les données sont au format base64
        if (strpos($image_data, 'data:image/') !== 0) {
            return null;
        }
        
        // Extraire les informations de l'image
        $image_parts = explode(";base64,", $image_data);
        $image_type_aux = explode("image/", $image_parts[0]);
        $image_type = $image_type_aux[1];
        $image_base64 = base64_decode($image_parts[1]);
        
        // Vérifier que le type de fichier est valide
        $allowed_types = ['jpeg', 'jpg', 'png', 'gif'];
        if (!in_array($image_type, $allowed_types)) {
            return null;
        }
        
        // Créer un nom de fichier unique
        $file_name = 'product_' . uniqid() . '.' . $image_type;
        
        // Chemin complet vers le dossier d'upload
        $upload_dir = __DIR__ . '/../assets/uploads/products/';
        $file_path = $upload_dir . $file_name;
        
        // Vérifier si le dossier existe, sinon le créer avec des permissions récursives
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
            // Set proper permissions on the directory
            chmod($upload_dir, 0777);
        }
        
        // Sauvegarder l'image
        if (file_put_contents($file_path, $image_base64)) {
            // Make sure the file is readable
            chmod($file_path, 0644);
            // Retourner l'URL relative
            return '/assets/uploads/products/' . $file_name;
        }
        
        return null;
    }

    // Ajouter un nouveau produit (admin seulement)
    public function ajouterProduit($donnees) {
        // Vérifier si l'utilisateur est connecté en tant qu'admin
        $resultat_auth = Auth::verifierAuthentification(true);
        
        if(!$resultat_auth['authentifie']) {
            return [
                'success' => false,
                'message' => 'Non autorisé. Vous devez être administrateur.'
            ];
        }
        
        // Vérifier les données requises
        if(!isset($donnees['nom']) || !isset($donnees['prix']) || !isset($donnees['stock'])) {
            return [
                'success' => false,
                'message' => 'Données incomplètes. Veuillez fournir nom, prix et stock.'
            ];
        }
        
        // Gérer l'upload d'image si présent
        $image_url = null;
        if (isset($donnees['image_data']) && !empty($donnees['image_data'])) {
            $image_url = $this->handleImageUpload($donnees['image_data']);
            if (!$image_url) {
                return [
                    'success' => false,
                    'message' => 'Erreur lors du téléchargement de l\'image. Format non supporté ou image corrompue.'
                ];
            }
        } elseif (isset($donnees['image_url']) && !empty($donnees['image_url'])) {
            $image_url = $donnees['image_url'];
        }
        
        // Ajouter le produit
        $id_produit = $this->produit->creer(
            $donnees['nom'],
            $donnees['description'] ?? '',
            $donnees['prix'],
            $donnees['stock'],
            $donnees['categorie'] ?? null,
            $image_url
        );
        
        if($id_produit) {
            return [
                'success' => true,
                'message' => 'Produit ajouté avec succès.',
                'id_produit' => $id_produit
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Erreur lors de l\'ajout du produit.'
            ];
        }
    }
    
    // Mettre à jour un produit (admin seulement)
    public function mettreAJourProduit($id_produit, $donnees) {
        // Vérifier si l'utilisateur est connecté en tant qu'admin
        $resultat_auth = Auth::verifierAuthentification(true);
        
        if(!$resultat_auth['authentifie']) {
            return [
                'success' => false,
                'message' => 'Non autorisé. Vous devez être administrateur.'
            ];
        }
        
        // Récupérer le produit
        if(!$this->produit->getById($id_produit)) {
            return [
                'success' => false,
                'message' => 'Produit non trouvé.'
            ];
        }
        
        // Gérer l'upload d'image si présent
        if (isset($donnees['image_data']) && !empty($donnees['image_data'])) {
            $image_url = $this->handleImageUpload($donnees['image_data']);
            if (!$image_url) {
                return [
                    'success' => false,
                    'message' => 'Erreur lors du téléchargement de l\'image. Format non supporté ou image corrompue.'
                ];
            }
            $this->produit->image_url = $image_url;
        } elseif (isset($donnees['image_url'])) {
            $this->produit->image_url = $donnees['image_url'];
        }
        
        // Mettre à jour les informations
        if(isset($donnees['nom'])) $this->produit->nom = $donnees['nom'];
        if(isset($donnees['description'])) $this->produit->description = $donnees['description'];
        if(isset($donnees['prix'])) $this->produit->prix = $donnees['prix'];
        if(isset($donnees['stock'])) $this->produit->stock = $donnees['stock'];
        if(isset($donnees['categorie'])) $this->produit->categorie = $donnees['categorie'];
        
        if($this->produit->mettreAJour()) {
            return [
                'success' => true,
                'message' => 'Produit mis à jour avec succès.'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Erreur lors de la mise à jour du produit.'
            ];
        }
    }
    
    // Supprimer un produit (admin seulement)
    public function supprimerProduit($id_produit) {
        // Vérifier si l'utilisateur est connecté en tant qu'admin
        $resultat_auth = Auth::verifierAuthentification(true);
        
        if(!$resultat_auth['authentifie']) {
            return [
                'success' => false,
                'message' => 'Non autorisé. Vous devez être administrateur.'
            ];
        }
        
        if($this->produit->supprimer($id_produit)) {
            return [
                'success' => true,
                'message' => 'Produit supprimé avec succès.'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Erreur lors de la suppression du produit.'
            ];
        }
    }
    
    // Obtenir les produits avec un stock faible (admin seulement)
    public function getLowStockProducts($limit = 5) {
        // Vérifier si l'utilisateur est connecté en tant qu'admin
        $resultat_auth = Auth::verifierAuthentification(true);
        
        if(!$resultat_auth['authentifie']) {
            return [
                'success' => false,
                'message' => 'Non autorisé. Vous devez être administrateur.'
            ];
        }
        
        try {
            // Récupérer les produits avec un stock faible (5 ou moins)
            $db = Database::getInstance()->getConnection();
            $query = "SELECT * FROM produits WHERE stock <= 5 ORDER BY stock ASC LIMIT :limite";
            
            $stmt = $db->prepare($query);
            $stmt->bindParam(':limite', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            $produits = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return [
                'success' => true,
                'produits' => $produits
            ];
        } catch(PDOException $e) {
            return [
                'success' => false,
                'message' => 'Erreur de base de données: ' . $e->getMessage()
            ];
        }
    }
}
?>