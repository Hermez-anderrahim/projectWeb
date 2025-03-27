<?php
// backend/controllers/ProductController.php
class ProductController {
    private $db;
    private $product;
    private $category;
    
    public function __construct($db) {
        $this->db = $db;
        
        // Include models if not already included
        if(!class_exists('Product')) {
            require_once __DIR__ . '/../models/Product.php';
        }
        
        if(!class_exists('Category')) {
            require_once __DIR__ . '/../models/Category.php';
        }
        
        $this->product = new Product($db);
        $this->category = new Category($db);
    }
    
    // Get products for the home page
    public function getHomePageProducts() {
        $result = [];
        
        // Get trending products
        $stmt = $this->product->getTrending(8);
        $result['trending'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get latest products
        $stmt = $this->product->read(null, 8, 0);
        $result['latest'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return $result;
    }
    
    // Get products for the products page with filtering and pagination
    public function getProducts($category_id = null, $page = 1, $limit = 12, $sort = null, $min_price = null, $max_price = null) {
        $result = [];
        
        // Calculate offset
        $offset = ($page - 1) * $limit;
        
        // Get products based on filters
        if($sort) {
            // Handle sorting
            // (In a real application, you would modify the read method to accept sorting parameters)
            $stmt = $this->product->read($category_id, $limit, $offset);
        } else {
            $stmt = $this->product->read($category_id, $limit, $offset);
        }
        
        $result['products'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Count total products for pagination
        $countQuery = "SELECT COUNT(*) as total FROM products";
        if($category_id) {
            $countQuery .= " WHERE category_id = :category_id";
        }
        $stmt = $this->db->prepare($countQuery);
        if($category_id) {
            $stmt->bindParam(':category_id', $category_id);
        }
        $stmt->execute();
        
        $result['total'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        $result['total_pages'] = ceil($result['total'] / $limit);
        $result['current_page'] = $page;
        
        return $result;
    }
    
    // Get product details
    public function getProductDetails($product_id) {
        $this->product->product_id = $product_id;
        
        if($this->product->readOne()) {
            // Get related products (same category)
            $stmt = $this->db->prepare("SELECT p.product_id, p.name, p.slug, p.description, p.price, p.old_price, 
                                       p.stock, p.image, c.name AS category_name
                                FROM products p
                                LEFT JOIN categories c ON p.category_id = c.category_id
                                WHERE p.category_id = :category_id AND p.product_id != :product_id
                                ORDER BY RAND()
                                LIMIT 4");
            $stmt->bindParam(':category_id', $this->product->category_id);
            $stmt->bindParam(':product_id', $product_id);
            $stmt->execute();
            
            $related_products = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return [
                'product' => [
                    'product_id' => $this->product->product_id,
                    'name' => $this->product->name,
                    'slug' => $this->product->slug,
                    'description' => $this->product->description,
                    'price' => $this->product->price,
                    'old_price' => $this->product->old_price,
                    'stock' => $this->product->stock,
                    'image' => $this->product->image,
                    'category_id' => $this->product->category_id,
                    'category_name' => $this->product->category_name
                ],
                'related_products' => $related_products
            ];
        }
        
        return false;
    }
    
    // Search products
    public function searchProducts($keyword, $category_id = null, $min_price = null, $max_price = null) {
        $stmt = $this->product->search($keyword, $category_id, $min_price, $max_price);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Add new product (admin)
    public function addProduct($data, $image = null) {
        // Set product properties
        $this->product->name = $data['name'];
        $this->product->slug = strtolower(str_replace(' ', '-', $data['name']));
        $this->product->description = $data['description'];
        $this->product->price = $data['price'];
        $this->product->old_price = empty($data['old_price']) ? null : $data['old_price'];
        $this->product->stock = $data['stock'];
        $this->product->category_id = $data['category_id'];
        
        // Handle image upload
        if($image && $image['error'] === UPLOAD_ERR_OK) {
            $upload_dir = __DIR__ . '/../../assets/images/products/';
            $temp_name = $image['tmp_name'];
            $filename = time() . '_' . $image['name'];
            
            if(move_uploaded_file($temp_name, $upload_dir . $filename)) {
                $this->product->image = $filename;
            } else {
                return ['error' => 'Error uploading image.'];
            }
        } else {
            $this->product->image = 'default-product.jpg'; // Default image
        }
        
        // Create product
        if($this->product->create()) {
            return ['success' => true, 'product_id' => $this->product->product_id];
        } else {
            return ['error' => 'Error creating product.'];
        }
    }
    
    // Update product (admin)
    public function updateProduct($product_id, $data, $image = null) {
        // First, check if product exists
        $this->product->product_id = $product_id;
        
        if(!$this->product->readOne()) {
            return ['error' => 'Product not found.'];
        }
        
        // Update product properties
        $this->product->name = $data['name'];
        $this->product->slug = strtolower(str_replace(' ', '-', $data['name']));
        $this->product->description = $data['description'];
        $this->product->price = $data['price'];
        $this->product->old_price = empty($data['old_price']) ? null : $data['old_price'];
        $this->product->stock = $data['stock'];
        $this->product->category_id = $data['category_id'];
        
        // Handle image upload
        if($image && $image['error'] === UPLOAD_ERR_OK) {
            $upload_dir = __DIR__ . '/../../assets/images/products/';
            $temp_name = $image['tmp_name'];
            $filename = time() . '_' . $image['name'];
            
            if(move_uploaded_file($temp_name, $upload_dir . $filename)) {
                // Delete old image if not default
                if($this->product->image != 'default-product.jpg') {
                    @unlink($upload_dir . $this->product->image);
                }
                
                $this->product->image = $filename;
            } else {
                return ['error' => 'Error uploading image.'];
            }
        }
        
        // Update product (would need to create an update method in the Product model)
        // For now, let's assume it exists
        if($this->product->update()) {
            return ['success' => true];
        } else {
            return ['error' => 'Error updating product.'];
        }
    }
    
    // Delete product (admin)
    public function deleteProduct($product_id) {
        // First, check if product exists
        $this->product->product_id = $product_id;
        
        if(!$this->product->readOne()) {
            return ['error' => 'Product not found.'];
        }
        
        // Check if product is in any order
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM order_items WHERE product_id = :product_id");
        $stmt->bindParam(':product_id', $product_id);
        $stmt->execute();
        
        if($stmt->fetch(PDO::FETCH_ASSOC)['count'] > 0) {
            return ['error' => 'Cannot delete product as it is referenced in orders.'];
        }
        
        // Delete product (would need to create a delete method in the Product model)
        // For now, let's simulate it with a direct query
        $stmt = $this->db->prepare("DELETE FROM products WHERE product_id = :product_id");
        $stmt->bindParam(':product_id', $product_id);
        
        if($stmt->execute()) {
            // Delete product image if not default
            if($this->product->image != 'default-product.jpg') {
                $upload_dir = __DIR__ . '/../../assets/images/products/';
                @unlink($upload_dir . $this->product->image);
            }
            
            return ['success' => true];
        } else {
            return ['error' => 'Error deleting product.'];
        }
    }
}
?>