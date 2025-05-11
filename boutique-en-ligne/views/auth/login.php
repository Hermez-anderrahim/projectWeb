<!-- In the login.php file, near the top -->
<?php
// Check if admin account exists and create one if needed
require_once __DIR__ . '/../../utils/create_admin.php';
$adminCheck = createDefaultAdmin();
?>

<link rel="stylesheet" href="/views/auth/auth-styles.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<!-- Add the API.js script before using UserAPI -->
<script src="/js/api.js"></script>

<main class="container">
    <div class="auth-container">
        <div class="auth-box">
            <h1 class="auth-title">Connexion</h1>
            
            <div id="login-message">
                <?php if ($adminCheck['created']): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    <strong>Compte Administrateur Créé</strong>
                    <p>Un compte administrateur par défaut a été créé. Utilisez les identifiants suivants pour vous connecter:</p>
                    <p><strong>Email:</strong> admin@footcap.com</p>
                    <p><strong>Mot de passe:</strong> Admin123!</p>
                    <p class="small text-muted">Veuillez changer le mot de passe après votre première connexion.</p>
                </div>
                <?php endif; ?>
            </div>
            
            <form id="login-form" class="auth-form">
                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <div class="input-with-icon">
                        <i class="fas fa-envelope"></i>
                        <input type="email" id="email" class="form-control" required placeholder="Votre adresse email" value="<?php echo $adminCheck['created'] ? 'admin@footcap.com' : ''; ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="mot_de_passe" class="form-label">Mot de passe</label>
                    <div class="input-with-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="mot_de_passe" class="form-control" required placeholder="Votre mot de passe" value="<?php echo $adminCheck['created'] ? 'Admin123!' : ''; ?>">
                        <button type="button" class="toggle-password" id="toggle-password">
                            <i class="fas fa-eye-slash"></i>
                        </button>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">
                    <i class="fas fa-sign-in-alt"></i> Se connecter
                </button>
            </form>
            
            <div class="auth-divider">
                <span>ou</span>
            </div>
            
            <p class="auth-footer">
                Vous n'avez pas de compte ? <a href="?route=register" class="link-primary">S'inscrire</a>
            </p>
        </div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('login-form');
    const messageContainer = document.getElementById('login-message');
    const togglePassword = document.getElementById('toggle-password');
    const passwordInput = document.getElementById('mot_de_passe');
    
    // Toggle password visibility
    togglePassword.addEventListener('click', function() {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        this.innerHTML = type === 'password' ? '<i class="fas fa-eye-slash"></i>' : '<i class="fas fa-eye"></i>';
    });
    
    // Handle login form submission
    loginForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const email = document.getElementById('email').value;
        const mot_de_passe = document.getElementById('mot_de_passe').value;
        
        messageContainer.innerHTML = '<div class="loading">Connexion en cours...</div>';
        loginForm.classList.add('processing');
        
        try {
            const data = await UserAPI.login(email, mot_de_passe);
            
            messageContainer.innerHTML = '<div class="alert alert-success"><i class="fas fa-check-circle"></i> Connexion réussie ! Redirection en cours...</div>';
            
            // Redirect after successful login
            setTimeout(() => {
                window.location.href = '?route=home';
            }, 1500);
            
        } catch (error) {
            loginForm.classList.remove('processing');
            messageContainer.innerHTML = `<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> ${error.message || 'Erreur de connexion'}</div>`;
        }
    });
});
</script>