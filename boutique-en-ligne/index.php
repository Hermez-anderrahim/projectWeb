<?php
// Main index.php - Frontend entry point
define('DIR', __DIR__);
session_start();

// Check if it's an API request
if (strpos($_SERVER['REQUEST_URI'], '/api/') === 0) {
    // Forward to API handler
    include_once __DIR__ . '/api/index.php';
    exit;
}

// Get the route parameter and debug it
$route = $_GET['route'] ?? 'home';

// Debug the route - you can comment this out after fixing
// echo "<div style='background:#f8d7da;color:#721c24;padding:10px;margin:10px;border:1px solid #f5c6cb;'>Detected Route: " . htmlspecialchars($route) . "</div>";

include_once 'views/partials/header.php';
include_once 'views/partials/navbar.php';

// Fixed routing with support for both hyphens and slash notations
switch ($route) {
    case 'home':
        include_once 'views/products/list.php';
        break;
    case 'product':
        include_once 'views/products/detail.php';
        break;
    case 'cart':
        include_once 'views/cart/index.php';
        break;
    case 'login':
        include_once 'views/auth/login.php';
        break;
    case 'register':
        include_once 'views/auth/register.php';
        break;
    case 'profile':
        include_once 'views/users/profile.php';
        break;
    case 'orders':
        include_once 'views/orders/history.php';
        break;
    case 'checkout':
        include_once 'views/orders/create.php';
        break;
    
    // Admin routes - support both formats
    case 'admin-dashboard':
    case 'admin/dashboard':
        include_once 'views/admin/dashboard.php';
        break;
    case 'admin-products':
    case 'admin/products':
        include_once 'views/admin/products.php';
        break;
    case 'admin-orders':
    case 'admin/orders':
        include_once 'views/admin/orders.php';
        break;
        
    // Shop route for category browsing
    case 'shop':
        include_once 'views/products/shop.php'; // Use our new shop page with filtering
        break;
        
    default:
        // If not found, show 404 or redirect to home
        include_once 'views/products/list.php';
        break;
}

// Include common footer
include_once 'views/partials/footer.php';
?>