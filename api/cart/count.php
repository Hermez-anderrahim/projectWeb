<?php
// api/cart/add.php
header('Content-Type: application/json');

// Start session if not already started
if(!isset($_SESSION)) {
    session_start();
}

// Check if user is logged in
if(!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Please login to add items to cart.'
    ]);
    exit;
}

// Get JSON data from request
$data = json_decode(file_get_contents('php://input'), true);

if(!isset($data['product_id']) || !isset($data['quantity'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request data.'
    ]);
    exit;
}

// Include database and cart model
include_once '../../backend/config/db_connect.php';
include_once '../../backend/models/Cart.php';

// Instantiate database and cart object
$database = new Database();
$db = $database->getConnection();

$cart = new Cart($db);
$cart_id = $cart->getCart($_SESSION['user_id']);

// Add item to cart
$result = $cart->addItem($data['product_id'], $data['quantity']);

if(isset($result['error'])) {
    echo json_encode([
        'success' => false,
        'message' => $result['error']
    ]);
} else {
    echo json_encode([
        'success' => true,
        'message' => 'Product added to cart successfully.'
    ]);
}
?>