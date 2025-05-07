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
    const cart = response.data;

    if (cart.items.length === 0) {
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

    let totalPrice = 0;
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

    cart.items.forEach((item) => {
      const itemTotal = item.prix * item.quantite;
      totalPrice += itemTotal;

      cartHTML += `
                <tr>
                    <td>
                        <div class="cart-product">
                            <img src="/api/placeholder/100/100" alt="${
                              item.nom
                            }" width="60">
                            <div>
                                <h4>${item.nom}</h4>
                            </div>
                        </div>
                    </td>
                    <td>${item.prix.toFixed(2)} €</td>
                    <td>
                        <div class="quantity-controls">
                            <button class="quantity-btn minus" data-id="${
                              item.produit_id
                            }">-</button>
                            <input type="number" class="quantity-input" value="${
                              item.quantite
                            }" min="1" data-id="${item.produit_id}">
                            <button class="quantity-btn plus" data-id="${
                              item.produit_id
                            }">+</button>
                        </div>
                    </td>
                    <td>${itemTotal.toFixed(2)} €</td>
                    <td>
                        <button class="btn btn-danger remove-item" data-id="${
                          item.produit_id
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
          await CartAPI.updateQuantity(productId, quantity);
          loadCart();
          updateCartCount();
        } catch (error) {
          alert("Erreur: " + error.message);
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
          await CartAPI.updateQuantity(productId, quantity);
          loadCart();
          updateCartCount();
        } catch (error) {
          alert("Erreur: " + error.message);
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
          await CartAPI.updateQuantity(productId, quantity);
          loadCart();
          updateCartCount();
        } catch (error) {
          alert("Erreur: " + error.message);
        }
      });
    });

    // Remove item
    document.querySelectorAll(".remove-item").forEach((button) => {
      button.addEventListener("click", async function () {
        const productId = this.getAttribute("data-id");

        try {
          await CartAPI.removeItem(productId);
          loadCart();
          updateCartCount();
        } catch (error) {
          alert("Erreur: " + error.message);
        }
      });
    });

    // Clear cart
    document
      .getElementById("clear-cart")
      .addEventListener("click", async function () {
        if (confirm("Êtes-vous sûr de vouloir vider votre panier ?")) {
          try {
            await CartAPI.clear();
            loadCart();
            updateCartCount();
          } catch (error) {
            alert("Erreur: " + error.message);
          }
        }
      });
  } catch (error) {
    cartContainer.innerHTML = `<p>Erreur lors du chargement du panier: ${error.message}</p>`;
  }
}
