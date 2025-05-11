// assets/js/auth.js
document.addEventListener("DOMContentLoaded", function () {
  const loginForm = document.getElementById("login-form");
  const registerForm = document.getElementById("register-form");

  if (loginForm) {
    loginForm.addEventListener("submit", handleLogin);
  }

  if (registerForm) {
    registerForm.addEventListener("submit", handleRegister);
  }
});

async function handleLogin(e) {
  e.preventDefault();

  const email = document.getElementById("email").value;
  const mot_de_passe = document.getElementById("mot_de_passe").value;
  const messageContainer = document.getElementById("login-message");

  messageContainer.innerHTML =
    '<div class="loading">Connexion en cours...</div>';

  try {
    // Use the existing UserAPI from api.js
    await UserAPI.login(email, mot_de_passe);
    messageContainer.innerHTML =
      '<div class="alert alert-success">Connexion réussie ! Redirection en cours...</div>';
    setTimeout(() => {
      window.location.href = "index.php";
    }, 1000);
  } catch (error) {
    messageContainer.innerHTML = `<div class="alert alert-error">${error.message}</div>`;
  }
}

async function handleRegister(e) {
  e.preventDefault();

  const userData = {
    nom: document.getElementById("nom").value,
    prenom: document.getElementById("prenom").value,
    email: document.getElementById("email").value,
    mot_de_passe: document.getElementById("mot_de_passe").value,
    adresse: document.getElementById("adresse").value,
    telephone: document.getElementById("telephone").value,
  };

  const passwordConfirm = document.getElementById("password_confirm").value;
  const messageContainer = document.getElementById("register-message");

  // Validate passwords match
  if (userData.mot_de_passe !== passwordConfirm) {
    messageContainer.innerHTML =
      '<div class="alert alert-error">Les mots de passe ne correspondent pas.</div>';
    return;
  }

  messageContainer.innerHTML =
    '<div class="loading">Inscription en cours...</div>';

  try {
    // Use the existing UserAPI from api.js
    await UserAPI.register(userData);
    messageContainer.innerHTML =
      '<div class="alert alert-success">Inscription réussie ! Vous allez être redirigé vers la page de connexion...</div>';
    setTimeout(() => {
      window.location.href = "index.php?route=login";
    }, 2000);
  } catch (error) {
    messageContainer.innerHTML = `<div class="alert alert-error">${error.message}</div>`;
  }
}
