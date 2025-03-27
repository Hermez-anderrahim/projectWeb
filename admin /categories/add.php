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

$success_message = '';
$error_message = '';

// Handle form submission
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Set category properties
    $category->name = $_POST['name'];
    $category->slug = strtolower(str_replace(' ', '-', $_POST['name']));
    $category->parent_id = empty($_POST['parent_id']) ? null : $_POST['parent_id'];
    $category->icon = $_POST['icon'];
    
    // Create category
    if($category->create()) {
        $success_message = 'Category created successfully.';
    } else {
        $error_message = 'Error creating category.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Category - Admin Dashboard</title>
    <link rel="stylesheet" href="../../assets/css/styles.css">
    <link rel="stylesheet" href="../../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="admin-body">
    <div class="admin-container">
        <?php include '../includes/sidebar.php'; ?>

        <div class="admin-content">
            <div class="admin-header">
                <h1 class="admin-title">Add New Category</h1>
                <div class="admin-actions">
                    <a href="index.php" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Back to Categories</a>
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
                <form action="add.php" method="POST" class="category-form">
                    <div class="form-group">
                        <label for="name">Category Name *</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="parent_id">Parent Category</label>
                        <select id="parent_id" name="parent_id">
                            <option value="">None (Top Level Category)</option>
                            <?php
                            $stmt = $category->readMain();
                            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                echo '<option value="' . $row['category_id'] . '">' . $row['name'] . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="icon">Icon Class *</label>
                        <input type="text" id="icon" name="icon" placeholder="fas fa-tag" required>
                        <div class="icon-preview">
                            <i class="fas fa-tag" id="icon-preview"></i>
                            <span>Preview</span>
                        </div>
                        <small>Enter a Font Awesome icon class (e.g. fas fa-tag, fas fa-shirt)</small>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn">Add Category</button>
                        <a href="index.php" class="btn btn-outline">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Icon preview
        const iconInput = document.getElementById('icon');
        const iconPreview = document.getElementById('icon-preview');
        
        iconInput.addEventListener('input', function() {
            iconPreview.className = this.value;
        });
    </script>
</body>
</html>