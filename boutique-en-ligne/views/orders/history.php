<main class="container">
    <div class="orders-page">
        <h1 class="page-title">Mes Commandes</h1>
        
        <div id="orders-content">
            <div class="loading-spinner">Chargement des commandes...</div>
        </div>
    </div>
</main>

<style>
/* Orders Page Styles */
.orders-page {
    padding: 2rem 0;
}

.order-sections {
    margin-top: 2rem;
}

.order-section {
    margin-bottom: 3rem;
}

.section-title {
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 1.5rem;
    color: var(--dark-color);
    display: flex;
    align-items: center;
}

.section-title i {
    margin-right: 0.75rem;
    color: var(--primary-color);
}

.no-orders {
    background-color: #fff;
    border-radius: 8px;
    padding: 2rem;
    text-align: center;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
}

.no-orders i {
    font-size: 3rem;
    color: var(--light-grey);
    margin-bottom: 1rem;
}

/* Order Cards */
.order-cards {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1.5rem;
}

.order-card {
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
    overflow: hidden;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.order-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
}

.order-header {
    padding: 1.25rem;
    border-bottom: 1px solid var(--light-grey);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.order-id {
    font-weight: 700;
    font-size: 1.1rem;
}

.order-date {
    color: var(--grey-color);
    font-size: 0.9rem;
}

.order-status {
    display: inline-flex;
    align-items: center;
    padding: 0.4rem 0.75rem;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
}

.status-en_attente {
    background-color: rgba(255, 193, 7, 0.15);
    color: #e5a500;
}

.status-validee {
    background-color: rgba(13, 110, 253, 0.15);
    color: #0a58ca;
}

.status-expediee {
    background-color: rgba(111, 66, 193, 0.15);
    color: #6f42c1;
}

.status-livree {
    background-color: rgba(25, 135, 84, 0.15);
    color: #146c43;
}

.status-annulee {
    background-color: rgba(220, 53, 69, 0.15);
    color: #b02a37;
}

.order-content {
    padding: 1.25rem;
}

.order-products {
    margin-bottom: 1.25rem;
}

.product-count {
    font-size: 0.95rem;
    margin-bottom: 0.75rem;
    color: var(--grey-color);
}

.product-preview {
    display: flex;
    margin-bottom: 1rem;
}

.product-image {
    width: 60px;
    height: 60px;
    border-radius: 4px;
    overflow: hidden;
    margin-right: 0.75rem;
}

.product-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.product-details {
    flex: 1;
}

.product-name {
    font-weight: 600;
    margin-bottom: 0.25rem;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
}

.product-price {
    font-size: 0.9rem;
    color: var(--grey-color);
}

.products-extra {
    color: var(--grey-color);
    font-size: 0.9rem;
    margin-top: 0.5rem;
    text-align: center;
}

.order-totals {
    padding-top: 1rem;
    border-top: 1px solid var(--light-grey);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.order-total-label {
    font-weight: 500;
}

.order-total-value {
    font-weight: 700;
    color: var(--primary-color);
    font-size: 1.2rem;
}

.order-actions {
    padding: 1.25rem;
    border-top: 1px solid var(--light-grey);
    display: flex;
    justify-content: space-between;
}

/* Order Details Modal */
.order-details-modal {
    max-width: 800px;
    width: 100%;
}

.detail-sections {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
    margin-bottom: 1.5rem;
}

.detail-section {
    background-color: var(--lighter-grey);
    border-radius: 8px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
}

.detail-grid {
    display: grid;
    gap: 0.75rem;
}

.detail-row {
    display: grid;
    grid-template-columns: 40% 60%;
    align-items: center;
}

.detail-label {
    font-weight: 500;
    color: var(--grey-color);
}

.detail-value {
    font-weight: 500;
}

.products-section {
    grid-column: 1 / -1;
}

.order-items-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 1rem;
}

.order-items-table th {
    background-color: rgba(0, 0, 0, 0.03);
    padding: 0.75rem;
    text-align: left;
    font-weight: 600;
    color: var(--dark-color);
}

.order-items-table td {
    padding: 0.75rem;
    border-bottom: 1px solid var(--light-grey);
}

.product-cell {
    display: flex;
    align-items: center;
}

.auth-message {
    text-align: center;
    padding: 3rem 2rem;
    max-width: 600px;
    margin: 0 auto;
}

.auth-message-icon {
    font-size: 4rem;
    color: var(--grey-color);
    margin-bottom: 1.5rem;
}

/* Responsive Adjustments */
@media (max-width: 992px) {
    .detail-sections {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .order-cards {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    loadOrders();
    
    async function loadOrders() {
        try {
            const userData = await UserAPI.getProfile();
            
            if (!userData.success) {
                document.getElementById('orders-content').innerHTML = `
                    <div class="auth-message">
                        <div class="auth-message-icon">
                            <i class="fas fa-user-lock"></i>
                        </div>
                        <h2>Authentification requise</h2>
                        <p>Veuillez vous connecter pour accéder à vos commandes.</p>
                        <a href="?route=login" class="btn btn-primary">Se connecter</a>
                    </div>
                `;
                return;
            }
            
            const ordersData = await OrderAPI.getHistory();
            
            if (!ordersData.success) {
                document.getElementById('orders-content').innerHTML = `
                    <div class="error-message">
                        <i class="fas fa-exclamation-circle"></i> ${ordersData.message || 'Une erreur est survenue lors du chargement des commandes.'}
                    </div>
                `;
                return;
            }
            
            displayOrderHistory(ordersData.historique);
            
        } catch (error) {
            console.error('Error loading orders:', error);
            document.getElementById('orders-content').innerHTML = '<div class="error-message"><i class="fas fa-exclamation-circle"></i> Erreur lors du chargement des commandes</div>';
        }
    }
    
    function displayOrderHistory(historique) {
        const container = document.getElementById('orders-content');
        const activesCommandes = historique.commandes_actives || [];
        const annuleesCommandes = historique.commandes_annulees || [];
        
        if (activesCommandes.length === 0 && annuleesCommandes.length === 0) {
            container.innerHTML = `
                <div class="no-orders">
                    <i class="fas fa-shopping-bag"></i>
                    <h2>Aucune commande</h2>
                    <p>Vous n'avez pas encore passé de commande.</p>
                    <a href="?route=shop" class="btn btn-primary">Découvrir nos produits</a>
                </div>
            `;
            return;
        }
        
        let html = '<div class="order-sections">';
        
        // Active orders section
        if (activesCommandes.length > 0) {
            html += `
                <div class="order-section active-orders">
                    <h2 class="section-title"><i class="fas fa-shopping-bag"></i> Commandes en cours</h2>
                    <div class="order-cards">
            `;
            
            activesCommandes.forEach(commande => {
                html += createOrderCard(commande);
            });
            
            html += `
                    </div>
                </div>
            `;
        }
        
        // Cancelled orders section
        if (annuleesCommandes.length > 0) {
            html += `
                <div class="order-section cancelled-orders">
                    <h2 class="section-title"><i class="fas fa-ban"></i> Commandes annulées</h2>
                    <div class="order-cards">
            `;
            
            annuleesCommandes.forEach(commande => {
                html += createOrderCard(commande);
            });
            
            html += `
                    </div>
                </div>
            `;
        }
        
        html += '</div>';
        
        container.innerHTML = html;
        
        // Add event listeners to view order details
        document.querySelectorAll('.view-order-details').forEach(button => {
            button.addEventListener('click', function() {
                const orderId = this.getAttribute('data-id');
                viewOrderDetails(orderId);
            });
        });
        
        // Add event listeners to cancel orders
        document.querySelectorAll('.cancel-order').forEach(button => {
            button.addEventListener('click', function() {
                const orderId = this.getAttribute('data-id');
                showCancelConfirmation(orderId);
            });
        });
    }
    
    function createOrderCard(commande) {
        const date = new Date(commande.date_commande).toLocaleDateString('fr-FR', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        });
        
        const total = parseFloat(commande.total).toFixed(2) + ' €';
        
        let statusClass = '';
        let statusText = '';
        
        switch(commande.statut) {
            case 'en_attente':
                statusClass = 'status-en_attente';
                statusText = 'En attente';
                break;
            case 'validee':
                statusClass = 'status-validee';
                statusText = 'Validée';
                break;
            case 'expediee':
                statusClass = 'status-expediee';
                statusText = 'Expédiée';
                break;
            case 'livree':
                statusClass = 'status-livree';
                statusText = 'Livrée';
                break;
            case 'annulee':
                statusClass = 'status-annulee';
                statusText = 'Annulée';
                break;
            default:
                statusClass = 'status-default';
                statusText = commande.statut;
        }
        
        // Get the first two products to display
        const products = commande.produits || [];
        let productPreviewsHTML = '';
        
        if (products.length > 0) {
            const previewCount = Math.min(2, products.length);
            
            for (let i = 0; i < previewCount; i++) {
                const product = products[i];
                const price = parseFloat(product.prix_unitaire).toFixed(2) + ' €';
                
                productPreviewsHTML += `
                    <div class="product-preview">
                        <div class="product-image">
                            <img src="${product.image_url || '/assets/images/placeholder.png'}" alt="${product.nom}" onerror="this.src='/assets/images/placeholder.png'">
                        </div>
                        <div class="product-details">
                            <div class="product-name">${product.nom}</div>
                            <div class="product-price">${price} x ${product.quantite}</div>
                        </div>
                    </div>
                `;
            }
            
            if (products.length > previewCount) {
                productPreviewsHTML += `
                    <div class="products-extra">
                        Et ${products.length - previewCount} autre(s) produit(s)
                    </div>
                `;
            }
        } else {
            productPreviewsHTML = '<div class="no-products">Aucun produit disponible</div>';
        }
        
        let html = `
            <div class="order-card">
                <div class="order-header">
                    <div class="order-id">#${commande.id_commande}</div>
                    <div class="order-date">${date}</div>
                </div>
                <div class="order-status-bar">
                    <span class="order-status ${statusClass}">${statusText}</span>
                </div>
                <div class="order-content">
                    <div class="order-products">
                        <div class="product-count">${products.length} produit(s)</div>
                        ${productPreviewsHTML}
                    </div>
                    <div class="order-totals">
                        <div class="order-total-label">Total</div>
                        <div class="order-total-value">${total}</div>
                    </div>
                </div>
                <div class="order-actions">
                    <button class="btn btn-sm view-order-details" data-id="${commande.id_commande}">
                        <i class="fas fa-eye"></i> Détails
                    </button>
                    ${commande.statut === 'en_attente' ? `
                        <button class="btn btn-sm btn-outline cancel-order" data-id="${commande.id_commande}">
                            <i class="fas fa-times"></i> Annuler
                        </button>
                    ` : ''}
                </div>
            </div>
        `;
        
        return html;
    }
    
    async function viewOrderDetails(orderId) {
        try {
            const data = await OrderAPI.getDetails(orderId);
            
            if (data.success && data.commande) {
                displayOrderDetailsModal(data.commande);
            } else {
                showNotification(data.message || 'Erreur lors du chargement des détails', 'error');
            }
        } catch (error) {
            console.error('Error loading order details:', error);
            showNotification('Erreur lors du chargement des détails', 'error');
        }
    }
    
    function displayOrderDetailsModal(order) {
        // Create modal backdrop
        const modalBackdrop = document.createElement('div');
        modalBackdrop.className = 'modal-backdrop';
        
        // Create modal content
        const modalContent = document.createElement('div');
        modalContent.className = 'modal-content order-details-modal';
        
        const date = new Date(order.date_commande).toLocaleDateString('fr-FR', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
        
        const total = parseFloat(order.total).toFixed(2) + ' €';
        
        let statusClass = '';
        let statusText = '';
        
        switch(order.statut) {
            case 'en_attente':
                statusClass = 'status-en_attente';
                statusText = 'En attente';
                break;
            case 'validee':
                statusClass = 'status-validee';
                statusText = 'Validée';
                break;
            case 'expediee':
                statusClass = 'status-expediee';
                statusText = 'Expédiée';
                break;
            case 'livree':
                statusClass = 'status-livree';
                statusText = 'Livrée';
                break;
            case 'annulee':
                statusClass = 'status-annulee';
                statusText = 'Annulée';
                break;
            default:
                statusClass = 'status-default';
                statusText = order.statut;
        }
        
        modalContent.innerHTML = `
            <div class="modal-header">
                <h2 class="modal-title">Détails de la commande #${order.id_commande}</h2>
                <button class="modal-close"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body">
                <div class="order-details">
                    <div class="detail-sections">
                        <div class="detail-section">
                            <h3 class="section-title">Informations de commande</h3>
                            <div class="detail-grid">
                                <div class="detail-row">
                                    <div class="detail-label">N° de commande</div>
                                    <div class="detail-value">#${order.id_commande}</div>
                                </div>
                                <div class="detail-row">
                                    <div class="detail-label">Date</div>
                                    <div class="detail-value">${date}</div>
                                </div>
                                <div class="detail-row">
                                    <div class="detail-label">Statut</div>
                                    <div class="detail-value">
                                        <span class="order-status ${statusClass}">${statusText}</span>
                                    </div>
                                </div>
                                <div class="detail-row">
                                    <div class="detail-label">Méthode de paiement</div>
                                    <div class="detail-value">${getPaymentMethodLabel(order.methode_paiement)}</div>
                                </div>
                                <div class="detail-row">
                                    <div class="detail-label">Total</div>
                                    <div class="detail-value">${total}</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="detail-section">
                            <h3 class="section-title">Adresse de livraison</h3>
                            <div class="detail-grid">
                                <div class="detail-row">
                                    <div class="detail-label">Nom</div>
                                    <div class="detail-value">${order.prenom} ${order.nom}</div>
                                </div>
                                <div class="detail-row">
                                    <div class="detail-label">Adresse</div>
                                    <div class="detail-value">${order.adresse || 'Non spécifiée'}</div>
                                </div>
                                <div class="detail-row">
                                    <div class="detail-label">Code postal</div>
                                    <div class="detail-value">${order.code_postal || 'Non spécifié'}</div>
                                </div>
                                <div class="detail-row">
                                    <div class="detail-label">Ville</div>
                                    <div class="detail-value">${order.ville || 'Non spécifiée'}</div>
                                </div>
                                <div class="detail-row">
                                    <div class="detail-label">Téléphone</div>
                                    <div class="detail-value">${order.telephone || 'Non spécifié'}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="detail-section products-section">
                        <h3 class="section-title">Produits</h3>
                        <table class="order-items-table">
                            <thead>
                                <tr>
                                    <th>Produit</th>
                                    <th>Prix</th>
                                    <th>Qté</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
        `;
        
        // Add order items
        if (order.produits && order.produits.length > 0) {
            order.produits.forEach(item => {
                const price = parseFloat(item.prix_unitaire).toFixed(2) + ' €';
                const itemTotal = parseFloat(item.prix_unitaire * item.quantite).toFixed(2) + ' €';
                
                modalContent.innerHTML += `
                    <tr>
                        <td class="product-cell">
                            <div class="product-image">
                                <img src="${item.image_url || '/assets/images/placeholder.png'}" alt="${item.nom}" onerror="this.src='/assets/images/placeholder.png'">
                            </div>
                            <div class="product-info">
                                <div class="product-name">${item.nom}</div>
                                <div class="product-category">${item.categorie || ''}</div>
                            </div>
                        </td>
                        <td>${price}</td>
                        <td>${item.quantite}</td>
                        <td>${itemTotal}</td>
                    </tr>
                `;
            });
        } else {
            modalContent.innerHTML += `
                <tr>
                    <td colspan="4" class="text-center">Aucun produit dans cette commande</td>
                </tr>
            `;
        }
        
        modalContent.innerHTML += `
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="total-label">Total</td>
                                    <td class="total-value">${total}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline modal-close-btn">Fermer</button>
                ${order.statut === 'en_attente' ? `
                    <button class="btn btn-outline cancel-order-btn" data-id="${order.id_commande}">
                        <i class="fas fa-times"></i> Annuler la commande
                    </button>
                ` : ''}
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
        modal.querySelector('.modal-close-btn').addEventListener('click', closeModal);
        
        const cancelBtn = modal.querySelector('.cancel-order-btn');
        if (cancelBtn) {
            cancelBtn.addEventListener('click', function() {
                const orderId = this.getAttribute('data-id');
                closeModal();
                showCancelConfirmation(orderId);
            });
        }
        
        // Show modal
        setTimeout(() => {
            modalBackdrop.classList.add('show');
            modal.classList.add('show');
            document.body.style.overflow = 'hidden'; // Prevent scrolling
        }, 10);
        
        function closeModal() {
            modalBackdrop.classList.remove('show');
            modal.classList.remove('show');
            document.body.style.overflow = ''; // Restore scrolling
            setTimeout(() => {
                modalBackdrop.remove();
                modal.remove();
            }, 300);
        }
    }
    
    function showCancelConfirmation(orderId) {
        // Create modal backdrop
        const modalBackdrop = document.createElement('div');
        modalBackdrop.className = 'modal-backdrop';
        
        // Create modal content
        const modalContent = document.createElement('div');
        modalContent.className = 'modal-content';
        
        modalContent.innerHTML = `
            <div class="modal-header">
                <h2 class="modal-title">Annuler la commande</h2>
                <button class="modal-close"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir annuler cette commande ? Cette action est irréversible.</p>
                <div class="form-group">
                    <label for="cancel-reason" class="form-label">Raison de l'annulation (optionnel)</label>
                    <textarea id="cancel-reason" class="form-control" rows="3" placeholder="Expliquez pourquoi vous annulez cette commande..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline modal-close-btn">Annuler</button>
                <button class="btn btn-primary confirm-cancel" data-id="${orderId}">Confirmer l'annulation</button>
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
        modal.querySelector('.modal-close-btn').addEventListener('click', closeModal);
        
        modal.querySelector('.confirm-cancel').addEventListener('click', async function() {
            const orderId = this.getAttribute('data-id');
            const reason = modal.querySelector('#cancel-reason').value;
            
            try {
                // Show loading
                this.disabled = true;
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Traitement en cours...';
                
                const result = await OrderAPI.cancel(orderId, reason);
                
                if (result.success) {
                    showNotification('Commande annulée avec succès', 'success');
                    closeModal();
                    loadOrders(); // Reload orders
                } else {
                    showNotification(result.message || 'Erreur lors de l\'annulation de la commande', 'error');
                }
            } catch (error) {
                console.error('Error cancelling order:', error);
                showNotification('Erreur lors de l\'annulation de la commande', 'error');
            } finally {
                this.disabled = false;
                this.innerHTML = 'Confirmer l\'annulation';
            }
        });
        
        // Show modal
        setTimeout(() => {
            modalBackdrop.classList.add('show');
            modal.classList.add('show');
            document.body.style.overflow = 'hidden'; // Prevent scrolling
        }, 10);
        
        function closeModal() {
            modalBackdrop.classList.remove('show');
            modal.classList.remove('show');
            document.body.style.overflow = ''; // Restore scrolling
            setTimeout(() => {
                modalBackdrop.remove();
                modal.remove();
            }, 300);
        }
    }
    
    function getPaymentMethodLabel(method) {
        switch(method) {
            case 'card': return 'Carte bancaire';
            case 'paypal': return 'PayPal';
            default: return method;
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