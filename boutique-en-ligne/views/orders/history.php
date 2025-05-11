<main class="container">
    <div class="orders-history-page">
        <h1 class="page-title">Historique des Commandes</h1>
        
        <div id="orders-content">
            <div class="loading-spinner">Chargement de vos commandes...</div>
        </div>
    </div>
</main>


<script>
document.addEventListener('DOMContentLoaded', function() {
    loadOrderHistory();
    
    async function loadOrderHistory() {
        try {
            const data = await OrderAPI.getHistory();
            
            if (data.success) {
                if (data.historique && data.historique.length > 0) {
                    displayOrderHistory(data.historique);
                } else {
                    displayNoOrders();
                }
            } else {
                if (data.message === "Utilisateur non connecté.") {
                    document.getElementById('orders-content').innerHTML = `
                        <div class="auth-message">
                            <div class="auth-message-icon">
                                <i class="fas fa-user-lock"></i>
                            </div>
                            <h2>Authentification requise</h2>
                            <p>Veuillez vous connecter pour accéder à votre historique de commandes.</p>
                            <a href="?route=login" class="btn btn-primary">Se connecter</a>
                        </div>
                    `;
                } else {
                    document.getElementById('orders-content').innerHTML = `
                        <div class="error-message">
                            <i class="fas fa-exclamation-circle"></i> ${data.message || 'Une erreur est survenue'}
                        </div>
                    `;
                }
            }
        } catch (error) {
            console.error('Error loading orders:', error);
            document.getElementById('orders-content').innerHTML = '<div class="error-message"><i class="fas fa-exclamation-circle"></i> Erreur lors du chargement des commandes</div>';
        }
    }
    
    function displayOrderHistory(orders) {
        const container = document.getElementById('orders-content');
        
        let html = `
            <div class="orders-list">
        `;
        
        orders.forEach(order => {
            const date = new Date(order.date_commande).toLocaleDateString('fr-FR', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            });
            
            const total = parseFloat(order.total).toFixed(2) + ' €';
            
            let statusClass = '';
            let statusIcon = '';
            
            switch(order.statut) {
                case 'en_attente':
                    statusClass = 'status-pending';
                    statusIcon = '<i class="fas fa-clock"></i>';
                    break;
                case 'validee':
                    statusClass = 'status-validated';
                    statusIcon = '<i class="fas fa-check"></i>';
                    break;
                case 'expediee':
                    statusClass = 'status-shipped';
                    statusIcon = '<i class="fas fa-shipping-fast"></i>';
                    break;
                case 'livree':
                    statusClass = 'status-delivered';
                    statusIcon = '<i class="fas fa-box-open"></i>';
                    break;
                case 'annulee':
                    statusClass = 'status-cancelled';
                    statusIcon = '<i class="fas fa-ban"></i>';
                    break;
                default:
                    statusClass = 'status-default';
                    statusIcon = '<i class="fas fa-question"></i>';
            }
            
            html += `
                <div class="order-card">
                    <div class="order-header">
                        <div class="order-id">
                            <span class="label">Commande #</span>
                            <span class="value">${order.id_commande}</span>
                        </div>
                        <div class="order-date">
                            <span class="label">Date</span>
                            <span class="value">${date}</span>
                        </div>
                        <div class="order-status ${statusClass}">
                            ${statusIcon} ${getStatusLabel(order.statut)}
                        </div>
                        <div class="order-total">
                            <span class="value">${total}</span>
                        </div>
                    </div>
                    
                    <div class="order-actions">
                        <button class="btn btn-sm btn-outline view-order" data-id="${order.id_commande}">
                            <i class="fas fa-eye"></i> Détails
                        </button>
                        ${order.statut === 'en_attente' ? `
                            <button class="btn btn-sm btn-outline btn-danger cancel-order" data-id="${order.id_commande}">
                                <i class="fas fa-times"></i> Annuler
                            </button>
                        ` : ''}
                    </div>
                </div>
            `;
        });
        
        html += `
            </div>
        `;
        
        container.innerHTML = html;
        
        // Add event listeners
        document.querySelectorAll('.view-order').forEach(btn => {
            btn.addEventListener('click', function() {
                const orderId = this.getAttribute('data-id');
                viewOrderDetails(orderId);
            });
        });
        
        document.querySelectorAll('.cancel-order').forEach(btn => {
            btn.addEventListener('click', function() {
                const orderId = this.getAttribute('data-id');
                cancelOrder(orderId);
            });
        });
    }
    
    function displayNoOrders() {
        document.getElementById('orders-content').innerHTML = `
            <div class="no-orders">
                <div class="no-orders-icon">
                    <i class="fas fa-shopping-bag"></i>
                </div>
                <h2>Aucune commande</h2>
                <p>Vous n'avez pas encore passé de commande.</p>
                <a href="?route=home" class="btn btn-primary">Découvrir nos produits</a>
            </div>
        `;
    }
    
    function getStatusLabel(status) {
        switch(status) {
            case 'en_attente': return 'En attente';
            case 'validee': return 'Validée';
            case 'expediee': return 'Expédiée';
            case 'livree': return 'Livrée';
            case 'annulee': return 'Annulée';
            default: return 'Inconnu';
        }
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
        modalContent.className = 'modal-content';
        
        const date = new Date(order.date_commande).toLocaleDateString('fr-FR', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
        
        const total = parseFloat(order.total).toFixed(2) + ' €';
        
        modalContent.innerHTML = `
            <div class="modal-header">
                <h2 class="modal-title">Détails de la commande #${order.id_commande}</h2>
                <button class="modal-close"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body">
                <div class="order-details">
                    <div class="order-info">
                        <div class="info-row">
                            <div class="info-label">Date</div>
                            <div class="info-value">${date}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Statut</div>
                            <div class="info-value status-${order.statut}">${getStatusLabel(order.statut)}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Total</div>
                            <div class="info-value">${total}</div>
                        </div>
                    </div>
                    
                    <h3 class="section-title">Produits</h3>
                    <div class="order-items">
        `;
        
        // Add order items
        if (order.produits && order.produits.length > 0) {
            order.produits.forEach(item => {
                const price = parseFloat(item.prix_unitaire).toFixed(2) + ' €';
                const itemTotal = parseFloat(item.prix_unitaire * item.quantite).toFixed(2) + ' €';
                
                modalContent.innerHTML += `
                    <div class="order-item">
                        <div class="item-image">
                            <img src="${item.image_url || '/assets/images/placeholder.jpg'}" alt="${item.nom}">
                        </div>
                        <div class="item-details">
                            <div class="item-name">${item.nom}</div>
                            <div class="item-category">${item.categorie || ''}</div>
                            <div class="item-price">${price} × ${item.quantite}</div>
                        </div>
                        <div class="item-total">${itemTotal}</div>
                    </div>
                `;
            });
        } else {
            modalContent.innerHTML += `<p>Aucun détail disponible pour cette commande.</p>`;
        }
        
        modalContent.innerHTML += `
                    </div>
                </div>
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
    
    async function cancelOrder(orderId) {
        if (confirm('Êtes-vous sûr de vouloir annuler cette commande ?')) {
            try {
                const result = await OrderAPI.cancel(orderId);
                if (result.success) {
                    showNotification('Commande annulée avec succès', 'success');
                    loadOrderHistory(); // Reload the order list
                } else {
                    showNotification(result.message || 'Erreur lors de l\'annulation', 'error');
                }
            } catch (error) {
                console.error('Error cancelling order:', error);
                showNotification('Erreur lors de l\'annulation', 'error');
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
});
</script>