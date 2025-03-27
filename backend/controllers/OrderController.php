<?php
// backend/controllers/OrderController.php
class OrderController {
    private $db;
    private $order;
    private $cart;
    
    public function __construct($db) {
        $this->db = $db;
        
        // Include models if not already included
        if(!class_exists('Order')) {
            require_once __DIR__ . '/../models/Order.php';
        }
        
        if(!class_exists('Cart')) {
            require_once __DIR__ . '/../models/Cart.php';
        }
        
        $this->order = new Order($db);
        $this->cart = new Cart($db);
    }
    
    // Create new order from cart
    public function createOrder($user_id, $shipping_address, $payment_method) {
        // First, check if cart has items
        $cart_id = $this->cart->getCart($user_id);
        $stmt = $this->cart->getItems();
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if(empty($items)) {
            return ['error' => 'Cart is empty.'];
        }
        
        // Set order properties
        $this->order->user_id = $user_id;
        $this->order->shipping_address = $shipping_address;
        $this->order->payment_method = $payment_method;
        
        // Create order (this will also empty the cart)
        $order_id = $this->order->create();
        
        if($order_id) {
            return [
                'success' => true,
                'order_id' => $order_id,
                'message' => 'Order placed successfully.'
            ];
        } else {
            return ['error' => 'Error creating order.'];
        }
    }
    
    // Get order details
    public function getOrderDetails($order_id, $user_id = null) {
        $stmt = $this->order->getOrderDetails($order_id);
        $order_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if(empty($order_items)) {
            return ['error' => 'Order not found.'];
        }
        
        // If user_id is provided, ensure it's the user's order
        if($user_id && $order_items[0]['user_id'] != $user_id) {
            return ['error' => 'Order not found.'];
        }
        
        // Get order information from the first row
        $order = $order_items[0];
        
        return [
            'order' => [
                'order_id' => $order_id,
                'user_id' => $order['user_id'],
                'total' => $order['order_total'],
                'status' => $order['status'],
                'shipping_address' => $order['shipping_address'],
                'payment_method' => $order['payment_method'],
                'created_at' => $order['created_at']
            ],
            'items' => $order_items
        ];
    }
    
    // Get user orders history
    public function getUserOrderHistory($user_id) {
        $stmt = $this->order->getUserOrders($user_id);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Cancel order
    public function cancelOrder($order_id, $user_id, $reason = 'Customer cancelled') {
        // First, check if it's the user's order
        $stmt = $this->db->prepare("SELECT user_id FROM orders WHERE order_id = :order_id");
        $stmt->bindParam(':order_id', $order_id);
        $stmt->execute();
        
        $order = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if(!$order) {
            return ['error' => 'Order not found.'];
        }
        
        if($order['user_id'] != $user_id) {
            return ['error' => 'Order not found.'];
        }
        
        // Set order ID
        $this->order->order_id = $order_id;
        
        // Cancel order
        $result = $this->order->cancel($reason);
        
        if(isset($result['error'])) {
            return $result;
        }
        
        return [
            'success' => true,
            'message' => 'Order cancelled successfully.'
        ];
    }
    
    // Get all orders (admin)
    public function getAllOrders($limit = 10, $offset = 0, $status = null) {
        $stmt = $this->order->getAllOrders($limit, $offset, $status);
        
        return [
            'orders' => $stmt->fetchAll(PDO::FETCH_ASSOC),
            'total' => $this->getTotalOrders($status)
        ];
    }
    
    // Update order status (admin)
    public function updateOrderStatus($order_id, $status) {
        // Check if order exists
        $stmt = $this->db->prepare("SELECT order_id FROM orders WHERE order_id = :order_id");
        $stmt->bindParam(':order_id', $order_id);
        $stmt->execute();
        
        if($stmt->rowCount() === 0) {
            return ['error' => 'Order not found.'];
        }
        
        // Set order properties
        $this->order->order_id = $order_id;
        $this->order->status = $status;
        
        // Update status
        if($this->order->updateStatus()) {
            return [
                'success' => true,
                'message' => 'Order status updated successfully.'
            ];
        } else {
            return ['error' => 'Error updating order status.'];
        }
    }
    
    // Get order statistics (admin)
    public function getOrderStats() {
        return $this->order->getOrderStats();
    }
    
    // Helper method to get total orders count
    private function getTotalOrders($status = null) {
        $query = "SELECT COUNT(*) as total FROM orders";
        
        if($status) {
            $query .= " WHERE status = :status";
        }
        
        $stmt = $this->db->prepare($query);
        
        if($status) {
            $stmt->bindParam(':status', $status);
        }
        
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }
}
?>