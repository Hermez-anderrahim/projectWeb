<?php
// index.php - Point d'entrée pour toutes les requêtes API
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Inclure les contrôleurs
require_once __DIR__ . '/../controllers/UtilisateurController.php';
require_once __DIR__ . '/../controllers/ProduitController.php';
require_once __DIR__ . '/../controllers/PanierController.php';
require_once __DIR__ . '/../controllers/CommandeController.php';
require_once __DIR__ . '/../utils/response.php';

// Récupérer la méthode HTTP et l'URL
$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['REQUEST_URI'], '/'));

// Récupérer le point d'entrée de l'API (utilisateur, produit, panier, commande)
$endpoint = $request[0] ?? null;

// Récupérer l'ID (s'il existe)
$id = $request[1] ?? null;

// Récupérer l'action (s'il existe)
$action = $request[2] ?? null;

// Récupérer les données envoyées
$data = json_decode(file_get_contents("php://input"), true);

// Router les requêtes vers les contrôleurs appropriés
switch ($endpoint) {
    case 'utilisateur':
        $controller = new UtilisateurController();
        
        if ($method === 'POST') {
            if ($action === 'connecter') {
                $result = $controller->connecter($data);
            } elseif ($action === 'deconnecter') {
                $result = $controller->deconnecter();
            } elseif ($action === 'mot-de-passe') {
                $result = $controller->changerMotDePasse($data);
            } else {
                // Inscription par défaut
                $result = $controller->inscrire($data);
            }
        } elseif ($method === 'GET') {
            // Récupérer les informations de l'utilisateur
            $result = $controller->getInfosUtilisateur();
        } elseif ($method === 'PUT') {
            // Mettre à jour les informations de l'utilisateur
            $result = $controller->mettreAJour($data);
        } else {
            $result = Response::error('Méthode non autorisée pour cette route.');
        }
        break;
        
    case 'produit':
        $controller = new ProduitController();
        
        if ($method === 'GET') {
            if ($id) {
                // Récupérer un produit spécifique
                $result = $controller->getProduitParId($id);
            } elseif ($action === 'recherche') {
                // Recherche de produits
                $terme = $_GET['terme'] ?? '';
                $categorie = $_GET['categorie'] ?? null;
                $prix_min = $_GET['prix_min'] ?? null;
                $prix_max = $_GET['prix_max'] ?? null;
                
                $result = $controller->rechercherProduits($terme, $categorie, $prix_min, $prix_max);
            } else {
                // Liste des produits
                $page = intval($_GET['page'] ?? 1);
                $limite = intval($_GET['limite'] ?? 10);
                $categorie = $_GET['categorie'] ?? null;
                
                $result = $controller->getTousProduits($page, $limite, $categorie);
            }
        } elseif ($method === 'POST') {
            // Ajouter un nouveau produit (admin seulement)
            $result = $controller->ajouterProduit($data);
        } elseif ($method === 'PUT') {
            // Mettre à jour un produit (admin seulement)
            if (!$id) {
                $result = Response::error('ID de produit manquant.');
            } else {
                $result = $controller->mettreAJourProduit($id, $data);
            }
        } elseif ($method === 'DELETE') {
            // Supprimer un produit (admin seulement)
            if (!$id) {
                $result = Response::error('ID de produit manquant.');
            } else {
                $result = $controller->supprimerProduit($id);
            }
        } else {
            $result = Response::error('Méthode non autorisée pour cette route.');
        }
        break;
        
    case 'panier':
        $controller = new PanierController();
        
        if ($method === 'GET') {
            // Récupérer le contenu du panier
            $result = $controller->getContenuPanier();
        } elseif ($method === 'POST') {
            if ($action === 'ajouter' && $id) {
                // Ajouter un produit au panier
                $quantite = intval($data['quantite'] ?? 1);
                $result = $controller->ajouterAuPanier($id, $quantite);
            } elseif ($action === 'mettre-a-jour' && $id) {
                // Mettre à jour la quantité d'un produit
                $quantite = intval($data['quantite'] ?? 1);
                $result = $controller->mettreAJourQuantite($id, $quantite);
            } elseif ($action === 'supprimer' && $id) {
                // Supprimer un produit du panier
                $result = $controller->supprimerDuPanier($id);
            } elseif ($action === 'vider') {
                // Vider le panier
                $result = $controller->viderPanier();
            } else {
                $result = Response::error('Action non reconnue ou ID manquant.');
            }
        } else {
            $result = Response::error('Méthode non autorisée pour cette route.');
        }
        break;
        
    case 'commande':
        $controller = new CommandeController();
        
        if ($method === 'GET') {
            if ($id) {
                // Récupérer les détails d'une commande spécifique
                $result = $controller->getDetailsCommande($id);
            } else {
                // Récupérer l'historique des commandes
                $result = $controller->getHistoriqueCommandes();
            }
        } elseif ($method === 'POST') {
            if ($action === 'creer') {
                // Créer une nouvelle commande
                $result = $controller->creerCommande();
            } elseif ($action === 'annuler' && $id) {
                // Annuler une commande
                $raison = $data['raison'] ?? null;
                $result = $controller->annulerCommande($id, $raison);
            } else {
                $result = Response::error('Action non reconnue ou ID manquant.');
            }
        } else {
            $result = Response::error('Méthode non autorisée pour cette route.');
        }
        break;
        
    default:
        $result = Response::error('Endpoint non reconnu.');
        break;
}

// Renvoyer la réponse en JSON
echo json_encode($result);
?>