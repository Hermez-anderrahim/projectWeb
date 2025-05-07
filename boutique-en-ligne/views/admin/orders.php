<main class="container admin-container">
    <div class="admin-header">
        <h1 class="page-title">Gestion des Commandes</h1>
    </div>
    
    <div class="admin-filters">
        <div class="filter-group">
            <select id="status-filter" class="form-control">
                <option value="">Tous les statuts</option>
                <option value="en_attente">En attente</option>
                <option value="validee">Validée</option>
                <option value="expediee">Expédiée</option>
                <option value="livree">Livrée</option>
                <option value="annulee">Annulée</option>
            </select>
        </div>
        
        <div class="pagination-controls" id="pagination-controls"></div>
    </div>
    
    <div id="orders-table-container">
        <div class="loading-spinner">Chargement des commandes...</div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Check if user is admin
    checkAdmin();
    
    // State
    let currentPage = 1;
    let currentStatus = '';
    let totalPages = 1;
    
    // Elements
    const statusFilter = document.getElementById('status-filter');
    
    // Event listeners
    statusFilter.addEventListener('change', function() {
        currentStatus = this.value;
        currentPage = 1;
        loadOrders(currentPage, currentStatus);
    });
    
    // Initial load
    loadOrders(currentPage, currentStatus);
    
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
    
    async function loadOrders(page, status) {
        const container = document.getElementById('orders-table-container');
        container.innerHTML = '<div class="loading-spinner">Chargement des commandes...</div>';
        
        try {
            const data = await OrderAPI.getAllOrders(status, page);
            
            if (data.success) {
                displayOrders(data.commandes);
                totalPages = data.pages_total;
                updatePagination(data.page, data.pages_total);
            } else {
                container.innerHTML = '<div class="no-results">Aucune commande trouvée</div>';
            }
        } catch (error) {
            console.error('Error loading orders:', error);
            container.innerHTML = '<div class="error-message">Erreur lors du chargement des commandes</div>';
        }
    }
    
    function displayOrders(orders) {
        const container = document.getElementById('orders-table-container');
        
        if (!orders || orders.length === 0) {
            container.innerHTML = '<div class="no-results">Aucune commande trouvée</div>';
            return;
        }
        
        let html = `
            <table class="admin-table orders-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Client</th>
                        <th>Date</th>
                        <th>Total</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
        `;
        
        orders.forEach(order => {
            const date = new Date(order.date_commande).toLocaleDateString('fr-FR', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            });
            
            const total = parseFloat(order.total).toFixed(2) + ' €';
            
            let statusClass = '';
            let statusText = '';
            
            switch(order.statut) {
                case 'en_attente':
                    statusClass = 'status-pending';
                    statusText = 'En attente';
                    break;
                case 'validee':
                    statusClass = 'status-validated';
                    statusText = 'Validée';
                    break;
                case 'expediee':
                    statusClass = 'status-shipped';
                    statusText = 'Expédiée';
                    break;
                case 'livree':
                    statusClass = 'status-delivered';
                    statusText = 'Livrée';
                    break;
                case 'annulee':
                    statusClass = 'status-cancelled';
                    statusText = 'Annulée';
                    break;
                default:
                    statusClass = 'status-default';
                    statusText = order.statut;
            }
            
            html += `
                <tr data-id="${order.id_commande}">
                    <td>#${order.id_commande}</td>
                    <td class="client-cell">
                        <div class="client-name">${order.prenom} ${order.nom}</div>
                        <div class="client-email">${order.email}</div>
                    </td>
                    <td>${date}</td>
                    <td>${total}</td>
                    <td>
                        <span class="order-status ${statusClass}">${statusText}</span>
                    </td>
                    <td class="actions-cell">
                        <button class="btn btn-sm btn-icon view-order" data-id="${order.id_commande}" title="Voir détails">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-sm btn-icon edit-status" data-id="${order.id_commande}" title="Modifier statut">
                            <i class="fas fa-edit"></i>
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
        document.querySelectorAll('.view-order').forEach(btn => {
            btn.addEventListener('click', function() {
                const orderId = this.getAttribute('data-id');
                viewOrderDetails(orderId);
            });
        });
        
        document.querySelectorAll('.edit-status').forEach(btn => {
            btn.addEventListener('click', function() {
                const orderId = this.getAttribute('data-id');
                showStatusModal(orderId);
            });
        });
    }
    
    function updatePagination(currentPage, totalPages) {
        const container = document.getElementById('pagination-controls');
        
        if (totalPages <= 1) {
            container.innerHTML = '';
            return;
        }
        
        let html = `
            <div class="pagination">
                <button class="pagination-btn prev-page" ${currentPage <= 1 ? 'disabled' : ''}>
                    <i class="fas fa-chevron-left"></i>
                </button>
                <span class="pagination-info">Page ${currentPage} sur ${totalPages}</span>
                <button class="pagination-btn next-page" ${currentPage >= totalPages ? 'disabled' : ''}>
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        `;
        
        container.innerHTML = html;
        
        // Add event listeners
        const prevBtn = container.querySelector('.prev-page');
        const nextBtn = container.querySelector('.next-page');
        
        if (prevBtn && !prevBtn.hasAttribute('disabled')) {
            prevBtn.addEventListener('click', function() {
                if (currentPage > 1) {
                    currentPage--;
                    loadOrders(currentPage, currentStatus);
                }
            });
        }
        
        if (nextBtn && !nextBtn.hasAttribute('disabled')) {
            nextBtn.addEventListener('click', function() {
                if (currentPage < totalPages) {
                    currentPage++;
                    loadOrders(currentPage, currentStatus);
                }
            });
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
        
        switch(order.statut) {
            case 'en_attente': statusClass = 'status-pending'; break;
            case 'validee': statusClass = 'status-validated'; break;
            case 'expediee': statusClass = 'status-shipped'; break;
            case 'livree': statusClass = 'status-delivered'; break;
            case 'annulee': statusClass = 'status-cancelled'; break;
            default: statusClass = 'status-default';
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
                                    <div class="detail-value status-${order.statut}">${getStatusLabel(order.statut)}</div>
                                </div>
                                <div class="detail-row">
                                    <div class="detail-label">Total</div>
                                    <div class="detail-value">${total}</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="detail-section">
                            <h3 class="section-title">Informations du client</h3>
                            <div class="detail-grid">
                                <div class="detail-row">
                                    <div class="detail-label">Nom</div>
                                    <div class="detail-value">${order.prenom} ${order.nom}</div>
                                </div>
                                <div class="detail-row">
                                    <div class="detail-label">Email</div>
                                    <div class="detail-value">${order.email}</div>
                                </div>
                                <div class="detail-row">
                                    <div class="detail-label">Adresse</div>
                                    <div class="detail-value">${order.adresse || 'Non spécifiée'}</div>
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
                                <img src="${item.image_url || 'assets/images/placeholder.jpg'}" alt="${item.nom}">
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
                    <td colspan="4">Aucun détail disponible pour cette commande</td>
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
                <button class="btn btn-primary edit-status-btn" data-id="${order.id_commande}">Modifier le statut</button>
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
        
        modal.querySelector('.edit-status-btn').addEventListener('click', function() {
            closeModal();
            showStatusModal(order.id_commande);
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
    
    function showStatusModal(orderId) {
        // Create modal backdrop
        const modalBackdrop = document.createElement('div');
        modalBackdrop.className = 'modal-backdrop';
        
        // Create modal content
        const modalContent = document.createElement('div');
        modalContent.className = 'modal-content status-modal';
        
        modalContent.innerHTML = `
            <div class="modal-header">
                <h2 class="modal-title">Modifier le statut de la commande #${orderId}</h2>
                <button class="modal-close"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body">
                <div class="status-selection">
                    <div class="status-option">
                        <input type="radio" id="status-pending" name="order-status" value="en_attente">
                        <label for="status-pending">
                            <span class="status-icon status-pending">
                                <i class="fas fa-clock"></i>
                            </span>
                            <span class="status-text">En attente</span>
                        </label>
                    </div>
                    
                    <div class="status-option">
                        <input type="radio" id="status-validated" name="order-status" value="validee">
                        <label for="status-validated">
                            <span class="status-icon status-validated">
                                <i class="fas fa-check"></i>
                            </span>
                            <span class="status-text">Validée</span>
                        </label>
                    </div>
                    
                    <div class="status-option">
                        <input type="radio" id="status-shipped" name="order-status" value="expediee">
                        <label for="status-shipped">
                            <span class="status-icon status-shipped">
                                <i class="fas fa-shipping-fast"></i>
                            </span>
                            <span class="status-text">Expédiée</span>
                        </label>
                    </div>
                    
                    <div class="status-option">
                        <input type="radio" id="status-delivered" name="order-status" value="livree">
                        <label for="status-delivered">
                            <span class="status-icon status-delivered">
                                <i class="fas fa-box-open"></i>
                            </span>
                            <span class="status-text">Livrée</span>
                        </label>
                    </div>
                    
                    <div class="status-option">
                        <input type="radio" id="status-cancelled" name="order-status" value="annulee">
                        <label for="status-cancelled">
                            <span class="status-icon status-cancelled">
                                <i class="fas fa-ban"></i>
                            </span>
                            <span class="status-text">Annulée</span>
                        </label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline modal-close-btn">Annuler</button>
                <button class="btn btn-primary" id="save-status">Enregistrer</button>
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
        
        modal.querySelector('#save-status').addEventListener('click', async function() {
            const selectedStatus = modal.querySelector('input[name="order-status"]:checked');
            
            if (!selectedStatus) {
                showNotification('Veuillez sélectionner un statut', 'error');
                return;
            }
            
            try {
                const result = await OrderAPI.updateStatus(orderId, selectedStatus.value);
                
                if (result.success) {
                    showNotification('Statut mis à jour avec succès', 'success');
                    closeModal();
                    loadOrders(currentPage, currentStatus);
                } else {
                    showNotification(result.message || 'Erreur lors de la mise à jour du statut', 'error');
                }
            } catch (error) {
                console.error('Error updating status:', error);
                showNotification('Erreur lors de la mise à jour du statut', 'error');
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