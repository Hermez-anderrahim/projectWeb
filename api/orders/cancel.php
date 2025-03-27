<?php
// api/orders/cancel.php
header('Content-Type: application/json');

// Start session if not already started
if(!isset($_SESSION)) {
    session_start();
}

// Check if user is logged in
if(!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Please login to cancel an order.'
    ]);
    exit;
}

// Get JSON data from request
$data = json_decode(file_get_contents('php://input'), true);

if(!isset($data['order_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request data.'
    ]);
    exit;
}

// Include database and order model
include_once '../../backend/config/db_connect.php';
include_once '../../backend/models/Order.php';

// Instantiate database and order object
$database = new Database();
$db = $database->getConnection();

$order = new Order($db);

// Check if order exists and belongs to the user
$stmt = $db->prepare("SELECT order_id, status FROM orders WHERE order_id = :order_id AND user_id = :user_id");
$stmt->bindParam(':order_id', $data['order_id']);
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->execute();

$order_info = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$order_info) {
    echo json_encode([
        'success' => false,
        'message' => 'Order not found.'
    ]);
    exit;
}

// Check if order can be cancelled
if($order_info['status'] == 'cancelled') {
    echo json_encode([
        'success' => false,
        'message' => 'Order is already cancelled.'
    ]);
    exit;
}

if($order_info['status'] != 'pending' && $order_info['status'] != 'processing') {
    echo json_encode([
        'success' => false,
        'message' => 'Orders can only be cancelled when in pending or processing status.'
    ]);
    exit;
}

// Cancel order
$order->order_id = $data['order_id'];
$result = $order->cancel('Customer cancelled via website');

if(isset($result['error'])) {
    echo json_encode([
        'success' => false,
        'message' => $result['error']
    ]);
} else {
    echo json_encode([
        'success' => true,
        'message' => 'Order cancelled successfully.'
    ]);
}
?>