<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Anon - Online Shopping</title>
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
    
    // Include database and product model
    include_once 'backend/config/db_connect.php';
    include_once 'backend/models/Product.php';
    include_once 'backend/models/Category.php';
    
    // Instantiate database and product object
    $database = new Database();
    $db = $database->getConnection();
    
    $product = new Product($db);
    $category = new Category($db);
    
    // Get trending products
    $stmt = $product->getTrending();
    $trending_products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get main categories
    $stmt = $category->readMain();
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <main class="container">
        <!-- Banner Section -->
        <section class="banner">
            <div class="banner-content">
                <h2>Trending Accessories</h2>
                <h1>MODERN SUNGLASSES</h1>
                <p>starting at $ 15.00</p>
                <a href="products.php?category=sunglasses" class="btn">SHOP NOW</a>
            </div>
            <div class="banner-image">
                <img src="assets/images/banner.jpg" alt="Modern Sunglasses">
            </div>
        </section>

        <!-- Categories Section -->
        <section class="categories-section">
            <h2 class="section-title">Shop by Category</h2>
            <div class="categories-grid">
                <?php foreach($categories as $cat): ?>
                <div class="category-card">
                    <a href="products.php?category=<?php echo $cat['slug']; ?>">
                        <div class="category-icon">
                            <i class="<?php echo $cat['icon']; ?>"></i>
                        </div>
                        <h3><?php echo $cat['name']; ?></h3>
                        <p>Shop Now <i class="fas fa-arrow-right"></i></p>
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- New Arrivals Section -->
        <section class="products-section">
            <h2 class="section-title">New Arrivals</h2>
            <div class="products-grid">
                <?php 
                // Get latest products (first 8)
                $stmt = $product->read(null, 8, 0);
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)):
                ?>
                <div class="product-card">
                    <div class="product-badge">New</div>
                    <a href="product-detail.php?id=<?php echo $row['product_id']; ?>">
                        <div class="product-image">
                            <img src="assets/images/products/<?php echo $row['image']; ?>" alt="<?php echo $row['name']; ?>">
                        </div>
                        <div class="product-info">
                            <span class="product-category"><?php echo $row['category_name']; ?></span>
                            <h3 class="product-name"><?php echo $row['name']; ?></h3>
                            <div class="product-price">
                                <span class="current-price">$<?php echo number_format($row['price'], 2); ?></span>
                                <?php if($row['old_price']): ?>
                                <span class="old-price">$<?php echo number_format($row['old_price'], 2); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </a>
                    <div class="product-actions">
                        <button class="add-to-cart" data-product="<?php echo $row['product_id']; ?>">
                            <i class="fas fa-shopping-cart"></i> Add to Cart
                        </button>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
            <div class="view-more">
                <a href="products.php" class="btn">View All Products</a>
            </div>
        </section>

        <!-- Trending Section -->
        <section class="trending-section">
            <h2 class="section-title">Trending Items</h2>
            <div class="products-grid">
                <?php foreach($trending_products as $product): ?>
                <div class="product-card">
                    <div class="product-badge hot">Hot</div>
                    <a href="product-detail.php?id=<?php echo $product['product_id']; ?>">
                        <div class="product-image">
                            <img src="assets/images/products/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">
                        </div>
                        <div class="product-info">
                            <span class="product-category"><?php echo $product['category_name']; ?></span>
                            <h3 class="product-name"><?php echo $product['name']; ?></h3>
                            <div class="product-price">
                                <span class="current-price">$<?php echo number_format($product['price'], 2); ?></span>
                                <?php if($product['old_price']): ?>
                                <span class="old-price">$<?php echo number_format($product['old_price'], 2); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </a>
                    <div class="product-actions">
                        <button class="add-to-cart" data-product="<?php echo $product['product_id']; ?>">
                            <i class="fas fa-shopping-cart"></i> Add to Cart
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Services Section -->
        <section class="services-section">
            <div class="services-grid">
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-truck"></i>
                    </div>
                    <div class="service-content">
                        <h3>Worldwide Delivery</h3>
                        <p>Free shipping on all orders over $100</p>
                    </div>
                </div>
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="service-content">
                        <h3>Next Day Delivery</h3>
                        <p>Order before 2pm</p>
                    </div>
                </div>
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-credit-card"></i>
                    </div>
                    <div class="service-content">
                        <h3>Secure Checkout</h3>
                        <p>Multiple payment methods</p>
                    </div>
                </div>
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-undo"></i>
                    </div>
                    <div class="service-content">
                        <h3>Return Policy</h3>
                        <p>Free & easy returns</p>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>
    
    <!-- Scripts -->
    <script src="assets/js/main.js"></script>
    <script>
        // Add to cart functionality
        document.querySelectorAll('.add-to-cart').forEach(button => {
            button.addEventListener('click', function() {
                const productId = this.getAttribute('data-product');
                addToCart(productId, 1);
            });
        });
        
        function addToCart(productId, quantity) {
            fetch('api/cart/add.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    product_id: productId,
                    quantity: quantity
                })
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    showNotification('Product added to cart!', 'success');
                    updateCartCount();
                } else {
                    showNotification(data.message, 'error');
                }
            })
            .catch(error => {
                showNotification('Error adding product to cart.', 'error');
            });
        }
        
        function updateCartCount() {
            fetch('api/cart/count.php')
            .then(response => response.json())
            .then(data => {
                const cartCountElement = document.querySelector('.cart-count');
                if(cartCountElement) {
                    cartCountElement.textContent = data.count;
                }
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