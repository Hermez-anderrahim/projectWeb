<main class="container">
    <div class="auth-container">
        <div class="auth-box">
            <h1 class="auth-title">Connexion</h1>
            
            <div id="login-message"></div>
            
            <form id="login-form" class="auth-form">
                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <div class="input-with-icon">
                        <i class="fas fa-envelope"></i>
                        <input type="email" id="email" class="form-control" required placeholder="Votre adresse email">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="mot_de_passe" class="form-label">Mot de passe</label>
                    <div class="input-with-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="mot_de_passe" class="form-control" required placeholder="Votre mot de passe">
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