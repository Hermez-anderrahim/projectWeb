<link rel="stylesheet" href="/views/auth/auth-styles.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<!-- Add the API.js script before using UserAPI -->
<script src="/js/api.js"></script>

<main class="container">
    <div class="auth-container">
        <div class="auth-box">
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
        
        const passwordValue = document.getElementById('mot_de_passe').value;
        const passwordConfirm = document.getElementById('password_confirm').value;
        
        // Validate passwords match
        if (passwordValue !== passwordConfirm) {
            messageContainer.innerHTML = '<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Les mots de passe ne correspondent pas.</div>';
            return;
        }
        
        messageContainer.innerHTML = '<div class="loading">Inscription en cours...</div>';
        registerForm.classList.add('processing');
        
        try {
            // Send the registration request directly to ensure correct parameter names
            const response = await fetch('/api/utilisateur.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    action: 'inscrire',
                    nom: document.getElementById('nom').value,
                    prenom: document.getElementById('prenom').value,
                    email: document.getElementById('email').value,
                    password: passwordValue, // Using the correct parameter name expected by the API
                    adresse: document.getElementById('adresse').value,
                    telephone: document.getElementById('telephone').value
                }),
                credentials: 'include'
            });
            
            const data = await response.json();
            
            if (data.success) {
                messageContainer.innerHTML = '<div class="alert alert-success"><i class="fas fa-check-circle"></i> Inscription réussie ! Vous allez être redirigé vers la page de connexion...</div>';
                
                // Redirect after successful registration
                setTimeout(() => {
                    window.location.href = '?route=login';
                }, 2000);
            } else {
                registerForm.classList.remove('processing');
                messageContainer.innerHTML = `<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> ${data.message || 'Erreur lors de l\'inscription'}</div>`;
            }
            
        } catch (error) {
            registerForm.classList.remove('processing');
            messageContainer.innerHTML = `<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> ${error.message || 'Erreur lors de l\'inscription'}</div>`;
        }
    });
});
</script>