<?php
// controllers/PanierController.php
require_once __DIR__ . '/../models/Panier.php';
require_once __DIR__ . '/../models/Produit.php';
require_once __DIR__ . '/../utils/auth.php';

class PanierController {
    private $panier;
    private $produit;
    
    public function __construct() {
        $this->panier = new Panier();
        $this->produit = new Produit();
    }
    
    // Récupérer le contenu du panier de l'utilisateur
    public function getContenuPanier() {
        // Vérifier si l'utilisateur est connecté
        $resultat_auth = Auth::verifierAuthentification();
        
        if(!$resultat_auth['authentifie']) {
            return [
                'success' => false,
                'message' => 'Utilisateur non connecté.'
            ];
        }
        
        // Récupérer ou créer le panier de l'utilisateur
        $id_panier = $this->panier->getOuCreerPourUtilisateur($resultat_auth['utilisateur']['id']);
        
        if(!$id_panier) {
            return [
                'success' => false,
                'message' => 'Erreur lors de la récupération du panier.'
            ];
        }
        
        // Récupérer le contenu du panier
        $contenu = $this->panier->getContenu();
        $total = $this->panier->calculerTotal();
        
        return [
            'success' => true,
            'panier' => [
                'id_panier' => $this->panier->id_panier,
                'contenu' => $contenu,
                'total' => $total,
                'nombre_articles' => count($contenu)
            ]
        ];
    }
    
    // Ajouter un produit au panier
    public function ajouterAuPanier($id_produit, $quantite = 1) {
        // Vérifier si l'utilisateur est connecté
        $resultat_auth = Auth::verifierAuthentification();
        
        if(!$resultat_auth['authentifie']) {
            return [
                'success' => false,
                'message' => 'Utilisateur non connecté.'
            ];
        }
        
        // Vérifier si le produit existe
        if(!$this->produit->getById($id_produit)) {
            return [
                'success' => false,
                'message' => 'Produit non trouvé.'
            ];
        }
        
        // Vérifier si le stock est suffisant
        if($this->produit->stock < $quantite) {
            return [
                'success' => false,
                'message' => 'Stock insuffisant. Stock disponible: ' . $this->produit->stock
            ];
        }
        
        // Récupérer ou créer le panier de l'utilisateur
        $id_panier = $this->panier->getOuCreerPourUtilisateur($resultat_auth['utilisateur']['id']);
        
        if(!$id_panier) {
            return [
                'success' => false,
                'message' => 'Erreur lors de la récupération du panier.'
            ];
        }
        
        // Ajouter le produit au panier
        if($this->panier->ajouterProduit($id_produit, $quantite)) {
            return [
                'success' => true,
                'message' => 'Produit ajouté au panier.',
                'panier' => [
                    'id_panier' => $this->panier->id_panier,
                    'contenu' => $this->panier->getContenu(),
                    'total' => $this->panier->calculerTotal()
                ]
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Erreur lors de l\'ajout du produit au panier.'
            ];
        }
    }
    
    // Mettre à jour la quantité d'un produit dans le panier
    public function mettreAJourQuantite($id_produit, $quantite) {
        // Vérifier si l'utilisateur est connecté
        $resultat_auth = Auth::verifierAuthentification();
        
        if(!$resultat_auth['authentifie']) {
            return [
                'success' => false,
                'message' => 'Utilisateur non connecté.'
            ];
        }
        
        // Vérifier si le produit existe
        if(!$this->produit->getById($id_produit)) {
            return [
                'success' => false,
                'message' => 'Produit non trouvé.'
            ];
        }
        
        // Si la quantité est 0 ou négative, supprimer le produit du panier
        if($quantite <= 0) {
            return $this->supprimerDuPanier($id_produit);
        }
        
        // Vérifier si le stock est suffisant
        if($this->produit->stock < $quantite) {
            return [
                'success' => false,
                'message' => 'Stock insuffisant. Stock disponible: ' . $this->produit->stock
            ];
        }
        
        // Récupérer ou créer le panier de l'utilisateur
        $id_panier = $this->panier->getOuCreerPourUtilisateur($resultat_auth['utilisateur']['id']);
        
        if(!$id_panier) {
            return [
                'success' => false,
                'message' => 'Erreur lors de la récupération du panier.'
            ];
        }
        
        // Mettre à jour la quantité
        if($this->panier->mettreAJourQuantite($id_produit, $quantite)) {
            return [
                'success' => true,
                'message' => 'Quantité mise à jour.',
                'panier' => [
                    'id_panier' => $this->panier->id_panier,
                    'contenu' => $this->panier->getContenu(),
                    'total' => $this->panier->calculerTotal()
                ]
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Erreur lors de la mise à jour de la quantité.'
            ];
        }
    }
    
    // Supprimer un produit du panier
    public function supprimerDuPanier($id_produit) {
        // Vérifier si l'utilisateur est connecté
        $resultat_auth = Auth::verifierAuthentification();
        
        if(!$resultat_auth['authentifie']) {
            return [
                'success' => false,
                'message' => 'Utilisateur non connecté.'
            ];
        }
        
        // Récupérer ou créer le panier de l'utilisateur
        $id_panier = $this->panier->getOuCreerPourUtilisateur($resultat_auth['utilisateur']['id']);
        
        if(!$id_panier) {
            return [
                'success' => false,
                'message' => 'Erreur lors de la récupération du panier.'
            ];
        }
        
        // Supprimer le produit du panier
        if($this->panier->supprimerProduit($id_produit)) {
            return [
                'success' => true,
                'message' => 'Produit supprimé du panier.',
                'panier' => [
                    'id_panier' => $this->panier->id_panier,
                    'contenu' => $this->panier->getContenu(),
                    'total' => $this->panier->calculerTotal()
                ]
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Erreur lors de la suppression du produit du panier.'
            ];
        }
    }
    
    // Vider le panier
    public function viderPanier() {
        // Vérifier si l'utilisateur est connecté
        $resultat_auth = Auth::verifierAuthentification();
        
        if(!$resultat_auth['authentifie']) {
            return [
                'success' => false,
                'message' => 'Utilisateur non connecté.'
            ];
        }
        
        // Récupérer ou créer le panier de l'utilisateur
        $id_panier = $this->panier->getOuCreerPourUtilisateur($resultat_auth['utilisateur']['id']);
        
        if(!$id_panier) {
            return [
                'success' => false,
                'message' => 'Erreur lors de la récupération du panier.'
            ];
        }
        
        // Vider le panier
        if($this->panier->vider()) {
            return [
                'success' => true,
                'message' => 'Panier vidé avec succès.'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Erreur lors du vidage du panier.'
            ];
        }
    }
}
?>