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
    
    // Créer une nouvelle commande à partir du panier
    public function creerDepuisPanier($id_utilisateur, $adresseLivraison = null, $methodePaiement = 'card') {
        $id_commande = null;
        
        try {
            // Appeler la procédure stockée pour finaliser la commande
            $this->conn->beginTransaction();
            
            $stmt = $this->conn->prepare("CALL finaliser_commande(?, @id_commande)");
            $stmt->bindParam(1, $id_utilisateur, PDO::PARAM_INT);
            $stmt->execute();
            
            $stmt = $this->conn->query("SELECT @id_commande as id_commande");
            $result = $stmt->fetch();
            
            if($result && isset($result['id_commande'])) {
                $id_commande = $result['id_commande'];
                
                // Enregistrer les informations de livraison
                if ($adresseLivraison) {
                    $this->sauvegarderInfosLivraison($id_commande, $adresseLivraison);
                }
                
                // Enregistrer la méthode de paiement
                $this->sauvegarderMethodePaiement($id_commande, $methodePaiement);
                
                // Mettre à jour le statut de la commande à "validee" au lieu de "en_attente"
                $this->updateStatut($id_commande, 'validee');
                
                $this->conn->commit();
                
                $this->id_commande = $id_commande;
                $this->id_utilisateur = $id_utilisateur;
                $this->adresse_livraison = $adresseLivraison;
                $this->methode_paiement = $methodePaiement;
                return $id_commande;
            }
            
            $this->conn->rollBack();
            return false;
        } catch(PDOException $e) {
            $this->conn->rollBack();
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
                      JOIN produits p ON dc.id_produit = p.id_produit
                      WHERE dc.id_commande = :id_commande";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id_commande', $id_commande);
            $stmt->execute();
            
            $produits = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Combine the results
            $commande['produits'] = $produits;
            
            return $commande;
        } catch(PDOException $e) {
            throw $e;
        }
    }
    
    // Récupérer l'historique des commandes d'un utilisateur
    public function getHistoriqueCommandes($id_utilisateur) {
        try {
            // Appeler la procédure stockée pour afficher l'historique des commandes
            $resultats = $this->db->execProcedureMultipleResults('historique_commandes', [$id_utilisateur]);
            
            if(count($resultats) >= 2) {
                return [
                    'commandes_actives' => $resultats[0],
                    'commandes_annulees' => $resultats[1]
                ];
            }
            
            return null;
        } catch(PDOException $e) {
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