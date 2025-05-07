<main class="container">
    <div class="auth-container">
        <div class="auth-box register-box">
            <h1 class="auth-title">Inscription</h1>
            
            <div id="register-message"></div>
            
            <form id="register-form" class="auth-form">
                <div class="form-row">
                    <div class="form-group">
                        <label for="nom" class="form-label">Nom</label>
                        <div class="input-with-icon">
                            <i class="fas fa-user"></i>
                            <input type="text" id="nom" class="form-control" required placeholder="Votre nom">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="prenom" class="form-label">Prénom</label>
                        <div class="input-with-icon">
                            <i class="fas fa-user"></i>
                            <input type="text" id="prenom" class="form-control" required placeholder="Votre prénom">
                        </div>
                    </div>
                </div>
                
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
                        <input type="password" id="mot_de_passe" class="form-control" required placeholder="Créez un mot de passe">
                        <button type="button" class="toggle-password" id="toggle-password">
                            <i class="fas fa-eye-slash"></i>
                        </button>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="password_confirm" class="form-label">Confirmer le mot de passe</label>
                    <div class="input-with-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="password_confirm" class="form-control" required placeholder="Confirmez votre mot de passe">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="adresse" class="form-label">Adresse</label>
                    <div class="input-with-icon">
                        <i class="fas fa-map-marker-alt"></i>
                        <input type="text" id="adresse" class="form-control" placeholder="Votre adresse complète">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="telephone" class="form-label">Téléphone</label>
                    <div class="input-with-icon">
                        <i class="fas fa-phone"></i>
                        <input type="text" id="telephone" class="form-control" placeholder="Votre numéro de téléphone">
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">
                    <i class="fas fa-user-plus"></i> S'inscrire
                </button>
            </form>
            
            <div class="auth-divider">
                <span>ou</span>
            </div>
            
            <p class="auth-footer">
                Vous avez déjà un compte ? <a href="?route=login" class="link-primary">Se connecter</a>
            </p>
        </div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const registerForm = document.getElementById('register-form');
    const messageContainer = document.getElementById('register-message');
    const togglePassword = document.getElementById('toggle-password');
    const passwordInput = document.getElementById('mot_de_passe');
    
    // Toggle password visibility
    togglePassword.addEventListener('click', function() {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        this.innerHTML = type === 'password' ? '<i class="fas fa-eye-slash"></i>' : '<i class="fas fa-eye"></i>';
    });
    
    // Handle registration form submission
    registerForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const userData = {
            nom: document.getElementById('nom').value,
            prenom: document.getElementById('prenom').value,
            email: document.getElementById('email').value,
            mot_de_passe: document.getElementById('mot_de_passe').value,
            adresse: document.getElementById('adresse').value,
            telephone: document.getElementById('telephone').value
        };
        
        const passwordConfirm = document.getElementById('password_confirm').value;
        
        // Validate passwords match
        if (userData.mot_de_passe !== passwordConfirm) {
            messageContainer.innerHTML = '<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Les mots de passe ne correspondent pas.</div>';
            return;
        }
        
        messageContainer.innerHTML = '<div class="loading">Inscription en cours...</div>';
        registerForm.classList.add('processing');
        
        try {
            const data = await UserAPI.register(userData);
            
            messageContainer.innerHTML = '<div class="alert alert-success"><i class="fas fa-check-circle"></i> Inscription réussie ! Vous allez être redirigé vers la page de connexion...</div>';
            
            // Redirect after successful registration
            setTimeout(() => {
                window.location.href = '?route=login';
            }, 2000);
            
        } catch (error) {
            registerForm.classList.remove('processing');
            messageContainer.innerHTML = `<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> ${error.message || 'Erreur lors de l\'inscription'}</div>`;
        }
    });
});
</script>