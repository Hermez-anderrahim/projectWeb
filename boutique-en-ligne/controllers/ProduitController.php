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
        
        // Ajouter le produit
        $id_produit = $this->produit->creer(
            $donnees['nom'],
            $donnees['description'] ?? '',
            $donnees['prix'],
            $donnees['stock'],
            $donnees['categorie'] ?? null,
            $donnees['image_url'] ?? null
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
        
        // Mettre à jour les informations
        if(isset($donnees['nom'])) $this->produit->nom = $donnees['nom'];
        if(isset($donnees['description'])) $this->produit->description = $donnees['description'];
        if(isset($donnees['prix'])) $this->produit->prix = $donnees['prix'];
        if(isset($donnees['stock'])) $this->produit->stock = $donnees['stock'];
        if(isset($donnees['categorie'])) $this->produit->categorie = $donnees['categorie'];
        if(isset($donnees['image_url'])) $this->produit->image_url = $donnees['image_url'];
        
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
}
?>