<?php
// api/utilisateur.php
// Fix CORS headers to allow credentials and proper origin
$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '*';
header("Access-Control-Allow-Origin: $origin");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Access-Control-Allow-Credentials: true");

require_once __DIR__ . '/../controllers/UtilisateurController.php';
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

// Récupérer les données envoyées
$data = json_decode(file_get_contents("php://input"), true);

// Initialiser le contrôleur
$controller = new UtilisateurController();

// Actions de l'API utilisateur
try {
    if ($method === "POST") {
        // Vérifier si l'action est spécifiée
        if (!isset($data['action'])) {
            echo json_encode(Response::error('Action non spécifiée', 400));
            exit;
        }
        
        $action = $data['action'];
        
        switch ($action) {
            case 'inscrire':
                // Vérifier les champs requis
                $required = ['nom', 'prenom', 'email', 'password', 'adresse'];
                foreach ($required as $field) {
                    if (!isset($data[$field]) || empty($data[$field])) {
                        echo json_encode(Response::error("Le champ $field est requis", 400));
                        exit;
                    }
                }
                
                $nom = $data['nom'];
                $prenom = $data['prenom'];
                $email = $data['email'];
                $password = $data['password'];
                $adresse = $data['adresse'];
                $telephone = $data['telephone'] ?? "";
                
                echo json_encode($controller->inscrireUtilisateur($nom, $prenom, $email, $password, $adresse, $telephone));
                break;
                
            case 'connecter':
                // Check both password and mot_de_passe fields to handle different parameter names
                if (!isset($data['email'])) {
                    echo json_encode(Response::error('Email requis', 400));
                    exit;
                }
                
                // Get password from either 'password' or 'mot_de_passe' field
                $password = null;
                if (isset($data['password']) && !empty($data['password'])) {
                    $password = $data['password'];
                } elseif (isset($data['mot_de_passe']) && !empty($data['mot_de_passe'])) {
                    $password = $data['mot_de_passe'];
                }
                
                if ($password === null) {
                    echo json_encode(Response::error('Mot de passe requis', 400));
                    exit;
                }
                
                $email = $data['email'];
                $se_souvenir = isset($data['se_souvenir']) ? (bool)$data['se_souvenir'] : false;
                
                echo json_encode($controller->connecterUtilisateur($email, $password, $se_souvenir));
                break;
                
            case 'deconnecter':
                echo json_encode($controller->deconnecterUtilisateur());
                break;
                
            case 'getInfosUtilisateur':
                error_log("DEBUG [api/utilisateur.php] - getInfosUtilisateur action called");
                error_log("DEBUG [api/utilisateur.php] - HTTP Headers: " . json_encode(getallheaders()));
                echo json_encode($controller->getInfosUtilisateur());
                break;
                
            case 'mettreAJour':
                // Vérifier l'authentification
                if (!isset($_SESSION['utilisateur'])) {
                    echo json_encode(Response::error('Utilisateur non connecté', 401));
                    exit;
                }
                
                echo json_encode($controller->mettreAJourUtilisateur($data));
                break;
                
            case 'changerMotDePasse':
                // Vérifier l'authentification
                if (!isset($_SESSION['utilisateur'])) {
                    echo json_encode(Response::error('Utilisateur non connecté', 401));
                    exit;
                }
                
                // Vérifier les champs requis
                if (!isset($data['ancien_password']) || !isset($data['nouveau_password'])) {
                    echo json_encode(Response::error('Ancien et nouveau mot de passe requis', 400));
                    exit;
                }
                
                $ancien_password = $data['ancien_password'];
                $nouveau_password = $data['nouveau_password'];
                
                echo json_encode($controller->changerMotDePasse($ancien_password, $nouveau_password));
                break;
                
            default:
                echo json_encode(Response::error('Action non reconnue', 400));
                break;
        }
    } else {
        echo json_encode(Response::error('Méthode non autorisée', 405));
    }
} catch (Exception $e) {
    echo json_encode(Response::error('Erreur serveur: ' . $e->getMessage(), 500));
}
?>