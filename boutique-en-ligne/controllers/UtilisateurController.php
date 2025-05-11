<?php
// controllers/UtilisateurController.php
require_once __DIR__ . '/../models/Utilisateur.php';
require_once __DIR__ . '/../utils/auth.php';

class UtilisateurController {
    private $utilisateur;
    
    public function __construct() {
        $this->utilisateur = new Utilisateur();
    }
    
    // Inscrire un nouvel utilisateur
    public function inscrire($donnees) {
        // Vérifier les données requises
        if(!isset($donnees['nom']) || !isset($donnees['prenom']) || !isset($donnees['email']) || !isset($donnees['mot_de_passe'])) {
            return [
                'success' => false,
                'message' => 'Données incomplètes. Veuillez fournir nom, prénom, email et mot de passe.'
            ];
        }
        
        // Vérifier si l'email existe déjà
        $query = "SELECT COUNT(*) as count FROM utilisateurs WHERE email = :email";
        $stmt = Database::getInstance()->getConnection()->prepare($query);
        $stmt->bindParam(':email', $donnees['email']);
        $stmt->execute();
        $result = $stmt->fetch();
        
        if($result['count'] > 0) {
            return [
                'success' => false,
                'message' => 'Cet email est déjà utilisé.'
            ];
        }
        
        // Créer l'utilisateur
        $id_utilisateur = $this->utilisateur->creer(
            $donnees['nom'],
            $donnees['prenom'],
            $donnees['email'],
            $donnees['mot_de_passe'],
            $donnees['adresse'] ?? null,
            $donnees['telephone'] ?? null,
            $donnees['est_admin'] ?? false
        );
        
        if($id_utilisateur) {
            return [
                'success' => true,
                'message' => 'Inscription réussie.',
                'id_utilisateur' => $id_utilisateur
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Erreur lors de l\'inscription.'
            ];
        }
    }
    
    // Inscrire un nouvel utilisateur
    public function inscrireUtilisateur($nom, $prenom, $email, $password, $adresse = null, $telephone = null) {
        // Vérifier si l'email existe déjà
        $query = "SELECT COUNT(*) as count FROM utilisateurs WHERE email = :email";
        $stmt = Database::getInstance()->getConnection()->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $result = $stmt->fetch();
        
        if($result['count'] > 0) {
            return [
                'success' => false,
                'message' => 'Cet email est déjà utilisé.'
            ];
        }
        
        // Créer l'utilisateur
        $id_utilisateur = $this->utilisateur->creer(
            $nom,
            $prenom,
            $email,
            $password,
            $adresse,
            $telephone,
            false // Par défaut, un nouvel utilisateur n'est pas admin
        );
        
        if($id_utilisateur) {
            return [
                'success' => true,
                'message' => 'Inscription réussie.',
                'id_utilisateur' => $id_utilisateur
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Erreur lors de l\'inscription.'
            ];
        }
    }
    
    // Connecter un utilisateur
    public function connecter($donnees) {
        // Vérifier les données requises
        if(!isset($donnees['email']) || !isset($donnees['mot_de_passe'])) {
            return [
                'success' => false,
                'message' => 'Données incomplètes. Veuillez fournir email et mot de passe.'
            ];
        }
        
        // Vérifier les identifiants
        if($this->utilisateur->connecter($donnees['email'], $donnees['mot_de_passe'])) {
            // Créer la session utilisateur
            session_start();
            $_SESSION['utilisateur'] = [
                'id' => $this->utilisateur->id_utilisateur,
                'nom' => $this->utilisateur->nom,
                'prenom' => $this->utilisateur->prenom,
                'email' => $this->utilisateur->email,
                'est_admin' => $this->utilisateur->est_admin
            ];
            
            return [
                'success' => true,
                'message' => 'Connexion réussie.',
                'utilisateur' => $_SESSION['utilisateur']
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Email ou mot de passe incorrect.'
            ];
        }
    }
    
    // Connecter un utilisateur (adapted for API endpoint)
    public function connecterUtilisateur($email, $password, $se_souvenir = false) {
        // Vérifier les identifiants
        if($this->utilisateur->connecter($email, $password)) {
            // Créer la session utilisateur
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['utilisateur'] = [
                'id' => $this->utilisateur->id_utilisateur,
                'nom' => $this->utilisateur->nom,
                'prenom' => $this->utilisateur->prenom,
                'email' => $this->utilisateur->email,
                'est_admin' => $this->utilisateur->est_admin
            ];
            
            // Set a remember-me cookie if requested
            if($se_souvenir) {
                $token = Auth::genererJeton();
                // Store the token in database (you would need to implement this)
                // $this->utilisateur->saveRememberToken($token);
                Auth::definirCookie('remember_token', $token);
            }
            
            return [
                'success' => true,
                'message' => 'Connexion réussie.',
                'utilisateur' => $_SESSION['utilisateur']
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Email ou mot de passe incorrect.'
            ];
        }
    }
    
    // Déconnecter l'utilisateur
    public function deconnecter() {
        session_start();
        session_unset();
        session_destroy();
        
        return [
            'success' => true,
            'message' => 'Déconnexion réussie.'
        ];
    }
    
    // Déconnecter l'utilisateur (adapted for API endpoint)
    public function deconnecterUtilisateur() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_unset();
        session_destroy();
        
        // Clear any remember-me cookies
        if(isset($_COOKIE['remember_token'])) {
            Auth::definirCookie('remember_token', '', -1); // Expire the cookie
        }
        
        return [
            'success' => true,
            'message' => 'Déconnexion réussie.'
        ];
    }
    
    // Obtenir les informations de l'utilisateur connecté
    public function getInfosUtilisateur() {
        // Vérifier si l'utilisateur est connecté
        $resultat_auth = Auth::verifierAuthentification();
        
        if(!$resultat_auth['authentifie']) {
            return [
                'success' => false,
                'message' => 'Utilisateur non connecté.'
            ];
        }
        
        // Récupérer les informations de l'utilisateur
        $id_utilisateur = $resultat_auth['utilisateur']['id'];
        
        if($this->utilisateur->getById($id_utilisateur)) {
            return [
                'success' => true,
                'utilisateur' => [
                    'id' => $this->utilisateur->id_utilisateur,
                    'nom' => $this->utilisateur->nom,
                    'prenom' => $this->utilisateur->prenom,
                    'email' => $this->utilisateur->email,
                    'adresse' => $this->utilisateur->adresse,
                    'telephone' => $this->utilisateur->telephone,
                    'est_admin' => $this->utilisateur->est_admin
                ]
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Utilisateur non trouvé.'
            ];
        }
    }
    
    // Mettre à jour les informations de l'utilisateur
    public function mettreAJour($donnees) {
        // Vérifier si l'utilisateur est connecté
        $resultat_auth = Auth::verifierAuthentification();
        
        if(!$resultat_auth['authentifie']) {
            return [
                'success' => false,
                'message' => 'Utilisateur non connecté.'
            ];
        }
        
        // Récupérer les informations de l'utilisateur
        $id_utilisateur = $resultat_auth['utilisateur']['id'];
        
        if(!$this->utilisateur->getById($id_utilisateur)) {
            return [
                'success' => false,
                'message' => 'Utilisateur non trouvé.'
            ];
        }
        
        // Mettre à jour les informations
        if(isset($donnees['nom'])) $this->utilisateur->nom = $donnees['nom'];
        if(isset($donnees['prenom'])) $this->utilisateur->prenom = $donnees['prenom'];
        if(isset($donnees['email'])) $this->utilisateur->email = $donnees['email'];
        if(isset($donnees['adresse'])) $this->utilisateur->adresse = $donnees['adresse'];
        if(isset($donnees['telephone'])) $this->utilisateur->telephone = $donnees['telephone'];
        
        if($this->utilisateur->mettreAJour()) {
            return [
                'success' => true,
                'message' => 'Informations mises à jour avec succès.'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Erreur lors de la mise à jour des informations.'
            ];
        }
    }
    
    // Mettre à jour les informations de l'utilisateur (adapted for API endpoint)
    public function mettreAJourUtilisateur($donnees) {
        // Vérifier si l'utilisateur est connecté
        $resultat_auth = Auth::verifierAuthentification();
        
        if(!$resultat_auth['authentifie']) {
            return [
                'success' => false,
                'message' => 'Utilisateur non connecté.'
            ];
        }
        
        // Récupérer les informations de l'utilisateur
        $id_utilisateur = $resultat_auth['utilisateur']['id'];
        
        if(!$this->utilisateur->getById($id_utilisateur)) {
            return [
                'success' => false,
                'message' => 'Utilisateur non trouvé.'
            ];
        }
        
        // Mettre à jour les informations
        if(isset($donnees['nom'])) $this->utilisateur->nom = $donnees['nom'];
        if(isset($donnees['prenom'])) $this->utilisateur->prenom = $donnees['prenom'];
        if(isset($donnees['email'])) $this->utilisateur->email = $donnees['email'];
        if(isset($donnees['adresse'])) $this->utilisateur->adresse = $donnees['adresse'];
        if(isset($donnees['telephone'])) $this->utilisateur->telephone = $donnees['telephone'];
        
        if($this->utilisateur->mettreAJour()) {
            // Update session data if it exists
            if(isset($_SESSION['utilisateur'])) {
                $_SESSION['utilisateur']['nom'] = $this->utilisateur->nom;
                $_SESSION['utilisateur']['prenom'] = $this->utilisateur->prenom;
                $_SESSION['utilisateur']['email'] = $this->utilisateur->email;
            }
            
            return [
                'success' => true,
                'message' => 'Informations mises à jour avec succès.'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Erreur lors de la mise à jour des informations.'
            ];
        }
    }
    
    // Changer le mot de passe
    public function changerMotDePasse($donnees) {
        // Vérifier si l'utilisateur est connecté
        $resultat_auth = Auth::verifierAuthentification();
        
        if(!$resultat_auth['authentifie']) {
            return [
                'success' => false,
                'message' => 'Utilisateur non connecté.'
            ];
        }
        
        // Vérifier les données requises
        if(!isset($donnees['ancien_mot_de_passe']) || !isset($donnees['nouveau_mot_de_passe'])) {
            return [
                'success' => false,
                'message' => 'Données incomplètes. Veuillez fournir l\'ancien et le nouveau mot de passe.'
            ];
        }
        
        // Récupérer les informations de l'utilisateur
        $id_utilisateur = $resultat_auth['utilisateur']['id'];
        
        if(!$this->utilisateur->getById($id_utilisateur)) {
            return [
                'success' => false,
                'message' => 'Utilisateur non trouvé.'
            ];
        }
        
        // Vérifier l'ancien mot de passe
        if($this->utilisateur->connecter($resultat_auth['utilisateur']['email'], $donnees['ancien_mot_de_passe'])) {
            // Changer le mot de passe
            if($this->utilisateur->changerMotDePasse($donnees['nouveau_mot_de_passe'])) {
                return [
                    'success' => true,
                    'message' => 'Mot de passe changé avec succès.'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Erreur lors du changement de mot de passe.'
                ];
            }
        } else {
            return [
                'success' => false,
                'message' => 'Ancien mot de passe incorrect.'
            ];
        }
    }
    
    
}
?>