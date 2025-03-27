<?php
// api/cart/remove.php
header('Content-Type: application/json');

// Start session if not already started
if(!isset($_SESSION)) {
    session_start();
}

// Check if user is logged in
if(!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Please login to update your cart.'
    ]);
    exit;
}

// Get JSON data from request
$data = json_decode(file_get_contents('php://input'), true);

if(!isset($data['cart_item_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request data.'
    ]);
    exit;
}

// Include database and cart model
include_once '../../backend/config/db_connect.php';
include_once '../../backend/models/Cart.php';

// Instantiate database
$database = new Database();
$db = $database->getConnection();

// Get cart ID
$cart = new Cart($db);
$cart_id = $cart->getCart($_SESSION['user_id']);

// Check if cart item exists and belongs to this cart
$stmt = $db->prepare("SELECT cart_item_id FROM cart_items 
                     WHERE cart_item_id = :cart_item_id AND cart_id = :cart_id");
$stmt->bindParam(':cart_item_id', $data['cart_item_id']);
$stmt->bindParam(':cart_id', $cart_id);
$stmt->execute();

if($stmt->rowCount() === 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Cart item not found.'
    ]);
    exit;
}

// Remove item
if($cart->removeItem($data['cart_item_id'])) {
    echo json_encode([
        'success' => true,
        'message' => 'Item removed from cart successfully.'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Error removing item from cart.'
    ]);
}
?>