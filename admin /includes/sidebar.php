<div class="admin-sidebar">
    <div class="admin-logo">
        <a href="index.php">Anon Admin</a>
    </div>
    <nav class="admin-menu">
        <a href="index.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
        <a href="products/index.php" class="<?php echo strpos($_SERVER['PHP_SELF'], 'products') !== false ? 'active' : ''; ?>">
            <i class="fas fa-box"></i> Products
        </a>
        <a href="categories/index.php" class="<?php echo strpos($_SERVER['PHP_SELF'], 'categories') !== false ? 'active' : ''; ?>">
            <i class="fas fa-tags"></i> Categories
        </a>
        <a href="orders/index.php" class="<?php echo strpos($_SERVER['PHP_SELF'], 'orders') !== false ? 'active' : ''; ?>">
            <i class="fas fa-shopping-bag"></i> Orders
        </a>
        <a href="users/index.php" class="<?php echo strpos($_SERVER['PHP_SELF'], 'users') !== false ? 'active' : ''; ?>">
            <i class="fas fa-users"></i> Users
        </a>
        <a href="settings/index.php" class="<?php echo strpos($_SERVER['PHP_SELF'], 'settings') !== false ? 'active' : ''; ?>">
            <i class="fas fa-cog"></i> Settings
        </a>
        <a href="../index.php">
            <i class="fas fa-store"></i> View Website
        </a>
        <a href="../logout.php">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </nav>
</div>