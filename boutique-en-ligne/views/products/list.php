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
            <a href="?route=shop" class="btn btn-primary">Shop Now <i class="fas fa-arrow-right"></i></a>
        </div>
        <div class="hero-image">
            <img src="assets/images/hero-shoe.png" alt="Featured Shoe" class="featured-product">
        </div>
    </section>
    
    <!-- Categories Section -->
    <section class="categories-section">
        <div class="category-grid">
            <div class="category-card men-collection">
                <h3 class="category-title">MEN COLLECTIONS</h3>
                <div class="category-image">
                    <img src="assets/images/men-shoe.png" alt="Men's Collection">
                </div>
                <a href="?route=shop&category=men" class="btn btn-outline">Explore All <i class="fas fa-arrow-right"></i></a>
            </div>
            
            <div class="category-card women-collection">
                <h3 class="category-title">WOMEN COLLECTIONS</h3>
                <div class="category-image">
                    <img src="assets/images/women-shoe.png" alt="Women's Collection">
                </div>
                <a href="?route=shop&category=women" class="btn btn-outline">Explore All <i class="fas fa-arrow-right"></i></a>
            </div>
            
            <div class="category-card sports-collection">
                <h3 class="category-title">SPORTS COLLECTIONS</h3>
                <div class="category-image">
                    <img src="assets/images/sports-shoe.png" alt="Sports Collection">
                </div>
                <a href="?route=shop&category=sports" class="btn btn-outline">Explore All <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>
    </section>
    
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Fetch featured products
    loadFeaturedProducts();
    
    async function loadFeaturedProducts() {
        try {
            const data = await ProductAPI.getAll(1, 8);
            
            if (data.success && data.produits && data.produits.length > 0) {
                displayProducts(data.produits);
            } else {
                document.getElementById('featured-products').innerHTML = '<p class="no-products">Aucun produit disponible pour le moment.</p>';
            }
        } catch (error) {
            console.error('Error loading products:', error);
            document.getElementById('featured-products').innerHTML = '<p class="error-message">Erreur lors du chargement des produits.</p>';
        }
    }
    
    function displayProducts(products) {
        const container = document.getElementById('featured-products');
        container.innerHTML = '';
        
        products.forEach(product => {
            const productCard = document.createElement('div');
            productCard.className = 'product-card';
            
            // Format price
            const price = parseFloat(product.prix).toFixed(2) + ' €';
            
            productCard.innerHTML = `
                <div class="product-image">
                    <img src="${product.image_url || 'assets/images/placeholder.jpg'}" alt="${product.nom}">
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
    }
    
    async function addToCart(productId) {
        try {
            const result = await CartAPI.addItem(productId, 1);
            if (result.success) {
                showNotification('Produit ajouté au panier', 'success');
                updateCartCount();
            } else {
                showNotification(result.message || 'Erreur lors de l\'ajout au panier', 'error');
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
        CartAPI.getContents()
            .then(data => {
                const cartCountElement = document.querySelector('.cart-count');
                if (cartCountElement) {
                    cartCountElement.textContent = data.panier.nombre_articles || 0;
                }
            })
            .catch(error => console.error('Error updating cart count:', error));
    }
});
</script>