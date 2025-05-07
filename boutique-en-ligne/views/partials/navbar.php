<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
$isLoggedIn = isset($_SESSION['utilisateur']);
$isAdmin = $isLoggedIn && isset($_SESSION['utilisateur']['est_admin']) && $_SESSION['utilisateur']['est_admin'] === true;
$userName = $isLoggedIn ? $_SESSION['utilisateur']['prenom'] : '';
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
                        <a href="index.php" class="nav-link <?php echo (!isset($_GET['route']) || $_GET['route'] === 'home') ? 'active' : ''; ?>">Accueil</a>
                    </li>
                    <li class="nav-item">
                        <a href="?route=shop" class="nav-link <?php echo (isset($_GET['route']) && $_GET['route'] === 'shop') ? 'active' : ''; ?>">Boutique</a>
                    </li>
                    <?php if ($isLoggedIn) : ?>
                        <li class="nav-item">
                            <a href="?route=orders" class="nav-link <?php echo (isset($_GET['route']) && $_GET['route'] === 'orders') ? 'active' : ''; ?>">Mes commandes</a>
                        </li>
                    <?php endif; ?>
                    <?php if ($isAdmin) : ?>
                        <li class="nav-item dropdown">
                            <a href="#" class="nav-link">Admin <i class="fas fa-chevron-down"></i></a>
                            <ul class="dropdown-menu">
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
                    <span class="bar"></span>
                    <span class="bar"></span>
                    <span class="bar"></span>
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
                        <a href="index.php" class="mobile-nav-link <?php echo (!isset($_GET['route']) || $_GET['route'] === 'home') ? 'active' : ''; ?>">Accueil</a>
                    </li>
                    <li class="mobile-nav-item">
                        <a href="?route=shop" class="mobile-nav-link <?php echo (isset($_GET['route']) && $_GET['route'] === 'shop') ? 'active' : ''; ?>">Boutique</a>
                    </li>
                    <?php if ($isLoggedIn) : ?>
                        <li class="mobile-nav-item">
                            <a href="?route=orders" class="mobile-nav-link <?php echo (isset($_GET['route']) && $_GET['route'] === 'orders') ? 'active' : ''; ?>">Mes commandes</a>
                        </li>
                    <?php endif; ?>
                    <?php if ($isAdmin) : ?>
                        <li class="mobile-nav-item mobile-dropdown">
                            <a href="#" class="mobile-nav-link mobile-dropdown-toggle">
                                Admin <i class="fas fa-chevron-down"></i>
                            </a>
                            <ul class="mobile-dropdown-menu">
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
    
    mobileMenuToggle.addEventListener('click', function() {
        mobileMenu.classList.add('active');
        document.body.classList.add('no-scroll');
    });
    
    mobileMenuClose.addEventListener('click', function() {
        mobileMenu.classList.remove('active');
        document.body.classList.remove('no-scroll');
    });
    
    // Mobile dropdown functionality
    const mobileDropdownToggles = document.querySelectorAll('.mobile-dropdown-toggle');
    mobileDropdownToggles.forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            this.closest('.mobile-dropdown').classList.toggle('active');
        });
    });
    
    // Search functionality
    const searchToggle = document.getElementById('search-toggle');
    const searchClose = document.getElementById('search-close');
    const searchOverlay = document.getElementById('search-overlay');
    const searchInput = document.getElementById('search-input');
    const searchSubmit = document.getElementById('search-submit');
    
    searchToggle.addEventListener('click', function() {
        searchOverlay.classList.add('active');
        setTimeout(() => {
            searchInput.focus();
        }, 300);
    });
    
    searchClose.addEventListener('click', function() {
        searchOverlay.classList.remove('active');
    });
    
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
    
    // User dropdown functionality
    const userBtn = document.querySelector('.user-btn');
    if (userBtn) {
        userBtn.addEventListener('click', function(e) {
            e.preventDefault();
            this.parentElement.classList.toggle('active');
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.user-dropdown')) {
                document.querySelector('.user-dropdown').classList.remove('active');
            }
        });
    }
    
    // Logout functionality
    const logoutBtns = document.querySelectorAll('.logout-btn');
    logoutBtns.forEach(btn => {
        btn.addEventListener('click', async function(e) {
            e.preventDefault();
            
            try {
                const result = await UserAPI.logout();
                
                if (result.success) {
                    // Show success notification
                    showNotification('Déconnexion réussie', 'success');
                    
                    // Redirect to home page after logout
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
        CartAPI.getContents()
            .then(data => {
                if (data.success) {
                    const cartCountElements = document.querySelectorAll('.cart-count');
                    cartCountElements.forEach(element => {
                        element.textContent = data.panier.nombre_articles || 0;
                    });
                }
            })
            .catch(error => {
                console.log('Non connecté ou erreur de panier:', error);
            });
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