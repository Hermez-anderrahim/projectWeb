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

<style>
/* Admin Orders Page Styling */
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
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
    margin-bottom: 1.5rem;
    background-color: var(--white-color);
    padding: 1rem;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
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
    width: 100%;
}

.filter-group select:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(255, 107, 107, 0.2);
}

#orders-table-container {
    background-color: var(--white-color);
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    margin-bottom: 1.5rem;
    overflow: auto;
    max-height: 70vh;
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

.client-cell {
    min-width: 200px;
}

.client-name {
    font-weight: 600;
    margin-bottom: 0.25rem;
}

.client-email {
    font-size: 0.85rem;
    color: var(--grey-color);
}

.order-status {
    display: inline-block;
    padding: 0.5rem 1rem;
    border-radius: 30px;
    font-size: 0.85rem;
    font-weight: 600;
}

.status-pending {
    background-color: #FEF9C3;
    color: #854D0E;
}

.status-validated {
    background-color: #DCFCE7;
    color: #166534;
}

.status-shipped {
    background-color: #E0F2FE;
    color: #075985;
}

.status-delivered {
    background-color: #E0E7FF;
    color: #3730A3;
}

.status-cancelled {
    background-color: #FEE2E2;
    color: #991B1B;
}

.actions-cell {
    width: 100px;
    text-align: right;
    white-space: nowrap;
}

.actions-cell .btn {
    margin-left: 0.5rem;
}

.pagination {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.pagination-btn {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: var(--white-color);
    border: 1px solid var(--light-grey);
    color: var(--dark-color);
    cursor: pointer;
    transition: all 0.2s ease;
}

.pagination-btn:hover:not([disabled]) {
    background-color: var(--primary-color);
    color: white;
    border-color: var(--primary-color);
}

.pagination-btn[disabled] {
    opacity: 0.5;
    cursor: not-allowed;
}

.pagination-info {
    font-size: 0.9rem;
    color: var(--grey-color);
}

/* Order Detail Modal Styling */
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
    max-width: 800px;
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

.modal-content {
    width: 100%;
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

.order-details-modal {
    max-width: 900px;
}

.order-details {
    display: flex;
    flex-direction: column;
    gap: 2rem;
}

.detail-sections {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 2rem;
}

.detail-section {
    background-color: #f8f9fa;
    border-radius: 8px;
    padding: 1.5rem;
}

.section-title {
    font-size: 1.1rem;
    margin-top: 0;
    margin-bottom: 1.25rem;
    color: var(--dark-color);
    border-bottom: 1px solid var(--light-grey);
    padding-bottom: 0.75rem;
}

.detail-grid {
    display: grid;
    gap: 1rem;
}

.detail-row {
    display: grid;
    grid-template-columns: 40% 60%;
    gap: 1rem;
}

.detail-label {
    font-size: 0.9rem;
    font-weight: 500;
    color: var(--grey-color);
}

.detail-value {
    font-size: 0.9rem;
    color: var(--dark-color);
}

.products-section {
    grid-column: 1 / -1;
}

.order-items-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 1rem;
}

.order-items-table th,
.order-items-table td {
    padding: 0.75rem;
    text-align: left;
    border-bottom: 1px solid var(--light-grey);
}

.order-items-table th {
    font-weight: 600;
    color: var(--grey-color);
    font-size: 0.9rem;
}

.product-cell {
    display: flex;
    align-items: center;
    gap: 1rem;
    min-width: 300px;
}

.product-image {
    width: 50px;
    height: 50px;
    border-radius: 4px;
    overflow: hidden;
    border: 1px solid var(--light-grey);
}

.product-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.product-info {
    flex: 1;
}

.product-name {
    font-weight: 600;
    margin-bottom: 0.25rem;
}

.product-category {
    font-size: 0.8rem;
    color: var (--grey-color);
}

.order-items-table tfoot {
    font-weight: 700;
}

.total-label {
    text-align: right;
}

.total-value {
    color: var(--primary-color);
}

/* Status Modal Styling */
.status-modal {
    max-width: 500px;
}

.status-selection {
    display: grid;
    gap: 1rem;
}

.status-option {
    display: flex;
    align-items: center;
}

.status-option input[type="radio"] {
    display: none;
}

.status-option label {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    border-radius: 8px;
    border: 1px solid var(--light-grey);
    width: 100%;
    cursor: pointer;
    transition: all 0.2s ease;
}

.status-option input[type="radio"]:checked + label {
    background-color: rgba(255, 107, 107, 0.1);
    border-color: var(--primary-color);
}

.status-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
}

.status-pending {
    background-color: #EAB308;
}

.status-validated {
    background-color: #10B981;
}

.status-shipped {
    background-color: #0EA5E9;
}

.status-delivered {
    background-color: #6366F1;
}

.status-cancelled {
    background-color: #EF4444;
}

.status-text {
    font-weight: 500;
}

.empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 3rem;
    text-align: center;
}

.empty-icon {
    font-size: 3rem;
    color: var(--light-grey);
    margin-bottom: 1rem;
}

.empty-text {
    color: var(--grey-color);
    margin-bottom: 1.5rem;
}

/* Responsive adjustments */
@media (max-width: 992px) {
    .detail-sections {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
}

@media (max-width: 768px) {
    .admin-filters {
        flex-direction: column;
        align-items: stretch;
    }
    
    .product-cell {
        min-width: 200px;
    }
    
    .order-items-table {
        min-width: 600px;
    }
    
    #orders-table-container {
        max-height: 60vh;
    }
    
    .admin-table {
        min-width: 800px;
    }
}

@media (max-width: 576px) {
    .detail-row {
        grid-template-columns: 1fr;
        gap: 0.25rem;
    }
    
    .detail-label {
        color: var(--dark-color);
    }
    
    .modal {
        width: 95%;
    }
}

/* Notifications */
.notification {
    position: fixed;
    bottom: 20px;
    right: 20px;
    padding: 1rem;
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    display: flex;
    align-items: center;
    transform: translateY(100px);
    opacity: 0;
    transition: transform 0.3s ease, opacity 0.3s ease;
    z-index: 1100;
    max-width: 350px;
}

.notification.show {
    transform: translateY(0);
    opacity: 1;
}

.notification.success {
    border-left: 4px solid #10B981;
}

.notification.error {
    border-left: 4px solid #EF4444;
}

.notification-icon {
    margin-right: 0.75rem;
    font-size: 1.25rem;
}

.notification.success .notification-icon {
    color: #10B981;
}

.notification.error .notification-icon {
    color: #EF4444;
}

.notification-message {
    flex: 1;
    font-size: 0.9rem;
}
</style>

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
                if (data.commandes && data.commandes.length > 0) {
                    displayOrders(data.commandes);
                    totalPages = data.pages_total || 1;
                    updatePagination(data.page, data.pages_total);
                } else {
                    displayEmptyState();
                }
            } else {
                container.innerHTML = '<div class="error-message">Erreur lors du chargement des commandes</div>';
            }
        } catch (error) {
            console.error('Error loading orders:', error);
            container.innerHTML = '<div class="error-message">Erreur lors du chargement des commandes</div>';
        }
    }
    
    function displayEmptyState() {
        const container = document.getElementById('orders-table-container');
        container.innerHTML = `
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-inbox"></i>
                </div>
                <h3>Aucune commande trouvée</h3>
                <p class="empty-text">Il n'y a pas de commandes correspondant à vos critères de recherche.</p>
            </div>
        `;
    }
    
    function displayOrders(orders) {
        const container = document.getElementById('orders-table-container');
        
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
                        <div class="client-name">${order.prenom || ''} ${order.nom || ''}</div>
                        <div class="client-email">${order.email || ''}</div>
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
                                    <div class="detail-value">
                                        <span class="order-status ${statusClass}">${getStatusLabel(order.statut)}</span>
                                    </div>
                                </div>
                                <div class="detail-row">
                                    <div class="detail-label">Total</div>
                                    <div class="detail-value">${total}</div>
                                </div>
                                <div class="detail-row">
                                    <div class="detail-label">Méthode de paiement</div>
                                    <div class="detail-value">${getPaymentMethodLabel(order.methode_paiement)}</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="detail-section">
                            <h3 class="section-title">Informations du client</h3>
                            <div class="detail-grid">
                                <div class="detail-row">
                                    <div class="detail-label">Nom</div>
                                    <div class="detail-value">${order.prenom || ''} ${order.nom || ''}</div>
                                </div>
                                <div class="detail-row">
                                    <div class="detail-label">Email</div>
                                    <div class="detail-value">${order.email || ''}</div>
                                </div>
                                <div class="detail-row">
                                    <div class="detail-label">Adresse</div>
                                    <div class="detail-value">${order.adresse || 'Non spécifiée'}</div>
                                </div>
                                <div class="detail-row">
                                    <div class="detail-label">Ville</div>
                                    <div class="detail-value">${(order.code_postal || '') + ' ' + (order.ville || '')}</div>
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
                                <img src="${item.image_url || 'assets/images/placeholder.png'}" alt="${item.nom}" onerror="this.src='assets/images/placeholder.png'">
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
        
        // Get order status first to pre-select the current status
        OrderAPI.getDetails(orderId)
            .then(data => {
                if (data.success && data.commande) {
                    const status = data.commande.statut;
                    const radioBtn = modal.querySelector(`input[value="${status}"]`);
                    if (radioBtn) {
                        radioBtn.checked = true;
                    }
                }
            })
            .catch(error => {
                console.error('Error getting order details:', error);
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
    
    function getPaymentMethodLabel(method) {
        switch(method) {
            case 'card': return 'Carte bancaire';
            case 'paypal': return 'PayPal';
            default: return method ? method : 'Non spécifié';
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