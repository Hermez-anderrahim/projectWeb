<?php
// models/Utilisateur.php
require_once __DIR__ . '/../config/database.php';

class Utilisateur {
    private $conn;
    
    // Propriétés
    public $id_utilisateur;
    public $nom;
    public $prenom;
    public $email;
    public $mot_de_passe;
    public $adresse;
    public $telephone;
    public $est_admin;
    public $date_creation;
    
    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
    }
    
    // Créer un nouvel utilisateur
    public function creer($nom, $prenom, $email, $mot_de_passe, $adresse = null, $telephone = null, $est_admin = false) {
        $query = "INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, adresse, telephone, est_admin) 
                  VALUES (:nom, :prenom, :email, :mot_de_passe, :adresse, :telephone, :est_admin)";
        
        $mot_de_passe_hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':prenom', $prenom);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':mot_de_passe', $mot_de_passe_hash);
        $stmt->bindParam(':adresse', $adresse);
        $stmt->bindParam(':telephone', $telephone);
        $stmt->bindParam(':est_admin', $est_admin, PDO::PARAM_BOOL);
        
        if($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }
    
    // Vérifier les identifiants de connexion
    public function connecter($email, $mot_de_passe) {
        $query = "SELECT * FROM utilisateurs WHERE email = :email LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        $utilisateur = $stmt->fetch();
        
        if($utilisateur && password_verify($mot_de_passe, $utilisateur['mot_de_passe'])) {
            $this->id_utilisateur = $utilisateur['id_utilisateur'];
            $this->nom = $utilisateur['nom'];
            $this->prenom = $utilisateur['prenom'];
            $this->email = $utilisateur['email'];
            $this->adresse = $utilisateur['adresse'];
            $this->telephone = $utilisateur['telephone'];
            $this->est_admin = $utilisateur['est_admin'];
            $this->date_creation = $utilisateur['date_creation'];
            
            return true;
        }
        
        return false;
    }
    
    // Récupérer un utilisateur par son ID
    public function getById($id_utilisateur) {
        $query = "SELECT * FROM utilisateurs WHERE id_utilisateur = :id LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id_utilisateur);
        $stmt->execute();
        
        $utilisateur = $stmt->fetch();
        
        if($utilisateur) {
            $this->id_utilisateur = $utilisateur['id_utilisateur'];
            $this->nom = $utilisateur['nom'];
            $this->prenom = $utilisateur['prenom'];
            $this->email = $utilisateur['email'];
            $this->adresse = $utilisateur['adresse'];
            $this->telephone = $utilisateur['telephone'];
            $this->est_admin = $utilisateur['est_admin'];
            $this->date_creation = $utilisateur['date_creation'];
            
            return true;
        }
        
        return false;
    }
    
    // Mettre à jour un utilisateur
    public function mettreAJour() {
        $query = "UPDATE utilisateurs SET 
                  nom = :nom, 
                  prenom = :prenom, 
                  email = :email, 
                  adresse = :adresse, 
                  telephone = :telephone 
                  WHERE id_utilisateur = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nom', $this->nom);
        $stmt->bindParam(':prenom', $this->prenom);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':adresse', $this->adresse);
        $stmt->bindParam(':telephone', $this->telephone);
        $stmt->bindParam(':id', $this->id_utilisateur);
        
        return $stmt->execute();
    }
    
    // Changer le mot de passe
    public function changerMotDePasse($nouveau_mot_de_passe) {
        $query = "UPDATE utilisateurs SET mot_de_passe = :mot_de_passe WHERE id_utilisateur = :id";
        
        $mot_de_passe_hash = password_hash($nouveau_mot_de_passe, PASSWORD_DEFAULT);
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':mot_de_passe', $mot_de_passe_hash);
        $stmt->bindParam(':id', $this->id_utilisateur);
        
        return $stmt->execute();
    }
}
?>