<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account - Anon Shop</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <?php 
    include 'includes/header.php';
    
    // Start session if not already started
    if(!isset($_SESSION)) {
        session_start();
    }
    
    // Check if user is logged in
    if(!isset($_SESSION['user_id'])) {
        header('Location: login.php?redirect=account.php');
        exit;
    }
    
    // Include database and user model
    include_once 'backend/config/db_connect.php';
    include_once 'backend/models/User.php';
    
    // Instantiate database and user object
    $database = new Database();
    $db = $database->getConnection();
    
    $user = new User($db);
    $user_details = $user->getUserDetails($_SESSION['user_id']);
    
    $success_message = '';
    $error_message = '';
    
    // Handle form submission for profile update
    if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
        $user->user_id = $_SESSION['user_id'];
        $user->first_name = $_POST['first_name'];
        $user->last_name = $_POST['last_name'];
        $user->address = $_POST['address'];
        $user->phone = $_POST['phone'];
        
        if($user->updateUser()) {
            $success_message = 'Profile updated successfully!';
            $user_details = $user->getUserDetails($_SESSION['user_id']);
        } else {
            $error_message = 'Error updating profile.';
        }
    }
    ?>

    <main class="container">
        <div class="breadcrumb">
            <a href="index.php">Home</a> &gt; 
            <span>My Account</span>
        </div>

        <div class="account-container">
            <div class="account-sidebar">
                <div class="user-info">
                    <div class="user-avatar">
                        <img src="assets/images/avatar.jpg" alt="User Avatar">
                    </div>
                    <div class="user-name">
                        <?php echo $user_details['first_name'] . ' ' . $user_details['last_name']; ?>
                    </div>
                    <div class="user-email">
                        <?php echo $user_details['email']; ?>
                    </div>
                </div>
                <ul class="account-menu">
                    <li class="active"><a href="#dashboard" data-tab="dashboard"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="#orders" data-tab="orders"><i class="fas fa-shopping-bag"></i> Orders</a></li>
                    <li><a href="#addresses" data-tab="addresses"><i class="fas fa-map-marker-alt"></i> Addresses</a></li>
                    <li><a href="#profile" data-tab="profile"><i class="fas fa-user"></i> Profile</a></li>
                    <li><a href="#wishlist" data-tab="wishlist"><i class="fas fa-heart"></i> Wishlist</a></li>
                    <?php if($user_details['is_admin']): ?>
                    <li><a href="admin/index.php"><i class="fas fa-lock"></i> Admin Panel</a></li>
                    <?php endif; ?>
                    <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </div>

            <div class="account-content">
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
                
                <div class="account-tab active" id="dashboard">
                    <h1>Dashboard</h1>
                    <p>Hello, <strong><?php echo $user_details['first_name'] . ' ' . $user_details['last_name']; ?></strong>! From your account dashboard you can view your recent orders, manage your shipping and billing addresses, and edit your password and account details.</p>
                    
                    <div class="dashboard-cards">
                        <div class="dashboard-card">
                            <div class="dashboard-card-icon">
                                <i class="fas fa-shopping-bag"></i>
                            </div>
                            <div class="dashboard-card-content">
                                <h3>My Orders</h3>
                                <p>View or track your recent orders</p>
                                <a href="#orders" class="dashboard-card-link">View Orders</a>
                            </div>
                        </div>
                        
                        <div class="dashboard-card">
                            <div class="dashboard-card-icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div class="dashboard-card-content">
                                <h3>My Addresses</h3>
                                <p>Manage your shipping and billing addresses</p>
                                <a href="#addresses" class="dashboard-card-link">View Addresses</a>
                            </div>
                        </div>
                        
                        <div class="dashboard-card">
                            <div class="dashboard-card-icon">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="dashboard-card-content">
                                <h3>My Profile</h3>
                                <p>Edit your profile and change password</p>
                                <a href="#profile" class="dashboard-card-link">Edit Profile</a>
                            </div>
                        </div>
                        
                        <div class="dashboard-card">
                            <div class="dashboard-card-icon">
                                <i class="fas fa-heart"></i>
                            </div>
                            <div class="dashboard-card-content">
                                <h3>My Wishlist</h3>
                                <p>View or manage your wishlist</p>
                                <a href="#wishlist" class="dashboard-card-link">View Wishlist</a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="account-tab" id="orders">
                    <h1>My Orders</h1>
                    
                    <?php
                    // Get order history
                    $stmt = $db->prepare("CALL GetOrderHistory(:user_id)");
                    $stmt->bindParam(':user_id', $_SESSION['user_id']);
                    $stmt->execute();
                    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    if(empty($orders)):
                    ?>
                    <div class="empty-data">
                        <i class="fas fa-shopping-bag"></i>
                        <h2>No orders found</h2>
                        <p>You haven't placed any orders yet.</p>
                        <a href="products.php" class="btn">Start Shopping</a>
                    </div>
                    <?php else: ?>
                    <div class="orders-table-wrapper">
                        <table class="orders-table">
                            <thead>
                                <tr>
                                    <th>Order</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Total</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($orders as $order): ?>
                                <tr>
                                    <td>#<?php echo $order['order_id']; ?></td>
                                    <td><?php echo date('F j, Y', strtotime($order['created_at'])); ?></td>
                                    <td>
                                        <span class="order-status <?php echo strtolower($order['status']); ?>">
                                            <?php echo ucfirst($order['status']); ?>
                                        </span>
                                    </td>
                                    <td>$<?php echo number_format($order['total'], 2); ?></td>
                                    <td>
                                        <a href="order-detail.php?id=<?php echo $order['order_id']; ?>" class="btn-sm">View</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>
                
                <div class="account-tab" id="addresses">
                    <h1>My Addresses</h1>
                    
                    <div class="addresses-container">
                        <div class="address-card">
                            <div class="address-card-header">
                                <h3>Shipping Address</h3>
                                <a href="#" class="edit-address"><i class="fas fa-edit"></i></a>
                            </div>
                            <div class="address-card-content">
                                <?php if($user_details['address']): ?>
                                <p><?php echo $user_details['first_name'] . ' ' . $user_details['last_name']; ?></p>
                                <p><?php echo $user_details['address']; ?></p>
                                <p><?php echo $user_details['phone']; ?></p>
                                <?php else: ?>
                                <p>No shipping address added yet.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="address-card">
                            <div class="address-card-header">
                                <h3>Billing Address</h3>
                                <a href="#" class="edit-address"><i class="fas fa-edit"></i></a>
                            </div>
                            <div class="address-card-content">
                                <?php if($user_details['address']): ?>
                                <p><?php echo $user_details['first_name'] . ' ' . $user_details['last_name']; ?></p>
                                <p><?php echo $user_details['address']; ?></p>
                                <p><?php echo $user_details['phone']; ?></p>
                                <?php else: ?>
                                <p>No billing address added yet.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="account-tab" id="profile">
                    <h1>My Profile</h1>
                    
                    <form class="profile-form" action="account.php" method="POST">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="first_name">First Name</label>
                                <input type="text" id="first_name" name="first_name" value="<?php echo $user_details['first_name']; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="last_name">Last Name</label>
                                <input type="text" id="last_name" name="last_name" value="<?php echo $user_details['last_name']; ?>" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" id="username" value="<?php echo $user_details['username']; ?>" disabled>
                            <span class="form-note">Username cannot be changed.</span>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" id="email" value="<?php echo $user_details['email']; ?>" disabled>
                            <span class="form-note">Email cannot be changed.</span>
                        </div>
                        
                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" id="phone" name="phone" value="<?php echo $user_details['phone']; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="address">Address</label>
                            <textarea id="address" name="address" rows="3"><?php echo $user_details['address']; ?></textarea>
                        </div>
                        
                        <button type="submit" name="update_profile" class="btn">Update Profile</button>
                    </form>
                    
                    <h2>Change Password</h2>
                    
                    <form class="password-form" action="change-password.php" method="POST">
                        <div class="form-group">
                            <label for="current_password">Current Password</label>
                            <input type="password" id="current_password" name="current_password" required>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="new_password">New Password</label>
                                <input type="password" id="new_password" name="new_password" required>
                            </div>
                            <div class="form-group">
                                <label for="confirm_password">Confirm New Password</label>
                                <input type="password" id="confirm_password" name="confirm_password" required>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn">Change Password</button>
                    </form>
                </div>
                
                <div class="account-tab" id="wishlist">
                    <h1>My Wishlist</h1>
                    
                    <div class="empty-data">
                        <i class="fas fa-heart"></i>
                        <h2>Your wishlist is empty</h2>
                        <p>Add items to your wishlist by clicking the heart icon on product pages.</p>
                        <a href="products.php" class="btn">Browse Products</a>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>
    
    <!-- Scripts -->
    <script src="assets/js/main.js"></script>
    <script>
        // Tab handling
        document.querySelectorAll('.account-menu a').forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Get the tab ID from the href
                const tabId = this.getAttribute('data-tab');
                
                // Remove active class from all menu items and tabs
                document.querySelectorAll('.account-menu li').forEach(item => {
                    item.classList.remove('active');
                });
                document.querySelectorAll('.account-tab').forEach(tab => {
                    tab.classList.remove('active');
                });
                
                // Add active class to clicked menu item and corresponding tab
                this.parentElement.classList.add('active');
                document.getElementById(tabId).classList.add('active');
                
                // Update URL hash
                window.location.hash = tabId;
            });
        });
        
        // Check URL hash on page load
        window.addEventListener('DOMContentLoaded', () => {
            const hash = window.location.hash.substring(1);
            if(hash && document.getElementById(hash)) {
                document.querySelectorAll('.account-menu li').forEach(item => {
                    item.classList.remove('active');
                });
                document.querySelectorAll('.account-tab').forEach(tab => {
                    tab.classList.remove('active');
                });
                
                document.querySelector(`.account-menu a[data-tab="${hash}"]`).parentElement.classList.add('active');
                document.getElementById(hash).classList.add('active');
            }
        });
        
        // Dashboard card links
        document.querySelectorAll('.dashboard-card-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Get the tab ID from the href
                const tabId = this.getAttribute('href').substring(1);
                
                // Trigger click on the corresponding menu item
                document.querySelector(`.account-menu a[data-tab="${tabId}"]`).click();
            });
        });
        
        // Password validation
        document.querySelector('.password-form').addEventListener('submit', function(e) {
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if(newPassword !== confirmPassword) {
                e.preventDefault();
                alert('New passwords do not match.');
            }
        });
    </script>
</body>
</html>