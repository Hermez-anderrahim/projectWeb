<style>
/* Product detail page styles */
.product-detail {
    margin: 2rem 0;
}

/* New layout: Two column design with image on left, info on right */
.product-layout {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 3rem;
    align-items: flex-start;
}

.product-gallery {
    position: relative;
}

/* Image styling to fill container */
.main-image {
    width: 100%;
    height: 500px; /* Increased height */
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
    background-color: #f8f9fa;
}

.main-image img {
    width: 100%;
    height: 100%;
    object-fit: contain; /* This ensures the image keeps its aspect ratio */
    display: block;
    background-color: #fff;
}

/* Product information styling */
.product-info {
    display: flex;
    flex-direction: column;
}

.product-title {
    font-size: 2.2rem;
    font-weight: 700;
    margin-bottom: 1rem;
    color: var(--dark-color);
    line-height: 1.2;
}

.product-meta {
    display: flex;
    justify-content: space-between;
    margin-bottom: 1.5rem;
    font-size: 0.9rem;
}

.product-category {
    color: var(--grey-color);
}

.product-category span {
    font-weight: 600;
    color: var(--dark-color);
}

.product-stock {
    font-weight: 600;
}

.in-stock {
    color: #28a745;
}

.out-of-stock {
    color: #dc3545;
}

.product-price {
    font-size: 2.5rem;
    font-weight: 800;
    color: var(--primary-color);
    margin: 1.5rem 0;
}

.product-description {
    line-height: 1.8;
    color: var(--dark-color);
    margin-bottom: 2rem;
    padding-bottom: 2rem;
    border-bottom: 1px solid rgba(0,0,0,0.1);
}

.product-actions {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    margin-top: 1rem;
}

.quantity-control {
    display: flex;
    align-items: center;
    border: 2px solid rgba(0,0,0,0.1);
    border-radius: 8px;
    overflow: hidden;
    max-width: 200px;
}

.quantity-btn {
    width: 40px;
    height: 40px;
    background: #f5f5f5;
    border: none;
    font-size: 1.1rem;
    font-weight: 600;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.quantity-btn:hover {
    background: #e9e9e9;
}

#product-quantity {
    width: 60px;
    height: 40px;
    text-align: center;
    border: none;
    border-left: 1px solid rgba(0,0,0,0.1);
    border-right: 1px solid rgba(0,0,0,0.1);
    font-size: 1rem;
    font-weight: 600;
}

/* Remove input number arrows */
#product-quantity::-webkit-inner-spin-button, 
#product-quantity::-webkit-outer-spin-button { 
    -webkit-appearance: none; 
    margin: 0; 
}

/* Add to cart button */
.btn-add-to-cart {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 50px;
    background-color: var(--primary-color);
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 1.1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    padding: 0 2rem;
    gap: 0.5rem;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    max-width: 100%;
    width: 100%;
}

.btn-add-to-cart i {
    font-size: 1.2rem;
}

.btn-add-to-cart:hover {
    background-color: var(--primary-dark);
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(0,0,0,0.15);
}

.btn-add-to-cart:disabled {
    background-color: #adb5bd;
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
}

.loading-spinner {
    text-align: center;
    padding: 3rem;
    color: var(--grey-color);
    font-size: 1.1rem;
}

.error-message {
    text-align: center;
    padding: 2rem;
    color: #dc3545;
    font-size: 1.1rem;
}

.notification {
    position: fixed;
    top: 20px;
    right: 20px;
    padding: 15px 25px;
    border-radius: 6px;
    background-color: white;
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    color: #333;
    font-weight: 600;
    z-index: 1000;
    transform: translateY(-20px);
    opacity: 0;
    transition: all 0.3s ease;
}

.notification.success {
    border-left: 4px solid #28a745;
}

.notification.error {
    border-left: 4px solid #dc3545;
}

.notification.show {
    transform: translateY(0);
    opacity: 1;
}

/* Responsive styles */
@media (max-width: 992px) {
    .product-layout {
        grid-template-columns: 1fr;
        gap: 2rem;
    }
    
    .main-image {
        height: 400px;
    }
    
    .product-gallery {
        position: relative;
    }
    
    .product-title {
        font-size: 1.8rem;
    }
    
    .product-price {
        font-size: 2rem;
        margin: 1rem 0;
    }
}

@media (max-width: 576px) {
    .product-actions {
        flex-direction: column;
        gap: 1rem;
    }
    
    .quantity-control {
        width: 100%;
    }
    
    .main-image {
        height: 300px;
    }
    
    .product-title {
        font-size: 1.5rem;
    }
    
    .product-price {
        font-size: 1.8rem;
    }
    
    .quick-add-to-cart {
        width: 45px;
        height: 45px;
    }
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
                        
                        <button class="btn-add-to-cart" id="add-to-cart" ${product.stock <= 0 ? 'disabled' : ''}>
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