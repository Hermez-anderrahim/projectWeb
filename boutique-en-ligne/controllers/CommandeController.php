<?php
// controllers/CommandeController.php
require_once __DIR__ . '/../models/Commande.php';
require_once __DIR__ . '/../utils/auth.php';

class CommandeController {
    private $commande;
    
    public function __construct() {
        $this->commande = new Commande();
    }
    
    // Créer une nouvelle commande à partir du panier de l'utilisateur
    public function creerCommande() {
        // Vérifier si l'utilisateur est connecté
        $resultat_auth = Auth::verifierAuthentification();
        
        if(!$resultat_auth['authentifie']) {
            return [
                'success' => false,
                'message' => 'Utilisateur non connecté.'
            ];
        }
        
        try {
            // Créer la commande
            $id_commande = $this->commande->creerDepuisPanier($resultat_auth['utilisateur']['id']);
            
            if($id_commande) {
                return [
                    'success' => true,
                    'message' => 'Commande créée avec succès.',
                    'id_commande' => $id_commande
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Erreur lors de la création de la commande. Vérifiez que votre panier n\'est pas vide et que tous les produits sont disponibles en stock.'
                ];
            }
        } catch(PDOException $e) {
            return [
                'success' => false,
                'message' => 'Erreur de base de données: ' . $e->getMessage()
            ];
        }
    }
    
    // Récupérer les détails d'une commande
    public function getDetailsCommande($id_commande) {
        // Vérifier si l'utilisateur est connecté
        $resultat_auth = Auth::verifierAuthentification();
        
        if(!$resultat_auth['authentifie']) {
            return [
                'success' => false,
                'message' => 'Utilisateur non connecté.'
            ];
        }
        
        try {
            // Récupérer les détails de la commande
            $details = $this->commande->getDetailsCommande($id_commande, $resultat_auth['utilisateur']['id']);
            
            if($details) {
                return [
                    'success' => true,
                    'commande' => $details
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Commande non trouvée ou non autorisée.'
                ];
            }
        } catch(PDOException $e) {
            return [
                'success' => false,
                'message' => 'Erreur de base de données: ' . $e->getMessage()
            ];
        }
    }
    
    // Récupérer l'historique des commandes de l'utilisateur
    public function getHistoriqueCommandes() {
        // Vérifier si l'utilisateur est connecté
        $resultat_auth = Auth::verifierAuthentification();
        
        if(!$resultat_auth['authentifie']) {
            return [
                'success' => false,
                'message' => 'Utilisateur non connecté.'
            ];
        }
        
        try {
            // Récupérer l'historique des commandes
            $historique = $this->commande->getHistoriqueCommandes($resultat_auth['utilisateur']['id']);
            
            if($historique) {
                return [
                    'success' => true,
                    'historique' => $historique
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Erreur lors de la récupération de l\'historique.'
                ];
            }
        } catch(PDOException $e) {
            return [
                'success' => false,
                'message' => 'Erreur de base de données: ' . $e->getMessage()
            ];
        }
    }
    
    // Annuler une commande
    public function annulerCommande($id_commande, $raison = null) {
        // Vérifier si l'utilisateur est connecté
        $resultat_auth = Auth::verifierAuthentification();
        
        if(!$resultat_auth['authentifie']) {
            return [
                'success' => false,
                'message' => 'Utilisateur non connecté.'
            ];
        }
        
        try {
            // Annuler la commande
            if($this->commande->annuler($id_commande, $resultat_auth['utilisateur']['id'], $raison)) {
                return [
                    'success' => true,
                    'message' => 'Commande annulée avec succès.'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Erreur lors de l\'annulation de la commande. Vérifiez que la commande existe et qu\'elle vous appartient.'
                ];
            }
        } catch(PDOException $e) {
            return [
                'success' => false,
                'message' => 'Erreur de base de données: ' . $e->getMessage()
            ];
        }
    }
    
    // Méthode pour l'administrateur : voir toutes les commandes
    public function getToutesCommandes($statut = null, $limite = 20, $page = 1) {
        // Vérifier si l'utilisateur est connecté en tant qu'admin
        $resultat_auth = Auth::verifierAuthentification(true);
        
        if(!$resultat_auth['authentifie']) {
            return [
                'success' => false,
                'message' => 'Non autorisé. Vous devez être administrateur.'
            ];
        }
        
        $offset = ($page - 1) * $limite;
        $condition = "";
        $params = [];
        
        if($statut) {
            $condition = "WHERE statut = :statut";
            $params[':statut'] = $statut;
        }
        
        $query = "SELECT c.*, u.nom, u.prenom, u.email 
                  FROM commandes c 
                  JOIN utilisateurs u ON c.id_utilisateur = u.id_utilisateur 
                  $condition 
                  ORDER BY c.date_commande DESC 
                  LIMIT :limite OFFSET :offset";
        
        $stmt = Database::getInstance()->getConnection()->prepare($query);
        $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        
        if($statut) {
            $stmt->bindParam(':statut', $statut);
        }
        
        $stmt->execute();
        $commandes = $stmt->fetchAll();
        
        // Compter le nombre total de commandes
        $query_count = "SELECT COUNT(*) as total FROM commandes $condition";
        $stmt_count = Database::getInstance()->getConnection()->prepare($query_count);
        
        if($statut) {
            $stmt_count->bindParam(':statut', $statut);
        }
        
        $stmt_count->execute();
        $total = $stmt_count->fetch()['total'];
        
        return [
            'success' => true,
            'commandes' => $commandes,
            'total' => $total,
            'page' => $page,
            'limite' => $limite,
            'pages_total' => ceil($total / $limite)
        ];
    }
    
    // Méthode pour l'administrateur : modifier le statut d'une commande
    public function modifierStatutCommande($id_commande, $nouveau_statut) {
        // Vérifier si l'utilisateur est connecté en tant qu'admin
        $resultat_auth = Auth::verifierAuthentification(true);
        
        if(!$resultat_auth['authentifie']) {
            return [
                'success' => false,
                'message' => 'Non autorisé. Vous devez être administrateur.'
            ];
        }
        
        // Vérifier que le statut est valide
        $statuts_valides = ['en_attente', 'validee', 'expediee', 'livree', 'annulee'];
        
        if(!in_array($nouveau_statut, $statuts_valides)) {
            return [
                'success' => false,
                'message' => 'Statut non valide. Les statuts valides sont: ' . implode(', ', $statuts_valides)
            ];
        }
        
        // Mettre à jour le statut de la commande
        $query = "UPDATE commandes SET statut = :statut WHERE id_commande = :id_commande";
        
        $stmt = Database::getInstance()->getConnection()->prepare($query);
        $stmt->bindParam(':statut', $nouveau_statut);
        $stmt->bindParam(':id_commande', $id_commande);
        
        if($stmt->execute()) {
            return [
                'success' => true,
                'message' => 'Statut de la commande mis à jour avec succès.'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Erreur lors de la mise à jour du statut de la commande.'
            ];
        }
    }
}
?>