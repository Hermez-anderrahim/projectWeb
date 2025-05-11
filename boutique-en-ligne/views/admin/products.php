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
    
    <div id="products-table-container">
        <div class="loading-spinner">Chargement des produits...</div>
    </div>
</main>

<style>
/* Product Form Styles with Image Upload */
.image-upload-container {
    margin-bottom: 1.5rem;
    border: 1px solid var(--border-color, #ddd);
    border-radius: 8px;
    padding: 1rem;
    background-color: var(--white-color, #fff);
}

.image-preview {
    width: 100%;
    height: 200px;
    background-color: var(--light-bg, #f8f9fa);
    border-radius: 4px;
    margin-bottom: 1rem;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px dashed var(--border-color, #ddd);
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
    color: var(--grey-color, #adb5bd);
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
    color: var(--grey-color, #6c757d);
    margin-top: 0.25rem;
}

.separator {
    text-align: center;
    position: relative;
    margin: 0.5rem 0;
    color: var(--grey-color, #6c757d);
}

.separator::before,
.separator::after {
    content: '';
    position: absolute;
    top: 50%;
    width: 40%;
    height: 1px;
    background-color: var(--border-color, #e0e0e0);
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
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Check if user is admin
    checkAdmin();
    
    // Elements
    const searchInput = document.getElementById('search-product');
    const categoryFilter = document.getElementById('category-filter');
    const addProductBtn = document.getElementById('add-product-btn');
    
    // Event listeners
    searchInput.addEventListener('input', debounce(filterProducts, 500));
    categoryFilter.addEventListener('change', filterProducts);
    addProductBtn.addEventListener('click', showAddProductModal);
    
    // Initial load
    loadProducts();
    
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
    
    async function loadProducts(searchTerm = '', category = '') {
        const container = document.getElementById('products-table-container');
        container.innerHTML = '<div class="loading-spinner">Chargement des produits...</div>';
        
        try {
            let products;
            
            if (searchTerm) {
                const searchResults = await ProductAPI.search(searchTerm, category);
                products = searchResults.produits;
            } else {
                const allProducts = await ProductAPI.getAll(1, 100, category);
                products = allProducts.produits;
            }
            
            if (products && products.length > 0) {
                displayProducts(products);
            } else {
                container.innerHTML = '<div class="no-results">Aucun produit trouvé</div>';
            }
        } catch (error) {
            console.error('Error loading products:', error);
            container.innerHTML = '<div class="error-message">Erreur lors du chargement des produits</div>';
        }
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
                <tbody>
        `;
        
        products.forEach(product => {
            const price = parseFloat(product.prix).toFixed(2) + ' €';
            
            html += `
                <tr data-id="${product.id_produit}">
                    <td>${product.id_produit}</td>
                    <td class="product-image-cell">
                        <img src="${product.image_url || '/assets/images/placeholder.jpg'}" alt="${product.nom}">
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
        });
        
        html += `
                </tbody>
            </table>
        `;
        
        container.innerHTML = html;
        
        // Add event listeners
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
    
    function filterProducts() {
        const searchTerm = searchInput.value.trim();
        const category = categoryFilter.value;
        loadProducts(searchTerm, category);
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
        // Create modal backdrop
        const modalBackdrop = document.createElement('div');
        modalBackdrop.className = 'modal-backdrop';
        
        // Create modal content
        const modalContent = document.createElement('div');
        modalContent.className = 'modal-content product-modal';
        
        const isEdit = product !== null;
        const modalTitle = isEdit ? 'Modifier le produit' : 'Ajouter un produit';
        
        modalContent.innerHTML = `
            <div class="modal-header">
                <h2 class="modal-title">${modalTitle}</h2>
                <button class="modal-close"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body">
                <form id="product-form" class="product-form" enctype="multipart/form-data">
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
                                    <label for="product-image-file" class="btn btn-outline btn-sm">
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
        
        // Create modal
        const modal = document.createElement('div');
        modal.className = 'modal';
        modal.appendChild(modalContent);
        
        // Add modal to document
        document.body.appendChild(modalBackdrop);
        document.body.appendChild(modal);
        
        // Add event listeners
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
        imageUrlInput.addEventListener('change', function() {
            if (this.value) {
                // Display image from URL
                imagePreview.innerHTML = `<img src="${this.value}" alt="Aperçu de l'image">`;
                
                // Clear any uploaded file data
                imageDataInput.value = '';
                imageFileInput.value = '';
            }
        });
        
        modal.querySelector('#save-product').addEventListener('click', async function() {
            const form = document.getElementById('product-form');
            
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }
            
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
                    }
                } else {
                    result = await ProductAPI.create(productData);
                    if (result.success) {
                        showNotification('Produit ajouté avec succès', 'success');
                    }
                }
                
                closeModal();
                loadProducts();
                
            } catch (error) {
                console.error('Error saving product:', error);
                showNotification('Erreur lors de l\'enregistrement du produit', 'error');
            }
        });
        
        // Show modal
        setTimeout(() => {
            modalBackdrop.classList.add('show');
            modal.classList.add('show');
        }, 10);
        
        function closeModal() {
            modalBackdrop.classList.remove('show');
            modal.classList.remove('show');
            setTimeout(() => {
                modalBackdrop.remove();
                modal.remove();
            }, 300);
        }
    }
    
    async function deleteProduct(productId) {
        if (confirm('Êtes-vous sûr de vouloir supprimer ce produit ?')) {
            try {
                const result = await ProductAPI.delete(productId);
                
                if (result.success) {
                    showNotification('Produit supprimé avec succès', 'success');
                    loadProducts();
                } else {
                    showNotification(result.message || 'Erreur lors de la suppression', 'error');
                }
            } catch (error) {
                console.error('Error deleting product:', error);
                showNotification('Erreur lors de la suppression du produit', 'error');
            }
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
<script/>