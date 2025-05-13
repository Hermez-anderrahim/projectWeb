// assets/js/cart.js
document.addEventListener("DOMContentLoaded", function () {
  loadCart();
});

async function loadCart() {
  const cartContainer = document.getElementById("cart-container");
  const cartActions = document.getElementById("cart-actions");

  if (!cartContainer) return;

  try {
    const response = await CartAPI.getContents();

    if (!response.success) {
      cartContainer.innerHTML = `
        <div class="error-message">
          <i class="fas fa-exclamation-circle"></i> ${
            response.message ||
            "Une erreur est survenue lors du chargement du panier."
          }
        </div>
      `;
      if (cartActions) cartActions.style.display = "none";
      return;
    }

    const cart = response.panier;

    if (!cart || !cart.contenu || cart.contenu.length === 0) {
      cartContainer.innerHTML = `
                <div class="empty-cart">
                    <i class="fas fa-shopping-cart fa-4x"></i>
                    <p>Votre panier est vide.</p>
                    <a href="index.php" class="btn btn-primary">Continuer les achats</a>
                </div>
            `;
      if (cartActions) cartActions.style.display = "none";
      return;
    }

    let totalPrice = parseFloat(cart.total) || 0;
    let cartHTML = `
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Produit</th>
                        <th>Prix</th>
                        <th>Quantité</th>
                        <th>Total</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
        `;

    cart.contenu.forEach((item) => {
      const itemTotal = parseFloat(item.sous_total) || 0;
      const price = parseFloat(item.prix_unitaire) || 0;

      cartHTML += `
                <tr>
                    <td>
                        <div class="cart-product">
                            <img src="${
                              item.image_url || "/assets/images/placeholder.png"
                            }" alt="${item.nom}" width="60">
                            <div>
                                <h4>${item.nom}</h4>
                            </div>
                        </div>
                    </td>
                    <td>${price.toFixed(2)} €</td>
                    <td>
                        <div class="quantity-controls">
                            <button class="quantity-btn minus" data-id="${
                              item.id_produit
                            }">-</button>
                            <input type="number" class="quantity-input" value="${
                              item.quantite
                            }" min="1" data-id="${item.id_produit}">
                            <button class="quantity-btn plus" data-id="${
                              item.id_produit
                            }">+</button>
                        </div>
                    </td>
                    <td>${itemTotal.toFixed(2)} €</td>
                    <td>
                        <button class="btn btn-danger remove-item" data-id="${
                          item.id_produit
                        }">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
    });

    cartHTML += `
                </tbody>
            </table>
            
            <div class="cart-summary">
                <div class="cart-total">
                    <span>Total:</span>
                    <span>${totalPrice.toFixed(2)} €</span>
                </div>
                <button id="clear-cart" class="btn btn-secondary">Vider le panier</button>
            </div>
        `;

    cartContainer.innerHTML = cartHTML;
    if (cartActions) cartActions.style.display = "flex";

    // Add event listeners

    // Quantity decrease
    document.querySelectorAll(".quantity-btn.minus").forEach((button) => {
      button.addEventListener("click", async function () {
        const productId = this.getAttribute("data-id");
        const input = document.querySelector(
          `.quantity-input[data-id="${productId}"]`
        );
        let quantity = parseInt(input.value) - 1;

        if (quantity < 1) quantity = 1;

        try {
          const result = await CartAPI.updateQuantity(productId, quantity);
          if (result.success) {
            loadCart();
            updateCartCount();
          } else {
            alert(
              "Erreur: " +
                (result.message || "Impossible de mettre à jour la quantité")
            );
          }
        } catch (error) {
          console.error("Error updating quantity:", error);
          alert("Erreur: Une erreur est survenue lors de la mise à jour");
        }
      });
    });

    // Quantity increase
    document.querySelectorAll(".quantity-btn.plus").forEach((button) => {
      button.addEventListener("click", async function () {
        const productId = this.getAttribute("data-id");
        const input = document.querySelector(
          `.quantity-input[data-id="${productId}"]`
        );
        const quantity = parseInt(input.value) + 1;

        try {
          const result = await CartAPI.updateQuantity(productId, quantity);
          if (result.success) {
            loadCart();
            updateCartCount();
          } else {
            alert(
              "Erreur: " +
                (result.message || "Impossible de mettre à jour la quantité")
            );
          }
        } catch (error) {
          console.error("Error updating quantity:", error);
          alert("Erreur: Une erreur est survenue lors de la mise à jour");
        }
      });
    });

    // Quantity input change
    document.querySelectorAll(".quantity-input").forEach((input) => {
      input.addEventListener("change", async function () {
        const productId = this.getAttribute("data-id");
        let quantity = parseInt(this.value);

        if (quantity < 1) {
          quantity = 1;
          this.value = 1;
        }

        try {
          const result = await CartAPI.updateQuantity(productId, quantity);
          if (result.success) {
            loadCart();
            updateCartCount();
          } else {
            alert(
              "Erreur: " +
                (result.message || "Impossible de mettre à jour la quantité")
            );
          }
        } catch (error) {
          console.error("Error updating quantity:", error);
          alert("Erreur: Une erreur est survenue lors de la mise à jour");
        }
      });
    });

    // Remove item
    document.querySelectorAll(".remove-item").forEach((button) => {
      button.addEventListener("click", async function () {
        const productId = this.getAttribute("data-id");

        try {
          const result = await CartAPI.removeItem(productId);
          if (result.success) {
            loadCart();
            updateCartCount();
          } else {
            alert(
              "Erreur: " +
                (result.message || "Impossible de supprimer l'article")
            );
          }
        } catch (error) {
          console.error("Error removing item:", error);
          alert("Erreur: Une erreur est survenue lors de la suppression");
        }
      });
    });

    // Clear cart
    document
      .getElementById("clear-cart")
      .addEventListener("click", async function () {
        if (confirm("Êtes-vous sûr de vouloir vider votre panier ?")) {
          try {
            const result = await CartAPI.clear();
            if (result.success) {
              loadCart();
              updateCartCount();
            } else {
              alert(
                "Erreur: " + (result.message || "Impossible de vider le panier")
              );
            }
          } catch (error) {
            console.error("Error clearing cart:", error);
            alert("Erreur: Une erreur est survenue lors du vidage du panier");
          }
        }
      });
  } catch (error) {
    console.error("Error loading cart:", error);
    cartContainer.innerHTML = `
      <div class="error-message">
        <i class="fas fa-exclamation-circle"></i> Une erreur est survenue lors du chargement du panier.
      </div>
    `;
    if (cartActions) cartActions.style.display = "none";
  }
}

// Function to update cart count in the navigation
function updateCartCount() {
  try {
    CartAPI.getContents()
      .then((response) => {
        if (response.success && response.panier) {
          const count = response.panier.nombre_articles || 0;
          const cartCountElements = document.querySelectorAll(".cart-count");
          cartCountElements.forEach((element) => {
            element.textContent = count;

            // Show/hide badge based on count
            if (count > 0) {
              element.classList.add("has-items");
            } else {
              element.classList.remove("has-items");
            }
          });
        }
      })
      .catch((error) => console.error("Error updating cart count:", error));
  } catch (error) {
    console.error("Error in updateCartCount:", error);
  }
}
