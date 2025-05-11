<?php
// api/admin.php - Admin API endpoints
require_once __DIR__ . '/../utils/auth.php';
require_once __DIR__ . '/../utils/response.php';
require_once __DIR__ . '/../models/Utilisateur.php';
require_once __DIR__ . '/../models/Produit.php';
require_once __DIR__ . '/../models/Commande.php';

/**
 * Helper function to send a formatted JSON response
 * 
 * @param bool $success Whether the request was successful
 * @param string $message Response message
 * @param array|null $data Optional data to include in the response
 * @param int $code HTTP status code
 */
function sendResponse($success, $message, $data = null, $code = 200) {
    header('Content-Type: application/json');
    http_response_code($code);
    
    $response = [
        'success' => $success,
        'message' => $message
    ];
    
    if ($data !== null) {
        $response['data'] = $data;
    }
    
    echo json_encode($response);
    exit;
}

// Check if user is logged in and is admin
session_start();
if (!isset($_SESSION['utilisateur']) || !$_SESSION['utilisateur']['est_admin']) {
    sendResponse(false, 'Non autorisé. Accès réservé aux administrateurs.', null, 403);
    exit;
}

// Get request method and action
$method = $_SERVER['REQUEST_METHOD'];
$action = isset($_GET['action']) ? $_GET['action'] : '';

// Process based on method and action
if ($method === 'GET') {
    switch ($action) {
        case 'stats':
            getDashboardStats();
            break;
        default:
            sendResponse(false, 'Action non valide', null, 400);
            break;
    }
} elseif ($method === 'POST') {
    // Handle POST requests
    // Currently there are no POST endpoints for admin
    sendResponse(false, 'Action non valide', null, 400);
} else {
    // Method not allowed
    sendResponse(false, 'Méthode non autorisée', null, 405);
}

/**
 * Get dashboard statistics
 */
function getDashboardStats() {
    try {
        $db = Database::getInstance()->getConnection();
        
        // Get total sales
        $query = "SELECT COALESCE(SUM(total), 0) as total_sales FROM commandes 
                 WHERE statut != 'annulee'";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $sales = $stmt->fetch(PDO::FETCH_ASSOC);
        $totalSales = $sales['total_sales'];
        
        // Get total orders
        $query = "SELECT COUNT(*) as total_orders FROM commandes";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $orders = $stmt->fetch(PDO::FETCH_ASSOC);
        $totalOrders = $orders['total_orders'];
        
        // Get total products
        $query = "SELECT COUNT(*) as total_products FROM produits";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $products = $stmt->fetch(PDO::FETCH_ASSOC);
        $totalProducts = $products['total_products'];
        
        // Get total customers
        $query = "SELECT COUNT(*) as total_customers FROM utilisateurs WHERE est_admin = 0";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $customers = $stmt->fetch(PDO::FETCH_ASSOC);
        $totalCustomers = $customers['total_customers'];
        
        // Recent orders will be handled by commande.php
        // Low stock products will be handled by produit.php
        
        $stats = [
            'total_sales' => (float) $totalSales,
            'total_orders' => (int) $totalOrders,
            'total_products' => (int) $totalProducts,
            'total_customers' => (int) $totalCustomers
        ];
        
        sendResponse(true, 'Statistiques récupérées avec succès', ['stats' => $stats]);
    } catch (PDOException $e) {
        sendResponse(false, 'Erreur lors de la récupération des statistiques: ' . $e->getMessage(), null, 500);
    }
}