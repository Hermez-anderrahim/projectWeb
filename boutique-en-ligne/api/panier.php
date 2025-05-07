<?php
// api/panier.php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once __DIR__ . '/../controllers/PanierController.php';
require_once __DIR__ . '/../utils/response.php';

// Récupérer la méthode HTTP
$method = $_SERVER['REQUEST_METHOD'];

// Récupérer l'action et le paramètre dans l'URL (si présents)
$request = [];
if(isset($_SERVER['PATH_INFO'])) {
    $request = explode('/', trim($_SERVER['PATH_INFO'], '/'));
}
$action = $request[0] ?? null;
$id_produit = $request[1] ?? null;

// Récupérer les données envoyées
$data = json_decode(file_get_contents("php://input"), true);

// Initialiser le contrôleur
$controller = new PanierController();

try {
    // Traiter la requête en fonction de la méthode HTTP et de l'action
    switch($method) {
        case 'GET':
            // GET /api/panier - Récupérer le contenu du panier
            echo json_encode($controller->getContenuPanier());
            break;
            
        case 'POST':
            if($action === 'ajouter' && is_numeric($id_produit)) {
                // POST /api/panier/ajouter/{id_produit} - Ajouter un produit au panier
                $quantite = isset($data['quantite']) ? intval($data['quantite']) : 1;
                echo json_encode($controller->ajouterAuPanier($id_produit, $quantite));
            } elseif($action === 'mettre-a-jour' && is_numeric($id_produit)) {
                // POST /api/panier/mettre-a-jour/{id_produit} - Mettre à jour la quantité
                $quantite = isset($data['quantite']) ? intval($data['quantite']) : 1;
                echo json_encode($controller->mettreAJourQuantite($id_produit, $quantite));
            } elseif($action === 'supprimer' && is_numeric($id_produit)) {
                // POST /api/panier/supprimer/{id_produit} - Supprimer un produit du panier
                echo json_encode($controller->supprimerDuPanier($id_produit));
            } elseif($action === 'vider') {
                // POST /api/panier/vider - Vider le panier
                echo json_encode($controller->viderPanier());
            } else {
                echo json_encode(Response::error('Action non reconnue ou ID de produit manquant', 400));
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