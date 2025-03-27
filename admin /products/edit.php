<?php
// Start session if not already started
if(!isset($_SESSION)) {
    session_start();
}

// Check if user is logged in and is admin
if(!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header('Location: ../../login.php');
    exit;
}

// Check if product ID is provided
if(!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php');
    exit;
}

// Include database and models
include_once '../../backend/config/db_connect.php';
include_once '../../backend/models/Product.php';
include_once '../../backend/models/Category.php';

// Instantiate database and objects
$database = new Database();
$db = $database->getConnection();

$product = new Product($db);
$category = new Category($db);

// Get product details
$product->product_id = $_GET['id'];
if(!$product->readOne()) {
    header('Location: index.php');
    exit;
}

$success_message = '';
$error_message = '';

// Handle form submission
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Set product properties
    $product->name = $_POST['name'];
    $product->slug = strtolower(str_replace(' ', '-', $_POST['name']));
    $product->description = $_POST['description'];
    $product->price = $_POST['price'];
    $product->old_price = empty($_POST['old_price']) ? null : $_POST['old_price'];
    $product->stock = $_POST['stock'];
    $product->category_id = $_POST['category_id'];
    
    // Handle image upload
    if(isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../../assets/images/products/';
        $temp_name = $_FILES['image']['tmp_name'];
        $filename = time() . '_' . $_FILES['image']['name'];
        
        if(move_uploaded_file($temp_name, $upload_dir . $filename)) {
            // Delete old image if not default
            if($product->image != 'default-product.jpg') {
                @unlink($upload_dir . $product->image);
            }
            
            $product->image = $filename;
        } else {
            $error_message = 'Error uploading image.';
        }
    }
    
    // Update product (would need to add update method to Product class)
    $stmt = $db->prepare("UPDATE products SET 
                         name = :name, 
                         slug = :slug, 
                         description = :description,
                         price = :price,
                         old_price = :old_price,
                         stock = :stock,
                         image = :image,
                         category_id = :category_id
                         WHERE product_id = :product_id");
                         
    $stmt->bindParam(':name', $product->name);
    $stmt->bindParam(':slug', $product->slug);
    $stmt->bindParam(':description', $product->description);
    $stmt->bindParam(':price', $product->price);
    $stmt->bindParam(':old_price', $product->old_price);
    $stmt->bindParam(':stock', $product->stock);
    $stmt->bindParam(':image', $product->image);
    $stmt->bindParam(':category_id', $product->category_id);
    $stmt->bindParam(':product_id', $product->product_id);
    
    if($stmt->execute()) {
        $success_message = 'Product updated successfully.';
        
        // Refresh product data
        $product->readOne();
    } else {
        $error_message = 'Error updating product.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product - Admin Dashboard</title>
    <link rel="stylesheet" href="../../assets/css/styles.css">
    <link rel="stylesheet" href="../../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="admin-body">
    <div class="admin-container">
        <?php include '../includes/sidebar.php'; ?>

        <div class="admin-content">
            <div class="admin-header">
                <h1 class="admin-title">Edit Product: <?php echo $product->name; ?></h1>
                <div class="admin-actions">
                    <a href="index.php" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Back to Products</a>
                    <a href="../../product-detail.php?id=<?php echo $product->product_id; ?>" class="btn btn-outline" target="_blank"><i class="fas fa-eye"></i> View Product</a>
                </div>
            </div>

            <?php if($success_message): ?>
            <div class="alert success">
                <?php echo $success_message; ?>
            </div>
            <?php endif; ?>

            <?php if($error_message): ?>
            <div class="alert error">
                <?php echo $error_message; ?>
            </div>
            <?php endif; ?>

            <div class="admin-card">
                <form action="edit.php?id=<?php echo $product->product_id; ?>" method="POST" enctype="multipart/form-data" class="product-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="name">Product Name *</label>
                            <input type="text" id="name" name="name" value="<?php echo $product->name; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="category_id">Category *</label>
                            <select id="category_id" name="category_id" required>
                                <option value="">Select Category</option>
                                <?php
                                $stmt = $category->read();
                                while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    $selected = ($row['category_id'] == $product->category_id) ? 'selected' : '';
                                    echo '<option value="' . $row['category_id'] . '" ' . $selected . '>' . $row['name'] . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Description *</label>
                        <textarea id="description" name="description" rows="5" required><?php echo $product->description; ?></textarea>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="price">Price ($) *</label>
                            <input type="number" id="price" name="price" step="0.01" min="0" value="<?php echo $product->price; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="old_price">Regular Price ($) <small>(Optional)</small></label>
                            <input type="number" id="old_price" name="old_price" step="0.01" min="0" value="<?php echo $product->old_price; ?>">
                        </div>
                        <div class="form-group">
                            <label for="stock">Stock *</label>
                            <input type="number" id="stock" name="stock" min="0" value="<?php echo $product->stock; ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="image">Product Image</label>
                        <div class="image-upload">
                            <div class="image-preview" id="imagePreview">
                                <img src="../../assets/images/products/<?php echo $product->image; ?>" alt="Product Image Preview" id="preview-image">
                                <span><?php echo $product->image; ?></span>
                            </div>
                            <input type="file" id="image" name="image" accept="image/*">
                            <label for="image" class="image-upload-label">Choose File</label>
                            <small>Leave empty to keep the current image</small>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn">Update Product</button>
                        <a href="index.php" class="btn btn-outline">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Image preview
        const imageInput = document.getElementById('image');
        const imagePreview = document.getElementById('preview-image');
        const imageLabel = document.querySelector('.image-preview span');
        
        imageInput.addEventListener('change', function() {
            const file = this.files[0];
            if(file) {
                const reader = new FileReader();
                
                reader.addEventListener('load', function() {
                    imagePreview.src = this.result;
                    imageLabel.textContent = file.name;
                });
                
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>