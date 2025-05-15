// js/api.js
const API_BASE_URL = ""; // Relative URL so it works on any domain

// Utility function for API calls
async function fetchAPI(endpoint, method = "GET", data = null) {
  console.log(`DEBUG [fetchAPI] - Calling ${method} ${endpoint}`, data);

  // Add timeout handling (10 seconds)
  const controller = new AbortController();
  const timeoutId = setTimeout(() => controller.abort(), 10000);

  const options = {
    method,
    headers: {
      "Content-Type": "application/json",
    },
    credentials: "include", // Include cookies for session
    signal: controller.signal,
  };

  if (data && (method === "POST" || method === "PUT")) {
    options.body = JSON.stringify(data);
  }

  try {
    console.log(`DEBUG [fetchAPI] - Request options:`, options);

    const response = await fetch(`${API_BASE_URL}${endpoint}`, options);
    // Clear the timeout since the request completed
    clearTimeout(timeoutId);

    console.log(
      `DEBUG [fetchAPI] - Response status:`,
      response.status,
      response.statusText
    );

    const responseHeaders = {};
    response.headers.forEach((value, name) => {
      responseHeaders[name] = value;
    });
    console.log(`DEBUG [fetchAPI] - Response headers:`, responseHeaders);

    // Get text first to safely handle non-JSON responses
    const responseText = await response.text();
    console.log(`DEBUG [fetchAPI] - Raw response:`, responseText);

    let result;
    try {
      // Only try to parse as JSON if there's content
      result = responseText ? JSON.parse(responseText) : {};
    } catch (e) {
      console.error(`DEBUG [fetchAPI] - JSON parse error:`, e);
      throw new Error(
        `Invalid JSON response: ${responseText.substring(0, 100)}...`
      );
    }

    console.log(`DEBUG [fetchAPI] - Parsed response:`, result);

    if (!response.ok) {
      console.error(`DEBUG [fetchAPI] - Error response:`, result);
      throw new Error(result.message || "Une erreur est survenue");
    }

    return result;
  } catch (error) {
    // Clear the timeout in case of error
    clearTimeout(timeoutId);

    if (error.name === "AbortError") {
      console.error(`DEBUG [fetchAPI] - Request timeout after 10 seconds`);
      throw new Error(
        "La requête a pris trop de temps à répondre. Veuillez réessayer."
      );
    }

    console.error(`DEBUG [fetchAPI] - Exception:`, error);
    throw error;
  }
}

// User API
const UserAPI = {
  login: (email, password) =>
    fetchAPI("/api/utilisateur.php", "POST", {
      action: "connecter",
      email,
      password,
    }),
  register: (userData) =>
    fetchAPI("/api/utilisateur.php", "POST", {
      action: "inscrire",
      ...userData,
    }),
  logout: () =>
    fetchAPI("/api/utilisateur.php", "POST", { action: "deconnecter" }),
  getProfile: () =>
    fetchAPI("/api/utilisateur.php", "POST", { action: "getInfosUtilisateur" }),
  updateProfile: (userData) =>
    fetchAPI("/api/utilisateur.php", "POST", {
      action: "mettreAJour",
      ...userData,
    }),
  changePassword: (passwordData) =>
    fetchAPI("/api/utilisateur.php", "POST", {
      action: "changerMotDePasse",
      ...passwordData,
    }),
};

// Product API
const ProductAPI = {
  getAll: (page = 1, limit = 12, category = null) => {
    let url = `/api/produit.php?page=${page}&limite=${limit}`;
    if (category) url += `&categorie=${category}`;
    return fetchAPI(url);
  },
  getById: (id) => fetchAPI(`/api/produit.php/${id}`),
  search: (
    term,
    category = null,
    page = 1,
    limit = 12,
    minPrice = null,
    maxPrice = null
  ) => {
    let url = `/api/produit.php/recherche?terme=${term}`;
    if (category) url += `&categorie=${category}`;
    if (page) url += `&page=${page}`;
    if (limit) url += `&limite=${limit}`;
    if (minPrice) url += `&prix_min=${minPrice}`;
    if (maxPrice) url += `&prix_max=${maxPrice}`;
    return fetchAPI(url);
  },
  create: (productData) => fetchAPI("/api/produit.php", "POST", productData),
  update: (id, productData) =>
    fetchAPI(`/api/produit.php/${id}`, "PUT", productData),
  delete: (id) => fetchAPI(`/api/produit.php/${id}`, "DELETE"),
};

// Cart API
const CartAPI = {
  getContents: () => fetchAPI("/api/panier.php"),
  // Add compatibility method for legacy code
  getCart: function () {
    console.warn(
      "CartAPI.getCart() is deprecated, use CartAPI.getContents() instead"
    );
    return this.getContents();
  },
  addItem: (productId, quantity = 1) =>
    fetchAPI(`/api/panier.php/ajouter/${productId}`, "POST", {
      quantite: quantity,
    }),
  updateQuantity: (productId, quantity) =>
    fetchAPI(`/api/panier.php/mettre-a-jour/${productId}`, "POST", {
      quantite: quantity,
    }),
  removeItem: (productId) =>
    fetchAPI(`/api/panier.php/supprimer/${productId}`, "POST"),
  clear: () => fetchAPI("/api/panier.php/vider", "POST"),
};

const OrderAPI = {
  getHistory: () => fetchAPI("/api/commande.php"),
  getDetails: (id) => fetchAPI(`/api/commande.php/${id}`),
  create: (orderData) => fetchAPI("/api/commande.php/creer", "POST", orderData),
  // Add compatibility method for legacy code
  createOrder: function (orderData) {
    console.warn(
      "OrderAPI.createOrder() is deprecated, use OrderAPI.create() instead"
    );
    return this.create(orderData);
  },
  cancel: (id, reason) =>
    fetchAPI(`/api/commande.php/annuler/${id}`, "POST", { raison: reason }),
  // Admin functions
  getAllOrders: (status = null, page = 1, limit = 20) => {
    let url = `/api/commande.php/admin/toutes?page=${page}&limite=${limit}`;
    if (status) url += `&statut=${status}`;
    return fetchAPI(url);
  },
  updateStatus: (id, status) =>
    fetchAPI(`/api/commande.php/admin/statut/${id}`, "POST", {
      statut: status,
    }),
};
