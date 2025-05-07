<main class="container">
    <div class="cart-page">
        <h1 class="page-title">Votre Panier</h1>

        <div id="cart-content">
            <div class="loading-spinner">Chargement du panier...</div>
        </div>

        <div class="cart-actions" id="cart-actions" style="display: none;">
            <a href="?route=home" class="btn btn-outline">Continuer vos achats</a>
            <a href="?route=checkout" class="btn btn-primary" id="checkout-btn">Procéder au paiement</a>
        </div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    loadCart();
    
    async function loadCart() {
        try {
            const data = await CartAPI.getContents();
            
            if (data.success) {
                if (data.panier && data.panier.contenu && data.panier.contenu.length > 0) {
                    displayCart(data.panier);
                    document.getElementById('cart-actions').style.display = 'flex';
                } else {
                    displayEmptyCart();
                }
            } else {
                if (data.message === "Utilisateur non connecté.") {
                    document.getElementById('cart-content').innerHTML = `
                        <div class="cart-message">
                            <p>Veuillez vous connecter pour voir votre panier.</p>
                            <a href="?route=login" class="btn btn-primary">Se connecter</a>
                        </div>
                    `;
                } else {
                    displayEmptyCart();
                }
            }
        } catch (error) {
            console.error('Error loading cart:', error);
            document.getElementById('cart-content').innerHTML = '<p class="error-message">Erreur lors du chargement du panier</p>';
        }
    }
    
    function displayCart(cart) {
        const container = document.getElementById('cart-content');
        const items = cart.contenu;
        const total = parseFloat(cart.total).toFixed(2) + ' €';
        
        let html = `
            <div class="cart-table">
                <div class="cart-header">
                    <div class="cart-cell">Produit</div>
                    <div class="cart-cell">Prix</div>
                    <div class="cart-cell">Quantité</div>
                    <div class="cart-cell">Total</div>
                    <div class="cart-cell"></div>
                </div>
        `;
        
        items.forEach(item => {
            const price = parseFloat(item.prix_unitaire).toFixed(2) + ' €';
            const itemTotal = parseFloat(item.prix_unitaire * item.quantite).toFixed(2) + ' €';
            
            html += `
                <div class="cart-row" data-id="${item.id_produit}">
                    <div class="cart-cell product-cell">
                        <div class="product-image">
                            <img src="${item.image_url || 'assets/images/placeholder.jpg'}" alt="${item.nom}">
                        </div>
                        <div class="product-info">
                            <h3 class="product-name">${item.nom}</h3>
                            <div class="product-category">${item.categorie || ''}</div>
                        </div>
                    </div>
                    <div class="cart-cell price-cell">${price}</div>
                    <div class="cart-cell quantity-cell">
                        <div class="quantity-control">
                            <button class="quantity-btn minus" data-id="${item.id_produit}">-</button>
                            <input type="number" class="quantity-input" value="${item.quantite}" min="1" max="${item.stock}" data-id="${item.id_produit}">
                            <button class="quantity-btn plus" data-id="${item.id_produit}">+</button>
                        </div>
                    </div>
                    <div class="cart-cell total-cell">${itemTotal}</div>
                    <div class="cart-cell action-cell">
                        <button class="remove-item" data-id="${item.id_produit}">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                </div>
            `;
        });
        
        html += `
            </div>
            
            <div class="cart-summary">
                <div class="summary-row">
                    <div class="summary-label">Sous-total</div>
                    <div class="summary-value">${total}</div>
                </div>
                <div class="summary-row">
                    <div class="summary-label">Livraison</div>
                    <div class="summary-value">Calculée à l'étape suivante</div>
                </div>
                <div class="summary-row total-row">
                    <div class="summary-label">Total</div>
                    <div class="summary-value">${total}</div>
                </div>
                <button id="clear-cart" class="btn btn-outline btn-danger">Vider le panier</button>
            </div>
        `;
        
        container.innerHTML = html;
        
        // Add event listeners
        document.querySelectorAll('.quantity-btn.minus').forEach(btn => {
            btn.addEventListener('click', function() {
                const productId = this.getAttribute('data-id');
                const input = document.querySelector(`.quantity-input[data-id="${productId}"]`);
                let value = parseInt(input.value);
                if (value > 1) {
                    value--;
                    input.value = value;
                    updateCartItem(productId, value);
                }
            });
        });
        
        document.querySelectorAll('.quantity-btn.plus').forEach(btn => {
            btn.addEventListener('click', function() {
                const productId = this.getAttribute('data-id');
                const input = document.querySelector(`.quantity-input[data-id="${productId}"]`);
                let value = parseInt(input.value);
                const max = parseInt(input.getAttribute('max'));
                if (value < max) {
                    value++;
                    input.value = value;
                    updateCartItem(productId, value);
                }
            });
        });
        
        document.querySelectorAll('.quantity-input').forEach(input => {
            input.addEventListener('change', function() {
                const productId = this.getAttribute('data-id');
                let value = parseInt(this.value);
                const max = parseInt(this.getAttribute('max'));
                
                if (isNaN(value) || value < 1) {
                    value = 1;
                } else if (value > max) {
                    value = max;
                }
                
                this.value = value;
                updateCartItem(productId, value);
            });
        });
        
        document.querySelectorAll('.remove-item').forEach(btn => {
            btn.addEventListener('click', function() {
                const productId = this.getAttribute('data-id');
                removeCartItem(productId);
            });
        });
        
        document.getElementById('clear-cart').addEventListener('click', clearCart);
    }
    
    function displayEmptyCart() {
        const container = document.getElementById('cart-content');
        container.innerHTML = `
            <div class="empty-cart">
                <div class="empty-cart-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <h2>Votre panier est vide</h2>
                <p>Découvrez nos produits et ajoutez-les à votre panier.</p>
                <a href="?route=home" class="btn btn-primary">Continuer vos achats</a>
            </div>
        `;
        document.getElementById('cart-actions').style.display = 'none';
    }
    
    async function updateCartItem(productId, quantity) {
        try {
            const result = await CartAPI.updateQuantity(productId, quantity);
            if (result.success) {
                updateCartDisplay(result.panier);
                showNotification('Panier mis à jour', 'success');
            } else {
                showNotification(result.message || 'Erreur lors de la mise à jour', 'error');
                loadCart(); // Reload cart to ensure consistency
            }
        } catch (error) {
            console.error('Error updating cart:', error);
            showNotification('Erreur lors de la mise à jour', 'error');
            loadCart(); // Reload cart to ensure consistency
        }
    }
    
    async function removeCartItem(productId) {
        try {
            const result = await CartAPI.removeItem(productId);
            if (result.success) {
                if (result.panier.contenu.length === 0) {
                    displayEmptyCart();
                } else {
                    updateCartDisplay(result.panier);
                }
                showNotification('Produit retiré du panier', 'success');
                updateCartCount();
            } else {
                showNotification(result.message || 'Erreur lors de la suppression', 'error');
            }
        } catch (error) {
            console.error('Error removing item:', error);
            showNotification('Erreur lors de la suppression', 'error');
        }
    }
    
    async function clearCart() {
        if (confirm('Êtes-vous sûr de vouloir vider votre panier ?')) {
            try {
                const result = await CartAPI.clear();
                if (result.success) {
                    displayEmptyCart();
                    showNotification('Panier vidé avec succès', 'success');
                    updateCartCount();
                } else {
                    showNotification(result.message || 'Erreur lors du vidage du panier', 'error');
                }
            } catch (error) {
                console.error('Error clearing cart:', error);
                showNotification('Erreur lors du vidage du panier', 'error');
            }
        }
    }
    
    function updateCartDisplay(cart) {
        const items = cart.contenu;
        const total = parseFloat(cart.total).toFixed(2) + ' €';
        
        // Update item totals
        items.forEach(item => {
            const itemRow = document.querySelector(`.cart-row[data-id="${item.id_produit}"]`);
            if (itemRow) {
                const totalCell = itemRow.querySelector('.total-cell');
                const itemTotal = parseFloat(item.prix_unitaire * item.quantite).toFixed(2) + ' €';
                totalCell.textContent = itemTotal;
            }
        });
        
        // Update cart total
        const totalValues = document.querySelectorAll('.summary-value');
        totalValues[0].textContent = total; // Subtotal
        totalValues[2].textContent = total; // Total
    }
    
    function updateCartCount() {
        CartAPI.getContents()
            .then(data => {
                if (data.success) {
                    const cartCountElement = document.querySelector('.cart-count');
                    if (cartCountElement) {
                        cartCountElement.textContent = data.panier.nombre_articles || 0;
                    }
                }
            })
            .catch(error => console.error('Error updating cart count:', error));
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
});
</script>