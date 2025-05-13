<main class="container admin-container">
    <div class="admin-header">
        <h1 class="page-title">Gestion des Produits</h1>
        <button id="add-product-btn" class="btn btn-primary">
            <i class="fas fa-plus"></i> Ajouter un produit
        </button>
    </div>
    
    <div class="admin-filters">
        <div class="search-bar">
            <input type="text" id="search-product" placeholder="Rechercher un produit...">
            <button id="search-btn" class="btn btn-icon">
                <i class="fas fa-search"></i>
            </button>
        </div>
        
        <div class="filter-group">
            <select id="category-filter" class="form-control">
                <option value="">Toutes les catégories</option>
                <option value="men">Hommes</option>
                <option value="women">Femmes</option>
                <option value="sports">Sports</option>
                <option value="casual">Casual</option>
            </select>
        </div>
    </div>
    
    <div id="products-table-container" class="products-table-container">
        <div class="loading-spinner">Chargement des produits...</div>
    </div>
    
    <div class="pagination-controls">
        <button id="load-more-btn" class="btn btn-outline">
            <i class="fas fa-sync"></i> Charger plus de produits
        </button>
    </div>
</main>

<style>
/* Enhanced Admin Products Page Styling */
.admin-container {
    padding: 2rem 0;
}

.admin-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.page-title {
    margin: 0;
    font-size: 2rem;
    color: var(--dark-color);
}

.admin-filters {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    margin-bottom: 1.5rem;
    background-color: var(--white-color);
    padding: 1rem;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.search-bar {
    display: flex;
    flex: 1;
    min-width: 250px;
    position: relative;
}

.search-bar input {
    width: 100%;
    padding: 0.75rem 1rem;
    padding-right: 3rem;
    border: 1px solid var(--light-grey);
    border-radius: 30px;
    font-size: 0.95rem;
    transition: all 0.3s ease;
}

.search-bar input:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(255, 107, 107, 0.2);
}

.search-bar .btn-icon {
    position: absolute;
    right: 5px;
    top: 50%;
    transform: translateY(-50%);
    background-color: var(--primary-color);
    color: white;
    width: 35px;
    height: 35px;
    border-radius: 50%;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}

.search-bar .btn-icon:hover {
    background-color: var(--primary-dark);
    transform: translateY(-50%) scale(1.05);
}

.filter-group {
    min-width: 200px;
}

.filter-group select {
    border-radius: 30px;
    padding: 0.75rem 1rem;
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%23333' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: calc(100% - 15px) center;
    padding-right: 40px;
    border: 1px solid var(--light-grey);
    transition: all 0.3s ease;
}

.filter-group select:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(255, 107, 107, 0.2);
}

/* Product table container with scrollbar */
.products-table-container {
    background-color: var(--white-color);
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    margin-bottom: 1.5rem;
    overflow: auto;
    max-height: 60vh; /* Set maximum height to 60% of viewport height */
}

.admin-table {
    width: 100%;
    border-collapse: collapse;
}

.admin-table thead th {
    background-color: var(--light-grey);
    padding: 1rem;
    text-align: left;
    font-weight: 600;
    color: var(--dark-color);
    position: sticky;
    top: 0;
    z-index: 10;
}

.admin-table tbody tr {
    border-bottom: 1px solid var(--light-grey);
    transition: background-color 0.2s ease;
}

.admin-table tbody tr:hover {
    background-color: rgba(0, 0, 0, 0.02);
}

.admin-table td {
    padding: 1rem;
    vertical-align: middle;
}

.product-image-cell {
    width: 80px;
}

.product-image-cell img {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 4px;
    border: 1px solid var(--light-grey);
}

.stock-cell.low-stock {
    color: var(--status-cancelled);
    font-weight: 700;
}

.actions-cell {
    width: 100px;
    text-align: right;
}

.actions-cell .btn {
    margin-left: 0.5rem;
}

.pagination-controls {
    display: flex;
    justify-content: center;
    margin-top: 1.5rem;
}

/* Modal Styles with Background Blur */
.modal-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(5px);
    z-index: 1000;
    display: flex;
    justify-content: center;
    align-items: center;
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.3s ease, visibility 0.3s ease;
}

.modal-backdrop.show {
    opacity: 1;
    visibility: visible;
}

.modal {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%) scale(0.9);
    z-index: 1001;
    background-color: var(--white-color);
    border-radius: 8px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    width: 90%;
    max-width: 600px;
    max-height: 90vh;
    overflow-y: auto;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
}

.modal.show {
    opacity: 1;
    visibility: visible;
    transform: translate(-50%, -50%) scale(1);
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.25rem;
    border-bottom: 1px solid var(--light-grey);
}

.modal-title {
    margin: 0;
    font-size: 1.25rem;
    color: var(--dark-color);
}

.modal-close {
    background: none;
    border: none;
    color: var(--grey-color);
    font-size: 1.25rem;
    cursor: pointer;
    transition: color 0.2s ease;
}

.modal-close:hover {
    color: var(--primary-color);
}

.modal-body {
    padding: 1.5rem;
}

.modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
    padding: 1.25rem;
    border-top: 1px solid var(--light-grey);
}

/* Product Form Styles with Image Upload */
.image-upload-container {
    margin-bottom: 1.5rem;
    border: 1px solid var(--light-grey);
    border-radius: 8px;
    padding: 1rem;
    background-color: var(--lighter-grey);
}

.image-preview {
    width: 100%;
    height: 200px;
    background-color: var(--white-color);
    border-radius: 4px;
    margin-bottom: 1rem;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px dashed var(--light-grey);
}

.image-preview img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
}

.no-image {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: var(--grey-color);
}

.no-image i {
    font-size: 3rem;
    margin-bottom: 0.5rem;
}

.image-upload-options {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.hidden-file-input {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    border: 0;
}

.help-text {
    font-size: 0.8rem;
    color: var(--grey-color);
    margin-top: 0.25rem;
}

.separator {
    text-align: center;
    position: relative;
    margin: 0.5rem 0;
    color: var(--grey-color);
}

.separator::before,
.separator::after {
    content: '';
    position: absolute;
    top: 50%;
    width: 40%;
    height: 1px;
    background-color: var(--light-grey);
}

.separator::before {
    left: 0;
}

.separator::after {
    right: 0;
}

.product-modal {
    max-width: 800px;
    width: 100%;
}

.form-group {
    margin-bottom: 1.25rem;
}

.form-row {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    margin-bottom: 1rem;
}

.form-row .form-group {
    flex: 1;
    min-width: 200px;
}

.form-label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
}

.form-control {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 1px solid var(--light-grey);
    border-radius: 6px;
    transition: all 0.3s ease;
}

.form-control:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(255, 107, 107, 0.2);
}

/* Status messages */
.no-results, .error-message, .loading-spinner {
    padding: 2rem;
    text-align: center;
    color: var(--grey-color);
}

.error-message {
    color: var(--status-cancelled);
}

/* Responsive adjustments */
@media (min-width: 768px) {
    .image-upload-options {
        flex-direction: row;
        align-items: center;
    }
    
    .separator {
        width: 50px;
        margin: 0 1rem;
    }
    
    .separator::before,
    .separator::after {
        display: none;
    }
}

@media (max-width: 768px) {
    .admin-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
    
    .admin-filters {
        flex-direction: column;
    }
    
    .products-table-container {
        max-height: 50vh;
    }
    
    .admin-table {
        min-width: 800px; /* Force horizontal scroll on small screens */
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Check if user is admin
    checkAdmin();
    
    // Elements
    const searchInput = document.getElementById('search-product');
    const searchBtn = document.getElementById('search-btn');
    const categoryFilter = document.getElementById('category-filter');
    const addProductBtn = document.getElementById('add-product-btn');
    const loadMoreBtn = document.getElementById('load-more-btn');
    
    // Tracking variables
    let currentPage = 1;
    let totalPages = 1;
    let currentSearchTerm = '';
    let currentCategory = '';
    
    // Event listeners
    searchInput.addEventListener('input', debounce(function() {
        resetPagination();
        filterProducts();
    }, 500));
    
    searchBtn.addEventListener('click', function() {
        resetPagination();
        filterProducts();
    });
    
    categoryFilter.addEventListener('change', function() {
        resetPagination();
        filterProducts();
    });
    
    addProductBtn.addEventListener('click', showAddProductModal);
    
    loadMoreBtn.addEventListener('click', function() {
        if (currentPage < totalPages) {
            currentPage++;
            loadProducts(currentSearchTerm, currentCategory, currentPage, true);
        }
    });
    
    // Press Enter to search
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            resetPagination();
            filterProducts();
        }
    });
    
    // Initial load
    loadProducts('', '', 1, false);
    
    // Functions
    async function checkAdmin() {
        try {
            const userData = await UserAPI.getProfile();
            
            if (!userData.success || !userData.utilisateur || !userData.utilisateur.est_admin) {
                // Not admin, redirect to home
                window.location.href = '?route=home';
            }
        } catch (error) {
            console.error('Error checking admin status:', error);
            window.location.href = '?route=home';
        }
    }
    
    function resetPagination() {
        currentPage = 1;
    }
    
    function filterProducts() {
        currentSearchTerm = searchInput.value.trim();
        currentCategory = categoryFilter.value;
        loadProducts(currentSearchTerm, currentCategory, 1, false);
    }
    
    async function loadProducts(searchTerm = '', category = '', page = 1, append = false) {
        const container = document.getElementById('products-table-container');
        
        if (!append) {
            container.innerHTML = '<div class="loading-spinner"><i class="fas fa-spinner fa-spin"></i> Chargement des produits...</div>';
        } else {
            // If appending, show loading state in load more button
            loadMoreBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Chargement...';
            loadMoreBtn.disabled = true;
        }
        
        try {
            let response;
            const limit = 10; // Number of products per page
            
            if (searchTerm) {
                response = await ProductAPI.search(searchTerm, category, page, limit);
            } else {
                response = await ProductAPI.getAll(page, limit, category);
            }
            
            if (response.success && response.produits) {
                const products = response.produits;
                
                // Update pagination info
                totalPages = Math.ceil(response.total / limit) || 1;
                updatePaginationControls();
                
                if (products.length > 0) {
                    if (append) {
                        // Append new products to existing table
                        appendProducts(products);
                    } else {
                        // Create fresh table
                        displayProducts(products);
                    }
                } else if (!append) {
                    container.innerHTML = '<div class="no-results">Aucun produit trouvé</div>';
                }
            } else {
                if (!append) {
                    container.innerHTML = '<div class="error-message">Erreur lors du chargement des produits</div>';
                }
            }
        } catch (error) {
            console.error('Error loading products:', error);
            if (!append) {
                container.innerHTML = '<div class="error-message">Erreur lors du chargement des produits</div>';
            }
        } finally {
            // Reset load more button
            if (append) {
                loadMoreBtn.innerHTML = '<i class="fas fa-sync"></i> Charger plus de produits';
                loadMoreBtn.disabled = false;
            }
        }
    }
    
    function updatePaginationControls() {
        loadMoreBtn.style.display = currentPage >= totalPages ? 'none' : 'block';
    }
    
    function displayProducts(products) {
        const container = document.getElementById('products-table-container');
        
        let html = `
            <table class="admin-table products-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Image</th>
                        <th>Nom</th>
                        <th>Catégorie</th>
                        <th>Prix</th>
                        <th>Stock</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="products-body">
        `;
        
        products.forEach(product => {
            html += createProductRow(product);
        });
        
        html += `
                </tbody>
            </table>
        `;
        
        container.innerHTML = html;
        addProductEventListeners();
    }
    
    function appendProducts(products) {
        const tbody = document.getElementById('products-body');
        if (!tbody) return;
        
        let html = '';
        products.forEach(product => {
            html += createProductRow(product);
        });
        
        // Append new rows to existing table
        tbody.insertAdjacentHTML('beforeend', html);
        addProductEventListeners();
    }
    
    function createProductRow(product) {
        const price = parseFloat(product.prix).toFixed(2) + ' €';
        
        return `
            <tr data-id="${product.id_produit}">
                <td>${product.id_produit}</td>
                <td class="product-image-cell">
                    <img src="${product.image_url || 'assets/images/placeholder.png'}" alt="${product.nom}" onerror="this.src='assets/images/placeholder.png'">
                </td>
                <td>${product.nom}</td>
                <td>${product.categorie || 'Non catégorisé'}</td>
                <td>${price}</td>
                <td class="stock-cell ${product.stock <= 5 ? 'low-stock' : ''}">${product.stock}</td>
                <td class="actions-cell">
                    <button class="btn btn-sm btn-icon edit-product" data-id="${product.id_produit}" title="Modifier">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-icon btn-danger delete-product" data-id="${product.id_produit}" title="Supprimer">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </td>
            </tr>
        `;
    }
    
    function addProductEventListeners() {
        // Add event listeners to buttons in the product table
        document.querySelectorAll('.edit-product').forEach(btn => {
            btn.addEventListener('click', function() {
                const productId = this.getAttribute('data-id');
                editProduct(productId);
            });
        });
        
        document.querySelectorAll('.delete-product').forEach(btn => {
            btn.addEventListener('click', function() {
                const productId = this.getAttribute('data-id');
                deleteProduct(productId);
            });
        });
    }
    
    async function showAddProductModal() {
        createProductModal();
    }
    
    async function editProduct(productId) {
        try {
            const data = await ProductAPI.getById(productId);
            
            if (data.success && data.produit) {
                createProductModal(data.produit);
            } else {
                showNotification('Erreur lors de la récupération du produit', 'error');
            }
        } catch (error) {
            console.error('Error loading product for edit:', error);
            showNotification('Erreur lors de la récupération du produit', 'error');
        }
    }
    
    function createProductModal(product = null) {
        // Remove any existing modals
        document.querySelectorAll('.modal-backdrop, .modal').forEach(el => el.remove());
        
        // Create modal backdrop
        const modalBackdrop = document.createElement('div');
        modalBackdrop.className = 'modal-backdrop';
        
        // Create modal
        const modal = document.createElement('div');
        modal.className = 'modal product-modal';
        
        const isEdit = product !== null;
        const modalTitle = isEdit ? 'Modifier le produit' : 'Ajouter un produit';
        
        modal.innerHTML = `
            <div class="modal-header">
                <h2 class="modal-title">${modalTitle}</h2>
                <button class="modal-close"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body">
                <form id="product-form" class="product-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="product-name" class="form-label">Nom du produit</label>
                            <input type="text" id="product-name" class="form-control" required value="${product ? product.nom : ''}">
                        </div>
                        
                        <div class="form-group">
                            <label for="product-category" class="form-label">Catégorie</label>
                            <select id="product-category" class="form-control">
                                <option value="">Sélectionner une catégorie</option>
                                <option value="men" ${product && product.categorie === 'men' ? 'selected' : ''}>Hommes</option>
                                <option value="women" ${product && product.categorie === 'women' ? 'selected' : ''}>Femmes</option>
                                <option value="sports" ${product && product.categorie === 'sports' ? 'selected' : ''}>Sports</option>
                                <option value="casual" ${product && product.categorie === 'casual' ? 'selected' : ''}>Casual</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="product-price" class="form-label">Prix (€)</label>
                            <input type="number" id="product-price" class="form-control" step="0.01" min="0" required value="${product ? product.prix : ''}">
                        </div>
                        
                        <div class="form-group">
                            <label for="product-stock" class="form-label">Stock</label>
                            <input type="number" id="product-stock" class="form-control" min="0" required value="${product ? product.stock : '0'}">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Image du produit</label>
                        <div class="image-upload-container">
                            <div class="image-preview" id="image-preview">
                                ${product && product.image_url ? 
                                    `<img src="${product.image_url}" alt="Aperçu de l'image">` : 
                                    `<div class="no-image"><i class="fas fa-image"></i><span>Aucune image</span></div>`
                                }
                            </div>
                            <div class="image-upload-options">
                                <div class="form-group">
                                    <label for="product-image-file" class="btn btn-outline">
                                        <i class="fas fa-upload"></i> Télécharger une image
                                    </label>
                                    <input type="file" id="product-image-file" class="hidden-file-input" accept="image/*">
                                    <p class="help-text">Formats supportés: JPG, PNG, GIF. Max 2MB.</p>
                                </div>
                                <div class="separator">OU</div>
                                <div class="form-group">
                                    <label for="product-image-url" class="form-label">URL de l'image</label>
                                    <input type="url" id="product-image-url" class="form-control" value="${product && product.image_url ? product.image_url : ''}">
                                </div>
                            </div>
                            <input type="hidden" id="product-image-data">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="product-description" class="form-label">Description</label>
                        <textarea id="product-description" class="form-control" rows="4">${product && product.description ? product.description : ''}</textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline modal-cancel">Annuler</button>
                <button class="btn btn-primary" id="save-product">Enregistrer</button>
            </div>
        `;
        
        // Add modal to document
        document.body.appendChild(modalBackdrop);
        document.body.appendChild(modal);
        
        // Add event listeners for modal controls
        modalBackdrop.addEventListener('click', closeModal);
        modal.querySelector('.modal-close').addEventListener('click', closeModal);
        modal.querySelector('.modal-cancel').addEventListener('click', closeModal);
        
        // Image file upload handling
        const imageFileInput = modal.querySelector('#product-image-file');
        const imagePreview = modal.querySelector('#image-preview');
        const imageUrlInput = modal.querySelector('#product-image-url');
        const imageDataInput = modal.querySelector('#product-image-data');
        
        imageFileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                if (file.size > 2 * 1024 * 1024) { // 2MB limit
                    showNotification('L\'image est trop volumineuse. Maximum 2MB.', 'error');
                    return;
                }
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    // Display image preview
                    imagePreview.innerHTML = `<img src="${e.target.result}" alt="Aperçu de l'image">`;
                    
                    // Store the base64 data
                    imageDataInput.value = e.target.result;
                    
                    // Clear URL input since we're using a file
                    imageUrlInput.value = '';
                };
                reader.readAsDataURL(file);
            }
        });
        
        // URL input handling
        imageUrlInput.addEventListener('input', function() {
            if (this.value) {
                // Update preview when URL changes
                imagePreview.innerHTML = `<img src="${this.value}" alt="Aperçu de l'image" onerror="this.parentNode.innerHTML='<div class=\'no-image\'><i class=\'fas fa-exclamation-circle\'></i><span>Image non disponible</span></div>'">`;
                
                // Clear any uploaded file data
                imageDataInput.value = '';
                imageFileInput.value = '';
            } else {
                // Show empty state if URL is cleared
                imagePreview.innerHTML = `<div class="no-image"><i class="fas fa-image"></i><span>Aucune image</span></div>`;
            }
        });
        
        // Save product button
        modal.querySelector('#save-product').addEventListener('click', async function() {
            const form = document.getElementById('product-form');
            
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }
            
            // Disable save button and show loading state
            const saveButton = this;
            saveButton.disabled = true;
            saveButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enregistrement...';
            
            const productData = {
                nom: document.getElementById('product-name').value,
                categorie: document.getElementById('product-category').value,
                prix: document.getElementById('product-price').value,
                stock: document.getElementById('product-stock').value,
                description: document.getElementById('product-description').value
            };
            
            // Handle image (either URL or uploaded file)
            if (imageUrlInput.value) {
                productData.image_url = imageUrlInput.value;
            } else if (imageDataInput.value) {
                productData.image_data = imageDataInput.value;
            }
            
            try {
                let result;
                
                if (isEdit) {
                    result = await ProductAPI.update(product.id_produit, productData);
                    if (result.success) {
                        showNotification('Produit mis à jour avec succès', 'success');
                    } else {
                        showNotification(result.message || 'Erreur lors de la mise à jour', 'error');
                    }
                } else {
                    result = await ProductAPI.create(productData);
                    if (result.success) {
                        showNotification('Produit ajouté avec succès', 'success');
                    } else {
                        showNotification(result.message || 'Erreur lors de l\'ajout', 'error');
                    }
                }
                
                closeModal();
                resetPagination();
                loadProducts(currentSearchTerm, currentCategory, 1, false);
                
            } catch (error) {
                console.error('Error saving product:', error);
                showNotification('Erreur lors de l\'enregistrement du produit', 'error');
                
                // Re-enable save button
                saveButton.disabled = false;
                saveButton.innerHTML = 'Enregistrer';
            }
        });
        
        // Show modal with animation
        setTimeout(() => {
            modalBackdrop.classList.add('show');
            modal.classList.add('show');
            document.body.style.overflow = 'hidden'; // Prevent page scrolling when modal is open
        }, 10);
        
        function closeModal() {
            modalBackdrop.classList.remove('show');
            modal.classList.remove('show');
            document.body.style.overflow = ''; // Restore page scrolling
            
            setTimeout(() => {
                modalBackdrop.remove();
                modal.remove();
            }, 300);
        }
    }
    
    async function deleteProduct(productId) {
        // Create and show a confirmation modal
        const modalBackdrop = document.createElement('div');
        modalBackdrop.className = 'modal-backdrop';
        
        const modal = document.createElement('div');
        modal.className = 'modal';
        modal.innerHTML = `
            <div class="modal-header">
                <h2 class="modal-title">Confirmation de suppression</h2>
                <button class="modal-close"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer ce produit ? Cette action est irréversible.</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline modal-cancel">Annuler</button>
                <button class="btn btn-danger" id="confirm-delete">Supprimer</button>
            </div>
        `;
        
        document.body.appendChild(modalBackdrop);
        document.body.appendChild(modal);
        
        // Event listeners
        modalBackdrop.addEventListener('click', closeModal);
        modal.querySelector('.modal-close').addEventListener('click', closeModal);
        modal.querySelector('.modal-cancel').addEventListener('click', closeModal);
        
        modal.querySelector('#confirm-delete').addEventListener('click', async function() {
            try {
                // Show loading state
                this.disabled = true;
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Suppression...';
                
                const result = await ProductAPI.delete(productId);
                
                if (result.success) {
                    showNotification('Produit supprimé avec succès', 'success');
                    closeModal();
                    resetPagination();
                    loadProducts(currentSearchTerm, currentCategory, 1, false);
                } else {
                    showNotification(result.message || 'Erreur lors de la suppression', 'error');
                    this.disabled = false;
                    this.innerHTML = 'Supprimer';
                }
            } catch (error) {
                console.error('Error deleting product:', error);
                showNotification('Erreur lors de la suppression du produit', 'error');
                this.disabled = false;
                this.innerHTML = 'Supprimer';
            }
        });
        
        // Show modal with animation
        setTimeout(() => {
            modalBackdrop.classList.add('show');
            modal.classList.add('show');
            document.body.style.overflow = 'hidden';
        }, 10);
        
        function closeModal() {
            modalBackdrop.classList.remove('show');
            modal.classList.remove('show');
            document.body.style.overflow = '';
            
            setTimeout(() => {
                modalBackdrop.remove();
                modal.remove();
            }, 300);
        }
    }
    
    function showNotification(message, type) {
        // Remove any existing notifications
        const existingNotifications = document.querySelectorAll('.notification');
        existingNotifications.forEach(notification => {
            notification.remove();
        });
        
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.innerHTML = `
            <div class="notification-icon">
                <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i>
            </div>
            <div class="notification-message">${message}</div>
        `;
        
        document.body.appendChild(notification);
        
        // Show with animation
        setTimeout(() => {
            notification.classList.add('show');
        }, 10);
        
        // Auto-dismiss after 3 seconds
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => {
                notification.remove();
            }, 300);
        }, 3000);
    }
    
    // Utility function to debounce search input
    function debounce(func, delay) {
        let timeout;
        return function() {
            const context = this;
            const args = arguments;
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(context, args), delay);
        };
    }
});
</script>