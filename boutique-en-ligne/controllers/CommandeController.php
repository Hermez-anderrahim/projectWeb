<?php
// controllers/CommandeController.php
require_once __DIR__ . '/../models/Commande.php';
require_once __DIR__ . '/../models/Panier.php'; // Adding the missing Panier class
require_once __DIR__ . '/../utils/auth.php';

class CommandeController {
    private $commande;
    
    public function __construct() {
        $this->commande = new Commande();
    }
    
    // Créer une nouvelle commande à partir du panier de l'utilisateur
    public function creerCommande($donnees = null) {
        error_log("DEBUG [CommandeController::creerCommande] - Starting order creation");
        error_log("DEBUG [CommandeController::creerCommande] - Request data: " . print_r($donnees, true));
        
        // Vérifier si l'utilisateur est connecté
        $resultat_auth = Auth::verifierAuthentification();
        error_log("DEBUG [CommandeController::creerCommande] - Auth check result: " . json_encode($resultat_auth));
        
        if(!$resultat_auth['authentifie']) {
            error_log("DEBUG [CommandeController::creerCommande] - User not authenticated");
            return [
                'success' => false,
                'message' => 'Utilisateur non connecté.'
            ];
        }
        
        try {
            // Si aucune donnée n'est fournie, on la récupère de la requête
            if (!$donnees) {
                error_log("DEBUG [CommandeController::creerCommande] - No data provided, reading from request body");
                $donnees = json_decode(file_get_contents("php://input"), true);
                error_log("DEBUG [CommandeController::creerCommande] - Request body data: " . print_r($donnees, true));
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
            error_log("DEBUG [CommandeController::creerCommande] - Shipping address: " . json_encode($adresseLivraison));
            
            // Valider les données de paiement
            $methodePaiement = $donnees['payment_method'] ?? 'card';
            error_log("DEBUG [CommandeController::creerCommande] - Payment method: $methodePaiement");
            
            // Simuler le traitement de paiement 
            error_log("DEBUG [CommandeController::creerCommande] - Processing payment");
            $paiementReussi = $this->traiterPaiement($donnees);
            error_log("DEBUG [CommandeController::creerCommande] - Payment processing result: " . ($paiementReussi ? "success" : "failure"));
            
            if (!$paiementReussi) {
                error_log("DEBUG [CommandeController::creerCommande] - Payment failed");
                return [
                    'success' => false,
                    'message' => 'Erreur lors du traitement du paiement. Veuillez vérifier vos informations bancaires.'
                ];
            }
            
            // Créer la commande avec les informations de livraison
            error_log("DEBUG [CommandeController::creerCommande] - Creating order with user ID: " . $resultat_auth['utilisateur']['id']);
            $id_commande = $this->commande->creerDepuisPanier(
                $resultat_auth['utilisateur']['id'],
                $adresseLivraison,
                $methodePaiement
            );
            
            if($id_commande) {
                error_log("DEBUG [CommandeController::creerCommande] - Order created successfully with ID: $id_commande");
                return [
                    'success' => true,
                    'message' => 'Commande créée avec succès.',
                    'id_commande' => $id_commande
                ];
            } else {
                error_log("DEBUG [CommandeController::creerCommande] - Order creation failed");
                return [
                    'success' => false,
                    'message' => 'Erreur lors de la création de la commande. Vérifiez que votre panier n\'est pas vide et que tous les produits sont disponibles en stock.'
                ];
            }
        } catch(PDOException $e) {
            error_log("DEBUG [CommandeController::creerCommande] - Database error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erreur de base de données: ' . $e->getMessage()
            ];
        } catch(Exception $e) {
            error_log("DEBUG [CommandeController::creerCommande] - General error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ];
        }
    }
    
    // Simuler le traitement du paiement
    private function traiterPaiement($data) {
        error_log("DEBUG [CommandeController::traiterPaiement] - Starting payment processing");
        error_log("DEBUG [CommandeController::traiterPaiement] - Payment method: " . ($data['payment_method'] ?? 'not provided'));
        
        // Dans un environnement de production, on intégrerait ici 
        // un service de paiement comme Stripe, PayPal, etc.
        
        // Vérifications basiques pour la simulation
        if ($data['payment_method'] === 'card') {
            // Vérification basique du numéro de carte (simulation)
            $cardNumber = $data['card_number'] ?? '';
            $cardExpiry = $data['card_expiry'] ?? '';
            $cardCVV = $data['card_cvv'] ?? '';
            $cardName = $data['card_name'] ?? '';
            
            error_log("DEBUG [CommandeController::traiterPaiement] - Card details - Last digits: " . 
                      (substr($cardNumber, -4) ?? 'none') . 
                      ", Expiry: $cardExpiry, Name: $cardName");
            
            // Vérifier que les champs obligatoires ont une valeur (même vide)
            if (!isset($cardNumber) || !isset($cardExpiry) || !isset($cardCVV) || !isset($cardName)) {
                error_log("DEBUG [CommandeController::traiterPaiement] - Missing required card fields");
                return false;
            }
            
            // Nettoyer le numéro de carte pour n'avoir que les chiffres
            $cardNumber = preg_replace('/\D/', '', $cardNumber);
            
            // SIMULATION: Pour le développement, accepter n'importe quel numéro de carte
            // En production, nous vérifierions la validité et longueur du numéro
            error_log("DEBUG [CommandeController::traiterPaiement] - Card number length: " . strlen($cardNumber) . " digits");
            
            // Pour la simulation, accepter tout format de date d'expiration
            // En production, il faudrait vérifier le format MM/YY ou MM/YYYY
            // et que la date n'est pas dépassée
            
            // Ici, nous simulons toujours un paiement réussi si les vérifications basiques passent
            error_log("DEBUG [CommandeController::traiterPaiement] - Card payment successful (simulated)");
            return true;
        } elseif ($data['payment_method'] === 'paypal') {
            // Simulation de paiement PayPal réussi
            error_log("DEBUG [CommandeController::traiterPaiement] - PayPal payment successful (simulated)");
            return true;
        }
        
        error_log("DEBUG [CommandeController::traiterPaiement] - Unknown payment method, payment failed");
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
        
        try {
            $offset = ($page - 1) * $limite;
            $condition = "";
            $params = [];
            
            if($statut) {
                $condition = "WHERE c.statut = :statut";
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
            $commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Compter le nombre total de commandes
            $query_count = "SELECT COUNT(*) as total FROM commandes c $condition";
            $stmt_count = Database::getInstance()->getConnection()->prepare($query_count);
            
            if($statut) {
                $stmt_count->bindParam(':statut', $statut);
            }
            
            $stmt_count->execute();
            $total = $stmt_count->fetch(PDO::FETCH_ASSOC)['total'];
            
            return [
                'success' => true,
                'commandes' => $commandes,
                'total' => (int)$total,
                'page' => (int)$page,
                'limite' => (int)$limite,
                'pages_total' => (int)ceil($total / $limite)
            ];
        } catch(PDOException $e) {
            return [
                'success' => false,
                'message' => 'Erreur de base de données: ' . $e->getMessage()
            ];
        }
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