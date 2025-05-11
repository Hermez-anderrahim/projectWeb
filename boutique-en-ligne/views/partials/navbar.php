<?php
// views/partials/navbar.php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
$isLoggedIn = isset($_SESSION['utilisateur']);
$isAdmin = $isLoggedIn && isset($_SESSION['utilisateur']['est_admin']) && $_SESSION['utilisateur']['est_admin'] == 1;
$userName = $isLoggedIn ? $_SESSION['utilisateur']['prenom'] : '';

// Get current route
$currentRoute = isset($_GET['route']) ? $_GET['route'] : 'home';
?>

<header class="main-header">
    <div class="container">
        <div class="header-wrapper">
            <!-- Logo -->
            <div class="logo">
                <a href="index.php">
                    <span class="logo-text">Foot<span class="logo-accent">cap</span></span>
                </a>
            </div>
            
            <!-- Navigation -->
            <nav class="main-nav">
                <ul class="nav-list">
                    <li class="nav-item">
                        <a href="index.php" class="nav-link <?php echo $currentRoute === 'home' ? 'active' : ''; ?>">Accueil</a>
                    </li>
                    <li class="nav-item">
                        <a href="?route=shop" class="nav-link <?php echo $currentRoute === 'shop' ? 'active' : ''; ?>">Boutique</a>
                    </li>
                    <?php if ($isLoggedIn) : ?>
                        <li class="nav-item">
                            <a href="?route=orders" class="nav-link <?php echo $currentRoute === 'orders' ? 'active' : ''; ?>">Mes commandes</a>
                        </li>
                    <?php endif; ?>
                    <?php if ($isAdmin) : ?>
                        <li class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle">Admin <i class="fas fa-chevron-down"></i></a>
                            <ul class="dropdown-menu">
                                <li><a href="?route=admin-dashboard">Tableau de bord</a></li>
                                <li><a href="?route=admin-products">Produits</a></li>
                                <li><a href="?route=admin-orders">Commandes</a></li>
                            </ul>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
            
            <!-- Right actions -->
            <div class="header-actions">
                <div class="search-toggle action-btn" id="search-toggle">
                    <i class="fas fa-search"></i>
                </div>
                
                <div class="user-actions">
                    <?php if ($isLoggedIn) : ?>
                        <div class="user-dropdown">
                            <button class="action-btn user-btn">
                                <i class="fas fa-user"></i>
                                <span class="user-name"><?php echo htmlspecialchars($userName); ?></span>
                            </button>
                            <div class="dropdown-menu">
                                <a href="?route=profile" class="dropdown-item">Mon profil</a>
                                <a href="?route=orders" class="dropdown-item">Mes commandes</a>
                                <?php if ($isAdmin) : ?>
                                    <a href="?route=admin-dashboard" class="dropdown-item">Administration</a>
                                <?php endif; ?>
                                <div class="dropdown-divider"></div>
                                <a href="#" id="logout-btn" class="dropdown-item logout-btn">Déconnexion</a>
                            </div>
                        </div>
                    <?php else : ?>
                        <a href="?route=login" class="action-btn">
                            <i class="fas fa-user"></i>
                        </a>
                    <?php endif; ?>
                    
                    <a href="?route=cart" class="action-btn cart-btn">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="cart-count">0</span>
                    </a>
                </div>
                
                <button class="mobile-menu-toggle" id="mobile-menu-toggle">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
        
        <!-- Search overlay -->
        <div class="search-overlay" id="search-overlay">
            <div class="search-container">
                <input type="text" id="search-input" placeholder="Rechercher un produit...">
                <button id="search-submit">
                    <i class="fas fa-search"></i>
                </button>
                <button id="search-close" class="search-close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        
        <!-- Mobile menu -->
        <div class="mobile-menu" id="mobile-menu">
            <div class="mobile-menu-header">
                <div class="logo">
                    <a href="index.php">
                        <span class="logo-text">Foot<span class="logo-accent">cap</span></span>
                    </a>
                </div>
                <button class="mobile-menu-close" id="mobile-menu-close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <nav class="mobile-nav">
                <ul class="mobile-nav-list">
                    <li class="mobile-nav-item">
                        <a href="index.php" class="mobile-nav-link <?php echo $currentRoute === 'home' ? 'active' : ''; ?>">Accueil</a>
                    </li>
                    <li class="mobile-nav-item">
                        <a href="?route=shop" class="mobile-nav-link <?php echo $currentRoute === 'shop' ? 'active' : ''; ?>">Boutique</a>
                    </li>
                    <?php if ($isLoggedIn) : ?>
                        <li class="mobile-nav-item">
                            <a href="?route=orders" class="mobile-nav-link <?php echo $currentRoute === 'orders' ? 'active' : ''; ?>">Mes commandes</a>
                        </li>
                    <?php endif; ?>
                    <?php if ($isAdmin) : ?>
                        <li class="mobile-nav-item mobile-dropdown">
                            <a href="#" class="mobile-nav-link mobile-dropdown-toggle">
                                Admin <i class="fas fa-chevron-down"></i>
                            </a>
                            <ul class="mobile-dropdown-menu">
                                <li><a href="?route=admin-dashboard">Tableau de bord</a></li>
                                <li><a href="?route=admin-products">Produits</a></li>
                                <li><a href="?route=admin-orders">Commandes</a></li>
                            </ul>
                        </li>
                    <?php endif; ?>
                    
                    <?php if ($isLoggedIn) : ?>
                        <li class="mobile-nav-item">
                            <a href="?route=profile" class="mobile-nav-link">Mon profil</a>
                        </li>
                        <li class="mobile-nav-item">
                            <a href="#" id="mobile-logout-btn" class="mobile-nav-link logout-btn">Déconnexion</a>
                        </li>
                    <?php else : ?>
                        <li class="mobile-nav-item">
                            <a href="?route=login" class="mobile-nav-link">Connexion</a>
                        </li>
                        <li class="mobile-nav-item">
                            <a href="?route=register" class="mobile-nav-link">Inscription</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </div>
</header>




<script>
document.addEventListener('DOMContentLoaded', function() {
    // Mobile menu functionality
    const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
    const mobileMenuClose = document.getElementById('mobile-menu-close');
    const mobileMenu = document.getElementById('mobile-menu');
    
    if (mobileMenuToggle && mobileMenu) {
        mobileMenuToggle.addEventListener('click', function() {
            mobileMenu.classList.add('active');
            document.body.classList.add('no-scroll');
        });
    }
    
    if (mobileMenuClose && mobileMenu) {
        mobileMenuClose.addEventListener('click', function() {
            mobileMenu.classList.remove('active');
            document.body.classList.remove('no-scroll');
        });
    }
    
    // Mobile dropdown functionality
    const mobileDropdownToggles = document.querySelectorAll('.mobile-dropdown-toggle');
    mobileDropdownToggles.forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            const parent = this.closest('.mobile-dropdown');
            if (parent) {
                parent.classList.toggle('active');
            }
        });
    });
    
    // Search functionality
    const searchToggle = document.getElementById('search-toggle');
    const searchClose = document.getElementById('search-close');
    const searchOverlay = document.getElementById('search-overlay');
    const searchInput = document.getElementById('search-input');
    const searchSubmit = document.getElementById('search-submit');
    
    if (searchToggle && searchOverlay) {
        searchToggle.addEventListener('click', function() {
            searchOverlay.classList.add('active');
            document.body.classList.add('no-scroll');
            setTimeout(() => {
                if (searchInput) searchInput.focus();
            }, 300);
        });
    }
    
    if (searchClose && searchOverlay) {
        searchClose.addEventListener('click', function() {
            searchOverlay.classList.remove('active');
            document.body.classList.remove('no-scroll');
        });
    }
    
    if (searchSubmit && searchInput) {
        searchSubmit.addEventListener('click', function() {
            const searchTerm = searchInput.value.trim();
            if (searchTerm) {
                window.location.href = `?route=shop&search=${encodeURIComponent(searchTerm)}`;
            }
        });
        
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                searchSubmit.click();
            }
        });
    }
    
    // User dropdown functionality
    const userBtn = document.querySelector('.user-btn');
    const userDropdown = document.querySelector('.user-dropdown');
    
    if (userBtn && userDropdown) {
        userBtn.addEventListener('click', function(e) {
            e.preventDefault();
            userDropdown.classList.toggle('active');
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (userDropdown && !e.target.closest('.user-dropdown')) {
                userDropdown.classList.remove('active');
            }
        });
    }
    
    // Logout functionality
    const logoutBtns = document.querySelectorAll('.logout-btn');
    logoutBtns.forEach(btn => {
        btn.addEventListener('click', async function(e) {
            e.preventDefault();
            
            try {
                const response = await fetch('/api/utilisateur.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'deconnecter'
                    }),
                });
                
                const result = await response.json();
                
                if (result.success) {
                    // Show notification
                    showNotification('Déconnexion réussie', 'success');
                    
                    // Redirect to home
                    setTimeout(() => {
                        window.location.href = 'index.php';
                    }, 1000);
                } else {
                    showNotification(result.message || 'Erreur lors de la déconnexion', 'error');
                }
            } catch (error) {
                console.error('Error during logout:', error);
                showNotification('Erreur lors de la déconnexion', 'error');
            }
        });
    });
    
    // Update cart count
    updateCartCount();
    
    function updateCartCount() {
        fetch('/api/panier.php', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const cartCountElements = document.querySelectorAll('.cart-count');
                const itemCount = data.panier && data.panier.contenu ? data.panier.contenu.length : 0;
                
                cartCountElements.forEach(element => {
                    element.textContent = itemCount;
                });
            }
        })
        .catch(error => {
            console.log('Non connecté ou erreur de panier:', error);
        });
    }
    
    function showNotification(message, type) {
        // Check if notification container exists, if not create it
        let notificationContainer = document.querySelector('.notification-container');
        
        if (!notificationContainer) {
            notificationContainer = document.createElement('div');
            notificationContainer.className = 'notification-container';
            document.body.appendChild(notificationContainer);
            
            // Add styles
            const style = document.createElement('style');
            style.textContent = `
                .notification-container {
                    position: fixed;
                    top: 20px;
                    right: 20px;
                    z-index: 9999;
                }
                
                .notification {
                    background-color: white;
                    border-radius: 4px;
                    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
                    color: #333;
                    font-size: 14px;
                    margin-bottom: 10px;
                    max-width: 300px;
                    padding: 15px 20px;
                    position: relative;
                    transform: translateX(100%);
                    transition: transform 0.3s ease;
                }
                
                .notification.show {
                    transform: translateX(0);
                }
                
                .notification.success {
                    border-left: 4px solid #4ecdc4;
                }
                
                .notification.error {
                    border-left: 4px solid #ff6b6b;
                }
                
                .notification.info {
                    border-left: 4px solid #4e8dc4;
                }
            `;
            document.head.appendChild(style);
        }
        
        // Create notification
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.textContent = message;
        
        // Add to container
        notificationContainer.appendChild(notification);
        
        // Show notification
        setTimeout(() => {
            notification.classList.add('show');
        }, 10);
        
        // Remove notification after 3 seconds
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => {
                notification.remove();
            }, 300);
        }, 3000);
    }
});
</script>