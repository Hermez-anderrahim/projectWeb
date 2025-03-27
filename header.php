<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - Anon Online Shopping</title>
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
        // Redirect to login with return URL
        header('Location: login.php?redirect=cart.php');
        exit;
    }
    
    // Include database and cart model
    include_once 'backend/config/db_connect.php';
    include_once 'backend/models/Cart.php';
    
    // Instantiate database and cart object
    $database = new Database();
    $db = $database->getConnection();
    
    $cart = new Cart($db);
    $cart_id = $cart->getCart($_SESSION['user_id']);
    $stmt = $cart->getItems();
    $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $cart_total = $cart->getTotal();
    
    // Calculate shipping based on total
    $shipping = $cart_total >= 100 ? 0 : 10;
    $grand_total = $cart_total + $shipping;
    ?>

    <main class="container">
        <div class="breadcrumb">
            <a href="index.php">Home</a> &gt; 
            <span>Shopping Cart</span>
        </div>

        <h1 class="page-title">Your Shopping Cart</h1>

        <?php if(empty($cart_items)): ?>
        <div class="empty-cart">
            <i class="fas fa-shopping-cart"></i>
            <h2>Your cart is empty</h2>
            <p>Looks like you haven't added any products to your cart yet.</p>
            <a href="products.php" class="btn">Continue Shopping</a>
        </div>
        <?php else: ?>
        <div class="cart-container">
            <div class="cart-items">
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Subtotal</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($cart_items as $item): ?>
                        <tr>
                            <td class="product-col">
                                <div class="product-info">
                                    <img src="assets/images/products/<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>">
                                    <div>
                                        <h3><?php echo $item['name']; ?></h3>
                                        <p>Stock: <?php echo $item['stock']; ?> available</p>
                                    </div>
                                </div>
                            </td>
                            <td class="price-col">$<?php echo number_format($item['price'], 2); ?></td>
                            <td class="quantity-col">
                                <div class="quantity-control">
                                    <button class="quantity-btn minus" data-id="<?php echo $item['cart_item_id']; ?>">-</button>
                                    <input type="number" 
                                           class="quantity-input" 
                                           value="<?php echo $item['quantity']; ?>" 
                                           min="1" 
                                           max="<?php echo $item['stock']; ?>"
                                           data-id="<?php echo $item['cart_item_id']; ?>"
                                           data-product="<?php echo $item['product_id']; ?>">
                                    <button class="quantity-btn plus" data-id="<?php echo $item['cart_item_id']; ?>">+</button>
                                </div>
                            </td>
                            <td class="subtotal-col">$<?php echo number_format($item['subtotal'], 2); ?></td>
                            <td class="remove-col">
                                <button class="remove-item" data-id="<?php echo $item['cart_item_id']; ?>">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="cart-summary">
                <h2>Cart Summary</h2>
                <div class="summary-item">
                    <span>Subtotal</span>
                    <span>$<?php echo number_format($cart_total, 2); ?></span>
                </div>
                <div class="summary-item">
                    <span>Shipping</span>
                    <span><?php echo $shipping > 0 ? '$' . number_format($shipping, 2) : 'FREE'; ?></span>
                </div>
                <?php if($shipping > 0): ?>
                <div class="free-shipping-message">
                    <i class="fas fa-truck"></i>
                    <span>Free shipping on orders over $100</span>
                </div>
                <?php endif; ?>
                <div class="summary-item total">
                    <span>Total</span>
                    <span>$<?php echo number_format($grand_total, 2); ?></span>
                </div>
                <a href="checkout.php" class="btn checkout-btn">Proceed to Checkout</a>
                <a href="products.php" class="continue-shopping">Continue Shopping</a>
            </div>
        </div>
        <?php endif; ?>
    </main>

    <?php include 'includes/footer.php'; ?>
    
    <!-- Scripts -->
    <script src="assets/js/main.js"></script>
    <script>
        // Quantity buttons
        document.querySelectorAll('.quantity-btn.minus').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const input = document.querySelector(`.quantity-input[data-id="${id}"]`);
                let value = parseInt(input.value);
                if (value > 1) {
                    input.value = value - 1;
                    updateQuantity(id, input.value);
                }
            });
        });
        
        document.querySelectorAll('.quantity-btn.plus').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const input = document.querySelector(`.quantity-input[data-id="${id}"]`);
                let value = parseInt(input.value);
                const max = parseInt(input.getAttribute('max'));
                if (value < max) {
                    input.value = value + 1;
                    updateQuantity(id, input.value);
                } else {
                    showNotification('Cannot add more than available stock', 'error');
                }
            });
        });
        
        document.querySelectorAll('.quantity-input').forEach(input => {
            input.addEventListener('change', function() {
                const id = this.getAttribute('data-id');
                const max = parseInt(this.getAttribute('max'));
                let value = parseInt(this.value);
                
                if (value < 1) {
                    this.value = 1;
                    value = 1;
                } else if (value > max) {
                    this.value = max;
                    value = max;
                    showNotification('Cannot add more than available stock', 'error');
                }
                
                updateQuantity(id, value);
            });
        });
        
        // Remove item
        document.querySelectorAll('.remove-item').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                removeItem(id);
            });
        });
        
        function updateQuantity(cartItemId, quantity) {
            fetch('api/cart/update.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    cart_item_id: cartItemId,
                    quantity: quantity
                })
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    location.reload();
                } else {
                    showNotification(data.message, 'error');
                }
            })
            .catch(error => {
                showNotification('Error updating quantity.', 'error');
            });
        }
        
        function removeItem(cartItemId) {
            fetch('api/cart/remove.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    cart_item_id: cartItemId
                })
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    location.reload();
                } else {
                    showNotification(data.message, 'error');
                }
            })
            .catch(error => {
                showNotification('Error removing item.', 'error');
            });
        }
        
        function showNotification(message, type) {
            const notification = document.createElement('div');
            notification.className = 'notification ' + type;
            notification.textContent = message;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.classList.add('show');
            }, 100);
            
            setTimeout(() => {
                notification.classList.remove('show');
                setTimeout(() => {
                    notification.remove();
                }, 500);
            }, 3000);
        }
    </script>
</body>
</html>