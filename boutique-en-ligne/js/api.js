// js/api.js
const API_BASE_URL = ""; // Relative URL so it works on any domain

// Utility function for API calls
async function fetchAPI(endpoint, method = "GET", data = null) {
  const options = {
    method,
    headers: {
      "Content-Type": "application/json",
    },
    credentials: "include", // Include cookies for session
  };

  if (data && (method === "POST" || method === "PUT")) {
    options.body = JSON.stringify(data);
  }

  try {
    const response = await fetch(`${API_BASE_URL}${endpoint}`, options);
    const result = await response.json();

    if (!response.ok) {
      throw new Error(result.message || "Une erreur est survenue");
    }

    return result;
  } catch (error) {
    console.error("API Error:", error);
    throw error;
  }
}

// User API
const UserAPI = {
  login: (email, password) =>
    fetchAPI("/api/utilisateur/connecter", "POST", { email, password }),
  register: (userData) => fetchAPI("/api/utilisateur", "POST", userData),
  logout: () => fetchAPI("/api/utilisateur/deconnecter", "POST"),
  getProfile: () => fetchAPI("/api/utilisateur"),
  updateProfile: (userData) => fetchAPI("/api/utilisateur", "PUT", userData),
  changePassword: (passwordData) =>
    fetchAPI("/api/utilisateur/mot-de-passe", "POST", passwordData),
};

// Product API
const ProductAPI = {
  getAll: (page = 1, limit = 12, category = null) => {
    let url = `/api/produit?page=${page}&limite=${limit}`;
    if (category) url += `&categorie=${category}`;
    return fetchAPI(url);
  },
  getById: (id) => fetchAPI(`/api/produit/${id}`),
  search: (term, category = null, minPrice = null, maxPrice = null) => {
    let url = `/api/produit/recherche?terme=${term}`;
    if (category) url += `&categorie=${category}`;
    if (minPrice) url += `&prix_min=${minPrice}`;
    if (maxPrice) url += `&prix_max=${maxPrice}`;
    return fetchAPI(url);
  },
  create: (productData) => fetchAPI("/api/produit", "POST", productData),
  update: (id, productData) =>
    fetchAPI(`/api/produit/${id}`, "PUT", productData),
  delete: (id) => fetchAPI(`/api/produit/${id}`, "DELETE"),
};

// Cart API
const CartAPI = {
  getContents: () => fetchAPI("/api/panier"),
  addItem: (productId, quantity = 1) =>
    fetchAPI(`/api/panier/ajouter/${productId}`, "POST", {
      quantite: quantity,
    }),
  updateQuantity: (productId, quantity) =>
    fetchAPI(`/api/panier/mettre-a-jour/${productId}`, "POST", {
      quantite: quantity,
    }),
  removeItem: (productId) =>
    fetchAPI(`/api/panier/supprimer/${productId}`, "POST"),
  clear: () => fetchAPI("/api/panier/vider", "POST"),
};

// Order API
const OrderAPI = {
  getHistory: () => fetchAPI("/api/commande"),
  getDetails: (id) => fetchAPI(`/api/commande/${id}`),
  create: () => fetchAPI("/api/commande/creer", "POST"),
  cancel: (id, reason) =>
    fetchAPI(`/api/commande/annuler/${id}`, "POST", { raison: reason }),
  // Admin functions
  getAllOrders: (status = null, page = 1, limit = 20) => {
    let url = `/api/commande/admin/toutes?page=${page}&limite=${limit}`;
    if (status) url += `&statut=${status}`;
    return fetchAPI(url);
  },
  updateStatus: (id, status) =>
    fetchAPI(`/api/commande/admin/statut/${id}`, "POST", { statut: status }),
};
