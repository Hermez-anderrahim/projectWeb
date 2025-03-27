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

// Include database and product model
include_once '../../backend/config/db_connect.php';
include_once '../../backend/models/Product.php';
include_once '../../backend/models/Category.php';

// Instantiate database and objects
$database = new Database();
$db = $database->getConnection();

$product = new Product($db);
$category = new Category($db);

// Handle product deletion
$delete_message = '';
if(isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $product_id = $_GET['delete'];
    
    // Check if product exists
    $stmt = $db->prepare("SELECT * FROM products WHERE product_id = :product_id");
    $stmt->bindParam(':product_id', $product_id);
    $stmt->execute();
    
    if($stmt->rowCount() > 0) {
        $stmt = $db->prepare("DELETE FROM products WHERE product_id = :product_id");
        $stmt->bindParam(':product_id', $product_id);
        
        if($stmt->execute()) {
            $delete_message = "Product deleted successfully.";
        } else {
            $delete_message = "Error deleting product.";
        }
    }
}

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Filtering
$category_id = isset($_GET['category']) ? $_GET['category'] : null;
$search = isset($_GET['search']) ? $_GET['search'] : null;

// Count total products
$count_query = "SELECT COUNT(*) as total FROM products p";
$params = [];

if($category_id || $search) {
    $count_query .= " WHERE";
    $conditions = [];
    
    if($category_id) {
        $conditions[] = "p.category_id = :category_id";
        $params[':category_id'] = $category_id;
    }
    
    if($search) {
        $conditions[] = "p.name LIKE :search";
        $params[':search'] = "%$search%";
    }
    
    $count_query .= " " . implode(" AND ", $conditions);
}

$stmt = $db->prepare($count_query);
foreach($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->execute();
$total_products = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
$total_pages = ceil($total_products / $limit);

// Get products
$query = "SELECT p.product_id, p.name, p.price, p.old_price, p.stock, p.image, 
                 c.name as category_name
          FROM products p
          LEFT JOIN categories c ON p.category_id = c.category_id";

if($category_id || $search) {
    $query .= " WHERE";
    $conditions = [];
    
    if($category_id) {
        $conditions[] = "p.category_id = :category_id";
    }
    
    if($search) {
        $conditions[] = "p.name LIKE :search";
    }
    
    $query .= " " . implode(" AND ", $conditions);
}

$query .= " ORDER BY p.product_id DESC LIMIT :limit OFFSET :offset";

$stmt = $db->prepare($query);
foreach($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - Admin Dashboard</title>
    <link rel="stylesheet" href="../../assets/css/styles.css">
    <link rel="stylesheet" href="../../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="admin-body">
    <div class="admin-container">
        <?php include '../includes/sidebar.php'; ?>

        <div class="admin-content">
            <div class="admin-header">
                <h1 class="admin-title">Products</h1>
                <div class="admin-actions">
                    <a href="add.php" class="btn"><i class="fas fa-plus"></i> Add New Product</a>
                </div>
            </div>

            <?php if($delete_message): ?>
            <div class="alert <?php echo strpos($delete_message, 'Error') !== false ? 'error' : 'success'; ?>">
                <?php echo $delete_message; ?>
            </div>
            <?php endif; ?>

            <div class="admin-filters">
                <form action="" method="GET" class="filter-form">
                    <div class="filter-row">
                        <div class="filter-group">
                            <label for="category">Category</label>
                            <select name="category" id="category">
                                <option value="">All Categories</option>
                                <?php
                                $stmt = $category->read();
                                while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    $selected = ($category_id == $row['category_id']) ? 'selected' : '';
                                    echo '<option value="' . $row['category_id'] . '" ' . $selected . '>' . $row['name'] . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label for="search">Search</label>
                            <input type="text" name="search" id="search" value="<?php echo htmlspecialchars($search ?? ''); ?>" placeholder="Search products...">
                        </div>
                        <div class="filter-actions">
                            <button type="submit" class="btn">Apply Filters</button>
                            <a href="index.php" class="btn btn-outline">Reset</a>
                        </div>
                    </div>
                </form>
            </div>

            <div class="admin-card">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($products as $item): ?>
                        <tr>
                            <td>#<?php echo $item['product_id']; ?></td>
                            <td class="product-image-cell">
                                <img src="../../assets/images/products/<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>">
                            </td>
                            <td><?php echo $item['name']; ?></td>
                            <td><?php echo $item['category_name']; ?></td>
                            <td>
                                $<?php echo number_format($item['price'], 2); ?>
                                <?php if($item['old_price']): ?>
                                <span class="old-price">$<?php echo number_format($item['old_price'], 2); ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="stock-badge <?php echo $item['stock'] <= 5 ? 'critical' : ($item['stock'] <= 10 ? 'low' : ''); ?>">
                                    <?php echo $item['stock']; ?>
                                </span>
                            </td>
                            <td>
                                <div class="table-actions">
                                    <a href="edit.php?id=<?php echo $item['product_id']; ?>" class="btn-sm btn-edit">Edit</a>
                                    <a href="../../product-detail.php?id=<?php echo $item['product_id']; ?>" class="btn-sm btn-view">View</a>
                                    <a href="#" class="btn-sm btn-delete" data-id="<?php echo $item['product_id']; ?>" data-name="<?php echo $item['name']; ?>">Delete</a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(empty($products)): ?>
                        <tr>
                            <td colspan="7" class="empty-table">No products found.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>

                <?php if($total_pages > 1): ?>
                <div class="pagination">
                    <?php if($page > 1): ?>
                    <a href="<?php echo $_SERVER['PHP_SELF']; ?>?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>" class="prev">
                        <i class="fas fa-chevron-left"></i> Previous
                    </a>
                    <?php endif; ?>
                    
                    <?php for($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                    <a href="<?php echo $_SERVER['PHP_SELF']; ?>?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>" class="<?php echo $i == $page ? 'active' : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                    <?php endfor; ?>
                    
                    <?php if($page < $total_pages): ?>
                    <a href="<?php echo $_SERVER['PHP_SELF']; ?>?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>" class="next">
                        Next <i class="fas fa-chevron-right"></i>
                    </a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal" id="delete-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Confirm Deletion</h2>
                <button class="close-modal">&times;</button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the product "<span id="product-name"></span>"?</p>
                <p>This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline close-modal">Cancel</button>
                <a href="#" class="btn btn-delete" id="confirm-delete">Delete</a>
            </div>
        </div>
    </div>

    <script src="../../assets/js/admin.js"></script>
    <script>
        // Delete confirmation modal
        const deleteButtons = document.querySelectorAll('.btn-delete');
        const deleteModal = document.getElementById('delete-modal');
        const productName = document.getElementById('product-name');
        const confirmDelete = document.getElementById('confirm-delete');
        const closeModal = document.querySelectorAll('.close-modal');
        
        deleteButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const id = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');
                
                productName.textContent = name;
                confirmDelete.href = `index.php?delete=${id}`;
                deleteModal.classList.add('show');
            });
        });
        
        closeModal.forEach(button => {
            button.addEventListener('click', function() {
                deleteModal.classList.remove('show');
            });
        });
        
        window.addEventListener('click', function(e) {
            if(e.target === deleteModal) {
                deleteModal.classList.remove('show');
            }
        });
    </script>
</body>
</html>