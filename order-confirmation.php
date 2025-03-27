<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - Anon Shop</title>
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
        header('Location: login.php');
        exit;
    }
    
    // Check if order ID is provided
    if(!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        header('Location: account.php');
        exit;
    }
    
    // Include database and order model
    include_once 'backend/config/db_connect.php';
    include_once 'backend/models/Order.php';
    
    // Instantiate database and order object
    $database = new Database();
    $db = $database->getConnection();
    
    $order = new Order($db);
    
    // Get order details
    $stmt = $order->getOrderDetails($_GET['id']);
    $order_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Check if order exists and belongs to the logged-in user
    if(empty($order_items) || $order_items[0]['user_id'] != $_SESSION['user_id']) {
        header('Location: account.php');
        exit;
    }
    
    // Get order information from the first row
    $order_info = $order_items[0];
    ?>

    <main class="container">
        <div class="order-confirmation">
            <div class="confirmation-header">
                <div class="success-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h1>Thank You for Your Order!</h1>
                <p>Your order has been placed successfully.</p>
            </div>
            
            <div class="order-info">
                <div class="order-info-box">
                    <h3>Order Information</h3>
                    <ul>
                        <li><strong>Order Number:</strong> #<?php echo $_GET['id']; ?></li>
                        <li><strong>Order Date:</strong> <?php echo date('F j, Y', strtotime($order_info['created_at'])); ?></li>
                        <li><strong>Order Status:</strong> <?php echo ucfirst($order_info['status']); ?></li>
                        <li><strong>Payment Method:</strong> <?php echo ucwords(str_replace('_', ' ', $order_info['payment_method'])); ?></li>
                    </ul>
                </div>
                
                <div class="order-info-box">
                    <h3>Shipping Address</h3>
                    <p><?php echo $order_info['shipping_address']; ?></p>
                </div>
            </div>
            
            <div class="order-summary">
                <h3>Order Summary</h3>
                <table class="confirmation-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($order_items as $item): ?>
                        <tr>
                            <td><?php echo $item['product_name']; ?></td>
                            <td><?php echo $item['quantity']; ?></td>
                            <td>$<?php echo number_format($item['price'], 2); ?></td>
                            <td>$<?php echo number_format($item['item_total'], 2); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3">Subtotal</td>
                            <td>$<?php echo number_format($order_info['order_total'], 2); ?></td>
                        </tr>
                        <tr>
                            <td colspan="3">Shipping</td>
                            <td>$0.00</td>
                        </tr>
                        <tr class="order-total">
                            <td colspan="3">Total</td>
                            <td>$<?php echo number_format($order_info['order_total'], 2); ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            
            <div class="confirmation-actions">
                <a href="order-detail.php?id=<?php echo $_GET['id']; ?>" class="btn">View Order Details</a>
                <a href="products.php" class="btn btn-outline">Continue Shopping</a>
            </div>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>
    
    <!-- Scripts -->
    <script src="assets/js/main.js"></script>
</body>
</html>