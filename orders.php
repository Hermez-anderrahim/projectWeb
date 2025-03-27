<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - Anon Shop</title>
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
        header('Location: login.php?redirect=orders.php');
        exit;
    }
    
    // Include database and necessary models
    include_once 'backend/config/db_connect.php';
    include_once 'backend/models/Order.php';
    
    // Instantiate database and order object
    $database = new Database();
    $db = $database->getConnection();
    
    $order = new Order($db);
    
    // Get user orders
    $stmt = $order->getUserOrders($_SESSION['user_id']);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <main class="container">
        <div class="breadcrumb">
            <a href="index.php">Home</a> &gt; 
            <a href="account.php">My Account</a> &gt; 
            <span>My Orders</span>
        </div>

        <h1 class="page-title">My Orders</h1>

        <?php if(empty($orders)): ?>
        <div class="empty-data">
            <i class="fas fa-shopping-bag"></i>
            <h2>No orders found</h2>
            <p>You haven't placed any orders yet.</p>
            <a href="products.php" class="btn">Start Shopping</a>
        </div>
        <?php else: ?>
        <div class="orders-container">
            <div class="orders-table-wrapper">
                <table class="orders-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
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
        </div>
        <?php endif; ?>
    </main>

    <?php include 'includes/footer.php'; ?>
    
    <!-- Scripts -->
    <script src="assets/js/main.js"></script>
</body>
</html>