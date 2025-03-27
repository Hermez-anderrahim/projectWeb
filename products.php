<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - Anon Online Shopping</title>
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
    
    // Instantiate database and objects
    $database = new Database();
    $db = $database->getConnection();
    
    $product = new Product($db);
    $category = new Category($db);
    
    // Get category if specified
    $category_id = null;
    $category_name = "All Products";
    if(isset($_GET['category'])) {
        $stmt = $db->prepare("SELECT category_id, name FROM categories WHERE slug = :slug");
        $stmt->bindParam(':slug', $_GET['category']);
        $stmt->execute();
        
        if($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $category_id = $row['category_id'];
            $category_name = $row['name'];
        }
    }
    
    // Get min and max price for filtering
    $stmt = $db->prepare("SELECT MIN(price) as min_price, MAX(price) as max_price FROM products");
    $stmt->execute();
    $price_range = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Pagination
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = 12;
    $offset = ($page - 1) * $limit;
    
    // Count total products
    $countQuery = "SELECT COUNT(*) as total FROM products";
    if($category_id) {
        $countQuery .= " WHERE category_id = :category_id";
    }
    $stmt = $db->prepare($countQuery);
    if($category_id) {
        $stmt->bindParam(':category_id', $category_id);
    }
    $stmt->execute();
    $total_products = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    $total_pages = ceil($total_products / $limit);
    
    // Get products
    $stmt = $product->read($category_id, $limit, $offset);
    ?>

    <main class="container">
        <div class="breadcrumb">
            <a href="index.php">Home</a> &gt; 
            <span><?php echo $category_name; ?></span>
        </div>

        <div class="products-container">
            <aside class="sidebar">
                <div class="sidebar-section">
                    <h3>Categories</h3>
                    <ul class="category-list">
                        <li><a href="products.php" class="<?php echo !isset($_GET['category']) ? 'active' : ''; ?>">All Categories</a></li>
                        <?php
                        $stmt = $category->readMain();
                        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            $active = (isset($_GET['category']) && $_GET['category'] == $row['slug']) ? 'active' : '';
                            echo '<li>
                                <a href="products.php?category=' . $row['slug'] . '" class="' . $active . '">
                                    <i class="' . $row['icon'] . '"></i> ' . $row['name'] . '
                                </a>';
                            
                            // Check for subcategories
                            $subCats = $category->readSub($row['category_id']);
                            if($subCats->rowCount() > 0) {
                                echo '<ul class="subcategory-list">';
                                while($subCat = $subCats->fetch(PDO::FETCH_ASSOC)) {
                                    $subActive = (isset($_GET['category']) && $_GET['category'] == $subCat['slug']) ? 'active' : '';
                                    echo '<li><a href="products.php?category=' . $subCat['slug'] . '" class="' . $subActive . '">' . $subCat['name'] . '</a></li>';
                                }
                                echo '</ul>';
                            }
                            
                            echo '</li>';
                        }
                        ?>
                    </ul>
                </div>

                <div class="sidebar-section">
                    <h3>Price Range</h3>
                    <div class="price-filter">
                        <form action="products.php" method="GET">
                            <?php if(isset($_GET['category'])): ?>
                            <input type="hidden" name="category" value="<?php echo $_GET['category']; ?>">
                            <?php endif; ?>
                            
                            <div class="price-inputs">
                                <div class="price-input">
                                    <label for="min_price">Min</label>
                                    <input type="number" id="min_price" name="min_price" value="<?php echo isset($_GET['min_price']) ? $_GET['min_price'] : floor($price_range['min_price']); ?>" min="0">
                                </div>
                                <span>-</span>
                                <div class="price-input">
                                    <label for="max_price">Max</label>
                                    <input type="number" id="max_price" name="max_price" value="<?php echo isset($_GET['max_price']) ? $_GET['max_price'] : ceil($price_range['max_price']); ?>" min="0">
                                </div>
                            </div>
                            
                            <button type="submit" class="btn">Apply</button>
                        </form>
                    </div>
                </div>

                <div class="sidebar-section">
                    <h3>Rating</h3>
                    <div class="rating-filter">
                        <label class="rating-checkbox">
                            <input type="checkbox" name="rating" value="5">
                            <span class="stars">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </span>
                            <span>(5)</span>
                        </label>
                        <label class="rating-checkbox">
                            <input type="checkbox" name="rating" value="4">
                            <span class="stars">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="far fa-star"></i>
                            </span>
                            <span>& Up (4+)</span>
                        </label>
                        <label class="rating-checkbox">
                            <input type="checkbox" name="rating" value="3">
                            <span class="stars">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="far fa-star"></i>
                                <i class="far fa-star"></i>
                            </span>
                            <span>& Up (3+)</span>
                        </label>
                        <label class="rating-checkbox">
                            <input type="checkbox" name="rating" value="2">
                            <span class="stars">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="far fa-star"></i>
                                <i class="far fa-star"></i>
                                <i class="far fa-star"></i>
                            </span>
                            <span>& Up (2+)</span>
                        </label>
                        <label class="rating-checkbox">
                            <input type="checkbox" name="rating" value="1">
                            <span class="stars">
                                <i class="fas fa-star"></i>
                                <i class="far fa-star"></i>
                                <i class="far fa-star"></i>
                                <i class="far fa-star"></i>
                                <i class="far fa-star"></i>
                            </span>
                            <span>& Up (1+)</span>
                        </label>
                    </div>
                </div>
            </aside>

            <div class="products-content">
                <div class="products-header">
                    <h1><?php echo $category_name; ?></h1>
                    <div class="products-filter">
                        <span>Showing <?php echo min(($page - 1) * $limit + 1, $total_products); ?>-<?php echo min($page * $limit, $total_products); ?> of <?php echo $total_products; ?> results</span>
                        <select id="sort-by" onchange="sortProducts(this.value)">
                            <option value="newest">Sort by: Newest</option>
                            <option value="price-low">Price: Low to High</option>
                            <option value="price-high">Price: High to Low</option>
                            <option value="popularity">Popularity</option>
                        </select>
                    </div>
                </div>

                <div class="products-grid">
                    <?php
                    // Get products
                    while($row = $stmt->fetch(PDO::FETCH_ASSOC)):
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
                            <button class="add-to-cart" data-product="<?php echo $row['product_id']; ?>">
                                <i class="fas fa-shopping-cart"></i> Add to Cart
                            </button>
                            <button class="add-to-wishlist" data-product="<?php echo $row['product_id']; ?>">
                                <i class="far fa-heart"></i>
                            </button>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>

                <?php if($total_pages > 1): ?>
                <div class="pagination">
                    <?php if($page > 1): ?>
                    <a href="<?php echo $_SERVER['PHP_SELF']; ?>?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>" class="prev">
                        <i class="fas fa-chevron-left"></i> Previous
                    </a>
                    <?php endif; ?>
                    
                    <?php for($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                    <a href="<?php echo $_SERVER['PHP_SELF']; ?>?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>" class="<?php echo $i == $page ? 'active' : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                    <?php endfor; ?>
                    
                    <?php if($page < $total_pages): ?>
                    <a href="<?php echo $_SERVER['PHP_SELF']; ?>?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>" class="next">
                        Next <i class="fas fa-chevron-right"></i>
                    </a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>
    
    <!-- Scripts -->
    <script src="assets/js/main.js"></script>
    <script>
        // Sort products
        function sortProducts(sortBy) {
            const params = new URLSearchParams(window.location.search);
            params.set('sort', sortBy);
            window.location.href = window.location.pathname + '?' + params.toString();
        }
        
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