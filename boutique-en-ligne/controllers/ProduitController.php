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
    public function getTousProduits($page = 1, $limite = 10, $categorie = null, $tri = null) {
        $produits = $this->produit->getTous($limite, $page, $categorie, $tri);
        
        // Process each product to include proper image URLs
        $processed_products = [];
        foreach ($produits as $produit) {
            // Skip products with zero or negative stock for display
            if ($produit['stock'] <= 0) {
                continue;
            }
            
            // Get primary image for this product if it exists
            $image_principale = $this->produit->getImagePrincipale($produit['id_produit']);
            
            $produit_with_image = $produit;
            if ($image_principale) {
                $produit_with_image['image_url'] = '/api/image.php?id=' . $image_principale['id_image'];
            }
            
            $processed_products[] = $produit_with_image;
        }
        
        return [
            'success' => true,
            'produits' => $processed_products,
            'page' => $page,
            'limite' => $limite
        ];
    }
    
    // Obtenir un produit par son ID
    public function getProduitParId($id_produit) {
        if($this->produit->getById($id_produit)) {
            // Récupérer les images du produit depuis la base de données
            $images = $this->produit->getImagesProduit($id_produit);
            $images_avec_url = [];
            
            foreach ($images as $image) {
                $images_avec_url[] = [
                    'id_image' => $image['id_image'],
                    'is_primary' => $image['is_primary'],
                    'titre' => $image['titre'],
                    'ordre' => $image['ordre'],
                    'url' => '/api/image.php?id=' . $image['id_image']
                ];
            }
            
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
                    'images' => $images_avec_url,
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
    public function rechercherProduits($terme, $categorie = null, $prix_min = null, $prix_max = null, $page = 1, $limite = 10, $tri = null) {
        $produits = $this->produit->rechercher($terme, $categorie, $prix_min, $prix_max, $page, $limite, $tri);
        $totalCount = $this->produit->compterRecherche($terme, $categorie, $prix_min, $prix_max);
        
        // Process each product to include proper image URLs
        $processed_products = [];
        foreach ($produits as $produit) {
            // Skip products with zero or negative stock for display
            if ($produit['stock'] <= 0) {
                continue;
            }
            
            // Get primary image for this product if it exists
            $image_principale = $this->produit->getImagePrincipale($produit['id_produit']);
            
            $produit_with_image = $produit;
            if ($image_principale) {
                $produit_with_image['image_url'] = '/api/image.php?id=' . $image_principale['id_image'];
            }
            
            $processed_products[] = $produit_with_image;
        }
        
        // Recalculate the total count excluding zero stock products
        $displayableCount = count($processed_products);
        
        return [
            'success' => true,
            'produits' => $processed_products,
            'nb_resultats' => $displayableCount,
            'total' => $totalCount,
            'page' => $page,
            'limite' => $limite
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

    /**
     * Gère le téléchargement d'une image dans la base de données
     * 
     * @param string $image_data Données de l'image au format base64
     * @param int $id_produit ID du produit associé
     * @param bool $is_primary Si c'est l'image principale
     * @param string $titre Titre optionnel de l'image
     * @return int|null L'ID de l'image ajoutée ou null si erreur
     */
    private function handleDatabaseImageUpload($image_data, $id_produit, $is_primary = false, $titre = null) {
        try {
            // Vérifier si les données sont au format base64
            if (strpos($image_data, 'data:image/') !== 0) {
                return null;
            }
            
            // Extraire les informations de l'image
            $image_parts = explode(";base64,", $image_data);
            $image_type_aux = explode("image/", $image_parts[0]);
            $image_type = $image_type_aux[1];
            $image_binary = base64_decode($image_parts[1]);
            
            // Vérifier que le type de fichier est valide
            $allowed_types = ['jpeg', 'jpg', 'png', 'gif', 'webp'];
            if (!in_array($image_type, $allowed_types)) {
                return null;
            }
            
            // Type MIME complet
            $mime_type = "image/" . $image_type;
            
            // Ajouter l'image dans la base de données
            return $this->produit->ajouterImage($id_produit, $image_binary, $mime_type, $is_primary, $titre);
            
        } catch (Exception $e) {
            error_log("Erreur lors du téléchargement de l'image: " . $e->getMessage());
            return null;
        }
    }

    // Ajouter une image à un produit existant (admin seulement)
    public function ajouterImageProduit($id_produit, $donnees) {
        // Vérifier si l'utilisateur est connecté en tant qu'admin
        $resultat_auth = Auth::verifierAuthentification(true);
        
        if(!$resultat_auth['authentifie']) {
            return [
                'success' => false,
                'message' => 'Non autorisé. Vous devez être administrateur.'
            ];
        }
        
        // Vérifier si le produit existe
        if(!$this->produit->getById($id_produit)) {
            return [
                'success' => false,
                'message' => 'Produit non trouvé.'
            ];
        }
        
        // Vérifier que les données d'image sont présentes
        if(!isset($donnees['image_data']) || empty($donnees['image_data'])) {
            return [
                'success' => false,
                'message' => 'Aucune donnée dimage fournie.'
            ];
        }
        
        // Définir si c'est l'image principale
        $is_primary = isset($donnees['is_primary']) ? (bool)$donnees['is_primary'] : false;
        
        // Titre optionnel
        $titre = $donnees['titre'] ?? null;
        
        // Gérer l'upload dans la base de données
        $id_image = $this->handleDatabaseImageUpload($donnees['image_data'], $id_produit, $is_primary, $titre);
        
        if($id_image) {
            return [
                'success' => true,
                'message' => 'Image ajoutée avec succès.',
                'id_image' => $id_image
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Erreur lors de l\'ajout de l\'image.'
            ];
        }
    }
    
    // Supprimer une image (admin seulement)
    public function supprimerImageProduit($id_image) {
        // Vérifier si l'utilisateur est connecté en tant qu'admin
        $resultat_auth = Auth::verifierAuthentification(true);
        
        if(!$resultat_auth['authentifie']) {
            return [
                'success' => false,
                'message' => 'Non autorisé. Vous devez être administrateur.'
            ];
        }
        
        if($this->produit->supprimerImage($id_image)) {
            return [
                'success' => true,
                'message' => 'Image supprimée avec succès.'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Erreur lors de la suppression de l\'image.'
            ];
        }
    }
    
    // Définir une image comme principale (admin seulement)
    public function definirImagePrincipale($id_image) {
        // Vérifier si l'utilisateur est connecté en tant qu'admin
        $resultat_auth = Auth::verifierAuthentification(true);
        
        if(!$resultat_auth['authentifie']) {
            return [
                'success' => false,
                'message' => 'Non autorisé. Vous devez être administrateur.'
            ];
        }
        
        if($this->produit->definirImagePrincipale($id_image)) {
            return [
                'success' => true,
                'message' => 'Image définie comme principale avec succès.'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Erreur lors de la définition de l\'image principale.'
            ];
        }
    }
    
    // Obtenir toutes les images d'un produit
    public function getImagesProduit($id_produit) {
        // Vérifier si le produit existe
        if(!$this->produit->getById($id_produit)) {
            return [
                'success' => false,
                'message' => 'Produit non trouvé.'
            ];
        }
        
        $images = $this->produit->getImagesProduit($id_produit);
        
        // Transformer les images pour inclure une URL vers l'API
        $images_avec_url = array_map(function($image) {
            $image['url'] = '/api/image.php?id=' . $image['id_image'];
            unset($image['image_data']);  // S'assurer que les données binaires ne sont pas renvoyées
            return $image;
        }, $images);
        
        return [
            'success' => true,
            'images' => $images_avec_url
        ];
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
        
        // Nous n'utilisons plus image_url pour les nouvelles images
        // mais conservons la compatibilité pour les anciens produits
        $image_url = isset($donnees['image_url']) && !empty($donnees['image_url']) 
            ? $donnees['image_url'] 
            : null;
        
        // Ajouter le produit
        $id_produit = $this->produit->creer(
            $donnees['nom'],
            $donnees['description'] ?? '',
            $donnees['prix'],
            $donnees['stock'],
            $donnees['categorie'] ?? null,
            $image_url // Peut être null si nous utilisons des images en base de données
        );
        
        if(!$id_produit) {
            return [
                'success' => false,
                'message' => 'Erreur lors de l\'ajout du produit.'
            ];
        }
        
        // Si des données d'image sont fournies, enregistrer l'image dans la base de données
        if (isset($donnees['image_data']) && !empty($donnees['image_data'])) {
            $id_image = $this->handleDatabaseImageUpload(
                $donnees['image_data'], 
                $id_produit, 
                true, // C'est l'image principale
                $donnees['titre_image'] ?? $donnees['nom'] // Utiliser le nom du produit comme titre par défaut
            );
            
            if (!$id_image) {
                // L'image n'a pas pu être ajoutée, mais le produit a été créé
                // Nous retournons quand même un succès mais avec un avertissement
                return [
                    'success' => true,
                    'warning' => true,
                    'message' => 'Produit ajouté avec succès, mais erreur lors de l\'ajout de l\'image.',
                    'id_produit' => $id_produit
                ];
            }
        }
        
        return [
            'success' => true,
            'message' => 'Produit ajouté avec succès.',
            'id_produit' => $id_produit
        ];
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
            
            // Process each product to include proper image URLs
            $processed_products = [];
            foreach ($produits as $produit) {
                // Get primary image for this product if it exists
                $image_principale = $this->produit->getImagePrincipale($produit['id_produit']);
                
                $produit_with_image = $produit;
                if ($image_principale) {
                    $produit_with_image['image_url'] = '/api/image.php?id=' . $image_principale['id_image'];
                }
                
                $processed_products[] = $produit_with_image;
            }
            
            return [
                'success' => true,
                'produits' => $processed_products
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