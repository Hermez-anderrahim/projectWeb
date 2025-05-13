<?php
// api/commande.php
// Fix CORS headers to allow credentials and proper origin
$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '*';
header("Access-Control-Allow-Origin: $origin");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Access-Control-Allow-Credentials: true");

require_once __DIR__ . '/../controllers/CommandeController.php';
require_once __DIR__ . '/../utils/response.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Récupérer la méthode HTTP
$method = $_SERVER['REQUEST_METHOD'];

// Handle preflight OPTIONS requests
if ($method === 'OPTIONS') {
    header("HTTP/1.1 200 OK");
    exit;
}

// Récupérer l'ID ou l'action dans l'URL (si présent)
$request = [];
if(isset($_SERVER['PATH_INFO'])) {
    $request = explode('/', trim($_SERVER['PATH_INFO'], '/'));
}
$id_or_action = $request[0] ?? null;
$param_secondaire = $request[1] ?? null;

// Récupérer les données envoyées
$data = json_decode(file_get_contents("php://input"), true);

// Initialiser le contrôleur
$controller = new CommandeController();

try {
    // Traiter la requête en fonction de la méthode HTTP et de l'ID/action
    switch($method) {
        case 'GET':
            // Support for admin dashboard recent orders
            if($id_or_action === 'recent' || isset($_GET['action']) && $_GET['action'] === 'recent') {
                $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 5;
                echo json_encode($controller->getRecentOrders($limit));
                break;
            }
            
            if(is_numeric($id_or_action)) {
                // GET /api/commande/{id} - Récupérer les détails d'une commande
                echo json_encode($controller->getDetailsCommande($id_or_action));
            } elseif($id_or_action === 'admin' && $param_secondaire === 'toutes') {
                // GET /api/commande/admin/toutes - Récupérer toutes les commandes (admin seulement)
                $statut = $_GET['statut'] ?? null;
                $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
                $limite = isset($_GET['limite']) ? intval($_GET['limite']) : 20;
                
                echo json_encode($controller->getToutesCommandes($statut, $limite, $page));
            } else {
                // GET /api/commande - Récupérer l'historique des commandes
                echo json_encode($controller->getHistoriqueCommandes());
            }
            break;
            
        case 'POST':
            if($id_or_action === 'creer') {
                // POST /api/commande/creer - Créer une nouvelle commande
                echo json_encode($controller->creerCommande($data));
            } elseif($id_or_action === 'annuler' && is_numeric($param_secondaire)) {
                // POST /api/commande/annuler/{id} - Annuler une commande
                $raison = $data['raison'] ?? null;
                echo json_encode($controller->annulerCommande($param_secondaire, $raison));
            } elseif($id_or_action === 'admin' && $param_secondaire === 'statut' && isset($request[2]) && is_numeric($request[2])) {
                // POST /api/commande/admin/statut/{id} - Modifier le statut d'une commande (admin seulement)
                $nouveau_statut = $data['statut'] ?? null;
                if(!$nouveau_statut) {
                    echo json_encode(Response::error('Statut manquant', 400));
                    break;
                }
                echo json_encode($controller->modifierStatutCommande($request[2], $nouveau_statut));
            } else {
                // If no specific action is detected but we have a POST request with data
                // This handles the case when OrderAPI.create(orderData) is called directly
                if (!$id_or_action && $data) {
                    echo json_encode($controller->creerCommande($data));
                } else {
                    echo json_encode(Response::error('Action non reconnue ou paramètres manquants', 400));
                }
            }
            break;
            
        default:
            echo json_encode(Response::error('Méthode non autorisée', 405));
            break;
    }
} catch(Exception $e) {
    echo json_encode(Response::error('Erreur serveur: ' . $e->getMessage(), 500));
}
?>