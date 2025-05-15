<main class="container">
    <div class="checkout-page">
        <h1 class="page-title">Finaliser votre commande</h1>
        
        <div id="checkout-content">
            <div class="loading-spinner">Chargement des informations...</div>
        </div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('DEBUG: DOMContentLoaded event fired - begin checkout page initialization');
    
    // Add a global error handler to catch unhandled errors
    window.onerror = function(message, source, lineno, colno, error) {
        console.error(`DEBUG: Unhandled error: ${message}`, error);
        document.getElementById('checkout-content').innerHTML = `
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i> Une erreur est survenue
                <p class="error-details">Détails: ${message}</p>
                <button class="btn btn-primary" onclick="location.reload()">Réessayer</button>
            </div>
        `;
        return true; 
    };
    
    
    const safetyTimeout = setTimeout(() => {
        console.error('DEBUG: Safety timeout triggered - checkout loading took too long');
        document.getElementById('checkout-content').innerHTML = `
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i> Le chargement a pris trop de temps
                <p class="error-details">Veuillez réessayer ou contacter le support si le problème persiste.</p>
                <button class="btn btn-primary" onclick="location.reload()">Réessayer</button>
            </div>
        `;
    }, 30000); // 30 seconds timeout
    
    // Call loadCheckout and clear the safety timeout if it completes
    loadCheckout().finally(() => {
        console.log('DEBUG: loadCheckout finished (success or failure)');
        clearTimeout(safetyTimeout);
    });
    
    async function loadCheckout() {
        console.log('DEBUG: Starting checkout process');
        try {
            // Show more detailed loading message
            document.getElementById('checkout-content').innerHTML = `
                <div class="loading-spinner">
                    <p>Chargement des informations...</p>
                    <p class="loading-details">Récupération du panier</p>
                    <div id="debug-status">Préparation de la requête...</div>
                </div>
            `;
            
            const updateDebugStatus = (message) => {
                console.log(`DEBUG: ${message}`);
                const statusElem = document.getElementById('debug-status');
                if (statusElem) {
                    statusElem.textContent = message;
                }
            };
            
            // Get cart contents first
            updateDebugStatus('Tentative de récupération du panier...');
            let cartData;
            try {
                cartData = await CartAPI.getContents();
                updateDebugStatus('Panier récupéré avec succès');
                console.log('DEBUG: Cart data received', cartData);
            } catch (cartError) {
                updateDebugStatus('Erreur lors de la récupération du panier');
                console.error('DEBUG: Cart data fetch error:', cartError);
                throw new Error(`Erreur panier: ${cartError.message}`);
            }
            
            if (!cartData.success) {
                updateDebugStatus('Le panier n\'a pas pu être récupéré');
                if (cartData.message === "Utilisateur non connecté.") {
                    document.getElementById('checkout-content').innerHTML = `
                        <div class="auth-message">
                            <div class="auth-message-icon">
                                <i class="fas fa-user-lock"></i>
                            </div>
                            <h2>Authentification requise</h2>
                            <p>Veuillez vous connecter pour finaliser votre commande.</p>
                            <a href="?route=login" class="btn btn-primary">Se connecter</a>
                        </div>
                    `;
                } else {
                    document.getElementById('checkout-content').innerHTML = `
                        <div class="error-message">
                            <i class="fas fa-exclamation-circle"></i> ${cartData.message || 'Une erreur est survenue'}
                        </div>
                    `;
                }
                return;
            }
            
            if (!cartData.panier || !cartData.panier.contenu || cartData.panier.contenu.length === 0) {
                updateDebugStatus('Le panier est vide');
                document.getElementById('checkout-content').innerHTML = `
                    <div class="empty-cart">
                        <div class="empty-cart-icon">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <h2>Votre panier est vide</h2>
                        <p>Ajoutez des produits à votre panier avant de passer commande.</p>
                        <a href="?route=home" class="btn btn-primary">Voir les produits</a>
                    </div>
                `;
                return;
            }
            
            // Update loading message
            document.getElementById('checkout-content').innerHTML = `
                <div class="loading-spinner">
                    <p>Chargement des informations...</p>
                    <p class="loading-details">Récupération de vos informations</p>
                    <div id="debug-status">Tentative de récupération du profil...</div>
                </div>
            `;
            
            updateDebugStatus('Tentative de récupération des données utilisateur...');
            // Try to get user information but proceed even if it fails
            let userData = null;
            let userProfile = null;
            
            try {
                userData = await UserAPI.getProfile();
                updateDebugStatus('Profil utilisateur récupéré');
                console.log('DEBUG: User profile data received', userData);
                
                if (userData.success) {
                    userProfile = userData.utilisateur;
                } else {
                    updateDebugStatus('Erreur: ' + (userData.message || 'Profil non disponible'));
                    console.error('User profile error:', userData);
                }
            } catch (userError) {
                updateDebugStatus('Exception lors de la récupération du profil');
                console.error('DEBUG: Error fetching user data:', userError);
            }
            
            // Proceed with checkout with or without user data
            if (userProfile) {
                updateDebugStatus('Affichage du formulaire de commande avec les données utilisateur');
                console.log('DEBUG: Displaying checkout with user data:', userProfile);
                displayCheckout(cartData.panier, userProfile);
            } else {
                updateDebugStatus('Affichage du formulaire de commande sans données utilisateur');
                console.log('DEBUG: Using fallback with empty user data');
                displayCheckout(cartData.panier, {
                    nom: '',
                    prenom: '',
                    adresse: '',
                    telephone: ''
                });
            }
            
        } catch (error) {
            console.error('DEBUG: General error loading checkout:', error);
            document.getElementById('checkout-content').innerHTML = `
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i> Erreur lors du chargement des informations
                    <p class="error-details">${error.message || 'Erreur inconnue'}</p>
                    <button class="btn btn-primary" onclick="location.reload()">Réessayer</button>
                </div>
            `;
        }
    }
    
    function displayCheckout(cart, user) {
        const container = document.getElementById('checkout-content');
        const items = cart.contenu;
        const total = parseFloat(cart.total).toFixed(2);
        
        // Calculate shipping cost (free above 50€)
        const shippingCost = total > 50 ? 0 : 4.99;
        const totalWithShipping = (parseFloat(total) + shippingCost).toFixed(2);
        
        let html = `
            <div class="checkout-layout">
                <div class="checkout-details">
                    <div class="checkout-section">
                        <h2 class="section-title">Informations de livraison</h2>
                        <form id="shipping-form" class="checkout-form">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="nom" class="form-label">Nom</label>
                                    <input type="text" id="nom" class="form-control" required value="${user.nom || ''}">
                                </div>
                                
                                <div class="form-group">
                                    <label for="prenom" class="form-label">Prénom</label>
                                    <input type="text" id="prenom" class="form-control" required value="${user.prenom || ''}">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="adresse" class="form-label">Adresse</label>
                                <input type="text" id="adresse" class="form-control" required value="${user.adresse || ''}">
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="code_postal" class="form-label">Code postal</label>
                                    <input type="text" id="code_postal" class="form-control" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="ville" class="form-label">Ville</label>
                                    <input type="text" id="ville" class="form-control" required>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="telephone" class="form-label">Téléphone</label>
                                <input type="tel" id="telephone" class="form-control" required value="${user.telephone || ''}">
                            </div>
                        </form>
                    </div>
                    
                    <div class="checkout-section">
                        <h2 class="section-title">Méthode de paiement</h2>
                        <div class="payment-methods">
                            <div class="payment-method selected">
                                <input type="radio" id="payment-card" name="payment_method" value="card" checked>
                                <label for="payment-card">
                                    <i class="fas fa-credit-card"></i>
                                    <span>Carte bancaire</span>
                                </label>
                            </div>
                            
                            <div class="payment-method">
                                <input type="radio" id="payment-paypal" name="payment_method" value="paypal">
                                <label for="payment-paypal">
                                    <i class="fab fa-paypal"></i>
                                    <span>PayPal</span>
                                </label>
                            </div>
                        </div>
                        
                        <div class="payment-form" id="card-payment-form">
                            <div class="form-group">
                                <label for="card_number" class="form-label">Numéro de carte</label>
                                <input type="text" id="card_number" class="form-control" placeholder="1234 5678 9012 3456">
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="card_expiry" class="form-label">Date d'expiration</label>
                                    <input type="text" id="card_expiry" class="form-control" placeholder="MM/AA">
                                </div>
                                
                                <div class="form-group">
                                    <label for="card_cvv" class="form-label">CVV</label>
                                    <input type="text" id="card_cvv" class="form-control" placeholder="123">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="card_name" class="form-label">Nom sur la carte</label>
                                <input type="text" id="card_name" class="form-control" placeholder="JOHN DOE">
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="checkout-summary">
                    <h2 class="section-title">Récapitulatif de la commande</h2>
                    
                    <div class="cart-items">
        `;
        
        // Add cart items
        items.forEach(item => {
            const price = parseFloat(item.prix_unitaire).toFixed(2) + ' €';
            const itemTotal = parseFloat(item.prix_unitaire * item.quantite).toFixed(2) + ' €';
            
            html += `
                <div class="cart-item">
                    <div class="item-image">
                        <img src="${item.image_url || '/assets/images/placeholder.jpg'}" alt="${item.nom}">
                        <span class="item-quantity">${item.quantite}</span>
                    </div>
                    <div class="item-details">
                        <div class="item-name">${item.nom}</div>
                        <div class="item-price">${price}</div>
                    </div>
                    <div class="item-total">${itemTotal}</div>
                </div>
            `;
        });
        
        html += `
                    </div>
                    
                    <div class="cart-totals">
                        <div class="total-row">
                            <div class="total-label">Sous-total</div>
                            <div class="total-value">${total} €</div>
                        </div>
                        <div class="total-row">
                            <div class="total-label">Frais de livraison</div>
                            <div class="total-value">${shippingCost.toFixed(2)} €</div>
                        </div>
                        <div class="total-row grand-total">
                            <div class="total-label">Total</div>
                            <div class="total-value">${totalWithShipping} €</div>
                        </div>
                    </div>
                    
                    <div class="checkout-actions">
                        <button id="place-order" class="btn btn-primary btn-block">
                            <i class="fas fa-check"></i> Passer la commande
                        </button>
                        <a href="?route=cart" class="btn btn-outline btn-block">
                            <i class="fas fa-arrow-left"></i> Retour au panier
                        </a>
                    </div>
                </div>
            </div>
        `;
        
        container.innerHTML = html;
        
        // Add event listeners
        document.getElementById('place-order').addEventListener('click', placeOrder);
        
        // Payment method switching
        const paymentMethods = document.querySelectorAll('.payment-method');
        paymentMethods.forEach(method => {
            method.addEventListener('click', function() {
                paymentMethods.forEach(m => m.classList.remove('selected'));
                this.classList.add('selected');
                this.querySelector('input').checked = true;
            });
        });
    }
    
    async function placeOrder() {
        console.log('DEBUG [placeOrder] - Starting order placement process');
        
        // Validate form
        const shippingForm = document.getElementById('shipping-form');
        
        if (!shippingForm.checkValidity()) {
            console.log('DEBUG [placeOrder] - Form validation failed');
            shippingForm.reportValidity();
            return;
        }
        
        console.log('DEBUG [placeOrder] - Form validation passed');
        
        // Get shipping information
        const nom = document.getElementById('nom').value;
        const prenom = document.getElementById('prenom').value;
        const adresse = document.getElementById('adresse').value;
        const code_postal = document.getElementById('code_postal').value;
        const ville = document.getElementById('ville').value;
        const telephone = document.getElementById('telephone').value;
        
        // Get payment method
        const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
        console.log(`DEBUG [placeOrder] - Selected payment method: ${paymentMethod}`);
        
        // Get payment details if credit card is selected
        let paymentDetails = {};
        if (paymentMethod === 'card') {
            const cardNumber = document.getElementById('card_number').value;
            const cardExpiry = document.getElementById('card_expiry').value;
            const cardCVV = document.getElementById('card_cvv').value;
            const cardName = document.getElementById('card_name').value;
            
            console.log('DEBUG [placeOrder] - Validating card payment details');
            // Validate payment information
            if (!cardNumber || !cardExpiry || !cardCVV || !cardName) {
                console.log('DEBUG [placeOrder] - Card payment validation failed');
                showNotification('Veuillez remplir tous les champs de paiement', 'error');
                return;
            }
            
            paymentDetails = {
                card_number: cardNumber,
                card_expiry: cardExpiry,
                card_cvv: cardCVV,
                card_name: cardName
            };
            console.log('DEBUG [placeOrder] - Card payment details validated');
        }
        
        // Prepare order data
        const orderData = {
            nom,
            prenom,
            adresse,
            code_postal,
            ville,
            telephone,
            payment_method: paymentMethod,
            ...paymentDetails
        };
        
        console.log('DEBUG [placeOrder] - Order data prepared:', orderData);
        
        // Show loading state
        const orderButton = document.getElementById('place-order');
        const originalButtonText = orderButton.innerHTML;
        orderButton.disabled = true;
        orderButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Traitement du paiement en cours...';
        console.log('DEBUG [placeOrder] - Order button disabled, showing loading state');
        
        try {
            console.log('DEBUG [placeOrder] - Making API call to create order');
            const result = await OrderAPI.create(orderData);
            console.log('DEBUG [placeOrder] - Order creation API call completed:', result);
            
            if (result.success) {
                console.log('DEBUG [placeOrder] - Order created successfully');
                // Show success message
                document.getElementById('checkout-content').innerHTML = `
                    <div class="order-success">
                        <div class="success-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <h2>Commande confirmée</h2>
                        <p>Votre commande #${result.id_commande} a été traitée avec succès.</p>
                        <p>Un email de confirmation vous a été envoyé.</p>
                        <div class="success-actions">
                            <a href="?route=orders" class="btn btn-primary">Voir mes commandes</a>
                            <a href="?route=home" class="btn btn-outline">Continuer mes achats</a>
                        </div>
                    </div>
                `;
                
                // Update cart badge
                const cartCountElement = document.querySelector('.cart-count');
                if (cartCountElement) {
                    cartCountElement.textContent = '0';
                }
            } else {
                console.error('DEBUG [placeOrder] - Order creation failed:', result.message);
                showNotification(result.message || 'Erreur lors de la création de la commande', 'error');
                orderButton.disabled = false;
                orderButton.innerHTML = originalButtonText;
            }
        } catch (error) {
            console.error('DEBUG [placeOrder] - Exception during order creation:', error);
            showNotification('Erreur lors du traitement du paiement', 'error');
            orderButton.disabled = false;
            orderButton.innerHTML = originalButtonText;
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
});
</script>