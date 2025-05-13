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
    public function creerCommande($donnees = null) {
        // Vérifier si l'utilisateur est connecté
        $resultat_auth = Auth::verifierAuthentification();
        
        if(!$resultat_auth['authentifie']) {
            return [
                'success' => false,
                'message' => 'Utilisateur non connecté.'
            ];
        }
        
        try {
            // Si aucune donnée n'est fournie, on la récupère de la requête
            if (!$donnees) {
                $donnees = json_decode(file_get_contents("php://input"), true);
            }
            
            // Valider les données de livraison
            $adresseLivraison = [
                'nom' => $donnees['nom'] ?? null,
                'prenom' => $donnees['prenom'] ?? null,
                'adresse' => $donnees['adresse'] ?? null,
                'code_postal' => $donnees['code_postal'] ?? null,
                'ville' => $donnees['ville'] ?? null,
                'telephone' => $donnees['telephone'] ?? null
            ];
            
            // Valider les données de paiement
            $methodePaiement = $donnees['payment_method'] ?? 'card';
            
            // Simuler le traitement de paiement 
            $paiementReussi = $this->traiterPaiement($donnees);
            
            if (!$paiementReussi) {
                return [
                    'success' => false,
                    'message' => 'Erreur lors du traitement du paiement. Veuillez vérifier vos informations bancaires.'
                ];
            }
            
            // Créer la commande avec les informations de livraison
            $id_commande = $this->commande->creerDepuisPanier(
                $resultat_auth['utilisateur']['id'],
                $adresseLivraison,
                $methodePaiement
            );
            
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
        } catch(Exception $e) {
            return [
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ];
        }
    }
    
    // Simuler le traitement du paiement
    private function traiterPaiement($data) {
        // Dans un environnement de production, on intégrerait ici 
        // un service de paiement comme Stripe, PayPal, etc.
        
        // Vérifications basiques pour la simulation
        if ($data['payment_method'] === 'card') {
            // Vérification basique du numéro de carte (simulation)
            $cardNumber = $data['card_number'] ?? '';
            $cardExpiry = $data['card_expiry'] ?? '';
            $cardCVV = $data['card_cvv'] ?? '';
            $cardName = $data['card_name'] ?? '';
            
            // Vérifier que les champs obligatoires sont remplis
            if (empty($cardNumber) || empty($cardExpiry) || empty($cardCVV) || empty($cardName)) {
                return false;
            }
            
            // Vérifier que le numéro de carte a au moins 16 chiffres (simulation)
            $cardNumber = preg_replace('/\D/', '', $cardNumber);
            if (strlen($cardNumber) < 15) {
                return false;
            }
            
            // Ici, nous simulons toujours un paiement réussi si les vérifications basiques passent
            return true;
        } elseif ($data['payment_method'] === 'paypal') {
            // Simulation de paiement PayPal réussi
            return true;
        }
        
        return false;
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

    // Méthode pour l'administrateur : récupérer les commandes récentes
    public function getRecentOrders($limit = 5) {
        // Vérifier si l'utilisateur est connecté en tant qu'admin
        $resultat_auth = Auth::verifierAuthentification(true);
        
        if(!$resultat_auth['authentifie']) {
            return [
                'success' => false,
                'message' => 'Non autorisé. Vous devez être administrateur.'
            ];
        }
        
        try {
            $query = "SELECT c.*, u.nom, u.prenom, u.email 
                      FROM commandes c 
                      JOIN utilisateurs u ON c.id_utilisateur = u.id_utilisateur 
                      ORDER BY c.date_commande DESC 
                      LIMIT :limite";
            
            $stmt = Database::getInstance()->getConnection()->prepare($query);
            $stmt->bindParam(':limite', $limit, PDO::PARAM_INT);
            $stmt->execute();
            $commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return [
                'success' => true,
                'commandes' => $commandes
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