<?php
// Start session if not already started
if(!isset($_SESSION)) {
    session_start();
}

// Check if user is logged in and is admin
if(!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header('Location: ../login.php');
    exit;
}

// Include database
include_once '../backend/config/db_connect.php';

// Instantiate database
$database = new Database();
$db = $database->getConnection();

// Get stats
$stats = [];

// Total products
$stmt = $db->prepare("SELECT COUNT(*) as count FROM products");
$stmt->execute();
$stats['products'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

// Total orders
$stmt = $db->prepare("SELECT COUNT(*) as count FROM orders");
$stmt->execute();
$stats['orders'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

// Total customers
$stmt = $db->prepare("SELECT COUNT(*) as count FROM users WHERE is_admin = 0");
$stmt->execute();
$stats['customers'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

// Total revenue
$stmt = $db->prepare("SELECT SUM(total) as total FROM orders WHERE status != 'cancelled'");
$stmt->execute();
$stats['revenue'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?: 0;

// Recent orders
$stmt = $db->prepare("SELECT o.order_id, o.created_at, o.status, o.total,
                             u.username, u.email
                      FROM orders o
                      JOIN users u ON o.user_id = u.user_id
                      ORDER BY o.created_at DESC
                      LIMIT 5");
$stmt->execute();
$recent_orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Low stock products
$stmt = $db->prepare("SELECT product_id, name, stock
                      FROM products
                      WHERE stock < 10
                      ORDER BY stock ASC
                      LIMIT 5");
$stmt->execute();
$low_stock = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Anon Shop</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="admin-body">
    <div class="admin-container">
        <?php include 'includes/sidebar.php'; ?>

        <div class="admin-content">
            <div class="admin-header">
                <h1 class="admin-title">Dashboard</h1>
                <div class="admin-user">
                    <span><?php echo $_SESSION['username']; ?></span>
                    <a href="../logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i></a>
                </div>
            </div>

            <div class="admin-stats">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-shopping-bag"></i></div>
                    <div class="stat-value"><?php echo $stats['orders']; ?></div>
                    <div class="stat-label">Total Orders</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-dollar-sign"></i></div>
                    <div class="stat-value">$<?php echo number_format($stats['revenue'], 2); ?></div>
                    <div class="stat-label">Total Revenue</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-box"></i></div>
                    <div class="stat-value"><?php echo $stats['products']; ?></div>
                    <div class="stat-label">Total Products</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-users"></i></div>
                    <div class="stat-value"><?php echo $stats['customers']; ?></div>
                    <div class="stat-label">Total Customers</div>
                </div>
            </div>

            <div class="admin-widgets">
                <div class="admin-widget">
                    <div class="widget-header">
                        <h2>Recent Orders</h2>
                        <a href="orders/index.php" class="widget-link">View All</a>
                    </div>
                    <div class="widget-content">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Total</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($recent_orders as $order): ?>
                                <tr>
                                    <td>#<?php echo $order['order_id']; ?></td>
                                    <td><?php echo $order['username']; ?></td>
                                    <td><?php echo date('M j, Y', strtotime($order['created_at'])); ?></td>
                                    <td>
                                        <span class="status-badge <?php echo strtolower($order['status']); ?>">
                                            <?php echo ucfirst($order['status']); ?>
                                        </span>
                                    </td>
                                    <td>$<?php echo number_format($order['total'], 2); ?></td>
                                    <td>
                                        <div class="table-actions">
                                            <a href="orders/view.php?id=<?php echo $order['order_id']; ?>" class="btn-sm btn-view">View</a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if(empty($recent_orders)): ?>
                                <tr>
                                    <td colspan="6" class="empty-table">No orders found.</td>
                                </tr>
                                <?php endif; ?>
                            </tbody></table>
                    </div>
                </div>
                
                <div class="admin-widget">
                    <div class="widget-header">
                        <h2>Low Stock Products</h2>
                        <a href="products/index.php" class="widget-link">View All Products</a>
                    </div>
                    <div class="widget-content">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Product ID</th>
                                    <th>Name</th>
                                    <th>Stock</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($low_stock as $product): ?>
                                <tr>
                                    <td>#<?php echo $product['product_id']; ?></td>
                                    <td><?php echo $product['name']; ?></td>
                                    <td>
                                        <span class="stock-badge <?php echo $product['stock'] <= 5 ? 'critical' : 'low'; ?>">
                                            <?php echo $product['stock']; ?> items
                                        </span>
                                    </td>
                                    <td>
                                        <div class="table-actions">
                                            <a href="products/edit.php?id=<?php echo $product['product_id']; ?>" class="btn-sm btn-edit">Edit</a>
                                            <a href="../product-detail.php?id=<?php echo $product['product_id']; ?>" class="btn-sm btn-view">View</a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if(empty($low_stock)): ?>
                                <tr>
                                    <td colspan="4" class="empty-table">No low stock products found.</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="admin-widgets">
                <div class="admin-widget full-width">
                    <div class="widget-header">
                        <h2>Sales Overview</h2>
                        <div class="widget-filters">
                            <select id="sales-period">
                                <option value="week">Last Week</option>
                                <option value="month" selected>Last Month</option>
                                <option value="year">Last Year</option>
                            </select>
                        </div>
                    </div>
                    <div class="widget-content">
                        <div class="sales-chart">
                            <!-- Sales chart would be rendered here using a library like Chart.js -->
                            <div class="chart-placeholder">
                                <i class="fas fa-chart-line"></i>
                                <p>Sales chart will be displayed here</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/admin.js"></script>
</body>
</html>