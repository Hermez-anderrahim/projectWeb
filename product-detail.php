<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Detail - Anon Shop</title>
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
    
    // Check if product ID is provided
    if(!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        header('Location: products.php');
        exit;
    }
    
    // Include database and product model
    include_once 'backend/config/db_connect.php';
    include_once 'backend/models/Product.php';
    
    // Instantiate database and product object
    $database = new Database();
    $db = $database->getConnection();
    
    $product = new Product($db);
    $product->product_id = $_GET['id'];
    
    // Get product details
    if(!$product->readOne()) {
        // Product not found
        header('Location: products.php');
        exit;
    }
    ?>

    <main class="container">
        <div class="breadcrumb">
            <a href="index.php">Home</a> &gt; 
            <a href="products.php?category=<?php echo urlencode($product->category_id); ?>"><?php echo $product->category_name; ?></a> &gt; 
            <span><?php echo $product->name; ?></span>
        </div>

        <div class="product-detail">
            <div class="product-gallery">
                <div class="main-image">
                    <img src="assets/images/products/<?php echo $product->image; ?>" alt="<?php echo $product->name; ?>" id="main-product-image">
                </div>
                <div class="thumbnail-images">
                    <div class="thumbnail active" data-image="<?php echo $product->image; ?>">
                        <img src="assets/images/products/<?php echo $product->image; ?>" alt="<?php echo $product->name; ?>">
                    </div>
                    <?php
                    // These would be additional product images in a real implementation
                    // For demo purposes, we'll just use the same image
                    for($i = 0; $i < 3; $i++): 
                    ?>
                    <div class="thumbnail" data-image="<?php echo $product->image; ?>">
                        <img src="assets/images/products/<?php echo $product->image; ?>" alt="<?php echo $product->name; ?>">
                    </div>
                    <?php endfor; ?>
                </div>
            </div>

            <div class="product-info-detail">
                <h1><?php echo $product->name; ?></h1>
                
                <div class="product-meta">
                    <div class="rating">
                        <div class="stars">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star-half-alt"></i>
                        </div>
                        <span>(4.5 - 24 reviews)</span>
                    </div>
                    <div class="availability <?php echo $product->stock > 0 ? '' : 'out-of-stock'; ?>">
                        <?php echo $product->stock > 0 ? 'In Stock (' . $product->stock . ' available)' : 'Out of Stock'; ?>
                    </div>
                </div>

                <div class="product-price-detail">
                    <span class="current-price-detail">$<?php echo number_format($product->price, 2); ?></span>
                    <?php if($product->old_price): ?>
                    <span class="old-price-detail">$<?php echo number_format($product->old_price, 2); ?></span>
                    <span class="discount-percent">-<?php echo round((($product->old_price - $product->price) / $product->old_price) * 100); ?>%</span>
                    <?php endif; ?>
                </div>

                <div class="product-description">
                    <p><?php echo $product->description; ?></p>
                </div>

                <?php if($product->stock > 0): ?>
                <div class="product-actions-detail">
                    <div class="quantity-control">
                        <button class="quantity-btn minus">-</button>
                        <input type="number" id="product-quantity" class="quantity-input" value="1" min="1" max="<?php echo $product->stock; ?>">
                        <button class="quantity-btn plus">+</button>
                    </div>
                    <div class="action-buttons">
                        <button class="btn add-to-cart-detail" data-product="<?php echo $product->product_id; ?>">
                            <i class="fas fa-shopping-cart"></i> Add to Cart
                        </button>
                        <button class="btn buy-now" data-product="<?php echo $product->product_id; ?>">
                            Buy Now
                        </button>
                    </div>
                </div>
                <?php endif; ?>

                <div class="product-meta-detail">
                    <p><strong>SKU:</strong> PROD-<?php echo $product->product_id; ?></p>
                    <p><strong>Category:</strong> <a href="products.php?category=<?php echo urlencode($product->category_id); ?>"><?php echo $product->category_name; ?></a></p>
                    <p><strong>Tags:</strong> Fashion, Trending, Summer</p>
                </div>
            </div>
        </div>

        <div class="product-tabs">
            <div class="tabs-header">
                <button class="tab-btn active" data-tab="description">Description</button>
                <button class="tab-btn" data-tab="specifications">Specifications</button>
                <button class="tab-btn" data-tab="reviews">Reviews (24)</button>
            </div>
            <div class="tabs-content">
                <div class="tab-pane active" id="description">
                    <div class="product-description-full">
                        <p><?php echo $product->description; ?></p>
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed euismod, nunc vel tincidunt lacinia, nisl nisl aliquam nisl, vel aliquam nisl nisl sit amet nisl. Sed euismod, nunc vel tincidunt lacinia, nisl nisl aliquam nisl, vel aliquam nisl nisl sit amet nisl.</p>
                        <p>Sed euismod, nunc vel tincidunt lacinia, nisl nisl aliquam nisl, vel aliquam nisl nisl sit amet nisl. Sed euismod, nunc vel tincidunt lacinia, nisl nisl aliquam nisl, vel aliquam nisl nisl sit amet nisl.</p>
                    </div>
                </div>
                <div class="tab-pane" id="specifications">
                    <table class="specifications-table">
                        <tbody>
                            <tr>
                                <th>Material</th>
                                <td>Cotton, Polyester</td>
                            </tr>
                            <tr>
                                <th>Color</th>
                                <td>Multiple colors available</td>
                            </tr>
                            <tr>
                                <th>Size</th>
                                <td>S, M, L, XL</td>
                            </tr>
                            <tr>
                                <th>Weight</th>
                                <td>0.5 kg</td>
                            </tr>
                            <tr>
                                <th>Dimensions</th>
                                <td>30 x 20 x 10 cm</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="tab-pane" id="reviews">
                    <div class="reviews-summary">
                        <div class="average-rating">
                            <div class="rating-number">4.5</div>
                            <div class="stars">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star-half-alt"></i>
                            </div>
                            <div class="total-reviews">Based on 24 reviews</div>
                        </div>
                        <div class="rating-bars">
                            <div class="rating-bar">
                                <span>5 stars</span>
                                <div class="bar-container">
                                    <div class="bar" style="width: 75%;"></div>
                                </div>
                                <span>18</span>
                            </div>
                            <div class="rating-bar">
                                <span>4 stars</span>
                                <div class="bar-container">
                                    <div class="bar" style="width: 20%;"></div>
                                </div>
                                <span>5</span>
                            </div>
                            <div class="rating-bar">
                                <span>3 stars</span>
                                <div class="bar-container">
                                    <div class="bar" style="width: 5%;"></div>
                                </div>
                                <span>1</span>
                            </div>
                            <div class="rating-bar">
                                <span>2 stars</span>
                                <div class="bar-container">
                                    <div class="bar" style="width: 0%;"></div>
                                </div>
                                <span>0</span>
                            </div>
                            <div class="rating-bar">
                                <span>1 star</span>
                                <div class="bar-container">
                                    <div class="bar" style="width: 0%;"></div>
                                </div>
                                <span>0</span>
                            </div>
                        </div>
                    </div>
                    <div class="reviews-list">
                        <?php for($i = 0; $i < 3; $i++): ?>
                        <div class="review-item">
                            <div class="review-header">
                                <div class="reviewer-info">
                                    <div class="reviewer-avatar">
                                        <img src="assets/images/avatar.jpg" alt="User">
                                    </div>
                                    <div class="reviewer-meta">
                                        <div class="reviewer-name">John Doe</div>
                                        <div class="review-date">March 15, 2023</div>
                                    </div>
                                </div>
                                <div class="review-rating">
                                    <div class="stars">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="review-content">
                                <p>Great product! Very comfortable and looks exactly as pictured. Highly recommend.</p>
                            </div>
                        </div>
                        <?php endfor; ?>
                        <div class="load-more-reviews">
                            <button class="btn">Load More Reviews</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <section class="related-products">
            <h2 class="section-title">Related Products</h2>
            <div class="products-grid">
                <?php
                // Get related products (same category)
                $stmt = $db->prepare("SELECT p.product_id, p.name, p.slug, p.description, p.price, p.old_price, 
                                           p.stock, p.image, c.name AS category_name
                                    FROM products p
                                    LEFT JOIN categories c ON p.category_id = c.category_id
                                    WHERE p.category_id = :category_id AND p.product_id != :product_id
                                    ORDER BY RAND()
                                    LIMIT 4");
                $stmt->bindParam(':category_id', $product->category_id);
                $stmt->bindParam(':product_id', $product->product_id);
                $stmt->execute();
                
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)):
                ?>
                <div class="product-card">
                    <?php if($row['old_price'] && $row['old_price'] > $row['price']): ?>
                    <div class="product-badge sale">Sale</div>
                    <?php endif; ?>
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
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>
    
    <!-- Scripts -->
    <script src="assets/js/main.js"></script>
    <script>
        // Thumbnail image handling
        document.querySelectorAll('.thumbnail').forEach(thumb => {
            thumb.addEventListener('click', function() {
                // Remove active class from all thumbnails
                document.querySelectorAll('.thumbnail').forEach(t => t.classList.remove('active'));
                
                // Add active class to clicked thumbnail
                this.classList.add('active');
                
                // Update main image
                const imageSrc = this.getAttribute('data-image');
                document.getElementById('main-product-image').src = 'assets/images/products/' + imageSrc;
            });
        });
        
        // Quantity buttons
        const quantityInput = document.getElementById('product-quantity');
        
        document.querySelector('.quantity-btn.minus').addEventListener('click', function() {
            let value = parseInt(quantityInput.value);
            if (value > 1) {
                quantityInput.value = value - 1;
            }
        });
        
        document.querySelector('.quantity-btn.plus').addEventListener('click', function() {
            let value = parseInt(quantityInput.value);
            const max = parseInt(quantityInput.getAttribute('max'));
            if (value < max) {
                quantityInput.value = value + 1;
            } else {
                showNotification('Cannot add more than available stock', 'error');
            }
        });
        
        // Tab handling
        document.querySelectorAll('.tab-btn').forEach(tab => {
            tab.addEventListener('click', function() {
                // Remove active class from all tabs
                document.querySelectorAll('.tab-btn').forEach(t => t.classList.remove('active'));
                document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
                
                // Add active class to clicked tab
                this.classList.add('active');
                
                // Show corresponding content
                const tabId = this.getAttribute('data-tab');
                document.getElementById(tabId).classList.add('active');
            });
        });
        
        // Add to cart
        document.querySelector('.add-to-cart-detail').addEventListener('click', function() {
            const productId = this.getAttribute('data-product');
            const quantity = parseInt(document.getElementById('product-quantity').value);
            addToCart(productId, quantity);
        });
        
        // Buy now
        document.querySelector('.buy-now').addEventListener('click', function() {
            const productId = this.getAttribute('data-product');
            const quantity = parseInt(document.getElementById('product-quantity').value);
            
            // Add to cart first
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
                    // Redirect to checkout
                    window.location.href = 'checkout.php';
                } else {
                    showNotification(data.message, 'error');
                }
            })
            .catch(error => {
                showNotification('Error adding product to cart.', 'error');
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