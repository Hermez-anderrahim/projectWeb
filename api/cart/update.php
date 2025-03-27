<?php
// api/cart/update.php
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

if(!isset($data['cart_item_id']) || !isset($data['quantity'])) {
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
$stmt = $db->prepare("SELECT ci.cart_item_id, ci.product_id, p.stock 
                     FROM cart_items ci
                     JOIN products p ON ci.product_id = p.product_id
                     WHERE ci.cart_item_id = :cart_item_id AND ci.cart_id = :cart_id");
$stmt->bindParam(':cart_item_id', $data['cart_item_id']);
$stmt->bindParam(':cart_id', $cart_id);
$stmt->execute();

$item = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$item) {
    echo json_encode([
        'success' => false,
        'message' => 'Cart item not found.'
    ]);
    exit;
}

// Check stock
if($data['quantity'] > $item['stock']) {
    echo json_encode([
        'success' => false,
        'message' => 'Not enough stock available.'
    ]);
    exit;
}

// Update quantity
$stmt = $db->prepare("UPDATE cart_items SET quantity = :quantity WHERE cart_item_id = :cart_item_id");
$stmt->bindParam(':quantity', $data['quantity']);
$stmt->bindParam(':cart_item_id', $data['cart_item_id']);

if($stmt->execute()) {
    echo json_encode([
        'success' => true,
        'message' => 'Cart updated successfully.'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Error updating cart.'
    ]);
}
?>