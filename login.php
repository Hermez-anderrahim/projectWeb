<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Anon Shop</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <?php 
    include 'includes/header.php';
    
    // Start session if not already started
    if(!isset($_SESSION)) {
        session_start();
    }
    
    // Check if user is already logged in
    if(isset($_SESSION['user_id'])) {
        // Redirect to home page or redirect URL
        $redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'index.php';
        header('Location: ' . $redirect);
        exit;
    }
    
    // Include database and user model
    include_once 'backend/config/db_connect.php';
    include_once 'backend/models/User.php';
    
    $error_message = '';
    
    // Handle login form submission
    if($_SERVER['REQUEST_METHOD'] === 'POST') {
        $database = new Database();
        $db = $database->getConnection();
        
        $user = new User($db);
        $user->email = $_POST['email'];
        $user->password = $_POST['password'];
        
        if($user->login()) {
            // Redirect to home page or redirect URL
            $redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'index.php';
            header('Location: ' . $redirect);
            exit;
        } else {
            $error_message = 'Invalid email or password.';
        }
    }
    ?>

    <main class="container">
        <div class="breadcrumb">
            <a href="index.php">Home</a> &gt; 
            <span>Login</span>
        </div>

        <div class="auth-container">
            <h1>Login</h1>
            
            <?php if($error_message): ?>
            <div class="alert error">
                <?php echo $error_message; ?>
            </div>
            <?php endif; ?>
            
            <form class="auth-form" action="login.php<?php echo isset($_GET['redirect']) ? '?redirect=' . urlencode($_GET['redirect']) : ''; ?>" method="POST">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="remember"> Remember me
                    </label>
                </div>
                
                <button type="submit" class="btn">Login</button>
            </form>
            
            <div class="auth-links">
                <p>Don't have an account? <a href="register.php<?php echo isset($_GET['redirect']) ? '?redirect=' . urlencode($_GET['redirect']) : ''; ?>">Register</a></p>
                <p><a href="forgot-password.php">Forgot your password?</a></p>
            </div>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>
    
    <!-- Scripts -->
    <script src="assets/js/main.js"></script>
</body>
</html>