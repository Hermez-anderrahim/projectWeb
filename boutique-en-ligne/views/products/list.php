<main class="container">
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-content">
            <span class="hero-tag">Best Sellers 2025</span>
            <h1 class="hero-title">Step into <span class="accent-text">Comfort</span> & Style</h1>
            <p class="hero-text">
                Discover our premium collection of footwear designed for the modern lifestyle. Quality materials, superior craftsmanship, and unmatched comfort.
            </p>
            <a href="?route=shop" class="hero-btn">Shop Collection <i class="fas fa-arrow-right"></i></a>
        </div>
        <div class="hero-image-wrapper">
            <div class="hero-image">
                <img src="assets/images/hero-shoe.png" alt="Featured Shoe" class="featured-product">
            </div>
            <div class="hero-blob"></div>
        </div>
    </section>

    <!-- Add custom hero section styles -->
    <style>
    .hero-section {
        display: flex;
        align-items: center;
        padding: 3rem 0;
        position: relative;
        overflow: hidden;
        min-height: 80vh; /* Changed from 500px to 80% of viewport height */
        margin-bottom: var(--spacing-xxl);
    }
    
    .hero-content {
        flex: 1;
        max-width: 550px;
        z-index: 2;
        padding: 4rem 0; /* Added vertical padding for more height */
    }
    
    .hero-tag {
        display: inline-block;
        background: rgba(255, 107, 107, 0.1);
        color: var(--primary-color);
        padding: 8px 16px;
        border-radius: 30px;
        font-weight: 600;
        font-size: 0.9rem;
        margin-bottom: 2rem; /* Increased from 1.5rem */
    }
    
    .hero-title {
        font-size: 4rem; /* Increased from 3.5rem */
        line-height: 1.1;
        margin-bottom: 2rem; /* Increased from 1.5rem */
        font-weight: 800;
        color: var(--dark-color);
    }
    
    .accent-text {
        color: var(--primary-color);
    }
    
    .hero-text {
        font-size: 1.1rem;
        color: var(--grey-color);
        margin-bottom: 3rem; /* Increased from 2.5rem */
        line-height: 1.6;
    }
    
    .hero-btn {
        display: inline-flex;
        align-items: center;
        background-color: var(--primary-color);
        color: white;
        padding: 15px 32px;
        border-radius: 50px;
        font-weight: 600;
        font-size: 1.1rem;
        text-decoration: none;
        transition: all 0.3s ease;
        box-shadow: 0 10px 20px rgba(255, 107, 107, 0.3);
    }
    
    .hero-btn i {
        margin-left: 10px;
        transition: transform 0.3s ease;
    }
    
    .hero-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 25px rgba(255, 107, 107, 0.4);
    }
    
    .hero-btn:hover i {
        transform: translateX(5px);
    }
    
    .hero-image-wrapper {
        position: relative;
        flex: 1;
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 1;
        min-height: 600px; /* Added minimum height */
    }
    
    .hero-image {
        position: relative;
        z-index: 2;
        width: 100%;
        display: flex;
        justify-content: center;
    }
    
    .featured-product {
        max-width: 100%;
        height: auto;
        transform: rotate(-15deg) scale(1.4);
        transition: transform 0.5s ease;
        filter: drop-shadow(0 20px 30px rgba(0, 0, 0, 0.2));
    }
    
    .hero-image:hover .featured-product {
        transform: rotate(-10deg) scale(1.45);
    }
    
    .hero-blob {
        position: absolute;
        width: 600px; /* Increased from 500px */
        height: 600px; /* Increased from 500px */
        background: linear-gradient(135deg, var(--primary-light) 0%, var(--primary-color) 100%);
        border-radius: 50%;
        top: 50%;
        right: -150px;
        transform: translateY(-50%);
        opacity: 0.8;
        z-index: 0;
        filter: blur(50px);
    }
    
    @media (max-width: 992px) {
        .hero-section {
            flex-direction: column;
            text-align: center;
            padding: 2rem 0;
            min-height: 70vh; /* Adjusted for mobile */
        }
        
        .hero-content {
            max-width: 100%;
            margin-bottom: 2rem;
        }
        
        .hero-title {
            font-size: 2.8rem;
        }
        
        .hero-blob {
            width: 400px;
            height: 400px;
            right: 50%;
            transform: translate(50%, -50%);
        }
    }
    
    @media (max-width: 768px) {
        .hero-section {
            min-height: 60vh; /* Further adjusted for smaller screens */
        }
        
        .hero-title {
            font-size: 2.3rem;
        }
        
        .hero-text {
            font-size: 1rem;
        }
        
        .featured-product {
            transform: rotate(-15deg) scale(1.2);
        }
        
        .hero-image:hover .featured-product {
            transform: rotate(-10deg) scale(1.25);
        }
        
        .hero-blob {
            width: 300px;
            height: 300px;
        }
    }
    
    @media (max-width: 576px) {
        .hero-title {
            font-size: 2rem;
        }
        
        .hero-btn {
            width: 100%;
            justify-content: center;
        }
    }
    </style>
    
    <!-- Categories Section -->
    <section class="collections-section">
        <div class="section-header">
            <h2 class="section-title">Our Collections</h2>
            <p class="section-subtitle">Explore our curated collections of premium footwear</p>
        </div>
        
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
    /* Enhanced Collection Section Styling */
    .collections-section {
        margin: var(--spacing-xxl) 0;
        padding: var(--spacing-xl) 0;
        position: relative;
    }
    
    .section-header {
        text-align: center;
        margin-bottom: var(--spacing-xl);
    }
    
    .section-title {
        font-size: 2.5rem;
        margin-bottom: var(--spacing-sm);
        color: var(--dark-color);
        font-weight: 800;
    }
    
    .section-subtitle {
        color: var(--grey-color);
        font-size: 1.1rem;
        max-width: 600px;
        margin: 0 auto var(--spacing-lg);
    }
    
    .category-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 2.5rem;
        margin-top: var(--spacing-xl);
    }
    
    .category-card {
        position: relative;
        overflow: hidden;
        border-radius: 16px;
        box-shadow: 0 15px 45px rgba(0, 0, 0, 0.1);
        height: 500px; /* Increased from 400px */
        transition: transform 0.4s ease, box-shadow 0.4s ease;
        background-color: #e9ecef;
    }
    
    .category-card:hover {
        transform: translateY(-15px);
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
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
        transition: transform 0.7s ease;
    }
    
    .category-card:hover .category-image-container img {
        transform: scale(1.08);
    }
    
    .category-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(to top, rgba(0, 0, 0, 0.9) 0%, rgba(0, 0, 0, 0.4) 50%, rgba(0, 0, 0, 0.1) 100%);
        display: flex;
        align-items: flex-end;
        padding: 3rem;
        opacity: 0;
        transition: opacity 0.4s ease;
    }
    
    .category-card:hover .category-overlay {
        opacity: 1;
    }
    
    .category-content {
        transform: translateY(30px);
        transition: transform 0.4s ease;
        color: white;
        text-align: center;
        width: 100%;
    }
    
    .category-card:hover .category-content {
        transform: translateY(0);
    }
    
    .category-name {
        font-size: 1.75rem;
        font-weight: 700;
        margin-bottom: 1rem;
        text-transform: uppercase;
        letter-spacing: 2px;
    }
    
    .category-description {
        font-size: 1rem;
        margin-bottom: 2rem;
        opacity: 0.9;
        line-height: 1.6;
    }
    
    .btn-light {
        background-color: white;
        color: #333;
        padding: 0.75rem 1.5rem;
        border-radius: 30px;
        font-weight: 600;
        font-size: 0.9rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        border: none;
        text-decoration: none;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }
    
    .btn-light i {
        margin-left: 0.5rem;
        transition: transform 0.3s ease;
    }
    
    .btn-light:hover {
        background-color: var(--primary-color);
        color: white;
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }
    
    .btn-light:hover i {
        transform: translateX(5px);
    }
    
    /* Add collection-specific styling */
    .men-collection .category-overlay {
        background: linear-gradient(to top, rgba(0, 50, 100, 0.9) 0%, rgba(0, 50, 100, 0.4) 50%, rgba(0, 50, 100, 0.1) 100%);
    }
    
    .women-collection .category-overlay {
        background: linear-gradient(to top, rgba(150, 50, 100, 0.9) 0%, rgba(150, 50, 100, 0.4) 50%, rgba(150, 50, 100, 0.1) 100%);
    }
    
    .sports-collection .category-overlay {
        background: linear-gradient(to top, rgba(50, 150, 50, 0.9) 0%, rgba(50, 150, 50, 0.4) 50%, rgba(50, 150, 50, 0.1) 100%);
    }
    
    /* Responsive adjustments */
    @media (max-width: 992px) {
        .category-grid {
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
        }
        
        .category-card {
            height: 450px;
        }
        
        .category-overlay {
            padding: 2.5rem;
        }
        
        .section-title {
            font-size: 2.2rem;
        }
    }
    
    @media (max-width: 768px) {
        .category-card {
            height: 400px;
        }
        
        .category-overlay {
            opacity: 1;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.8) 0%, rgba(0, 0, 0, 0.3) 70%, rgba(0, 0, 0, 0) 100%);
            padding: 2rem;
        }
        
        .category-content {
            transform: translateY(0);
        }
        
        .category-name {
            font-size: 1.5rem;
            margin-bottom: 0.75rem;
        }
        
        .category-description {
            font-size: 0.9rem;
            margin-bottom: 1.5rem;
        }
        
        .section-title {
            font-size: 2rem;
        }
        
        .men-collection .category-overlay,
        .women-collection .category-overlay,
        .sports-collection .category-overlay {
            background: linear-gradient(to top, rgba(0, 0, 0, 0.8) 0%, rgba(0, 0, 0, 0.3) 70%, rgba(0, 0, 0, 0) 100%);
        }
    }
    
    @media (max-width: 576px) {
        .category-card {
            height: 350px;
        }
        
        .category-name {
            font-size: 1.3rem;
        }
        
        .category-overlay {
            padding: 1.5rem;
        }
        
        .section-title {
            font-size: 1.8rem;
        }
        
        .section-subtitle {
            font-size: 1rem;
        }
    }
    </style>
    
    <!-- Featured Products -->
    <section class="products-section">
        <div class="section-header">
            <h2 class="section-title">Produits Populaires</h2>
            <p class="section-subtitle">Découvrez nos produits les plus appréciés par nos clients</p>
            <div class="view-all">
                <a href="?route=shop" class="btn-outline-primary">Voir Tout <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>
        
        <div class="product-grid" id="featured-products">
            <!-- Products will be loaded here via JS -->
            <div class="loading-spinner">Chargement des produits...</div>
        </div>
    </section>

    <style>
    /* Enhanced Products Section Styling */
    .products-section {
        margin: var(--spacing-xxl) 0;
        padding: var(--spacing-xl) 0;
        position: relative;
    }
    
    .products-section .section-header {
        text-align: center;
        margin-bottom: var(--spacing-xl);
    }
    
    .products-section .section-title {
        font-size: 2.5rem;
        margin-bottom: var(--spacing-sm);
        color: var(--dark-color);
        font-weight: 800;
    }
    
    .products-section .section-subtitle {
        color: var(--grey-color);
        font-size: 1.1rem;
        max-width: 600px;
        margin: 0 auto var(--spacing-lg);
    }
    
    .btn-outline-primary {
        display: inline-flex;
        align-items: center;
        background-color: transparent;
        color: var(--primary-color);
        border: 2px solid var(--primary-color);
        padding: 12px 24px;
        border-radius: 30px;
        font-weight: 600;
        font-size: 1rem;
        text-decoration: none;
        transition: all 0.3s ease;
        margin-top: var(--spacing-md);
    }
    
    .btn-outline-primary i {
        margin-left: 8px;
        transition: transform 0.3s ease;
    }
    
    .btn-outline-primary:hover {
        background-color: var(--primary-color);
        color: white;
        transform: translateY(-3px);
        box-shadow: 0 8px 15px rgba(255, 107, 107, 0.2);
    }
    
    .btn-outline-primary:hover i {
        transform: translateX(5px);
    }
    
    .view-all {
        margin-top: var(--spacing-md);
    }
    
    .product-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr); /* Changed from auto-fill to exactly 3 columns */
        gap: 2.5rem;
        margin: 2.5rem 0;
    }
    
    .product-card {
        background-color: #fff;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        transition: transform 0.4s ease, box-shadow 0.4s ease;
        display: flex;
        flex-direction: column;
        height: 100%;
        margin-bottom: 0.5rem;
    }
    
    .product-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
    }
    
    .product-image {
        position: relative;
        height: 280px; /* Increased from 220px */
        overflow: hidden;
        background-color: #f8f9fa;
        width: 100%; /* Ensure full width */
        display: flex; /* Add flex display */
        align-items: center; /* Center content vertically */
        justify-content: center; /* Center content horizontally */
    }
    
    .product-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.6s ease;
        display: block; /* Ensure block display */
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
        font-size: 4rem;
        color: #adb5bd;
        opacity: 0.5;
    }
    
    .product-actions {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: linear-gradient(to top, rgba(0, 0, 0, 0.7), transparent);
        padding: 1.5rem;
        opacity: 0;
        transform: translateY(20px);
        transition: opacity 0.4s ease, transform 0.4s ease;
        display: flex;
        justify-content: center;
    }
    
    .product-card:hover .product-actions {
        opacity: 1;
        transform: translateY(0);
    }
    
    .add-to-cart {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        background-color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }
    
    .add-to-cart i {
        font-size: 1.1rem;
        color: var(--dark-color);
        transition: color 0.3s ease;
    }
    
    .add-to-cart:hover {
        background-color: var(--primary-color);
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(255, 107, 107, 0.3);
    }
    
    .add-to-cart:hover i {
        color: white;
    }
    
    .product-info {
        padding: 1.5rem;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        border-top: 1px solid rgba(0,0,0,0.05);
    }
    
    .product-name {
        font-size: 1.1rem;
        margin: 0 0 0.75rem 0;
        font-weight: 700;
        line-height: 1.4;
    }
    
    .product-name a {
        color: var(--dark-color);
        text-decoration: none;
        transition: color 0.3s ease;
    }
    
    .product-name a:hover {
        color: var(--primary-color);
    }
    
    .product-category {
        color: var(--grey-color);
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 0.75rem;
    }
    
    .product-price {
        margin-top: auto;
        font-weight: 800;
        color: var(--primary-color);
        font-size: 1.25rem;
    }
    
    .loading-spinner {
        grid-column: 1 / -1;
        text-align: center;
        padding: var(--spacing-xl);
        color: var(--grey-color);
        font-size: 1.1rem;
    }
    
    /* Responsive adjustments */
    @media (max-width: 992px) {
        .product-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 2rem;
        }
        
        .products-section .section-title {
            font-size: 2.2rem;
        }
    }
    
    @media (max-width: 768px) {
        .product-image {
            height: 240px;
        }
        
        .products-section .section-title {
            font-size: 2rem;
        }
        
        .products-section .section-subtitle {
            font-size: 1rem;
        }
    }
    
    @media (max-width: 576px) {
        .product-grid {
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }
        
        .product-image {
            height: 220px;
        }
        
        .product-info {
            padding: 1.25rem;
        }
        
        .product-name {
            font-size: 1rem;
        }
        
        .products-section .section-title {
            font-size: 1.8rem;
        }
    }
    </style>
</main>

<!-- Include API scripts (fixed to remove duplicate) -->
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
            
            // Create safe image with error handling
            const createSafeImage = (url, alt) => {
                if (!url) return `<div class="placeholder-image"><i class="fas fa-shoe-prints"></i></div>`;
                
                return `
                    <img src="${url}" alt="${alt}" onerror="this.onerror=null; this.src='assets/images/placeholder.png';">
                `;
            };
            
            // Check if image exists or use placeholder
            const hasImage = product.image_url && product.image_url !== '';
            const imageContent = createSafeImage(product.image_url, product.nom);
            
            productCard.innerHTML = `
                <a href="?route=product&id=${product.id_produit}" class="product-link">
                    <div class="product-image ${!hasImage ? 'no-image' : ''}">
                        ${imageContent}
                        <button class="add-to-cart-btn" data-id="${product.id_produit}" title="Ajouter au panier">
                            <i class="fas fa-shopping-cart"></i>
                        </button>
                    </div>
                </a>
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
            const addToCartBtn = productCard.querySelector('.add-to-cart-btn');
            addToCartBtn.addEventListener('click', function(e) {
                e.preventDefault(); // Prevent navigation to detail page
                const productId = this.getAttribute('data-id');
                addToCart(productId);
            });
        });
        
        // Add custom styles for product cards
        if (!document.getElementById('product-card-styles')) {
            const style = document.createElement('style');
            style.id = 'product-card-styles';
            style.textContent = `
                .product-card {
                    display: flex;
                    flex-direction: column;
                    height: 100%;
                    transition: transform 0.3s ease, box-shadow 0.3s ease;
                    position: relative;
                }
                
                .product-link {
                    display: block;
                    text-decoration: none;
                    color: inherit;
                }
                
                .product-image {
                    position: relative;
                    height: 220px;
                    overflow: hidden;
                    background-color: #f8f9fa;
                    border-radius: 8px 8px 0 0;
                }
                
                .product-image img {
                    width: 100%;
                    height: 100%;
                    object-fit: cover;
                    transition: transform 0.5s ease;
                }
                
                .product-card:hover .product-image img {
                    transform: scale(1.05);
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
                }
                
                .product-name a {
                    color: var(--dark-color);
                    text-decoration: none;
                    transition: color 0.3s ease;
                }
                
                .product-name a:hover {
                    color: var(--primary-color);
                }
                
                .product-actions {
                    padding: 0 1rem 1rem 1rem;
                }
                
                .add-to-cart-btn {
                    width: 100%;
                    padding: 0.75rem;
                    background-color: var(--primary-color);
                    color: white;
                    border: none;
                    border-radius: 6px;
                    cursor: pointer;
                    font-weight: 600;
                    transition: background-color 0.3s ease, transform 0.3s ease;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }
                
                .add-to-cart-btn i {
                    margin-right: 0.5rem;
                }
                
                .add-to-cart-btn:hover {
                    background-color: var(--primary-dark);
                    transform: translateY(-2px);
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