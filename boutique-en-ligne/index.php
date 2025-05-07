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

// Frontend routing
$route = $_GET['route'] ?? 'home';

// Include common header
include_once 'views/partials/header.php';
include_once 'views/partials/navbar.php';

// Router
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
    case 'orders':
        include_once 'views/orders/history.php';
        break;
    case 'checkout':
        include_once 'views/orders/create.php';
        break;
    case 'admin-products':
        // Check if user is admin
        include_once 'views/admin/products.php';
        break;
    case 'admin-orders':
        // Check if user is admin
        include_once 'views/admin/orders.php';
        break;
    default:
        include_once 'views/products/list.php';
        break;
}

// Include common footer
include_once 'views/partials/footer.php';
?>