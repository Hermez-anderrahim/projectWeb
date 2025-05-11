<style>
/* Product placeholder styles */
.product-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #e9ecef;
    border-radius: 8px;
}

.product-placeholder i {
    font-size: 4rem;
    color: #adb5bd;
    opacity: 0.5;
}

.main-image {
    width: 100%;
    height: 400px;
    overflow: hidden;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}
</style>

<main class="container">
    <div id="product-detail" class="product-detail">
        <div class="loading-spinner">Chargement du produit...</div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get product ID from URL
    const urlParams = new URLSearchParams(window.location.search);
    const productId = urlParams.get('id');
    
    if (!productId) {
        window.location.href = '?route=home';
        return;
    }
    
    loadProductDetail(productId);
    
    async function loadProductDetail(id) {
        try {
            const data = await ProductAPI.getById(id);
            
            if (data.success && data.produit) {
                displayProductDetail(data.produit);
            } else {
                document.getElementById('product-detail').innerHTML = '<p class="error-message">Produit non trouvé</p>';
                setTimeout(() => {
                    window.location.href = '?route=home';
                }, 2000);
            }
        } catch (error) {
            console.error('Error loading product:', error);
            document.getElementById('product-detail').innerHTML = '<p class="error-message">Erreur lors du chargement du produit</p>';
        }
    }
    
    function displayProductDetail(product) {
        const container = document.getElementById('product-detail');
        
        // Format price
        const price = parseFloat(product.prix).toFixed(2) + ' €';
        
        container.innerHTML = `
            <div class="breadcrumb">
                <a href="?route=home">Accueil</a> > 
                <a href="?route=shop&category=${product.categorie || ''}">${product.categorie || 'Produits'}</a> > 
                <span>${product.nom}</span>
            </div>
            
            <div class="product-layout">
                <div class="product-gallery">
                    <div class="main-image">
                        ${product.image_url ? 
                            `<img src="${product.image_url}" alt="${product.nom}" id="main-product-image">` : 
                            `<div class="product-placeholder">
                                <i class="fas fa-shoe-prints"></i>
                            </div>`
                        }
                    </div>
                </div>
                
                <div class="product-info">
                    <h1 class="product-title">${product.nom}</h1>
                    <div class="product-meta">
                        <div class="product-category">Catégorie: <span>${product.categorie || 'Non catégorisé'}</span></div>
                        <div class="product-stock ${product.stock > 0 ? 'in-stock' : 'out-of-stock'}">
                            ${product.stock > 0 ? `<i class="fas fa-check-circle"></i> En stock (${product.stock})` : '<i class="fas fa-times-circle"></i> Rupture de stock'}
                        </div>
                    </div>
                    
                    <div class="product-price">${price}</div>
                    
                    <div class="product-description">
                        ${product.description || 'Pas de description disponible pour ce produit.'}
                    </div>
                    
                    <div class="product-actions">
                        <div class="quantity-control">
                            <button class="quantity-btn minus" id="decrease-quantity">-</button>
                            <input type="number" id="product-quantity" min="1" value="1" max="${product.stock}">
                            <button class="quantity-btn plus" id="increase-quantity">+</button>
                        </div>
                        
                        <button class="btn btn-primary btn-add-to-cart" id="add-to-cart" ${product.stock <= 0 ? 'disabled' : ''}>
                            <i class="fas fa-shopping-cart"></i> Ajouter au panier
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        // Add event listeners
        const quantityInput = document.getElementById('product-quantity');
        const decreaseBtn = document.getElementById('decrease-quantity');
        const increaseBtn = document.getElementById('increase-quantity');
        const addToCartBtn = document.getElementById('add-to-cart');
        
        decreaseBtn.addEventListener('click', function() {
            const currentValue = parseInt(quantityInput.value);
            if (currentValue > 1) {
                quantityInput.value = currentValue - 1;
            }
        });
        
        increaseBtn.addEventListener('click', function() {
            const currentValue = parseInt(quantityInput.value);
            const maxValue = parseInt(product.stock);
            if (currentValue < maxValue) {
                quantityInput.value = currentValue + 1;
            }
        });
        
        quantityInput.addEventListener('change', function() {
            let value = parseInt(this.value);
            if (isNaN(value) || value < 1) {
                value = 1;
            } else if (value > product.stock) {
                value = product.stock;
            }
            this.value = value;
        });
        
        addToCartBtn.addEventListener('click', function() {
            const quantity = parseInt(quantityInput.value);
            addToCart(product.id_produit, quantity);
        });
    }
    
    async function addToCart(productId, quantity) {
        try {
            const result = await CartAPI.addItem(productId, quantity);
            if (result.success) {
                showNotification(`${quantity} produit(s) ajouté(s) au panier`, 'success');
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