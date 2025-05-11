<?php
// views/users/profile.php
// Ensure user is logged in
if (!isset($_SESSION['utilisateur'])) {
    header('Location: ?route=login');
    exit;
}

$user = $_SESSION['utilisateur'];
$userId = $user['id'];
$firstName = $user['prenom'] ?? '';
$lastName = $user['nom'] ?? '';
$email = $user['email'] ?? '';

// Get messages if any
$successMessage = $_SESSION['success_message'] ?? null;
$errorMessage = $_SESSION['error_message'] ?? null;

// Clear messages after display
unset($_SESSION['success_message']);
unset($_SESSION['error_message']);
?>

<section class="profile-section">
    <div class="container">
        <div class="section-header">
            <h1 class="section-title">Mon Profil</h1>
            <p class="section-subtitle">Gérez vos informations personnelles</p>
        </div>

        <?php if ($successMessage): ?>
        <div class="alert alert-success">
            <?php echo htmlspecialchars($successMessage); ?>
        </div>
        <?php endif; ?>

        <?php if ($errorMessage): ?>
        <div class="alert alert-danger">
            <?php echo htmlspecialchars($errorMessage); ?>
        </div>
        <?php endif; ?>

        <div class="profile-container">
            <div class="profile-card">
                <div class="profile-header">
                    <div class="profile-avatar">
                        <i class="fas fa-user-circle"></i>
                    </div>
                    <div class="profile-title">
                        <h2><?php echo htmlspecialchars($firstName . ' ' . $lastName); ?></h2>
                        <p><?php echo htmlspecialchars($email); ?></p>
                    </div>
                </div>

                <div class="profile-content">
                    <div class="profile-form-container">
                        <h3>Informations Personnelles</h3>
                        <form id="personal-info-form" class="profile-form">
                            <input type="hidden" name="action" value="update_info">
                            <div class="form-group">
                                <label for="prenom">Prénom</label>
                                <input type="text" id="prenom" name="prenom" value="<?php echo htmlspecialchars($firstName); ?>" class="form-control">
                            </div>

                            <div class="form-group">
                                <label for="nom">Nom</label>
                                <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($lastName); ?>" class="form-control">
                            </div>

                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" class="form-control">
                            </div>

                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary">Mettre à jour</button>
                            </div>
                        </form>
                    </div>

                    <div class="profile-form-container">
                        <h3>Changer le mot de passe</h3>
                        <form id="password-form" class="profile-form">
                            <input type="hidden" name="action" value="update_password">
                            <div class="form-group">
                                <label for="current_password">Mot de passe actuel</label>
                                <input type="password" id="current_password" name="current_password" class="form-control">
                            </div>

                            <div class="form-group">
                                <label for="new_password">Nouveau mot de passe</label>
                                <input type="password" id="new_password" name="new_password" class="form-control">
                            </div>

                            <div class="form-group">
                                <label for="confirm_password">Confirmer le mot de passe</label>
                                <input type="password" id="confirm_password" name="confirm_password" class="form-control">
                            </div>

                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary">Modifier le mot de passe</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="profile-sidebar">
                <div class="profile-links">
                    <h3>Mon compte</h3>
                    <ul>
                        <li><a href="?route=profile" class="active"><i class="fas fa-user"></i> Mon profil</a></li>
                        <li><a href="?route=orders"><i class="fas fa-shopping-bag"></i> Mes commandes</a></li>
                        <li><a href="#" id="logout-link" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Déconnexion</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Personal Info Form Submission
    const personalInfoForm = document.getElementById('personal-info-form');
    if (personalInfoForm) {
        personalInfoForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const formDataObj = {};
            formData.forEach((value, key) => {
                formDataObj[key] = value;
            });
            
            try {
                const response = await fetch('/api/utilisateur.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(formDataObj),
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showNotification('Informations mises à jour avec succès', 'success');
                    // Update session data
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    showNotification(result.message || 'Erreur lors de la mise à jour', 'error');
                }
            } catch (error) {
                console.error('Error updating profile:', error);
                showNotification('Erreur lors de la mise à jour du profil', 'error');
            }
        });
    }
    
    // Password Form Submission
    const passwordForm = document.getElementById('password-form');
    if (passwordForm) {
        passwordForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const formDataObj = {};
            formData.forEach((value, key) => {
                formDataObj[key] = value;
            });
            
            // Validation
            if (formDataObj.new_password !== formDataObj.confirm_password) {
                showNotification('Les mots de passe ne correspondent pas', 'error');
                return;
            }
            
            try {
                const response = await fetch('/api/utilisateur.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(formDataObj),
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showNotification('Mot de passe mis à jour avec succès', 'success');
                    passwordForm.reset();
                } else {
                    showNotification(result.message || 'Erreur lors de la mise à jour du mot de passe', 'error');
                }
            } catch (error) {
                console.error('Error updating password:', error);
                showNotification('Erreur lors de la mise à jour du mot de passe', 'error');
            }
        });
    }
    
    // Logout functionality
    const logoutLink = document.getElementById('logout-link');
    if (logoutLink) {
        logoutLink.addEventListener('click', async function(e) {
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
                    showNotification('Déconnexion réussie', 'success');
                    
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
    }
    
    function showNotification(message, type) {
        // Check if notification container exists, if not create it
        let notificationContainer = document.querySelector('.notification-container');
        
        if (!notificationContainer) {
            notificationContainer = document.createElement('div');
            notificationContainer.className = 'notification-container';
            document.body.appendChild(notificationContainer);
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