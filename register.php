<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Anon Shop</title>
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
    $success_message = '';
    
    // Handle registration form submission
    if($_SERVER['REQUEST_METHOD'] === 'POST') {
        $database = new Database();
        $db = $database->getConnection();
        
        $user = new User($db);
        $user->username = $_POST['username'];
        $user->email = $_POST['email'];
        $user->password = $_POST['password'];
        $user->first_name = $_POST['first_name'];
        $user->last_name = $_POST['last_name'];
        $user->address = $_POST['address'];
        $user->phone = $_POST['phone'];
        
        // Validate password match
        if($_POST['password'] !== $_POST['confirm_password']) {
            $error_message = 'Passwords do not match.';
        } else {
            $result = $user->register();
            
            if(isset($result['error'])) {
                $error_message = $result['error'];
            } else {
                // Auto login after registration
                $user->email = $_POST['email'];
                $user->password = $_POST['password'];
                
                if($user->login()) {
                    // Redirect to home page or redirect URL
                    $redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'index.php';
                    header('Location: ' . $redirect);
                    exit;
                } else {
                    $success_message = 'Registration successful! Please login with your credentials.';
                }
            }
        }
    }
    ?>

    <main class="container">
        <div class="breadcrumb">
            <a href="index.php">Home</a> &gt; 
            <span>Register</span>
        </div>

        <div class="auth-container">
            <h1>Create an Account</h1>
            
            <?php if($error_message): ?>
            <div class="alert error">
                <?php echo $error_message; ?>
            </div>
            <?php endif; ?>
            
            <?php if($success_message): ?>
            <div class="alert success">
                <?php echo $success_message; ?>
            </div>
            <?php endif; ?>
            
            <form class="auth-form" action="register.php<?php echo isset($_GET['redirect']) ? '?redirect=' . urlencode($_GET['redirect']) : ''; ?>" method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <label for="first_name">First Name</label>
                        <input type="text" id="first_name" name="first_name" required>
                    </div>
                    <div class="form-group">
                        <label for="last_name">Last Name</label>
                        <input type="text" id="last_name" name="last_name" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone">
                </div>
                
                <div class="form-group">
                    <label for="address">Address</label>
                    <input type="text" id="address" name="address">
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirm Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="terms" required>
                        I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>
                    </label>
                </div>
                
                <button type="submit" class="btn">Register</button>
            </form>
            
            <div class="auth-links">
                <p>Already have an account? <a href="login.php<?php echo isset($_GET['redirect']) ? '?redirect=' . urlencode($_GET['redirect']) : ''; ?>">Login</a></p>
            </div>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>
    
    <!-- Scripts -->
    <script src="assets/js/main.js"></script>
    <script>
        // Form validation
        document.querySelector('.auth-form').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if(password !== confirmPassword) {
                e.preventDefault();
                
                // Show error message
                const errorDiv = document.querySelector('.alert.error');
                if(errorDiv) {
                    errorDiv.textContent = 'Passwords do not match.';
                } else {
                    const newErrorDiv = document.createElement('div');
                    newErrorDiv.className = 'alert error';
                    newErrorDiv.textContent = 'Passwords do not match.';
                    
                    const h1 = document.querySelector('h1');
                    h1.insertAdjacentElement('afterend', newErrorDiv);
                }
                
                // Highlight password fields
                document.getElementById('password').classList.add('error');
                document.getElementById('confirm_password').classList.add('error');
            }
        });
    </script>
</body>
</html>