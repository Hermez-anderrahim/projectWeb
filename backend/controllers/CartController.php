<?php
// backend/controllers/CartController.php
class CartController {
    private $db;
    private $cart;
    
    public function __construct($db) {
        $this->db = $db;
        
        // Include model if not already included
        if(!class_exists('Cart')) {
            require_once __DIR__ . '/../models/Cart.php';
        }
        
        $this->cart = new Cart($db);
    }
    
    // Get or create cart for user
    public function getCart($user_id) {
        $cart_id = $this->cart->getCart($user_id);
        
        // Get cart items with product details
        $stmt = $this->cart->getItems();
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get cart total
        $total = $this->cart->getTotal();
        
        // Calculate shipping based on total
        $shipping = $total >= 100 ? 0 : 10;
        
        return [
            'cart_id' => $cart_id,
            'items' => $items,
            'subtotal' => $total,
            'shipping' => $shipping,
            'total' => $total + $shipping,
            'item_count' => count($items)
        ];
    }
    
    // Add item to cart
    public function addToCart($user_id, $product_id, $quantity = 1) {
        // First, get or create cart
        $cart_id = $this->cart->getCart($user_id);
        
        // Check product exists and has enough stock
        $stmt = $this->db->prepare("SELECT stock FROM products WHERE product_id = :product_id");
        $stmt->bindParam(':product_id', $product_id);
        $stmt->execute();
        
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if(!$product) {
            return ['error' => 'Product not found.'];
        }
        
        if($product['stock'] < $quantity) {
            return ['error' => 'Not enough stock available.'];
        }
        
        // Add to cart
        $result = $this->cart->addItem($product_id, $quantity);
        
        if(isset($result['error'])) {
            return $result;
        }
        
        return [
            'success' => true,
            'message' => 'Product added to cart successfully.',
            'cart_count' => $this->cart->getItemCount()
        ];
    }
    
    // Update cart item quantity
    public function updateCartItem($user_id, $cart_item_id, $quantity) {
        // First, ensure it's the user's cart
        $cart_id = $this->cart->getCart($user_id);
        
        // Check if cart item exists and belongs to this cart
        $stmt = $this->db->prepare("SELECT ci.cart_item_id, ci.product_id, p.stock 
                                   FROM cart_items ci
                                   JOIN products p ON ci.product_id = p.product_id
                                   WHERE ci.cart_item_id = :cart_item_id AND ci.cart_id = :cart_id");
        $stmt->bindParam(':cart_item_id', $cart_item_id);
        $stmt->bindParam(':cart_id', $cart_id);
        $stmt->execute();
        
        $item = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if(!$item) {
            return ['error' => 'Cart item not found.'];
        }
        
        // Check stock
        if($quantity > $item['stock']) {
            return ['error' => 'Not enough stock available.'];
        }
        
        // Update quantity (would need to create an updateItem method in the Cart model)
        // For now, let's simulate it with a direct query
        $stmt = $this->db->prepare("UPDATE cart_items SET quantity = :quantity WHERE cart_item_id = :cart_item_id");
        $stmt->bindParam(':quantity', $quantity);
        $stmt->bindParam(':cart_item_id', $cart_item_id);
        
        if($stmt->execute()) {
            return [
                'success' => true,
                'message' => 'Cart item updated successfully.'
            ];
        } else {
            return ['error' => 'Error updating cart item.'];
        }
    }
    
    // Remove item from cart
    public function removeFromCart($user_id, $cart_item_id) {
        // First, ensure it's the user's cart
        $cart_id = $this->cart->getCart($user_id);
        
        // Check if cart item exists and belongs to this cart
        $stmt = $this->db->prepare("SELECT cart_item_id FROM cart_items 
                                   WHERE cart_item_id = :cart_item_id AND cart_id = :cart_id");
        $stmt->bindParam(':cart_item_id', $cart_item_id);
        $stmt->bindParam(':cart_id', $cart_id);
        $stmt->execute();
        
        if($stmt->rowCount() === 0) {
            return ['error' => 'Cart item not found.'];
        }
        
        // Remove item
        if($this->cart->removeItem($cart_item_id)) {
            return [
                'success' => true,
                'message' => 'Item removed from cart successfully.'
            ];
        } else {
            return ['error' => 'Error removing item from cart.'];
        }
    }
    
    // Clear cart
    public function clearCart($user_id) {
        // First, ensure it's the user's cart
        $cart_id = $this->cart->getCart($user_id);
        
        // Clear cart
        if($this->cart->clear()) {
            return [
                'success' => true,
                'message' => 'Cart cleared successfully.'
            ];
        } else {
            return ['error' => 'Error clearing cart.'];
        }
    }
    
    // Get cart count (for header display)
    public function getCartCount($user_id) {
        // First, ensure it's the user's cart
        $cart_id = $this->cart->getCart($user_id);
        
        return [
            'count' => $this->cart->getItemCount()
        ];
    }
}
?>