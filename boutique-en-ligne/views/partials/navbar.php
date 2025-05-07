<?php
// views/partials/navbar.php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
$isLoggedIn = isset($_SESSION['utilisateur']);
$isAdmin = $isLoggedIn && isset($_SESSION['utilisateur']['est_admin']) && $_SESSION['utilisateur']['est_admin'] === true;
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

<!-- CSS Additions - Add this to your style.css file -->
<style>
:root {
  /* Colors */
  --primary-color: #ff6b6b;
  --primary-light: #ffd8d8;
  --primary-dark: #e05050;
  --secondary-color: #4ecdc4;
  --accent-color: #ffe66d;
  --dark-color: #1a535c;
  --grey-color: #6c757d;
  --light-grey: #e9ecef;
  --lighter-grey: #f8f9fa;
  --white-color: #ffffff;
  --black-color: #212529;

  /* Spacing */
  --spacing-xs: 0.25rem;
  --spacing-sm: 0.5rem;
  --spacing-md: 1rem;
  --spacing-lg: 1.5rem;
  --spacing-xl: 2rem;
  --spacing-xxl: 3rem;
}

/* Header & Navigation */
.main-header {
  background-color: var(--white-color);
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  position: sticky;
  top: 0;
  z-index: 1000;
  width: 100%;
}

.header-wrapper {
  display: flex;
  justify-content: space-between;
  align-items: center;
  height: 80px;
  padding: 0 var(--spacing-md);
}

.logo {
  display: flex;
  align-items: center;
  font-size: 1.5rem;
  font-weight: 700;
  text-transform: uppercase;
}

.logo-text {
  color: var(--dark-color);
}

.logo-accent {
  color: var(--primary-color);
}

.main-nav {
  flex-grow: 1;
  display: flex;
  justify-content: center;
  margin: 0 var(--spacing-xl);
}

.nav-list {
  display: flex;
  gap: var(--spacing-xl);
  margin: 0;
  padding: 0;
  list-style: none;
}

.nav-link {
  font-weight: 500;
  padding: var(--spacing-xs) var(--spacing-sm);
  position: relative;
  color: var(--dark-color);
  text-decoration: none;
  transition: color 0.3s ease;
}

.nav-link:hover,
.nav-link.active {
  color: var(--primary-color);
}

.nav-link::after {
  content: "";
  position: absolute;
  bottom: -5px;
  left: 0;
  width: 0;
  height: 2px;
  background-color: var(--primary-color);
  transition: width 0.3s ease;
}

.nav-link:hover::after,
.nav-link.active::after {
  width: 100%;
}

/* Dropdown styling */
.dropdown {
  position: relative;
}

.dropdown-toggle {
  display: flex;
  align-items: center;
  gap: var(--spacing-xs);
}

.dropdown-toggle i {
  font-size: 0.8rem;
  transition: transform 0.3s ease;
}

.dropdown:hover .dropdown-toggle i {
  transform: rotate(180deg);
}

.dropdown-menu {
  position: absolute;
  top: 100%;
  left: 0;
  min-width: 180px;
  background-color: var(--white-color);
  border-radius: var(--radius-md);
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
  padding: var(--spacing-sm) 0;
  opacity: 0;
  visibility: hidden;
  transform: translateY(10px);
  transition: all 0.3s ease;
  z-index: 100;
}

.dropdown:hover .dropdown-menu {
  opacity: 1;
  visibility: visible;
  transform: translateY(0);
}

.dropdown-menu a {
  display: block;
  padding: var(--spacing-sm) var(--spacing-md);
  color: var(--dark-color);
  text-decoration: none;
  transition: background-color 0.3s ease;
}

.dropdown-menu a:hover {
  background-color: var(--lighter-grey);
  color: var(--primary-color);
}

.dropdown-divider {
  height: 1px;
  background-color: var(--light-grey);
  margin: var(--spacing-xs) 0;
}

/* Header actions */
.header-actions {
  display: flex;
  align-items: center;
  gap: var(--spacing-md);
}

.action-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: transparent;
  color: var(--dark-color);
  cursor: pointer;
  transition: background-color 0.3s ease;
  border: none;
  text-decoration: none;
}

.action-btn:hover {
  background-color: var(--lighter-grey);
  color: var(--primary-color);
}

.user-btn {
  display: flex;
  align-items: center;
  gap: var(--spacing-sm);
  width: auto;
  padding: 0 var(--spacing-sm);
  border-radius: 20px;
}

.user-name {
  font-weight: 500;
  margin-left: var(--spacing-xs);
}

.cart-btn {
  position: relative;
}

.cart-count {
  position: absolute;
  top: -5px;
  right: -5px;
  width: 20px;
  height: 20px;
  border-radius: 50%;
  background-color: var(--primary-color);
  color: var(--white-color);
  font-size: 0.75rem;
  font-weight: bold;
  display: flex;
  align-items: center;
  justify-content: center;
}

/* User dropdown */
.user-dropdown {
  position: relative;
}

.user-dropdown .dropdown-menu {
  right: 0;
  left: auto;
}

.user-dropdown.active .dropdown-menu {
  opacity: 1;
  visibility: visible;
  transform: translateY(0);
}

/* Mobile menu toggle */
.mobile-menu-toggle {
  display: none;
  flex-direction: column;
  justify-content: space-between;
  width: 24px;
  height: 18px;
  background: transparent;
  border: none;
  cursor: pointer;
}

.mobile-menu-toggle i {
  font-size: 1.5rem;
  color: var(--dark-color);
}

/* Search overlay */
.search-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.8);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 2000;
  opacity: 0;
  visibility: hidden;
  transition: all 0.3s ease;
}

.search-overlay.active {
  opacity: 1;
  visibility: visible;
}

.search-container {
  width: 80%;
  max-width: 600px;
  position: relative;
}

.search-container input {
  width: 100%;
  padding: var(--spacing-md) var(--spacing-lg);
  font-size: 1.25rem;
  border: none;
  border-radius: 30px;
  background-color: var(--white-color);
}

.search-container input:focus {
  outline: none;
  box-shadow: 0 0 10px rgba(255, 255, 255, 0.5);
}

.search-container button {
  position: absolute;
  right: 15px;
  top: 50%;
  transform: translateY(-50%);
  background: transparent;
  border: none;
  cursor: pointer;
  font-size: 1.25rem;
  color: var(--dark-color);
}

.search-close {
  position: absolute;
  top: 20px;
  right: 20px;
  background: transparent;
  border: none;
  color: var(--white-color);
  font-size: 1.5rem;
  cursor: pointer;
}

/* Mobile menu */
.mobile-menu {
  position: fixed;
  top: 0;
  left: 0;
  width: 80%;
  max-width: 320px;
  height: 100%;
  background-color: var(--white-color);
  z-index: 1500;
  transform: translateX(-100%);
  transition: transform 0.3s ease;
  overflow-y: auto;
  box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
}

.mobile-menu.active {
  transform: translateX(0);
}

.mobile-menu-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: var(--spacing-md);
  border-bottom: 1px solid var(--light-grey);
}

.mobile-menu-close {
  background: transparent;
  border: none;
  font-size: 1.25rem;
  color: var(--dark-color);
  cursor: pointer;
}

.mobile-nav-list {
  padding: var(--spacing-md);
  margin: 0;
  list-style: none;
}

.mobile-nav-item {
  margin-bottom: var(--spacing-md);
}

.mobile-nav-link {
  display: block;
  font-size: 1.1rem;
  font-weight: 500;
  color: var(--dark-color);
  text-decoration: none;
  padding: var(--spacing-sm) 0;
  transition: color 0.3s ease;
}

.mobile-nav-link:hover,
.mobile-nav-link.active {
  color: var(--primary-color);
}

/* Mobile dropdown */
.mobile-dropdown {
  position: relative;
}

.mobile-dropdown-toggle {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.mobile-dropdown-toggle i {
  transition: transform 0.3s ease;
}

.mobile-dropdown.active .mobile-dropdown-toggle i {
  transform: rotate(180deg);
}

.mobile-dropdown-menu {
  padding-left: var(--spacing-md);
  list-style: none;
  max-height: 0;
  overflow: hidden;
  transition: max-height 0.3s ease;
}

.mobile-dropdown.active .mobile-dropdown-menu {
  max-height: 200px; /* Adjust as needed */
}

.mobile-dropdown-menu a {
  display: block;
  padding: var(--spacing-sm) 0;
  color: var(--dark-color);
  text-decoration: none;
  transition: color 0.3s ease;
}

.mobile-dropdown-menu a:hover {
  color: var(--primary-color);
}

/* No-scroll class for body when mobile menu is open */
body.no-scroll {
  overflow: hidden;
}

/* Responsive styles */
@media (max-width: 992px) {
  .main-nav {
    display: none;
  }
  
  .mobile-menu-toggle {
    display: flex;
  }
  
  .user-name {
    display: none;
  }
}

@media (max-width: 576px) {
  .header-wrapper {
    padding: 0 var(--spacing-sm);
    height: 60px;
  }
  
  .logo {
    font-size: 1.25rem;
  }
  
  .action-btn {
    width: 35px;
    height: 35px;
  }
  
  .header-actions {
    gap: var(--spacing-sm);
  }
}
</style>

<!-- JavaScript - Add this to the end of your navbar.php file -->
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