<main class="container">
    <div class="checkout-page">
        <h1 class="page-title">Finaliser votre commande</h1>
        
        <div class="checkout-container">
            <div class="checkout-form-container">
                <h2 class="checkout-section-title">Informations de livraison</h2>
                
                <form id="checkout-form" class="checkout-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="prenom">Prénom <span class="required">*</span></label>
                            <input type="text" id="prenom" name="prenom" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="nom">Nom <span class="required">*</span></label>
                            <input type="text" id="nom" name="nom" class="form-control" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email <span class="required">*</span></label>
                        <input type="email" id="email" name="email" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="telephone">Téléphone</label>
                        <input type="tel" id="telephone" name="telephone" class="form-control">
                    </div>
                    
                    <div class="form-group">
                        <label for="adresse">Adresse <span class="required">*</span></label>
                        <input type="text" id="adresse" name="adresse" class="form-control" required>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="code_postal">Code postal <span class="required">*</span></label>
                            <input type="text" id="code_postal" name="code_postal" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="ville">Ville <span class="required">*</span></label>
                            <input type="text" id="ville" name="ville" class="form-control" required>
                        </div>
                    </div>
                    
                    <h2 class="checkout-section-title">Mode de paiement</h2>
                    
                    <div class="payment-methods">
                        <div class="payment-method">
                            <input type="radio" id="payment-card" name="methode_paiement" value="card" checked>
                            <label for="payment-card">
                                <span class="payment-icon"><i class="fas fa-credit-card"></i></span>
                                <span class="payment-label">Carte bancaire</span>
                            </label>
                        </div>
                        
                        <div class="payment-method">
                            <input type="radio" id="payment-paypal" name="methode_paiement" value="paypal">
                            <label for="payment-paypal">
                                <span class="payment-icon"><i class="fab fa-paypal"></i></span>
                                <span class="payment-label">PayPal</span>
                            </label>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary btn-block checkout-btn">
                            Passer la commande
                        </button>
                    </div>
                </form>
            </div>
            
            <div class="checkout-summary">
                <div class="checkout-summary-header">
                    <h2 class="checkout-section-title">Récapitulatif de la commande</h2>
                </div>
                
                <div class="checkout-items" id="checkout-items">
                    <!-- Cart items will be loaded here -->
                    <div class="checkout-loading">
                        <div class="spinner"></div>
                        <p>Chargement des articles...</p>
                    </div>
                </div>
                
                <div class="checkout-summary-footer">
                    <div class="checkout-totals">
                        <div class="checkout-total-row">
                            <span>Total articles:</span>
                            <span id="checkout-subtotal">0.00 €</span>
                        </div>
                        <div class="checkout-total-row">
                            <span>Livraison:</span>
                            <span id="checkout-shipping">Gratuit</span>
                        </div>
                        <div class="checkout-total-row checkout-total">
                            <span>Total:</span>
                            <span id="checkout-total">0.00 €</span>
                        </div>
                    </div>
                    
                    <div class="checkout-back-link">
                        <a href="?route=cart">&larr; Retour au panier</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<style>
/* Checkout Page Styles */
.checkout-page {
    padding: 2rem 0;
}

.checkout-layout {
    display: grid;
    grid-template-columns: 3fr 2fr;
    gap: 2rem;
    margin-top: 2rem;
}

/* Checkout Sections */
.checkout-section {
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
    padding: 1.5rem;
    margin-bottom: 1.5rem;
}

.section-title {
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 1.5rem;
    color: var(--dark-color);
}

/* Form Styles */
.checkout-form {
    margin-top: 1rem;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
    margin-bottom: 1rem;
}

.form-group {
    margin-bottom: 1rem;
}

.form-label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
    color: var(--dark-color);
}

.form-control {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 1px solid var(--light-grey);
    border-radius: 4px;
    transition: border-color 0.2s ease;
}

.form-control:focus {
    border-color: var(--primary-color);
    outline: none;
}

/* Payment Methods */
.payment-methods {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.payment-method {
    position: relative;
    border: 2px solid var(--light-grey);
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s ease;
}

.payment-method input {
    position: absolute;
    opacity: 0;
    width: 0;
    height: 0;
}

.payment-method label {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 1.25rem;
    cursor: pointer;
}

.payment-method i {
    font-size: 2rem;
    margin-bottom: 0.5rem;
    color: var(--dark-color);
}

.payment-method.selected {
    border-color: var(--primary-color);
    background-color: rgba(255, 107, 107, 0.05);
}

.payment-method.selected i {
    color: var(--primary-color);
}

/* Cart Summary */
.checkout-summary {
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
    padding: 1.5rem;
    position: sticky;
    top: 2rem;
}

.cart-items {
    max-height: 300px;
    overflow-y: auto;
    margin-bottom: 1.5rem;
    padding-right: 0.5rem;
}

.cart-item {
    display: flex;
    align-items: center;
    margin-bottom: 1rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid var(--light-grey);
}

.cart-item:last-child {
    margin-bottom: 0;
    padding-bottom: 0;
    border-bottom: none;
}

.item-image {
    width: 70px;
    height: 70px;
    border-radius: 4px;
    overflow: hidden;
    position: relative;
    margin-right: 1rem;
}

.item-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.item-quantity {
    position: absolute;
    bottom: 0;
    right: 0;
    background-color: var(--primary-color);
    color: white;
    width: 22px;
    height: 22px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    font-weight: 600;
    border-radius: 4px 0 4px 0;
}

.item-details {
    flex: 1;
}

.item-name {
    font-weight: 600;
    margin-bottom: 0.25rem;
}

.item-price {
    font-size: 0.9rem;
    color: var(--grey-color);
}

.item-total {
    font-weight: 600;
}

.cart-totals {
    border-top: 1px solid var(--light-grey);
    padding-top: 1.5rem;
    margin-bottom: 1.5rem;
}

.total-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 0.75rem;
}

.total-label {
    color: var(--grey-color);
}

.grand-total {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--dark-color);
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid var(--light-grey);
}

.grand-total .total-value {
    color: var(--primary-color);
}

/* Checkout Actions */
.checkout-actions {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.btn-block {
    width: 100%;
}

/* Success Page */
.order-success {
    text-align: center;
    padding: 3rem 2rem;
    max-width: 600px;
    margin: 0 auto;
}

.success-icon {
    font-size: 4rem;
    color: #28a745;
    margin-bottom: 1.5rem;
}

.success-actions {
    margin-top: 2rem;
    display: flex;
    justify-content: center;
    gap: 1rem;
}

/* Auth and Error Messages */
.auth-message, .error-message, .empty-cart {
    text-align: center;
    padding: 3rem 2rem;
    max-width: 600px;
    margin: 0 auto;
}

.auth-message-icon, .empty-cart-icon {
    font-size: 4rem;
    color: var(--grey-color);
    margin-bottom: 1.5rem;
}

/* Loading Spinner */
.loading-spinner {
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 300px;
    color: var(--grey-color);
}

/* Responsive Adjustments */
@media (max-width: 992px) {
    .checkout-layout {
        grid-template-columns: 1fr;
    }
    
    .checkout-summary {
        position: static;
    }
}

@media (max-width: 768px) {
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .payment-methods {
        grid-template-columns: 1fr;
    }
}

.checkout-page {
    padding: 2rem;
    max-width: 1200px;
    margin: 0 auto;
}

.page-title {
    margin-bottom: 2rem;
    font-size: 1.8rem;
    color: var(--dark-color);
}

.checkout-container {
    display: grid;
    grid-template-columns: 1fr 400px;
    gap: 2rem;
}

.checkout-form-container,
.checkout-summary {
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    overflow: hidden;
}

.checkout-section-title {
    font-size: 1.25rem;
    margin-bottom: 1.5rem;
    color: var(--dark-color);
    font-weight: 600;
}

.checkout-form-container {
    padding: 2rem;
}

.checkout-form {
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.form-group label {
    font-weight: 500;
    font-size: 0.9rem;
    color: var(--dark-color);
}

.form-control {
    padding: 0.75rem 1rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    transition: border-color 0.2s ease;
    font-size: 1rem;
}

.form-control:focus {
    border-color: var(--primary-color);
    outline: none;
    box-shadow: 0 0 0 3px rgba(var(--primary-rgb), 0.2);
}

.required {
    color: #e74c3c;
}

.payment-methods {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.payment-method {
    position: relative;
}

.payment-method input[type="radio"] {
    position: absolute;
    opacity: 0;
    height: 0;
    width: 0;
}

.payment-method label {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.2s ease;
}

.payment-method input[type="radio"]:checked + label {
    border-color: var(--primary-color);
    background-color: rgba(var(--primary-rgb), 0.05);
}

.payment-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    background-color: rgba(var(--primary-rgb), 0.1);
    border-radius: 50%;
    color: var(--primary-color);
}

.payment-label {
    font-weight: 500;
}

.checkout-btn {
    padding: 1rem;
    font-size: 1rem;
    font-weight: 600;
}

.checkout-summary {
    display: flex;
    flex-direction: column;
}

.checkout-summary-header {
    padding: 1.5rem;
    border-bottom: 1px solid #eee;
}

.checkout-items {
    flex: 1;
    max-height: 400px;
    overflow-y: auto;
    padding: 0 1.5rem;
}

.checkout-item {
    display: flex;
    gap: 1rem;
    padding: 1rem 0;
    border-bottom: 1px solid #eee;
}

.checkout-item:last-child {
    border-bottom: none;
}

.checkout-item-image {
    width: 60px;
    height: 60px;
    border-radius: 4px;
    overflow: hidden;
}

.checkout-item-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.checkout-item-details {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.checkout-item-name {
    font-weight: 500;
    color: var(--dark-color);
}

.checkout-item-price {
    color: var(--grey-color);
    font-size: 0.9rem;
}

.checkout-item-quantity {
    color: var(--grey-color);
    font-size: 0.9rem;
}

.checkout-item-total {
    font-weight: 600;
    color: var(--dark-color);
    text-align: right;
}

.checkout-loading {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 2rem 0;
    color: var(--grey-color);
}

.spinner {
    width: 40px;
    height: 40px;
    border: 3px solid rgba(var(--primary-rgb), 0.2);
    border-radius: 50%;
    border-top-color: var(--primary-color);
    animation: spin 1s linear infinite;
    margin-bottom: 1rem;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

.checkout-summary-footer {
    padding: 1.5rem;
    border-top: 1px solid #eee;
}

.checkout-totals {
    margin-bottom: 1.5rem;
}

.checkout-total-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 0.75rem;
    color: var(--grey-color);
}

.checkout-total {
    font-weight: 700;
    color: var(--dark-color);
    font-size: 1.1rem;
    margin-top: 0.5rem;
    padding-top: 0.75rem;
    border-top: 1px solid #eee;
}

.checkout-back-link {
    text-align: center;
}

.checkout-back-link a {
    color: var(--primary-color);
    text-decoration: none;
    font-weight: 500;
}

.checkout-back-link a:hover {
    text-decoration: underline;
}

/* Order Success */
.order-success {
    text-align: center;
    padding: 3rem 2rem;
    max-width: 600px;
    margin: 2rem auto;
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.success-icon {
    width: 80px;
    height: 80px;
    margin: 0 auto 2rem;
    background-color: #28a745;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 2rem;
}

.success-title {
    font-size: 1.8rem;
    margin-bottom: 1rem;
    color: var(--dark-color);
}

.success-message {
    color: var(--grey-color);
    margin-bottom: 2rem;
}

.order-details {
    text-align: left;
    background-color: #f9fafb;
    padding: 1.5rem;
    border-radius: 4px;
    margin-bottom: 2rem;
}

.order-detail {
    display: flex;
    justify-content: space-between;
    margin-bottom: 0.75rem;
}

.order-detail:last-child {
    margin-bottom: 0;
}

.order-detail-label {
    font-weight: 500;
    color: var(--grey-color);
}

.order-detail-value {
    font-weight: 600;
    color: var(--dark-color);
}

.success-actions {
    display: flex;
    justify-content: center;
    gap: 1rem;
}

/* Responsive */
@media (max-width: 992px) {
    .checkout-container {
        grid-template-columns: 1fr;
    }
    
    .checkout-summary {
        order: -1;
    }
}

@media (max-width: 768px) {
    .checkout-page {
        padding: 1rem;
    }
    
    .checkout-form-container,
    .checkout-summary {
        border-radius: 0;
        box-shadow: none;
    }
    
    .checkout-form-container {
        padding: 1.5rem;
    }
    
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .checkout-btn {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        border-radius: 0;
        z-index: 100;
    }
    
    .checkout-back-link {
        margin-bottom: 4rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', async function() {
    // Initialize variables
    let cartItems = [];
    let cartTotal = 0;
    
    // DOM elements
    const checkoutItemsContainer = document.getElementById('checkout-items');
    const subtotalElement = document.getElementById('checkout-subtotal');
    const totalElement = document.getElementById('checkout-total');
    const checkoutForm = document.getElementById('checkout-form');
    
    // Check if user is logged in
    const userData = await UserAPI.getProfile();
    if (userData.success && userData.user) {
        // Pre-fill form with user data
        document.getElementById('prenom').value = userData.user.prenom || '';
        document.getElementById('nom').value = userData.user.nom || '';
        document.getElementById('email').value = userData.user.email || '';
        document.getElementById('telephone').value = userData.user.telephone || '';
        document.getElementById('adresse').value = userData.user.adresse || '';
        document.getElementById('code_postal').value = userData.user.code_postal || '';
        document.getElementById('ville').value = userData.user.ville || '';
    }
    
    // Load cart items
    await loadCartItems();
    
    // Add event listeners
    checkoutForm.addEventListener('submit', handleCheckout);
    
    async function loadCartItems() {
        try {
            const response = await CartAPI.getCart();
            
            if (response.success) {
                cartItems = response.panier || [];
                cartTotal = response.total || 0;
                
                renderCartItems();
                updateTotals();
            } else {
                checkoutItemsContainer.innerHTML = `
                    <div class="empty-checkout">
                        <p>Votre panier est vide.</p>
                        <a href="?route=products" class="btn btn-outline">Continuer vos achats</a>
                    </div>
                `;
            }
        } catch (error) {
            console.error('Error loading cart items:', error);
            checkoutItemsContainer.innerHTML = `
                <div class="empty-checkout">
                    <p>Une erreur est survenue lors du chargement de votre panier.</p>
                    <a href="?route=cart" class="btn btn-outline">Retour au panier</a>
                </div>
            `;
        }
    }
    
    function renderCartItems() {
        if (!cartItems.length) {
            checkoutItemsContainer.innerHTML = `
                <div class="empty-checkout">
                    <p>Votre panier est vide.</p>
                    <a href="?route=products" class="btn btn-outline">Continuer vos achats</a>
                </div>
            `;
            return;
        }
        
        let html = '';
        
        cartItems.forEach(item => {
            const itemTotal = (parseFloat(item.prix) * item.quantite).toFixed(2) + ' €';
            
            html += `
                <div class="checkout-item">
                    <div class="checkout-item-image">
                        <img src="${item.image_url || '/assets/images/placeholder.png'}" alt="${item.nom}" onerror="this.src='/assets/images/placeholder.png'">
                    </div>
                    <div class="checkout-item-details">
                        <div class="checkout-item-name">${item.nom}</div>
                        <div class="checkout-item-price">${parseFloat(item.prix).toFixed(2)} €</div>
                        <div class="checkout-item-quantity">Quantité: ${item.quantite}</div>
                    </div>
                    <div class="checkout-item-total">${itemTotal}</div>
                </div>
            `;
        });
        
        checkoutItemsContainer.innerHTML = html;
    }
    
    function updateTotals() {
        subtotalElement.textContent = cartTotal.toFixed(2) + ' €';
        totalElement.textContent = cartTotal.toFixed(2) + ' €';
    }
    
    async function handleCheckout(e) {
        e.preventDefault();
        
        // Disable submit button to prevent multiple submissions
        const submitButton = checkoutForm.querySelector('button[type="submit"]');
        submitButton.disabled = true;
        submitButton.innerHTML = `
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            Traitement en cours...
        `;
        
        // Get form data
        const formData = new FormData(checkoutForm);
        const orderData = {
            prenom: formData.get('prenom'),
            nom: formData.get('nom'),
            email: formData.get('email'),
            telephone: formData.get('telephone') || null,
            adresse: formData.get('adresse'),
            code_postal: formData.get('code_postal'),
            ville: formData.get('ville'),
            methode_paiement: formData.get('methode_paiement')
        };
        
        try {
            // Create order
            const response = await OrderAPI.createOrder(orderData);
            
            if (response.success) {
                // Show success page
                showOrderSuccess(response.commande);
            } else {
                // Show error message
                showNotification(response.message || 'Une erreur est survenue lors de la création de la commande.', 'error');
                submitButton.disabled = false;
                submitButton.textContent = 'Passer la commande';
            }
        } catch (error) {
            console.error('Error creating order:', error);
            showNotification('Une erreur est survenue lors de la création de la commande.', 'error');
            submitButton.disabled = false;
            submitButton.textContent = 'Passer la commande';
        }
    }
    
    function showOrderSuccess(order) {
        // Update page content with order success message
        const checkoutPage = document.querySelector('.checkout-page');
        
        const date = new Date(order.date_commande).toLocaleDateString('fr-FR', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        });
        
        checkoutPage.innerHTML = `
            <div class="order-success">
                <div class="success-icon">
                    <i class="fas fa-check"></i>
                </div>
                <h1 class="success-title">Commande confirmée</h1>
                <p class="success-message">Merci pour votre commande. Nous vous avons envoyé un e-mail de confirmation avec les détails de votre commande.</p>
                
                <div class="order-details">
                    <div class="order-detail">
                        <span class="order-detail-label">N° de commande:</span>
                        <span class="order-detail-value">#${order.id_commande}</span>
                    </div>
                    <div class="order-detail">
                        <span class="order-detail-label">Date:</span>
                        <span class="order-detail-value">${date}</span>
                    </div>
                    <div class="order-detail">
                        <span class="order-detail-label">Méthode de paiement:</span>
                        <span class="order-detail-value">${getPaymentMethodLabel(order.methode_paiement)}</span>
                    </div>
                    <div class="order-detail">
                        <span class="order-detail-label">Total:</span>
                        <span class="order-detail-value">${parseFloat(order.total).toFixed(2)} €</span>
                    </div>
                </div>
                
                <div class="success-actions">
                    <a href="?route=orders/history" class="btn btn-outline">Voir mes commandes</a>
                    <a href="?route=products" class="btn btn-primary">Continuer mes achats</a>
                </div>
            </div>
        `;
        
        // Scroll to top
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
    
    function getPaymentMethodLabel(method) {
        switch(method) {
            case 'card': return 'Carte bancaire';
            case 'paypal': return 'PayPal';
            default: return method || 'Non spécifiée';
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
});
</script>