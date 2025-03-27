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

// Include database and category model
include_once '../../backend/config/db_connect.php';
include_once '../../backend/models/Category.php';

// Instantiate database and category object
$database = new Database();
$db = $database->getConnection();

$category = new Category($db);

// Handle category deletion
$delete_message = '';
if(isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $category_id = $_GET['delete'];
    
    // Check if category exists
    $stmt = $db->prepare("SELECT * FROM categories WHERE category_id = :category_id");
    $stmt->bindParam(':category_id', $category_id);
    $stmt->execute();
    
    if($stmt->rowCount() > 0) {
        // Check if category has subcategories
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM categories WHERE parent_id = :category_id");
        $stmt->bindParam(':category_id', $category_id);
        $stmt->execute();
        
        if($stmt->fetch(PDO::FETCH_ASSOC)['count'] > 0) {
            $delete_message = "Cannot delete category that has subcategories.";
        } else {
            // Check if category has products
            $stmt = $db->prepare("SELECT COUNT(*) as count FROM products WHERE category_id = :category_id");
            $stmt->bindParam(':category_id', $category_id);
            $stmt->execute();
            
            if($stmt->fetch(PDO::FETCH_ASSOC)['count'] > 0) {
                $delete_message = "Cannot delete category that has products.";
            } else {
                $stmt = $db->prepare("DELETE FROM categories WHERE category_id = :category_id");
                $stmt->bindParam(':category_id', $category_id);
                
                if($stmt->execute()) {
                    $delete_message = "Category deleted successfully.";
                } else {
                    $delete_message = "Error deleting category.";
                }
            }
        }
    }
}

// Get all categories
$stmt = $category->read();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categories - Admin Dashboard</title>
    <link rel="stylesheet" href="../../assets/css/styles.css">
    <link rel="stylesheet" href="../../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="admin-body">
    <div class="admin-container">
        <?php include '../includes/sidebar.php'; ?>

        <div class="admin-content">
            <div class="admin-header">
                <h1 class="admin-title">Categories</h1>
                <div class="admin-actions">
                    <a href="add.php" class="btn"><i class="fas fa-plus"></i> Add New Category</a>
                </div>
            </div>

            <?php if($delete_message): ?>
            <div class="alert <?php echo strpos($delete_message, 'Error') !== false || strpos($delete_message, 'Cannot') !== false ? 'error' : 'success'; ?>">
                <?php echo $delete_message; ?>
            </div>
            <?php endif; ?>

            <div class="admin-card">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Slug</th>
                            <th>Parent Category</th>
                            <th>Icon</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($categories as $cat): ?>
                        <tr>
                            <td>#<?php echo $cat['category_id']; ?></td>
                            <td><?php echo $cat['name']; ?></td>
                            <td><?php echo $cat['slug']; ?></td>
                            <td><?php echo $cat['parent_name'] ?: '-'; ?></td>
                            <td><i class="<?php echo $cat['icon']; ?>"></i> <?php echo $cat['icon']; ?></td>
                            <td>
                                <div class="table-actions">
                                    <a href="edit.php?id=<?php echo $cat['category_id']; ?>" class="btn-sm btn-edit">Edit</a>
                                    <a href="../../products.php?category=<?php echo $cat['slug']; ?>" class="btn-sm btn-view">View</a>
                                    <a href="#" class="btn-sm btn-delete" data-id="<?php echo $cat['category_id']; ?>" data-name="<?php echo $cat['name']; ?>">Delete</a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(empty($categories)): ?>
                        <tr>
                            <td colspan="6" class="empty-table">No categories found.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
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
                <p>Are you sure you want to delete the category "<span id="category-name"></span>"?</p>
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
        const categoryName = document.getElementById('category-name');
        const confirmDelete = document.getElementById('confirm-delete');
        const closeModal = document.querySelectorAll('.close-modal');
        
        deleteButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const id = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');
                
                categoryName.textContent = name;
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