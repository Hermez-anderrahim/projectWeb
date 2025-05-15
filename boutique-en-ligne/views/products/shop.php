<main class="container shop-page">
    <div class="shop-layout">
        <!-- Sidebar Filters -->
        <aside class="shop-sidebar">
            <div class="filter-container">
                <h3 class="filter-title">Filtres</h3>
                
                <!-- Search Filter -->
                <div class="filter-group">
                    <h4 class="filter-subtitle">Recherche</h4>
                    <div class="search-filter">
                        <input type="text" id="product-search" class="filter-search" placeholder="Chercher un produit...">
                        <button id="search-button" class="filter-search-btn">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Category Filter -->
                <div class="filter-group">
                    <h4 class="filter-subtitle">Catégories</h4>
                    <div class="category-filter">
                        <div class="custom-select">
                            <select id="category-select" class="filter-select">
                                <option value="">Toutes les catégories</option>
                                <option value="men">Hommes</option>
                                <option value="women">Femmes</option>
                                <option value="sports">Sports</option>
                            </select>
                            <span class="select-arrow"><i class="fas fa-chevron-down"></i></span>
                        </div>
                    </div>
                </div>
                
                <!-- Price Range Filter -->
                <div class="filter-group">
                    <h4 class="filter-subtitle">Prix</h4>
                    <div class="price-filter">
                        <div class="price-inputs">
                            <div class="price-input">
                                <label for="min-price">Min</label>
                                <div class="input-with-icon">
                                    <span class="currency-symbol">€</span>
                                    <input type="number" id="min-price" class="filter-price" placeholder="0">
                                </div>
                            </div>
                            <div class="price-separator">-</div>
                            <div class="price-input">
                                <label for="max-price">Max</label>
                                <div class="input-with-icon">
                                    <span class="currency-symbol">€</span>
                                    <input type="number" id="max-price" class="filter-price" placeholder="1000">
                                </div>
                            </div>
                        </div>
                        <div class="range-slider">
                            <div class="price-slider" id="price-slider"></div>
                        </div>
                    </div>
                </div>
                
                <!-- Apply Filters -->
                <div class="filter-actions">
                    <button id="apply-filters" class="btn-primary filter-btn">Appliquer les filtres</button>
                    <button id="reset-filters" class="btn-outline filter-btn">Réinitialiser</button>
                </div>
            </div>
        </aside>
        
        <!-- Products Section -->
        <section class="shop-products">
            <div class="shop-header">
                <div class="shop-title">
                    <h1>Notre Collection</h1>
                    <p>Découvrez notre sélection de chaussures de qualité</p>
                </div>
                <div class="shop-sorting">
                    <div class="custom-select">
                        <select id="sort-select" class="filter-select">
                            <option value="popular">Popularité</option>
                            <option value="price-asc">Prix croissant</option>
                            <option value="price-desc">Prix décroissant</option>
                            <option value="name-asc">Nom (A-Z)</option>
                            <option value="name-desc">Nom (Z-A)</option>
                        </select>
                        <span class="select-arrow"><i class="fas fa-chevron-down"></i></span>
                    </div>
                </div>
            </div>
            
            <!-- Categories Toggle on Mobile -->
            <div class="mobile-categories">
                <div class="category-pills">
                    <button class="category-pill active" data-category="">Tous</button>
                    <button class="category-pill" data-category="men">Hommes</button>
                    <button class="category-pill" data-category="women">Femmes</button>
                    <button class="category-pill" data-category="sports">Sports</button>
                </div>
                <button class="filter-toggle-btn">
                    <i class="fas fa-sliders-h"></i> Filtres
                </button>
            </div>
            
            <!-- Products Grid -->
            <div class="products-grid" id="products-container">
                <div class="loading-spinner">Chargement des produits...</div>
            </div>
            
            <!-- Pagination -->
            <div class="shop-pagination" id="pagination"></div>
        </section>
    </div>
</main>

<!-- Mobile Filter Overlay -->
<div class="mobile-filters-overlay" id="mobile-filters">
    <div class="mobile-filters-container">
        <div class="mobile-filters-header">
            <h3>Filtres</h3>
            <button class="mobile-filters-close"><i class="fas fa-times"></i></button>
        </div>
        <div class="mobile-filters-content">
            <!-- Search Filter -->
            <div class="filter-group">
                <h4 class="filter-subtitle">Recherche</h4>
                <div class="search-filter">
                    <input type="text" id="mobile-product-search" class="filter-search" placeholder="Chercher un produit...">
                </div>
            </div>
            
            <!-- Category Filter -->
            <div class="filter-group">
                <h4 class="filter-subtitle">Catégories</h4>
                <div class="mobile-category-options">
                    <label class="mobile-category-option">
                        <input type="radio" name="mobile-category" value="" checked>
                        <span class="checkmark"></span>
                        Toutes les catégories
                    </label>
                    <label class="mobile-category-option">
                        <input type="radio" name="mobile-category" value="men">
                        <span class="checkmark"></span>
                        Hommes
                    </label>
                    <label class="mobile-category-option">
                        <input type="radio" name="mobile-category" value="women">
                        <span class="checkmark"></span>
                        Femmes
                    </label>
                    <label class="mobile-category-option">
                        <input type="radio" name="mobile-category" value="sports">
                        <span class="checkmark"></span>
                        Sports
                    </label>
                </div>
            </div>
            
            <!-- Price Range Filter -->
            <div class="filter-group">
                <h4 class="filter-subtitle">Prix</h4>
                <div class="price-filter">
                    <div class="price-inputs">
                        <div class="price-input">
                            <label for="mobile-min-price">Min</label>
                            <div class="input-with-icon">
                                <span class="currency-symbol">€</span>
                                <input type="number" id="mobile-min-price" class="filter-price" placeholder="0">
                            </div>
                        </div>
                        <div class="price-separator">-</div>
                        <div class="price-input">
                            <label for="mobile-max-price">Max</label>
                            <div class="input-with-icon">
                                <span class="currency-symbol">€</span>
                                <input type="number" id="mobile-max-price" class="filter-price" placeholder="1000">
                            </div>
                        </div>
                    </div>
                    <div class="range-slider">
                        <div class="price-slider" id="mobile-price-slider"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="mobile-filters-footer">
            <button id="mobile-apply-filters" class="btn-primary filter-btn">Appliquer</button>
            <button id="mobile-reset-filters" class="btn-outline filter-btn">Réinitialiser</button>
        </div>
    </div>
</div>

<style>
/* Shop Page Styles */
.shop-page {
    padding-top: var(--spacing-xl);
    padding-bottom: var(--spacing-xxl);
}

.shop-layout {
    display: grid;
    grid-template-columns: 280px 1fr;
    gap: var(--spacing-xl);
    position: relative;
}

/* Sidebar Styles */
.shop-sidebar {
    position: sticky;
    top: 100px;
    height: fit-content;
}

.filter-container {
    background-color: var(--white-color);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-sm);
    padding: var(--spacing-lg);
    margin-bottom: var(--spacing-lg);
}

.filter-title {
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: var(--spacing-lg);
    color: var(--dark-color);
    border-bottom: 1px solid var(--light-grey);
    padding-bottom: var(--spacing-sm);
}

.filter-group {
    margin-bottom: var(--spacing-lg);
}

.filter-subtitle {
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: var(--spacing-md);
    color: var(--dark-color);
}

/* Search Filter */
.search-filter {
    position: relative;
}

.filter-search {
    width: 100%;
    padding: 0.75rem 3rem 0.75rem 1rem;
    border: 1px solid var(--light-grey);
    border-radius: var(--radius-md);
    font-size: 0.95rem;
    transition: all var(--transition-fast);
}

.filter-search:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 2px rgba(255, 107, 107, 0.2);
    outline: none;
}

.filter-search-btn {
    position: absolute;
    right: 0.5rem;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: var(--grey-color);
    font-size: 1rem;
    cursor: pointer;
    transition: color var(--transition-fast);
    padding: 0.5rem;
}

.filter-search-btn:hover {
    color: var(--primary-color);
}

/* Select Styles */
.custom-select {
    position: relative;
    width: 100%;
}

.filter-select {
    width: 100%;
    padding: 0.75rem 2.5rem 0.75rem 1rem;
    appearance: none;
    border: 1px solid var(--light-grey);
    border-radius: var(--radius-md);
    font-size: 0.95rem;
    background-color: var(--white-color);
    cursor: pointer;
    transition: all var(--transition-fast);
}

.filter-select:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 2px rgba(255, 107, 107, 0.2);
    outline: none;
}

.select-arrow {
    position: absolute;
    right: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--grey-color);
    pointer-events: none;
    transition: transform var(--transition-fast);
}

.filter-select:focus + .select-arrow {
    transform: translateY(-50%) rotate(180deg);
    color: var(--primary-color);
}

/* Price Filter */
.price-filter {
    margin-top: var(--spacing-sm);
}

.price-inputs {
    display: flex;
    align-items: center;
    margin-bottom: var(--spacing-md);
}

.price-input {
    flex: 1;
}

.price-input label {
    display: block;
    font-size: 0.8rem;
    color: var(--grey-color);
    margin-bottom: 0.25rem;
}

.input-with-icon {
    position: relative;
}

.currency-symbol {
    position: absolute;
    left: 0.75rem;
    top: 50%;
    transform: translateY(-50%);
    font-size: 0.9rem;
    color: var(--grey-color);
}

.filter-price {
    width: 100%;
    padding: 0.6rem 0.5rem 0.6rem 1.75rem;
    border: 1px solid var(--light-grey);
    border-radius: var(--radius-md);
    font-size: 0.9rem;
    transition: all var(--transition-fast);
}

.filter-price:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 2px rgba(255, 107, 107, 0.2);
    outline: none;
}

.price-separator {
    margin: 0 0.5rem;
    color: var(--grey-color);
    font-weight: 500;
}

.range-slider {
    margin-top: var(--spacing-md);
    padding: 0 var(--spacing-xs);
}

.price-slider {
    height: 4px;
    background: var(--light-grey);
    border-radius: 2px;
    position: relative;
}

.price-slider::before,
.price-slider::after {
    content: "";
    width: 16px;
    height: 16px;
    border-radius: 50%;
    background-color: var(--primary-color);
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    z-index: 2;
}

.price-slider::before {
    left: 0;
}

.price-slider::after {
    right: 0;
}

.price-slider-range {
    position: absolute;
    height: 100%;
    background-color: var(--primary-color);
    top: 0;
    left: 0;
    right: 0;
}

/* Filter Actions */
.filter-actions {
    display: flex;
    gap: var(--spacing-sm);
}

.filter-btn {
    flex: 1;
    padding: 0.75rem 1rem;
    font-size: 0.95rem;
    display: flex;
    justify-content: center;
    align-items: center;
    border-radius: var(--radius-md);
    transition: all var(--transition-fast);
    font-weight: 600;
    cursor: pointer;
}

.btn-primary.filter-btn {
    background-color: var(--primary-color);
    color: var(--white-color);
    border: none;
}

.btn-primary.filter-btn:hover {
    background-color: var(--primary-dark);
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(255, 107, 107, 0.3);
}

.btn-outline.filter-btn {
    background-color: transparent;
    color: var(--dark-color);
    border: 1px solid var(--light-grey);
}

.btn-outline.filter-btn:hover {
    border-color: var(--primary-color);
    color: var(--primary-color);
}

/* Shop Products Section */
.shop-products {
    flex: 1;
}

.shop-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: var(--spacing-lg);
}

.shop-title h1 {
    font-size: 2rem;
    font-weight: 800;
    margin-bottom: var(--spacing-xs);
    color: var(--dark-color);
}

.shop-title p {
    color: var(--grey-color);
    font-size: 1rem;
}

.shop-sorting {
    width: 200px;
}

/* Mobile Categories */
.mobile-categories {
    display: none;
    justify-content: space-between;
    margin-bottom: var(--spacing-lg);
    overflow-x: auto;
    white-space: nowrap;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: none; /* Firefox */
    -ms-overflow-style: none; /* IE/Edge */
}

.mobile-categories::-webkit-scrollbar {
    display: none; /* Chrome/Safari/Opera */
}

.category-pills {
    display: flex;
    gap: var(--spacing-xs);
}

.category-pill {
    padding: 0.5rem 1rem;
    border-radius: 50px;
    font-size: 0.9rem;
    font-weight: 500;
    background-color: var(--white-color);
    border: 1px solid var(--light-grey);
    color: var(--dark-color);
    cursor: pointer;
    transition: all var(--transition-fast);
    white-space: nowrap;
}

.category-pill.active {
    background-color: var(--primary-color);
    color: var(--white-color);
    border-color: var(--primary-color);
}

.filter-toggle-btn {
    padding: 0.5rem 1rem;
    border-radius: 50px;
    font-size: 0.9rem;
    font-weight: 500;
    background-color: var(--white-color);
    border: 1px solid var(--light-grey);
    color: var(--dark-color);
    cursor: pointer;
    transition: all var(--transition-fast);
    white-space: nowrap;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.filter-toggle-btn:hover {
    border-color: var(--primary-color);
    color: var(--primary-color);
}

/* Products Grid */
.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
    gap: var(--spacing-lg);
    margin-bottom: var(--spacing-xl);
}

/* Loading and Empty States */
.loading-spinner {
    grid-column: 1 / -1;
    text-align: center;
    padding: var(--spacing-xl);
    color: var(--grey-color);
    font-size: 1.1rem;
}

.empty-products {
    grid-column: 1 / -1;
    text-align: center;
    padding: var(--spacing-xl);
    background-color: var(--white-color);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-sm);
}

.empty-icon {
    font-size: 3rem;
    color: var(--primary-light);
    margin-bottom: var(--spacing-md);
}

.empty-text {
    color: var(--grey-color);
    margin-bottom: var(--spacing-lg);
}

/* Pagination */
.shop-pagination {
    display: flex;
    justify-content: center;
    margin-top: var(--spacing-lg);
}

.pagination-btn {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: var(--radius-md);
    margin: 0 var(--spacing-xs);
    background-color: var(--white-color);
    border: 1px solid var(--light-grey);
    color: var(--dark-color);
    font-weight: 500;
    transition: all var(--transition-fast);
    cursor: pointer;
}

.pagination-btn:hover {
    border-color: var(--primary-color);
    color: var(--primary-color);
}

.pagination-btn.active {
    background-color: var(--primary-color);
    color: var(--white-color);
    border-color: var(--primary-color);
}

.pagination-btn.disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

/* Mobile Filter Overlay */
.mobile-filters-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 1000;
    display: none;
    opacity: 0;
    transition: opacity var(--transition-normal);
}

.mobile-filters-overlay.active {
    display: block;
    opacity: 1;
}

.mobile-filters-container {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background-color: var(--white-color);
    border-top-left-radius: 20px;
    border-top-right-radius: 20px;
    box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.1);
    transform: translateY(100%);
    transition: transform var(--transition-normal);
    z-index: 1001;
    max-height: 90vh;
    display: flex;
    flex-direction: column;
}

.mobile-filters-overlay.active .mobile-filters-container {
    transform: translateY(0);
}

.mobile-filters-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: var(--spacing-md) var(--spacing-lg);
    border-bottom: 1px solid var(--light-grey);
}

.mobile-filters-header h3 {
    font-size: 1.2rem;
    font-weight: 600;
    color: var(--dark-color);
}

.mobile-filters-close {
    background: none;
    border: none;
    color: var(--grey-color);
    font-size: 1.2rem;
    cursor: pointer;
    padding: var(--spacing-xs);
}

.mobile-filters-content {
    padding: var(--spacing-md) var(--spacing-lg);
    overflow-y: auto;
    flex: 1;
}

.mobile-category-options {
    display: flex;
    flex-direction: column;
    gap: var(--spacing-sm);
}

.mobile-category-option {
    display: flex;
    align-items: center;
    position: relative;
    padding-left: 30px;
    cursor: pointer;
    font-size: 1rem;
    user-select: none;
}

.mobile-category-option input {
    position: absolute;
    opacity: 0;
    cursor: pointer;
}

.checkmark {
    position: absolute;
    top: 0;
    left: 0;
    height: 20px;
    width: 20px;
    background-color: var(--white-color);
    border: 2px solid var(--light-grey);
    border-radius: 50%;
}

.mobile-category-option:hover input ~ .checkmark {
    border-color: var(--primary-color);
}

.mobile-category-option input:checked ~ .checkmark {
    background-color: var(--white-color);
    border-color: var(--primary-color);
}

.checkmark:after {
    content: "";
    position: absolute;
    display: none;
}

.mobile-category-option input:checked ~ .checkmark:after {
    display: block;
}

.mobile-category-option .checkmark:after {
    left: 50%;
    top: 50%;
    transform: translate(-50%, -50%);
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: var(--primary-color);
}

.mobile-filters-footer {
    padding: var(--spacing-md) var(--spacing-lg);
    border-top: 1px solid var(--light-grey);
    display: flex;
    gap: var(--spacing-md);
}

/* Responsive Styles */
@media screen and (max-width: 992px) {
    .shop-layout {
        grid-template-columns: 1fr;
    }
    
    .shop-sidebar {
        display: none;
    }
    
    .mobile-categories {
        display: flex;
    }
}

@media screen and (max-width: 768px) {
    .shop-header {
        flex-direction: column;
        gap: var(--spacing-md);
    }
    
    .shop-sorting {
        width: 100%;
    }
    
    .products-grid {
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: var(--spacing-md);
    }
}

@media screen and (max-width: 576px) {
    .shop-page {
        padding-top: var(--spacing-md);
    }
    
    .shop-title h1 {
        font-size: 1.5rem;
    }
    
    .products-grid {
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: var(--spacing-sm);
    }
    
    .filter-actions {
        flex-direction: column;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Variables for filters
    let currentPage = 1;
    let currentCategory = getUrlParameter('category') || '';
    let currentSearch = '';
    let currentMinPrice = '';
    let currentMaxPrice = '';
    let currentSort = 'popular';
    
    // Initialize the page
    initShopPage();
    
    function initShopPage() {
        // Set filter values based on URL parameters
        setInitialFilterValues();
        
        // Load products
        loadProducts();
        
        // Add event listeners for filter controls
        setupEventListeners();
        
        // Initialize price slider (you would need a library like noUiSlider)
        initPriceSlider();
    }
    
    function setInitialFilterValues() {
        // URL parameter values
        const urlCategory = getUrlParameter('category');
        const urlSearch = getUrlParameter('search');
        const urlMinPrice = getUrlParameter('min_price');
        const urlMaxPrice = getUrlParameter('max_price');
        const urlSort = getUrlParameter('sort');
        
        // Set category
        if (urlCategory) {
            currentCategory = urlCategory;
            document.querySelector('#category-select').value = urlCategory;
            
            // Also set mobile category pills
            const pills = document.querySelectorAll('.category-pill');
            pills.forEach(pill => {
                if (pill.dataset.category === urlCategory) {
                    pill.classList.add('active');
                } else {
                    pill.classList.remove('active');
                }
            });
            
            // Set mobile radio buttons
            const radios = document.querySelectorAll('input[name="mobile-category"]');
            radios.forEach(radio => {
                radio.checked = (radio.value === urlCategory);
            });
        }
        
        // Set search
        if (urlSearch) {
            currentSearch = urlSearch;
            document.querySelector('#product-search').value = urlSearch;
            document.querySelector('#mobile-product-search').value = urlSearch;
        }
        
        // Set price range
        if (urlMinPrice) {
            currentMinPrice = urlMinPrice;
            document.querySelector('#min-price').value = urlMinPrice;
            document.querySelector('#mobile-min-price').value = urlMinPrice;
        }
        
        if (urlMaxPrice) {
            currentMaxPrice = urlMaxPrice;
            document.querySelector('#max-price').value = urlMaxPrice;
            document.querySelector('#mobile-max-price').value = urlMaxPrice;
        }
        
        // Set sort
        if (urlSort) {
            currentSort = urlSort;
            document.querySelector('#sort-select').value = urlSort;
        }
    }
    
    function setupEventListeners() {
        // Apply filters button
        document.querySelector('#apply-filters').addEventListener('click', applyFilters);
        document.querySelector('#mobile-apply-filters').addEventListener('click', () => {
            applyMobileFilters();
            closeMobileFilters();
        });
        
        // Reset filters button
        document.querySelector('#reset-filters').addEventListener('click', resetFilters);
        document.querySelector('#mobile-reset-filters').addEventListener('click', resetFilters);
        
        // Search input (desktop)
        document.querySelector('#search-button').addEventListener('click', () => {
            currentSearch = document.querySelector('#product-search').value.trim();
            applyFilters();
        });
        
        document.querySelector('#product-search').addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                currentSearch = e.target.value.trim();
                applyFilters();
            }
        });
        
        // Category select
        document.querySelector('#category-select').addEventListener('change', (e) => {
            currentCategory = e.target.value;
        });
        
        // Sort select
        document.querySelector('#sort-select').addEventListener('change', (e) => {
            currentSort = e.target.value;
            applyFilters();
        });
        
        // Mobile category pills
        document.querySelectorAll('.category-pill').forEach(pill => {
            pill.addEventListener('click', () => {
                document.querySelectorAll('.category-pill').forEach(p => p.classList.remove('active'));
                pill.classList.add('active');
                currentCategory = pill.dataset.category;
                applyFilters();
            });
        });
        
        // Mobile filter toggle
        document.querySelector('.filter-toggle-btn').addEventListener('click', openMobileFilters);
        document.querySelector('.mobile-filters-close').addEventListener('click', closeMobileFilters);
        
        // Close mobile filters when clicking outside
        document.querySelector('.mobile-filters-overlay').addEventListener('click', (e) => {
            if (e.target === document.querySelector('.mobile-filters-overlay')) {
                closeMobileFilters();
            }
        });
    }
    
    function applyFilters() {
        // Update URL with filter parameters
        updateUrlParameters();
        
        // Reset to page 1 when filters change
        currentPage = 1;
        
        // Load products with updated filters
        loadProducts();
    }
    
    function applyMobileFilters() {
        // Get values from mobile filter inputs
        currentSearch = document.querySelector('#mobile-product-search').value.trim();
        
        // Get selected category from radio buttons
        const selectedCategory = document.querySelector('input[name="mobile-category"]:checked');
        if (selectedCategory) {
            currentCategory = selectedCategory.value;
        }
        
        // Get price values
        currentMinPrice = document.querySelector('#mobile-min-price').value;
        currentMaxPrice = document.querySelector('#mobile-max-price').value;
        
        // Apply filters
        applyFilters();
        
        // Update category pills to reflect selection
        document.querySelectorAll('.category-pill').forEach(pill => {
            pill.classList.toggle('active', pill.dataset.category === currentCategory);
        });
    }
    
    function resetFilters() {
        // Reset filter values
        currentCategory = '';
        currentSearch = '';
        currentMinPrice = '';
        currentMaxPrice = '';
        currentSort = 'popular';
        currentPage = 1;
        
        // Reset form elements
        document.querySelector('#category-select').value = '';
        document.querySelector('#product-search').value = '';
        document.querySelector('#min-price').value = '';
        document.querySelector('#max-price').value = '';
        document.querySelector('#sort-select').value = 'popular';
        
        // Reset mobile filters
        document.querySelector('#mobile-product-search').value = '';
        document.querySelector('input[name="mobile-category"][value=""]').checked = true;
        document.querySelector('#mobile-min-price').value = '';
        document.querySelector('#mobile-max-price').value = '';
        
        // Reset category pills
        document.querySelectorAll('.category-pill').forEach(pill => {
            pill.classList.toggle('active', pill.dataset.category === '');
        });
        
        // Update URL and load products
        updateUrlParameters();
        loadProducts();
    }
    
    function updateUrlParameters() {
        const url = new URL(window.location.href);
        
        // Set or remove parameters based on filter values
        if (currentCategory) {
            url.searchParams.set('category', currentCategory);
        } else {
            url.searchParams.delete('category');
        }
        
        if (currentSearch) {
            url.searchParams.set('search', currentSearch);
        } else {
            url.searchParams.delete('search');
        }
        
        if (currentMinPrice) {
            url.searchParams.set('min_price', currentMinPrice);
        } else {
            url.searchParams.delete('min_price');
        }
        
        if (currentMaxPrice) {
            url.searchParams.set('max_price', currentMaxPrice);
        } else {
            url.searchParams.delete('max_price');
        }
        
        if (currentSort !== 'popular') {
            url.searchParams.set('sort', currentSort);
        } else {
            url.searchParams.delete('sort');
        }
        
        url.searchParams.set('page', currentPage);
        
        // Update browser URL without reloading page
        window.history.replaceState({}, '', url);
    }
    
    async function loadProducts() {
        const productsContainer = document.getElementById('products-container');
        const paginationContainer = document.getElementById('pagination');
        
        // Show loading spinner
        productsContainer.innerHTML = '<div class="loading-spinner">Chargement des produits...</div>';
        
        // Debug information
        console.log('Loading products with filters:', {
            page: currentPage,
            category: currentCategory,
            search: currentSearch,
            minPrice: currentMinPrice,
            maxPrice: currentMaxPrice,
            sort: currentSort
        });
        
        try {
            // Construct API URL with all filter parameters
            const params = new URLSearchParams();
            params.append('page', currentPage);
            params.append('limite', 12);
            
            if (currentCategory) {
                params.append('categorie', currentCategory);
            }
            
            // Add search term if available
            if (currentSearch && currentSearch.trim() !== '') {
                params.append('terme', currentSearch.trim());
            }
            
            // Add price range if available
            if (currentMinPrice && !isNaN(parseFloat(currentMinPrice))) {
                params.append('prix_min', parseFloat(currentMinPrice));
            }
            
            if (currentMaxPrice && !isNaN(parseFloat(currentMaxPrice))) {
                params.append('prix_max', parseFloat(currentMaxPrice));
            }
            
            // Add sorting if not default
            if (currentSort && currentSort !== 'popular') {
                params.append('tri', currentSort);
            }
            
            console.log('Request parameters:', Object.fromEntries(params.entries()));
            
            // Use the search endpoint for all filtering operations
            const apiUrl = `/api/produit.php/recherche?${params.toString()}`;
            console.log('API URL:', apiUrl);
            
            const response = await fetch(apiUrl);
            if (!response.ok) {
                throw new Error(`API error: ${response.status} ${response.statusText}`);
            }
            
            const data = await response.json();
            console.log('API response:', data);
            
            // Display products
            if (data.success && data.produits && data.produits.length > 0) {
                console.log(`Found ${data.produits.length} products to display`);
                displayProducts(data.produits);
                
                // Display pagination if there are multiple pages
                if (data.pagination && data.pagination.total_pages > 1) {
                    displayPagination(data.pagination);
                } else {
                    paginationContainer.innerHTML = '';
                }
            } else {
                console.log('No products found or API returned error');
                showEmptyProductsState();
                paginationContainer.innerHTML = '';
            }
        } catch (error) {
            console.error('Error loading products:', error);
            productsContainer.innerHTML = `
                <div class="empty-products">
                    <div class="empty-icon"><i class="fas fa-exclamation-circle"></i></div>
                    <p class="empty-text">Une erreur est survenue lors du chargement des produits.</p>
                    <p class="error-details">${error.message || 'Unknown error'}</p>
                    <button id="try-again" class="btn-primary filter-btn">Réessayer</button>
                    <button id="debug-fetch" class="btn-outline filter-btn">Diagnostic</button>
                </div>
            `;
            
            document.getElementById('try-again')?.addEventListener('click', loadProducts);
            document.getElementById('debug-fetch')?.addEventListener('click', debugFetch);
            paginationContainer.innerHTML = '';
        }
    }
    
    // Debug function to help diagnose API issues
    async function debugFetch() {
        try {
            const debugContainer = document.createElement('div');
            debugContainer.className = 'debug-info';
            debugContainer.style.cssText = 'background: #f8f9fa; border: 1px solid #dee2e6; padding: 15px; margin: 15px 0; border-radius: 5px; font-family: monospace; font-size: 14px; white-space: pre-wrap; overflow: auto;';
            
            document.getElementById('products-container').appendChild(debugContainer);
            
            debugContainer.textContent = 'Testing API endpoints...\n\n';
            
            // First test simple endpoint
            debugContainer.textContent += '1. Testing basic products endpoint:\n';
            const basicResponse = await fetch('/api/produit.php');
            const basicData = await basicResponse.json();
            debugContainer.textContent += `Status: ${basicResponse.status}\n`;
            debugContainer.textContent += `Success: ${basicData.success}\n`;
            debugContainer.textContent += `Products count: ${basicData.produits ? basicData.produits.length : 0}\n\n`;
            
            // Add details about the first product if available
            if (basicData.produits && basicData.produits.length > 0) {
                const firstProduct = basicData.produits[0];
                debugContainer.textContent += `First product found: ${firstProduct.nom} (ID: ${firstProduct.id_produit})\n\n`;
            }
            
            // Test with a specific product ID
            debugContainer.textContent += '2. Testing with a specific ID:\n';
            const idResponse = await fetch('/api/produit.php?id=1');
            const idData = await idResponse.json();
            debugContainer.textContent += `Status: ${idResponse.status}\n`;
            debugContainer.textContent += `Success: ${idData.success}\n`;
            debugContainer.textContent += `Product found: ${idData.produit ? 'Yes' : 'No'}\n\n`;
            
            // Display server info
            debugContainer.textContent += '3. API status check:\n';
            try {
                const infoResponse = await fetch('/api/index.php');
                const infoData = await infoResponse.text();
                debugContainer.textContent += `Response: ${infoData.substring(0, 100)}...\n\n`;
            } catch (err) {
                debugContainer.textContent += `Error: ${err.message}\n\n`;
            }
            
            debugContainer.textContent += 'Debug complete. Please share this information with the developer.';
        } catch (error) {
            console.error('Debug error:', error);
            alert('Error during diagnostic: ' + error.message);
        }
    }
    
    function displayProducts(products) {
        const productsContainer = document.getElementById('products-container');
        productsContainer.innerHTML = '';
        
        // Create a single placeholder icon element for reuse
        const placeholderIcon = '<i class="fas fa-shoe-prints"></i>';
        
        products.forEach(product => {
            const productCard = document.createElement('div');
            productCard.className = 'product-card';
            
            // Format price
            const price = parseFloat(product.prix).toFixed(2) + ' €';
            
            // Check if image exists or use placeholder
            const hasImage = product.image_url && product.image_url !== '';
            const imageContent = hasImage ? 
                `<img src="${product.image_url}" alt="${product.nom}">` : 
                `<div class="placeholder-image">${placeholderIcon}</div>`;
            
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
            
            productsContainer.appendChild(productCard);
            
            // Add event listener to "Add to Cart" button
            const addToCartBtn = productCard.querySelector('.add-to-cart');
            addToCartBtn.addEventListener('click', function() {
                const productId = this.getAttribute('data-id');
                addToCart(productId);
            });
        });
    }
    
    function displayPagination(pagination) {
        const paginationContainer = document.getElementById('pagination');
        paginationContainer.innerHTML = '';
        
        const { current_page, total_pages } = pagination;
        
        // Create a pagination wrapper
        const paginationWrapper = document.createElement('div');
        paginationWrapper.className = 'pagination-buttons';
        
        // Previous button
        const prevBtn = document.createElement('button');
        prevBtn.className = `pagination-btn ${current_page === 1 ? 'disabled' : ''}`;
        prevBtn.innerHTML = '<i class="fas fa-chevron-left"></i>';
        
        if (current_page > 1) {
            prevBtn.addEventListener('click', () => {
                currentPage = current_page - 1;
                updateUrlParameters();
                loadProducts();
                
                // Scroll to top of products
                document.querySelector('.shop-products').scrollIntoView({ behavior: 'smooth' });
            });
        }
        
        paginationWrapper.appendChild(prevBtn);
        
        // Page numbers
        const displayPages = getPageNumbersToDisplay(current_page, total_pages);
        
        displayPages.forEach(page => {
            if (page === '...') {
                // Ellipsis
                const ellipsis = document.createElement('span');
                ellipsis.className = 'pagination-ellipsis';
                ellipsis.textContent = '...';
                paginationWrapper.appendChild(ellipsis);
            } else {
                // Page button
                const pageBtn = document.createElement('button');
                pageBtn.className = `pagination-btn ${page === current_page ? 'active' : ''}`;
                pageBtn.textContent = page;
                
                if (page !== current_page) {
                    pageBtn.addEventListener('click', () => {
                        currentPage = page;
                        updateUrlParameters();
                        loadProducts();
                        
                        // Scroll to top of products
                        document.querySelector('.shop-products').scrollIntoView({ behavior: 'smooth' });
                    });
                }
                
                paginationWrapper.appendChild(pageBtn);
            }
        });
        
        // Next button
        const nextBtn = document.createElement('button');
        nextBtn.className = `pagination-btn ${current_page === total_pages ? 'disabled' : ''}`;
        nextBtn.innerHTML = '<i class="fas fa-chevron-right"></i>';
        
        if (current_page < total_pages) {
            nextBtn.addEventListener('click', () => {
                currentPage = current_page + 1;
                updateUrlParameters();
                loadProducts();
                
                // Scroll to top of products
                document.querySelector('.shop-products').scrollIntoView({ behavior: 'smooth' });
            });
        }
        
        paginationWrapper.appendChild(nextBtn);
        
        // Add pagination to container
        paginationContainer.appendChild(paginationWrapper);
    }
    
    function getPageNumbersToDisplay(currentPage, totalPages) {
        // Logic to determine which page numbers to show
        let pages = [];
        
        if (totalPages <= 7) {
            // If we have 7 or fewer pages, show all of them
            for (let i = 1; i <= totalPages; i++) {
                pages.push(i);
            }
        } else {
            // Always include page 1
            pages.push(1);
            
            // If current page is close to the beginning
            if (currentPage <= 4) {
                pages.push(2, 3, 4, 5, '...', totalPages);
            }
            // If current page is close to the end
            else if (currentPage >= totalPages - 3) {
                pages.push('...', totalPages - 4, totalPages - 3, totalPages - 2, totalPages - 1, totalPages);
            }
            // If current page is in the middle
            else {
                pages.push('...', currentPage - 1, currentPage, currentPage + 1, '...', totalPages);
            }
        }
        
        return pages;
    }
    
    function showEmptyProductsState() {
        const productsContainer = document.getElementById('products-container');
        productsContainer.innerHTML = `
            <div class="empty-products">
                <div class="empty-icon"><i class="fas fa-search fa-3x"></i></div>
                <p class="empty-text">Aucun produit ne correspond à vos critères de recherche.</p>
                <button id="reset-all-filters" class="btn-primary filter-btn">Réinitialiser les filtres</button>
            </div>
        `;
        
        // Add event listener to reset filters button
        document.getElementById('reset-all-filters').addEventListener('click', resetFilters);
    }
    
    function initPriceSlider() {
        // This would typically use a library like noUiSlider
        // For this implementation, we'll create a simple version with native elements
        
        // We'll leave this as a placeholder for now, as implementing a full slider
        // without a library is complex. In a real implementation you would initialize
        // the slider library here.
        
        // Instead, we'll just make sure the price inputs work
        document.getElementById('min-price').addEventListener('change', function() {
            currentMinPrice = this.value;
        });
        
        document.getElementById('max-price').addEventListener('change', function() {
            currentMaxPrice = this.value;
        });
        
        document.getElementById('mobile-min-price').addEventListener('change', function() {
            currentMinPrice = this.value;
        });
        
        document.getElementById('mobile-max-price').addEventListener('change', function() {
            currentMaxPrice = this.value;
        });
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
    
    function openMobileFilters() {
        const mobileFilters = document.getElementById('mobile-filters');
        mobileFilters.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
    
    function closeMobileFilters() {
        const mobileFilters = document.getElementById('mobile-filters');
        mobileFilters.classList.remove('active');
        document.body.style.overflow = '';
    }
    
    // Helper function to get URL parameters
    function getUrlParameter(name) {
        name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
        const regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
        const results = regex.exec(location.search);
        return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
    }
});
</script>