// assets/js/orders.js
document.addEventListener("DOMContentLoaded", function () {
  const isOrdersHistory = window.location.href.includes("route=orders");
  const isCheckout = window.location.href.includes("route=checkout");
  const isAdminOrders = window.location.href.includes("route=admin-orders");

  if (isOrdersHistory) {
    loadOrdersHistory();
  } else if (isCheckout) {
    loadCheckout();
  } else if (isAdminOrders) {
    loadAdminOrders();
  }
});

async function loadOrdersHistory() {
  const ordersContainer = document.getElementById("orders-container");

  if (!ordersContainer) return;

  ordersContainer.innerHTML =
    '<div class="loading">Chargement de vos commandes...</div>';

  try {
    const response = await OrderAPI.getHistory();
    const orders = response.data;

    if (orders.length === 0) {
      ordersContainer.innerHTML = `
                <div class="empty-orders">
                    <i class="fas fa-shopping-bag fa-4x"></i>
                    <p>Vous n'avez pas encore passé de commande.</p>
                    <a href="index.php" class="btn btn-primary">Parcourir les produits</a>
                </div>
            `;
      return;
    }

    let ordersHTML = `
            <table class="orders-table">
                <thead>
                    <tr>
                        <th>N° Commande</th>
                        <th>Date</th>
                        <th>Statut</th>
                        <th>Total</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
        `;

    orders.forEach((order) => {
      const orderDate = new Date(order.date_commande).toLocaleDateString();

      let statusClass = "";
      switch (order.statut) {
        case "en attente":
          statusClass = "status-pending";
          break;
        case "confirmée":
          statusClass = "status-confirmed";
          break;
        case "expédiée":
          statusClass = "status-shipped";
          break;
        case "livrée":
          statusClass = "status-delivered";
          break;
        case "annulée":
          statusClass = "status-cancelled";
          break;
      }

      ordersHTML += `
                <tr>
                    <td>${order.id}</td>
                    <td>${orderDate}</td>
                    <td><span class="order-status ${statusClass}">${
        order.statut
      }</span></td>
                    <td>${order.total.toFixed(2)} €</td>
                    <td>
                        <button class="btn btn-secondary view-order" data-id="${
                          order.id
                        }">
                            <i class="fas fa-eye"></i> Détails
                        </button>
                        ${
                          order.statut === "en attente"
                            ? `
                            <button class="btn btn-danger cancel-order" data-id="${order.id}">
                                <i class="fas fa-times"></i> Annuler
                            </button>
                        `
                            : ""
                        }
                    </td>
                </tr>
            `;
    });

    ordersHTML += `
                </tbody>
            </table>
        `;

    ordersContainer.innerHTML = ordersHTML;

    // Add event listeners
    document.querySelectorAll(".view-order").forEach((button) => {
      button.addEventListener("click", function () {
        const orderId = this.getAttribute("data-id");
        showOrderDetails(orderId);
      });
    });

    document.querySelectorAll(".cancel-order").forEach((button) => {
      button.addEventListener("click", async function () {
        if (confirm("Êtes-vous sûr de vouloir annuler cette commande ?")) {
          const orderId = this.getAttribute("data-id");
          try {
            await OrderAPI.cancel(orderId);
            alert("Commande annulée avec succès !");
            loadOrdersHistory();
          } catch (error) {
            alert("Erreur: " + error.message);
          }
        }
      });
    });
  } catch (error) {
    ordersContainer.innerHTML = `<p>Erreur lors du chargement des commandes: ${error.message}</p>`;
  }
}

async function showOrderDetails(orderId) {
  try {
    const response = await OrderAPI.getDetails(orderId);
    const order = response.data;
    const orderDate = new Date(order.date_commande).toLocaleDateString();

    const modalHTML = `
            <div class="modal-overlay">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2>Détails de la commande #${order.id}</h2>
                        <button class="modal-close">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="order-info">
                            <p><strong>Date:</strong> ${orderDate}</p>
                            <p><strong>Statut:</strong> <span class="order-status">${
                              order.statut
                            }</span></p>
                            <p><strong>Adresse de livraison:</strong> ${
                              order.adresse_livraison
                            }</p>
                        </div>
                        
                        <h3>Articles</h3>
                        <table class="order-items-table">
                            <thead>
                                <tr>
                                    <th>Produit</th>
                                    <th>Prix unitaire</th>
                                    <th>Quantité</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${order.items
                                  .map(
                                    (item) => `
                                    <tr>
                                        <td>${item.nom_produit}</td>
                                        <td>${item.prix_unitaire.toFixed(
                                          2
                                        )} €</td>
                                        <td>${item.quantite}</td>
                                        <td>${(
                                          item.prix_unitaire * item.quantite
                                        ).toFixed(2)} €</td>
                                    </tr>
                                `
                                  )
                                  .join("")}
                            </tbody>
                        </table>
                        
                        <div class="order-total">
                            <p><strong>Total:</strong> ${order.total.toFixed(
                              2
                            )} €</p>
                        </div>
                    </div>
                </div>
            </div>
        `;

    const modalContainer = document.createElement("div");
    modalContainer.innerHTML = modalHTML;
    document.body.appendChild(modalContainer);

    document
      .querySelector(".modal-close")
      .addEventListener("click", function () {
        document.body.removeChild(modalContainer);
      });
  } catch (error) {
    alert("Erreur: " + error.message);
  }
}

async function loadCheckout() {
  const checkoutContainer = document.getElementById("checkout-container");

  if (!checkoutContainer) return;

  checkoutContainer.innerHTML =
    '<div class="loading">Chargement du panier...</div>';

  try {
    const response = await CartAPI.getContents();
    const cart = response.data;

    if (cart.items.length === 0) {
      checkoutContainer.innerHTML = `
                <div class="empty-cart">
                    <i class="fas fa-shopping-cart fa-4x"></i>
                    <p>Votre panier est vide.</p>
                    <a href="index.php" class="btn btn-primary">Continuer les achats</a>
                </div>
            `;
      return;
    }

    let totalPrice = 0;
    let checkoutHTML = "<h2>Récapitulatif de la commande</h2>";

    checkoutHTML += `
            <div class="checkout-items">
                <h3>Articles (${cart.items.length})</h3>
                <ul class="checkout-items-list">
        `;

    cart.items.forEach((item) => {
      const itemTotal = item.prix * item.quantite;
      totalPrice += itemTotal;

      checkoutHTML += `
                <li>
                    <div class="checkout-item">
                        <img src="/api/placeholder/100/100" alt="${
                          item.nom
                        }" width="60">
                        <div class="checkout-item-details">
                            <h4>${item.nom}</h4>
                            <p>Quantité: ${item.quantite}</p>
                            <p>Prix unitaire: ${item.prix.toFixed(2)} €</p>
                            <p>Total: ${itemTotal.toFixed(2)} €</p>
                        </div>
                    </div>
                </li>
            `;
    });

    checkoutHTML += `
                </ul>
            </div>
            
            <div class="checkout-user-info">
                <h3>Informations de livraison</h3>
                <form id="checkout-form">
                    <div class="form-group">
                        <label for="adresse">Adresse</label>
                        <input type="text" id="adresse" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="code_postal">Code postal</label>
                        <input type="text" id="code_postal" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="ville">Ville</label>
                        <input type="text" id="ville" class="form-control" required>
                    </div>
                </form>
            </div>
            
            <div class="checkout-summary">
                <h3>Résumé</h3>
                <div class="checkout-total">
                    <span>Total:</span>
                    <span>${totalPrice.toFixed(2)} €</span>
                </div>
                
                <button id="place-order" class="btn btn-primary">
                    <i class="fas fa-check"></i> Passer la commande
                </button>
                <a href="index.php?route=cart" class="btn btn-secondary">Retour au panier</a>
            </div>
        `;

    checkoutContainer.innerHTML = checkoutHTML;

    // Get user profile to populate address fields
    try {
      const userResponse = await UserAPI.getProfile();
      const user = userResponse.data;

      document.getElementById("adresse").value = user.adresse || "";
      document.getElementById("code_postal").value = user.code_postal || "";
      document.getElementById("ville").value = user.ville || "";
    } catch (error) {
      console.error("Erreur lors de la récupération du profil:", error);
    }

    // Add event listener for place order button
    document
      .getElementById("place-order")
      .addEventListener("click", async function () {
        // Validate form
        const adresse = document.getElementById("adresse").value;
        const codePostal = document.getElementById("code_postal").value;
        const ville = document.getElementById("ville").value;

        if (!adresse || !codePostal || !ville) {
          alert("Veuillez remplir toutes les informations de livraison.");
          return;
        }

        try {
          await OrderAPI.create();
          alert("Commande créée avec succès !");
          window.location.href = "index.php?route=orders";
        } catch (error) {
          alert("Erreur: " + error.message);
        }
      });
  } catch (error) {
    checkoutContainer.innerHTML = `<p>Erreur lors du chargement du panier: ${error.message}</p>`;
  }
}

async function loadAdminOrders() {
  const adminOrdersContainer = document.getElementById(
    "admin-orders-container"
  );

  if (!adminOrdersContainer) return;

  adminOrdersContainer.innerHTML =
    '<div class="loading">Chargement des commandes...</div>';

  try {
    const response = await OrderAPI.getAllOrders();
    const orders = response.data;

    if (orders.length === 0) {
      adminOrdersContainer.innerHTML = "<p>Aucune commande trouvée.</p>";
      return;
    }

    let ordersHTML = `
            <div class="admin-filters">
                <select id="status-filter" class="form-control">
                    <option value="">Tous les statuts</option>
                    <option value="en attente">En attente</option>
                    <option value="confirmée">Confirmée</option>
                    <option value="expédiée">Expédiée</option>
                    <option value="livrée">Livrée</option>
                    <option value="annulée">Annulée</option>
                </select>
                <button id="apply-filter" class="btn btn-primary">Filtrer</button>
            </div>
            
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>N° Commande</th>
                        <th>Client</th>
                        <th>Date</th>
                        <th>Statut</th>
                        <th>Total</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
        `;

    orders.forEach((order) => {
      const orderDate = new Date(order.date_commande).toLocaleDateString();

      ordersHTML += `
                <tr>
                    <td>${order.id}</td>
                    <td>${order.nom_client} ${order.prenom_client}</td>
                    <td>${orderDate}</td>
                    <td>
                        <select class="status-select" data-id="${order.id}">
                            <option value="en attente" ${
                              order.statut === "en attente" ? "selected" : ""
                            }>En attente</option>
                            <option value="confirmée" ${
                              order.statut === "confirmée" ? "selected" : ""
                            }>Confirmée</option>
                            <option value="expédiée" ${
                              order.statut === "expédiée" ? "selected" : ""
                            }>Expédiée</option>
                            <option value="livrée" ${
                              order.statut === "livrée" ? "selected" : ""
                            }>Livrée</option>
                            <option value="annulée" ${
                              order.statut === "annulée" ? "selected" : ""
                            }>Annulée</option>
                        </select>
                    </td>
                    <td>${order.total.toFixed(2)} €</td>
                    <td>
                        <button class="btn btn-secondary view-order" data-id="${
                          order.id
                        }">
                            <i class="fas fa-eye"></i>
                        </button>
                    </td>
                </tr>
            `;
    });

    ordersHTML += `
                </tbody>
            </table>
        `;

    adminOrdersContainer.innerHTML = ordersHTML;

    // Add event listeners
    document
      .getElementById("apply-filter")
      .addEventListener("click", function () {
        const status = document.getElementById("status-filter").value;
        loadAdminOrdersWithFilter(status);
      });

    document.querySelectorAll(".status-select").forEach((select) => {
      select.addEventListener("change", async function () {
        const orderId = this.getAttribute("data-id");
        const newStatus = this.value;

        try {
          await OrderAPI.updateStatus(orderId, newStatus);
          alert("Statut mis à jour avec succès !");
        } catch (error) {
          alert("Erreur: " + error.message);
          // Reset to previous value
          this.value = order.statut;
        }
      });
    });

    document.querySelectorAll(".view-order").forEach((button) => {
      button.addEventListener("click", function () {
        const orderId = this.getAttribute("data-id");
        showOrderDetails(orderId);
      });
    });
  } catch (error) {
    adminOrdersContainer.innerHTML = `<p>Erreur lors du chargement des commandes: ${error.message}</p>`;
  }
}

async function loadAdminOrdersWithFilter(status) {
  const adminOrdersContainer = document.getElementById(
    "admin-orders-container"
  );
  const currentHTML = adminOrdersContainer.innerHTML;

  adminOrdersContainer.innerHTML =
    '<div class="loading">Chargement des commandes...</div>';

  try {
    const response = await OrderAPI.getAllOrders(status);

    // Restore HTML with updated data
    loadAdminOrders();

    // Set filter back to selected value
    setTimeout(() => {
      const statusFilter = document.getElementById("status-filter");
      if (statusFilter) statusFilter.value = status;
    }, 100);
  } catch (error) {
    adminOrdersContainer.innerHTML = currentHTML;
    alert("Erreur: " + error.message);
  }
}
