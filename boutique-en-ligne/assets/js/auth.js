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

// Implementation of UserAPI
const UserAPI = {
  // Base URL for API endpoints
  baseUrl: "/api",

  // Login user
  async login(email, mot_de_passe) {
    try {
      const response = await fetch(`${this.baseUrl}/utilisateur.php`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          action: "connecter",
          email: email,
          mot_de_passe: mot_de_passe,
        }),
      });

      const data = await response.json();

      if (!data.success) {
        throw new Error(data.message || "Échec de la connexion");
      }

      return data;
    } catch (error) {
      throw error;
    }
  },

  // Register user
  async register(userData) {
    try {
      const response = await fetch(`${this.baseUrl}/utilisateur.php`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          action: "inscrire",
          ...userData,
        }),
      });

      const data = await response.json();

      if (!data.success) {
        throw new Error(data.message || "Échec de l'inscription");
      }

      return data;
    } catch (error) {
      throw error;
    }
  },

  // Logout user
  async logout() {
    try {
      const response = await fetch(`${this.baseUrl}/utilisateur.php`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          action: "deconnecter",
        }),
      });

      const data = await response.json();

      if (!data.success) {
        throw new Error(data.message || "Échec de la déconnexion");
      }

      return data;
    } catch (error) {
      throw error;
    }
  },

  // Get user info
  async getUserInfo() {
    try {
      const response = await fetch(`${this.baseUrl}/utilisateur.php`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          action: "getInfosUtilisateur",
        }),
      });

      const data = await response.json();

      if (!data.success) {
        throw new Error(
          data.message ||
            "Échec de la récupération des informations utilisateur"
        );
      }

      return data.utilisateur;
    } catch (error) {
      throw error;
    }
  },

  // Update user info
  async updateUserInfo(userData) {
    try {
      const response = await fetch(`${this.baseUrl}/utilisateur.php`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          action: "mettreAJour",
          ...userData,
        }),
      });

      const data = await response.json();

      if (!data.success) {
        throw new Error(
          data.message || "Échec de la mise à jour des informations utilisateur"
        );
      }

      return data;
    } catch (error) {
      throw error;
    }
  },

  // Change password
  async changePassword(ancien_mot_de_passe, nouveau_mot_de_passe) {
    try {
      const response = await fetch(`${this.baseUrl}/utilisateur.php`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          action: "changerMotDePasse",
          ancien_mot_de_passe,
          nouveau_mot_de_passe,
        }),
      });

      const data = await response.json();

      if (!data.success) {
        throw new Error(data.message || "Échec du changement de mot de passe");
      }

      return data;
    } catch (error) {
      throw error;
    }
  },
};
