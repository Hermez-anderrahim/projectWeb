<main class="container">
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-content">
            <h2 class="hero-subtitle">New Summer</h2>
            <h1 class="hero-title">Shoes Collection</h1>
            <p class="hero-text">
                Compétentement expédite alternative benefits whereas leading-edge catalysts for change. 
                Globally leverage existing an expanded array of leadership.
            </p>
            <a href="?route=shop" class="btn btn-primary btn-sm">Shop Now <i class="fas fa-arrow-right"></i></a>
        </div>
        <div class="hero-image">
            <img src="assets/images/hero-shoe.png" alt="Featured Shoe" class="featured-product">
        </div>
    </section>
    
    <!-- Categories Section -->
    <section class="categories-section">
        <div class="category-grid">
            <div class="category-card men-collection">
                <div class="category-image-container">
                    <img src="/assets/images/men-shoe.png" alt="Men's Collection">
                    <div class="category-overlay">
                        <div class="category-content">
                            <h3 class="category-name">MEN COLLECTIONS</h3>
                            <p class="category-description">Discover premium footwear designed for the modern man</p>
                            <a href="?route=shop&category=men" class="btn btn-light">Explore All <i class="fas fa-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="category-card women-collection">
                <div class="category-image-container">
                    <img src="assets/images/women-shoe.png" alt="Women's Collection">
                    <div class="category-overlay">
                        <div class="category-content">
                            <h3 class="category-name">WOMEN COLLECTIONS</h3>
                            <p class="category-description">Elegant and stylish footwear for the contemporary woman</p>
                            <a href="?route=shop&category=women" class="btn btn-light">Explore All <i class="fas fa-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="category-card sports-collection">
                <div class="category-image-container">
                    <img src="assets/images/sports-shoe.png" alt="Sports Collection">
                    <div class="category-overlay">
                        <div class="category-content">
                            <h3 class="category-name">SPORTS COLLECTIONS</h3>
                            <p class="category-description">Performance footwear designed for athletes and active lifestyles</p>
                            <a href="?route=shop&category=sports" class="btn btn-light">Explore All <i class="fas fa-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <style>
    /* Enhanced Category Styling */
    .categories-section {
        margin: 3rem 0;
    }
    
    .category-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 2rem;
    }
    
    .category-card {
        position: relative;
        overflow: hidden;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        height: 400px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        background-color: #e9ecef;
    }
    
    .category-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
    }
    
    .category-image-container {
        width: 100%;
        height: 100%;
        position: relative;
        overflow: hidden;
    }
    
    .category-image-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }
    
    .category-card:hover .category-image-container img {
        transform: scale(1.05);
    }
    
    .category-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(to top, rgba(0, 0, 0, 0.8) 0%, rgba(0, 0, 0, 0.4) 50%, rgba(0, 0, 0, 0) 100%);
        display: flex;
        align-items: flex-end;
        padding: 2rem;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .category-card:hover .category-overlay {
        opacity: 1;
    }
    
    .category-content {
        transform: translateY(20px);
        transition: transform 0.3s ease;
        color: white;
        text-align: center;
        width: 100%;
    }
    
    .category-card:hover .category-content {
        transform: translateY(0);
    }
    
    .category-name {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    
    .category-description {
        font-size: 0.9rem;
        margin-bottom: 1.5rem;
        opacity: 0.9;
    }
    
    .btn-light {
        background-color: white;
        color: #333;
        padding: 0.5rem 1.2rem;
        border-radius: 30px;
        font-weight: 600;
        font-size: 0.85rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        border: none;
        text-decoration: none;
    }
    
    .btn-light i {
        margin-left: 0.4rem;
        transition: transform 0.2s ease;
    }
    
    .btn-light:hover {
        background-color: var(--primary-color, #3f51b5);
        color: white;
    }
    
    .btn-light:hover i {
        transform: translateX(3px);
    }
    
    /* Responsive adjustments */
    @media (max-width: 992px) {
        .category-grid {
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        }
        
        .category-card {
            height: 350px;
        }
    }
    
    @media (max-width: 768px) {
        .category-card {
            height: 300px;
        }
        
        .category-overlay {
            opacity: 1;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.7) 0%, rgba(0, 0, 0, 0.3) 70%, rgba(0, 0, 0, 0) 100%);
        }
        
        .category-content {
            transform: translateY(0);
        }
        
        .category-name {
            font-size: 1.3rem;
        }
        
        .category-description {
            font-size: 0.8rem;
            margin-bottom: 1rem;
        }
    }
    
    @media (max-width: 480px) {
        .category-card {
            height: 250px;
        }
        
        .category-name {
            font-size: 1.1rem;
        }
        
        .category-overlay {
            padding: 1.5rem;
        }
    }
    </style>
    
    <!-- Featured Products -->
    <section class="products-section">
        <div class="section-header">
            <h2 class="section-title">Produits Populaires</h2>
            <div class="view-all">
                <a href="?route=shop" class="link-primary">Voir Tout <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>
        
        <div class="product-grid" id="featured-products">
            <!-- Products will be loaded here via JS -->
            <div class="loading-spinner">Chargement des produits...</div>
        </div>
    </section>
</main>

<!-- Include API scripts -->
<script src="assets/js/api.js"></script>
<script src="js/api.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Fetch featured products
    loadFeaturedProducts();
    
    async function loadFeaturedProducts() {
        try {
            // First try using the ProductAPI if available
            if (typeof ProductAPI !== 'undefined') {
                const data = await ProductAPI.getAll(1, 8);
                
                if (data.success && data.produits && data.produits.length > 0) {
                    displayProducts(data.produits);
                } else {
                    showEmptyProductsState();
                }
            } else {
                // Fallback to fetch API if ProductAPI is not defined
                fetch('/api/produit.php?page=1&limit=8')
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.produits && data.produits.length > 0) {
                            displayProducts(data.produits);
                        } else {
                            showEmptyProductsState();
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching products:', error);
                        showEmptyProductsState('Erreur lors du chargement des produits.');
                    });
            }
        } catch (error) {
            console.error('Error loading products:', error);
            showEmptyProductsState('Erreur lors du chargement des produits.');
        }
    }
    
    function showEmptyProductsState(message = 'Aucun produit disponible pour le moment.') {
        const container = document.getElementById('featured-products');
        container.innerHTML = `
            <div class="empty-products">
                <div class="empty-icon">
                    <i class="fas fa-box-open fa-4x"></i>
                </div>
                <p class="empty-text">${message}</p>
                <a href="?route=shop" class="btn btn-primary">Explorer la boutique</a>
            </div>
        `;
        
        // Add custom styles for empty state
        const style = document.createElement('style');
        style.textContent = `
            .empty-products {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                padding: var(--spacing-xl);
                background-color: var(--white-color);
                border-radius: var(--radius-md);
                box-shadow: var(--shadow-sm);
                grid-column: 1 / -1;
                text-align: center;
            }
            
            .empty-icon {
                color: var(--primary-light);
                margin-bottom: var(--spacing-md);
            }
            
            .empty-text {
                margin-bottom: var(--spacing-lg);
                font-size: 1.1rem;
                color: var(--grey-color);
            }
        `;
        document.head.appendChild(style);
    }
    
    function displayProducts(products) {
        const container = document.getElementById('featured-products');
        container.innerHTML = '';
        
        products.forEach(product => {
            const productCard = document.createElement('div');
            productCard.className = 'product-card';
            
            // Format price
            const price = parseFloat(product.prix).toFixed(2) + ' €';
            
            // Check if image exists or use placeholder
            const hasImage = product.image_url && product.image_url !== '';
            const imageContent = hasImage ? 
                `<img src="${product.image_url}" alt="${product.nom}">` : 
                `<div class="placeholder-image">
                    <i class="fas fa-shoe-prints"></i>
                </div>`;
            
            productCard.innerHTML = `
                <div class="product-image ${!hasImage ? 'no-image' : ''}">
                    ${imageContent}
                    <div class="product-actions">
                        <button class="add-to-cart" data-id="${product.id_produit}" title="Ajouter au panier">
                            <i class="fas fa-shopping-cart"></i>
                        </button>
                    </div>
                </div>
                <div class="product-info">
                    <h3 class="product-name">
                        <a href="?route=product&id=${product.id_produit}">${product.nom}</a>
                    </h3>
                    <div class="product-category">${product.categorie || ''}</div>
                    <div class="product-price">${price}</div>
                </div>
            `;
            
            container.appendChild(productCard);
            
            // Add event listener to "Add to Cart" button
            const addToCartBtn = productCard.querySelector('.add-to-cart');
            addToCartBtn.addEventListener('click', function() {
                const productId = this.getAttribute('data-id');
                addToCart(productId);
            });
        });
        
        // Add custom styles for product cards
        if (!document.getElementById('product-card-styles')) {
            const style = document.createElement('style');
            style.id = 'product-card-styles';
            style.textContent = `
                .product-grid {
                    display: grid;
                    grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
                    gap: 2rem;
                    margin: 2.5rem 0;
                }
                
                .product-card {
                    background-color: #fff;
                    border-radius: 8px;
                    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
                    overflow: hidden;
                    transition: transform 0.3s ease, box-shadow 0.3s ease;
                    display: flex;
                    flex-direction: column;
                    height: 100%;
                    margin-bottom: 0.5rem;
                }
                
                .product-card:hover {
                    transform: translateY(-5px);
                    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
                }
                
                .product-image {
                    position: relative;
                    height: 220px;
                    overflow: hidden;
                    background-color: #f8f9fa;
                }
                
                .product-image img {
                    width: 100%;
                    height: 100%;
                    object-fit: cover;
                    transition: transform 0.5s ease;
                }
                
                .product-image.no-image {
                    background-color: #e9ecef;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }
                
                .placeholder-image {
                    width: 100%;
                    height: 100%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }
                
                .placeholder-image i {
                    font-size: 3.5rem;
                    color: #adb5bd;
                    opacity: 0.5;
                }
                
                .product-card:hover .product-image img {
                    transform: scale(1.05);
                }
                
                .product-actions {
                    position: absolute;
                    bottom: 0;
                    left: 0;
                    right: 0;
                    background: linear-gradient(to top, rgba(0, 0, 0, 0.7), transparent);
                    padding: 1rem;
                    opacity: 0;
                    transform: translateY(20px);
                    transition: opacity 0.3s ease, transform 0.3s ease;
                    display: flex;
                    justify-content: center;
                }
                
                .product-card:hover .product-actions {
                    opacity: 1;
                    transform: translateY(0);
                }
                
                .add-to-cart {
                    width: 36px;
                    height: 36px;
                    border-radius: 50%;
                    background-color: #fff;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    border: none;
                    cursor: pointer;
                    transition: background-color 0.3s ease, color 0.3s ease;
                }
                
                .add-to-cart:hover {
                    background-color: var(--primary-color, #3f51b5);
                    color: white;
                }
                
                .product-info {
                    padding: 1rem;
                    flex-grow: 1;
                    display: flex;
                    flex-direction: column;
                }
                
                .product-name {
                    font-size: 1rem;
                    margin: 0 0 0.5rem 0;
                    font-weight: 600;
                    line-height: 1.3;
                }
                
                .product-name a {
                    color: #333;
                    text-decoration: none;
                    transition: color 0.2s ease;
                }
                
                .product-name a:hover {
                    color: var(--primary-color, #3f51b5);
                }
                
                .product-category {
                    color: #6c757d;
                    font-size: 0.75rem;
                    text-transform: uppercase;
                    letter-spacing: 0.5px;
                    margin-bottom: 0.5rem;
                }
                
                .product-price {
                    margin-top: auto;
                    font-weight: 700;
                    color: var(--primary-color, #3f51b5);
                    font-size: 1.1rem;
                }
                
                /* Responsive adjustments */
                @media (max-width: 768px) {
                    .product-grid {
                        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
                        gap: 1rem;
                    }
                    
                    .product-image {
                        height: 180px;
                    }
                }
                
                @media (max-width: 480px) {
                    .product-grid {
                        grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
                        gap: 0.75rem;
                    }
                    
                    .product-image {
                        height: 150px;
                    }
                    
                    .product-info {
                        padding: 0.75rem;
                    }
                    
                    .product-name {
                        font-size: 0.9rem;
                    }
                }
            `;
            document.head.appendChild(style);
        }
    }
    
    async function addToCart(productId) {
        try {
            // Check if CartAPI is defined
            if (typeof CartAPI !== 'undefined') {
                const result = await CartAPI.addItem(productId, 1);
                if (result.success) {
                    showNotification('Produit ajouté au panier', 'success');
                    updateCartCount();
                } else {
                    showNotification(result.message || 'Erreur lors de l\'ajout au panier', 'error');
                }
            } else {
                // Fallback to direct fetch API
                fetch('/api/panier.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        id_produit: productId,
                        quantite: 1
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification('Produit ajouté au panier', 'success');
                        updateCartCount();
                    } else {
                        showNotification(data.message || 'Erreur lors de l\'ajout au panier', 'error');
                    }
                })
                .catch(error => {
                    showNotification('Erreur lors de l\'ajout au panier', 'error');
                    console.error(error);
                });
            }
        } catch (error) {
            showNotification('Erreur lors de l\'ajout au panier', 'error');
            console.error(error);
        }
    }
    
    function showNotification(message, type) {
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.textContent = message;
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.classList.add('show');
        }, 10);
        
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => {
                notification.remove();
            }, 300);
        }, 3000);
    }
    
    function updateCartCount() {
        if (typeof CartAPI !== 'undefined') {
            CartAPI.getContents()
                .then(data => {
                    const cartCountElement = document.querySelector('.cart-count');
                    if (cartCountElement) {
                        cartCountElement.textContent = data.panier.nombre_articles || 0;
                    }
                })
                .catch(error => console.error('Error updating cart count:', error));
        } else {
            fetch('/api/panier.php')
                .then(response => response.json())
                .then(data => {
                    const cartCountElement = document.querySelector('.cart-count');
                    if (cartCountElement && data.panier) {
                        cartCountElement.textContent = data.panier.nombre_articles || 0;
                    }
                })
                .catch(error => console.error('Error updating cart count:', error));
        }
    }
});
</script>