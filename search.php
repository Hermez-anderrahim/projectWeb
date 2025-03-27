<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results - Anon Shop</title>
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
    
    // Check if search query is provided
    if(!isset($_GET['q']) || empty($_GET['q'])) {
        header('Location: index.php');
        exit;
    }
    
    $search_query = $_GET['q'];
    $category_id = isset($_GET['category']) ? $_GET['category'] : null;
    $min_price = isset($_GET['min_price']) ? $_GET['min_price'] : null;
    $max_price = isset($_GET['max_price']) ? $_GET['max_price'] : null;
    
    // Include database and product model
    include_once 'backend/config/db_connect.php';
    include_once 'backend/models/Product.php';
    include_once 'backend/models/Category.php';
    
    // Instantiate database and objects
    $database = new Database();
    $db = $database->getConnection();
    
    $product = new Product($db);
    $category = new Category($db);
    
    // Get search results
    $stmt = $product->search($search_query, $category_id, $min_price, $max_price);
    $search_results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $total_results = count($search_results);
    
    // Get min and max price for filtering
    $stmt = $db->prepare("SELECT MIN(price) as min_price, MAX(price) as max_price FROM products");
    $stmt->execute();
    $price_range = $stmt->fetch(PDO::FETCH_ASSOC);
    ?>

    <main class="container">
        <div class="breadcrumb">
            <a href="index.php">Home</a> &gt; 
            <span>Search Results for "<?php echo htmlspecialchars($search_query); ?>"</span>
        </div>

        <div class="search-container">
            <div class="search-header">
                <h1>Search Results for "<?php echo htmlspecialchars($search_query); ?>"</h1>
                <p><?php echo $total_results; ?> products found</p>
            </div>

            <div class="search-filters">
                <form action="search.php" method="GET" class="search-filter-form">
                    <input type="hidden" name="q" value="<?php echo htmlspecialchars($search_query); ?>">
                    
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
                        <label for="min_price">Min Price</label>
                        <input type="number" name="min_price" id="min_price" min="0" value="<?php echo $min_price ?? floor($price_range['min_price']); ?>">
                    </div>
                    
                    <div class="filter-group">
                        <label for="max_price">Max Price</label>
                        <input type="number" name="max_price" id="max_price" min="0" value="<?php echo $max_price ?? ceil($price_range['max_price']); ?>">
                    </div>
                    
                    <button type="submit" class="btn">Apply Filters</button>
                </form>
            </div>

            <?php if(empty($search_results)): ?>
            <div class="no-results">
                <i class="fas fa-search"></i>
                <h2>No results found</h2>
                <p>We couldn't find any products matching your search criteria.</p>
                <p>Try different keywords or browse our categories.</p>
                <a href="products.php" class="btn">Browse All Products</a>
            </div>
            <?php else: ?>
            <div class="products-grid">
                <?php foreach($search_results as $product): ?>
                <div class="product-card">
                    <?php if($product['old_price'] && $product['old_price'] > $product['price']): ?>
                    <div class="product-badge sale">Sale</div>
                    <?php endif; ?>
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
                            <div class="product-rating">
                                <div class="stars">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star-half-alt"></i>
                                </div>
                                <span>(4.5)</span>
                            </div>
                        </div>
                    </a>
                    <div class="product-actions">
                        <button class="add-to-cart" data-product="<?php echo $product['product_id']; ?>">
                            <i class="fas fa-shopping-cart"></i> Add to Cart
                        </button>
                        <button class="add-to-wishlist" data-product="<?php echo $product['product_id']; ?>">
                            <i class="far fa-heart"></i>
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
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