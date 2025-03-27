<?php
// backend/models/Order.php
class Order {
    private $conn;
    private $table = "orders";
    private $items_table = "order_items";

    // Properties
    public $order_id;
    public $user_id;
    public $total;
    public $status;
    public $shipping_address;
    public $payment_method;
    public $created_at;
    public $updated_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Get all orders for a user
    public function getUserOrders($user_id) {
        $query = "SELECT o.order_id, o.total, o.status, o.created_at, o.shipping_address, o.payment_method,
                         COUNT(oi.order_item_id) as item_count
                  FROM " . $this->table . " o
                  LEFT JOIN " . $this->items_table . " oi ON o.order_id = oi.order_id
                  WHERE o.user_id = :user_id
                  GROUP BY o.order_id
                  ORDER BY o.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        
        return $stmt;
    }
    
    // Get single order details with items
    public function getOrderDetails($order_id) {
        // Use the stored procedure to get order details
        $query = "CALL GetOrderDetails(:order_id)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':order_id', $order_id);
        $stmt->execute();
        
        return $stmt;
    }
    
    // Create new order
    public function create() {
        try {
            // Call the stored procedure to finalize order and empty cart
            $stmt = $this->conn->prepare("CALL FinalizeOrder(:user_id, :shipping_address, :payment_method, @order_id)");
            
            // Sanitize data
            $this->shipping_address = htmlspecialchars(strip_tags($this->shipping_address));
            $this->payment_method = htmlspecialchars(strip_tags($this->payment_method));
            
            $stmt->bindParam(':user_id', $this->user_id);
            $stmt->bindParam(':shipping_address', $this->shipping_address);
            $stmt->bindParam(':payment_method', $this->payment_method);
            
            $stmt->execute();
            
            // Get the order ID from the output parameter
            $result = $this->conn->query("SELECT @order_id AS order_id")->fetch(PDO::FETCH_ASSOC);
            $this->order_id = $result['order_id'];
            
            return $this->order_id;
        } catch(PDOException $e) {
            return false;
        }
    }
    
    // Update order status
    public function updateStatus() {
        $query = "UPDATE " . $this->table . "
                  SET status = :status
                  WHERE order_id = :order_id";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitize data
        $this->status = htmlspecialchars(strip_tags($this->status));
        
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':order_id', $this->order_id);
        
        if($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    // Cancel order
    public function cancel($reason = 'Customer cancelled') {
        // First, check if the order exists and is in a cancellable state
        $query = "SELECT status FROM " . $this->table . " WHERE order_id = :order_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':order_id', $this->order_id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if(!$row) {
            return ['error' => 'Order not found.'];
        }
        
        if($row['status'] == 'cancelled') {
            return ['error' => 'Order is already cancelled.'];
        }
        
        if($row['status'] != 'pending' && $row['status'] != 'processing') {
            return ['error' => 'Order cannot be cancelled at this stage.'];
        }
        
        try {
            // Start transaction
            $this->conn->beginTransaction();
            
            // Update order status to cancelled
            $query = "UPDATE " . $this->table . " SET status = 'cancelled' WHERE order_id = :order_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':order_id', $this->order_id);
            $stmt->execute();
            
            // Get order details for the history
            $query = "SELECT user_id, total FROM " . $this->table . " WHERE order_id = :order_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':order_id', $this->order_id);
            $stmt->execute();
            $order = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Insert into cancelled orders history
            $query = "INSERT INTO cancelled_orders (order_id, user_id, total, reason) 
                      VALUES (:order_id, :user_id, :total, :reason)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':order_id', $this->order_id);
            $stmt->bindParam(':user_id', $order['user_id']);
            $stmt->bindParam(':total', $order['total']);
            $stmt->bindParam(':reason', $reason);
            $stmt->execute();
            
            // Restore product stock - the after_order_status_update trigger will handle this
            
            // Commit transaction
            $this->conn->commit();
            
            return ['success' => true];
        } catch(PDOException $e) {
            // Rollback transaction on error
            $this->conn->rollBack();
            return ['error' => $e->getMessage()];
        }
    }
    
    // Get all orders (for admin)
    public function getAllOrders($limit = 10, $offset = 0, $status = null) {
        $query = "SELECT o.order_id, o.user_id, o.total, o.status, o.created_at, o.shipping_address, o.payment_method,
                         u.username, u.email
                  FROM " . $this->table . " o
                  JOIN users u ON o.user_id = u.user_id";
        
        if($status) {
            $query .= " WHERE o.status = :status";
        }
        
        $query .= " ORDER BY o.created_at DESC LIMIT :limit OFFSET :offset";
        
        $stmt = $this->conn->prepare($query);
        
        if($status) {
            $stmt->bindParam(':status', $status);
        }
        
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        
        $stmt->execute();
        
        return $stmt;
    }
    
    // Get order statistics (for admin dashboard)
    public function getOrderStats() {
        $stats = [];
        
        // Total orders
        $query = "SELECT COUNT(*) as total FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $stats['total'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Pending orders
        $query = "SELECT COUNT(*) as pending FROM " . $this->table . " WHERE status = 'pending'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $stats['pending'] = $stmt->fetch(PDO::FETCH_ASSOC)['pending'];
        
        // Processing orders
        $query = "SELECT COUNT(*) as processing FROM " . $this->table . " WHERE status = 'processing'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $stats['processing'] = $stmt->fetch(PDO::FETCH_ASSOC)['processing'];
        
        // Shipped orders
        $query = "SELECT COUNT(*) as shipped FROM " . $this->table . " WHERE status = 'shipped'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $stats['shipped'] = $stmt->fetch(PDO::FETCH_ASSOC)['shipped'];
        
        // Delivered orders
        $query = "SELECT COUNT(*) as delivered FROM " . $this->table . " WHERE status = 'delivered'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $stats['delivered'] = $stmt->fetch(PDO::FETCH_ASSOC)['delivered'];
        
        // Cancelled orders
        $query = "SELECT COUNT(*) as cancelled FROM " . $this->table . " WHERE status = 'cancelled'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $stats['cancelled'] = $stmt->fetch(PDO::FETCH_ASSOC)['cancelled'];
        
        // Total revenue
        $query = "SELECT SUM(total) as revenue FROM " . $this->table . " WHERE status != 'cancelled'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $stats['revenue'] = $stmt->fetch(PDO::FETCH_ASSOC)['revenue'] ?: 0;
        
        return $stats;
    }
}
?>