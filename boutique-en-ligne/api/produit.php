<?php
// api/produit.php
// Fix CORS headers to allow credentials and proper origin
$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '*';
header("Access-Control-Allow-Origin: $origin");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Access-Control-Allow-Credentials: true");

require_once __DIR__ . '/../controllers/ProduitController.php';
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

// Récupérer les données envoyées
$data = json_decode(file_get_contents("php://input"), true);

// Initialiser le contrôleur
$controller = new ProduitController();

try {
    // Traiter la requête en fonction de la méthode HTTP et de l'ID/action
    switch($method) {
        case 'GET':
            // Support for admin dashboard low stock products
            if($id_or_action === 'low_stock' || isset($_GET['action']) && $_GET['action'] === 'low_stock') {
                $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 5;
                echo json_encode($controller->getLowStockProducts($limit));
                break;
            }
            
            if(is_numeric($id_or_action)) {
                // GET /api/produit/{id} - Récupérer un produit par son ID
                echo json_encode($controller->getProduitParId($id_or_action));
            } elseif($id_or_action === 'recherche') {
                // GET /api/produit/recherche?terme=xyz&categorie=abc - Rechercher des produits
                $terme = $_GET['terme'] ?? '';
                $categorie = $_GET['categorie'] ?? null;
                $prix_min = isset($_GET['prix_min']) ? floatval($_GET['prix_min']) : null;
                $prix_max = isset($_GET['prix_max']) ? floatval($_GET['prix_max']) : null;
                $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
                $limite = isset($_GET['limite']) ? intval($_GET['limite']) : 10;
                
                echo json_encode($controller->rechercherProduits($terme, $categorie, $prix_min, $prix_max, $page, $limite));
            } else {
                // GET /api/produit - Récupérer tous les produits avec pagination
                $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
                $limite = isset($_GET['limite']) ? intval($_GET['limite']) : 10;
                $categorie = $_GET['categorie'] ?? null;
                
                echo json_encode($controller->getTousProduits($page, $limite, $categorie));
            }
            break;
            
        case 'POST':
            // POST /api/produit - Ajouter un nouveau produit (admin seulement)
            echo json_encode($controller->ajouterProduit($data));
            break;
            
        case 'PUT':
            if(!is_numeric($id_or_action)) {
                echo json_encode(Response::error('ID de produit non valide ou manquant', 400));
                break;
            }
            // PUT /api/produit/{id} - Mettre à jour un produit (admin seulement)
            echo json_encode($controller->mettreAJourProduit($id_or_action, $data));
            break;
            
        case 'DELETE':
            if(!is_numeric($id_or_action)) {
                echo json_encode(Response::error('ID de produit non valide ou manquant', 400));
                break;
            }
            // DELETE /api/produit/{id} - Supprimer un produit (admin seulement)
            echo json_encode($controller->supprimerProduit($id_or_action));
            break;
            
        default:
            echo json_encode(Response::error('Méthode non autorisée', 405));
            break;
    }
} catch(Exception $e) {
    echo json_encode(Response::error('Erreur serveur: ' . $e->getMessage(), 500));
}
?>