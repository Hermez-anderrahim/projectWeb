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
    
    public function __construct() {
        $this->db = Database::getInstance();
        $this->conn = $this->db->getConnection();
    }
    
    // Créer une nouvelle commande à partir du panier
    public function creerDepuisPanier($id_utilisateur) {
        $id_commande = null;
        
        try {
            // Appeler la procédure stockée pour finaliser la commande
            $this->conn->beginTransaction();
            
            $stmt = $this->conn->prepare("CALL finaliser_commande(?, @id_commande)");
            $stmt->bindParam(1, $id_utilisateur, PDO::PARAM_INT);
            $stmt->execute();
            
            $stmt = $this->conn->query("SELECT @id_commande as id_commande");
            $result = $stmt->fetch();
            
            $this->conn->commit();
            
            if($result && isset($result['id_commande'])) {
                $id_commande = $result['id_commande'];
                $this->id_commande = $id_commande;
                $this->id_utilisateur = $id_utilisateur;
                return $id_commande;
            }
            
            return false;
        } catch(PDOException $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }
    
    // Récupérer les détails d'une commande
    public function getDetailsCommande($id_commande, $id_utilisateur) {
        try {
            // Appeler la procédure stockée pour afficher les détails de la commande
            $resultats = $this->db->execProcedureMultipleResults('afficher_details_commande', [$id_commande, $id_utilisateur]);
            
            if(count($resultats) >= 2) {
                $details = $resultats[0];
                $total = $resultats[1][0]['total_a_payer'] ?? 0;
                
                return [
                    'details' => $details,
                    'total' => $total
                ];
            }
            
            return null;
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