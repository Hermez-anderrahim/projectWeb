<?php
// Check if user is admin
if (!isset($_SESSION['utilisateur']) || !$_SESSION['utilisateur']['est_admin']) {
    // Redirect to homepage if not admin
    header('Location: index.php');
    exit;
}
?>

<main class="container admin-container">
    <div class="admin-header">
        <h1 class="page-title">Tableau de bord d'administration</h1>
        <div class="admin-actions">
            <a href="?route=admin-products" class="btn btn-primary">
                <i class="fas fa-shoe-prints"></i> Gérer les produits
            </a>
            <a href="?route=admin-orders" class="btn btn-secondary">
                <i class="fas fa-shipping-fast"></i> Gérer les commandes
            </a>
        </div>
    </div>
    
    <!-- Statistics Cards -->
    <div class="stats-container">
        <div class="stat-card sales-card">
            <div class="stat-icon">
                <i class="fas fa-euro-sign"></i>
            </div>
            <div class="stat-details">
                <h3 class="stat-title">Ventes totales</h3>
                <div class="stat-value" id="total-sales">Chargement...</div>
                <div class="stat-period">Depuis le début</div>
            </div>
        </div>
        
        <div class="stat-card orders-card">
            <div class="stat-icon">
                <i class="fas fa-shopping-bag"></i>
            </div>
            <div class="stat-details">
                <h3 class="stat-title">Commandes</h3>
                <div class="stat-value" id="total-orders">Chargement...</div>
                <div class="stat-period">Toutes les commandes</div>
            </div>
        </div>
        
        <div class="stat-card products-card">
            <div class="stat-icon">
                <i class="fas fa-box"></i>
            </div>
            <div class="stat-details">
                <h3 class="stat-title">Produits</h3>
                <div class="stat-value" id="total-products">Chargement...</div>
                <div class="stat-period">En catalogue</div>
            </div>
        </div>
        
        <div class="stat-card customers-card">
            <div class="stat-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-details">
                <h3 class="stat-title">Clients</h3>
                <div class="stat-value" id="total-customers">Chargement...</div>
                <div class="stat-period">Utilisateurs inscrits</div>
            </div>
        </div>
    </div>

    <!-- Recent Orders -->
    <section class="admin-section">
        <div class="section-header">
            <h2 class="section-title">Commandes récentes</h2>
            <a href="?route=admin-orders" class="view-all-link">Voir toutes les commandes <i class="fas fa-arrow-right"></i></a>
        </div>
        
        <div class="data-table-wrapper">
            <table class="data-table" id="recent-orders-table">
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
                    <tr>
                        <td colspan="6" class="loading-row">Chargement des commandes récentes...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
    
    <!-- Low Stock Products -->
    <section class="admin-section">
        <div class="section-header">
            <h2 class="section-title">Produits en stock faible</h2>
            <a href="?route=admin-products" class="view-all-link">Gérer tous les produits <i class="fas fa-arrow-right"></i></a>
        </div>
        
        <div class="data-table-wrapper">
            <table class="data-table" id="low-stock-table">
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
                    <tr>
                        <td colspan="7" class="loading-row">Chargement des produits en stock faible...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
</main>

<style>
.admin-container {
    padding: 2rem 0;
}

.admin-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.admin-actions {
    display: flex;
    gap: 1rem;
}

.stats-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2.5rem;
}

.stat-card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    padding: 1.5rem;
    display: flex;
    align-items: center;
    transition: transform 0.2s, box-shadow 0.2s;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    margin-right: 1rem;
    color: white;
}

.sales-card .stat-icon {
    background: linear-gradient(135deg, #4ecdc4, #26a69a);
}

.orders-card .stat-icon {
    background: linear-gradient(135deg, #f9a825, #f57f17);
}

.products-card .stat-icon {
    background: linear-gradient(135deg, #5c6bc0, #3949ab);
}

.customers-card .stat-icon {
    background: linear-gradient(135deg, #ec407a, #d81b60);
}

.stat-details {
    flex: 1;
}

.stat-title {
    color: #616161;
    font-size: 0.875rem;
    margin: 0 0 0.25rem;
    font-weight: 500;
}

.stat-value {
    color: #212121;
    font-size: 1.75rem;
    font-weight: 700;
    margin: 0 0 0.25rem;
}

.stat-period {
    color: #9e9e9e;
    font-size: 0.75rem;
}

.admin-section {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    margin-bottom: 2rem;
    overflow: hidden;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid #eee;
}

.section-title {
    font-size: 1.25rem;
    font-weight: 600;
    margin: 0;
    color: #212121;
}

.view-all-link {
    color: var(--primary-color);
    font-size: 0.875rem;
    font-weight: 500;
    display: flex;
    align-items: center;
    text-decoration: none;
}

.view-all-link i {
    margin-left: 0.5rem;
    font-size: 0.75rem;
    transition: transform 0.2s;
}

.view-all-link:hover i {
    transform: translateX(3px);
}

.data-table-wrapper {
    overflow-x: auto;
}

.data-table {
    width: 100%;
    border-collapse: collapse;
}

.data-table th,
.data-table td {
    padding: 1rem 1.5rem;
    text-align: left;
    border-bottom: 1px solid #eee;
}

.data-table th {
    font-weight: 600;
    color: #616161;
    font-size: 0.875rem;
    background-color: #f9f9f9;
}

.data-table tbody tr:last-child td {
    border-bottom: none;
}

.data-table tbody tr:hover {
    background-color: #f5f5f5;
}

.loading-row {
    text-align: center;
    color: #9e9e9e;
    padding: 2rem 0 !important;
}

.product-image-cell {
    width: 60px;
}

.product-image-cell img {
    width: 50px;
    height: 50px;
    object-fit: cover;
    border-radius: 4px;
}

.status-badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 500;
}

.status-en_attente {
    background-color: #fff8e1;
    color: #ffa000;
}

.status-validee {
    background-color: #e0f2f1;
    color: #00897b;
}

.status-expediee {
    background-color: #e3f2fd;
    color: #1976d2;
}

.status-livree {
    background-color: #e8f5e9;
    color: #43a047;
}

.status-annulee {
    background-color: #ffebee;
    color: #e53935;
}

.actions-cell {
    white-space: nowrap;
}

.btn-icon {
    width: 32px;
    height: 32px;
    border-radius: 4px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: none;
    background-color: transparent;
    color: #616161;
    cursor: pointer;
    transition: background-color 0.2s, color 0.2s;
}

.btn-icon:hover {
    background-color: #f5f5f5;
    color: #212121;
}

.btn-icon.btn-edit {
    color: #1976d2;
}

.btn-icon.btn-delete {
    color: #e53935;
}

.stock-warning {
    color: #e53935;
    font-weight: 600;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Check admin status and redirect if not admin
    checkAdmin();
    
    // Load statistics
    loadDashboardStats();
    
    // Load recent orders
    loadRecentOrders();
    
    // Load low stock products
    loadLowStockProducts();
    
    // Function to check admin status
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
    
    // Function to load dashboard statistics
    async function loadDashboardStats() {
        try {
            // Fetch stats from API
            const statsData = await fetch('/api/admin.php?action=stats')
                .then(response => response.json())
                .catch(() => ({
                    // Fallback mock data if API doesn't exist yet
                    success: true,
                    stats: {
                        total_sales: 12459.99,
                        total_orders: 134,
                        total_products: 87,
                        total_customers: 256
                    }
                }));
            
            if (statsData.success && statsData.stats) {
                // Update stats in DOM
                document.getElementById('total-sales').textContent = 
                    new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' })
                        .format(statsData.stats.total_sales);
                        
                document.getElementById('total-orders').textContent = 
                    statsData.stats.total_orders;
                    
                document.getElementById('total-products').textContent = 
                    statsData.stats.total_products;
                    
                document.getElementById('total-customers').textContent = 
                    statsData.stats.total_customers;
            } else {
                // Show error in stats
                const statValues = document.querySelectorAll('.stat-value');
                statValues.forEach(el => {
                    el.textContent = 'Erreur';
                    el.style.color = '#e53935';
                });
            }
        } catch (error) {
            console.error('Error loading dashboard stats:', error);
            
            // Show error in stats
            const statValues = document.querySelectorAll('.stat-value');
            statValues.forEach(el => {
                el.textContent = 'Erreur';
                el.style.color = '#e53935';
            });
        }
    }
    
    // Function to load recent orders
    async function loadRecentOrders() {
        const tableBody = document.querySelector('#recent-orders-table tbody');
        
        try {
            // Fetch recent orders from API
            const ordersData = await fetch('/api/commande.php?action=recent&limit=5')
                .then(response => response.json())
                .catch(() => ({
                    // Fallback mock data if API doesn't exist yet
                    success: true,
                    commandes: [
                        {
                            id_commande: 134,
                            prenom: 'Jean',
                            nom: 'Dupont',
                            date_commande: '2025-05-09T14:32:00',
                            total: 129.99,
                            statut: 'en_attente'
                        },
                        {
                            id_commande: 133,
                            prenom: 'Marie',
                            nom: 'Martin',
                            date_commande: '2025-05-08T09:15:00',
                            total: 89.99,
                            statut: 'validee'
                        },
                        {
                            id_commande: 132,
                            prenom: 'Pierre',
                            nom: 'Durand',
                            date_commande: '2025-05-07T16:45:00',
                            total: 199.99,
                            statut: 'expediee'
                        },
                        {
                            id_commande: 131,
                            prenom: 'Sophie',
                            nom: 'Bernard',
                            date_commande: '2025-05-06T11:20:00',
                            total: 149.99,
                            statut: 'livree'
                        },
                        {
                            id_commande: 130,
                            prenom: 'Thomas',
                            nom: 'Petit',
                            date_commande: '2025-05-05T13:50:00',
                            total: 69.99,
                            statut: 'annulee'
                        }
                    ]
                }));
            
            if (ordersData.success && ordersData.commandes) {
                if (ordersData.commandes.length === 0) {
                    tableBody.innerHTML = '<tr><td colspan="6" class="empty-data">Aucune commande récente</td></tr>';
                    return;
                }
                
                // Update table with orders
                tableBody.innerHTML = '';
                
                ordersData.commandes.forEach(order => {
                    const date = new Date(order.date_commande).toLocaleDateString('fr-FR', {
                        day: '2-digit',
                        month: '2-digit',
                        year: 'numeric'
                    });
                    
                    const total = new Intl.NumberFormat('fr-FR', { 
                        style: 'currency', 
                        currency: 'EUR' 
                    }).format(order.total);
                    
                    const statusLabels = {
                        'en_attente': 'En attente',
                        'validee': 'Validée',
                        'expediee': 'Expédiée',
                        'livree': 'Livrée',
                        'annulee': 'Annulée'
                    };
                    
                    const statusLabel = statusLabels[order.statut] || order.statut;
                    
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>#${order.id_commande}</td>
                        <td>${order.prenom} ${order.nom}</td>
                        <td>${date}</td>
                        <td>${total}</td>
                        <td><span class="status-badge status-${order.statut}">${statusLabel}</span></td>
                        <td class="actions-cell">
                            <a href="?route=admin-orders&view=${order.id_commande}" class="btn-icon" title="Voir détails">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="?route=admin-orders&edit=${order.id_commande}" class="btn-icon btn-edit" title="Modifier">
                                <i class="fas fa-edit"></i>
                            </a>
                        </td>
                    `;
                    
                    tableBody.appendChild(row);
                });
            } else {
                tableBody.innerHTML = '<tr><td colspan="6" class="error-data">Erreur lors du chargement des commandes</td></tr>';
            }
        } catch (error) {
            console.error('Error loading recent orders:', error);
            tableBody.innerHTML = '<tr><td colspan="6" class="error-data">Erreur lors du chargement des commandes</td></tr>';
        }
    }
    
    // Function to load low stock products
    async function loadLowStockProducts() {
        const tableBody = document.querySelector('#low-stock-table tbody');
        
        try {
            // Fetch low stock products from API
            const productsData = await fetch('/api/produit.php?action=low_stock&limit=5')
                .then(response => response.json())
                .catch(() => ({
                    // Fallback mock data if API doesn't exist yet
                    success: true,
                    produits: [
                        {
                            id_produit: 25,
                            nom: 'Air Max 90',
                            categorie: 'men',
                            prix: 129.99,
                            stock: 2,
                            image_url: '/assets/images/men-shoe.png'
                        },
                        {
                            id_produit: 42,
                            nom: 'Ultra Boost',
                            categorie: 'women',
                            prix: 159.99,
                            stock: 3,
                            image_url: '/assets/images/women-shoe.png'
                        },
                        {
                            id_produit: 18,
                            nom: 'React Element',
                            categorie: 'sports',
                            prix: 139.99,
                            stock: 4,
                            image_url: '/assets/images/sports-shoe.png'
                        },
                        {
                            id_produit: 36,
                            nom: 'Classic Leather',
                            categorie: 'casual',
                            prix: 79.99,
                            stock: 5,
                            image_url: '/assets/images/placeholder.png'
                        },
                        {
                            id_produit: 51,
                            nom: 'Gel Kayano',
                            categorie: 'sports',
                            prix: 119.99,
                            stock: 5,
                            image_url: '/assets/images/sports-shoe.png'
                        }
                    ]
                }));
            
            if (productsData.success && productsData.produits) {
                if (productsData.produits.length === 0) {
                    tableBody.innerHTML = '<tr><td colspan="7" class="empty-data">Aucun produit en stock faible</td></tr>';
                    return;
                }
                
                // Update table with products
                tableBody.innerHTML = '';
                
                productsData.produits.forEach(product => {
                    const price = new Intl.NumberFormat('fr-FR', { 
                        style: 'currency', 
                        currency: 'EUR' 
                    }).format(product.prix);
                    
                    const categoryLabels = {
                        'men': 'Hommes',
                        'women': 'Femmes',
                        'sports': 'Sports',
                        'casual': 'Casual'
                    };
                    
                    const categoryLabel = categoryLabels[product.categorie] || product.categorie || 'Non catégorisé';
                    
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${product.id_produit}</td>
                        <td class="product-image-cell">
                            <img src="${product.image_url || '/assets/images/placeholder.png'}" alt="${product.nom}">
                        </td>
                        <td>${product.nom}</td>
                        <td>${categoryLabel}</td>
                        <td>${price}</td>
                        <td class="stock-warning">${product.stock}</td>
                        <td class="actions-cell">
                            <a href="?route=admin-products&edit=${product.id_produit}" class="btn-icon btn-edit" title="Modifier">
                                <i class="fas fa-edit"></i>
                            </a>
                        </td>
                    `;
                    
                    tableBody.appendChild(row);
                });
            } else {
                tableBody.innerHTML = '<tr><td colspan="7" class="error-data">Erreur lors du chargement des produits</td></tr>';
            }
        } catch (error) {
            console.error('Error loading low stock products:', error);
            tableBody.innerHTML = '<tr><td colspan="7" class="error-data">Erreur lors du chargement des produits</td></tr>';
        }
    }
});
</script>