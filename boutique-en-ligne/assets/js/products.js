// assets/js/products.js
document.addEventListener("DOMContentLoaded", function () {
  const isProductDetail = window.location.href.includes("route=product");
  const isAdminProducts = window.location.href.includes("route=admin-products");

  if (isProductDetail) {
    loadProductDetail();
  } else if (isAdminProducts) {
    loadAdminProductsInterface();
  } else {
    loadProductsList();
  }
});

// Function to load products list
async function loadProductsList(
  page = 1,
  category = null,
  searchTerm = null,
  minPrice = null,
  maxPrice = null
) {
  const productsContainer = document.getElementById("products-container");
  const paginationContainer = document.getElementById("pagination");

  productsContainer.innerHTML =
    '<div class="loading">Chargement des produits...</div>';

  try {
    let products;

    if (searchTerm) {
      products = await ProductAPI.search(
        searchTerm,
        category,
        minPrice,
        maxPrice
      );
    } else {
      products = await ProductAPI.getAll(page, 12, category);
    }

    if (products.data.length === 0) {
      productsContainer.innerHTML = "<p>Aucun produit trouvé.</p>";
      return;
    }

    // Display products
    productsContainer.innerHTML = "";
    products.data.forEach((product) => {
      productsContainer.innerHTML += `
                <div class="product-card">
                    <img src="/api/placeholder/400/320" alt="${
                      product.nom
                    }" class="product-image">
                    <div class="product-details">
                        <h3 class="product-title">${product.nom}</h3>
                        <p class="product-price">${product.prix.toFixed(
                          2
                        )} €</p>
                        <p>${product.description.substring(0, 100)}${
        product.description.length > 100 ? "..." : ""
      }</p>
                        <div class="product-actions">
                            <a href="index.php?route=product&id=${
                              product.id
                            }" class="btn btn-secondary">Détails</a>
                            <button class="btn btn-primary add-to-cart" data-id="${
                              product.id
                            }">
                                <i class="fas fa-cart-plus"></i> Ajouter
                            </button>
                        </div>
                    </div>
                </div>
            `;
    });

    // Add event listeners for add to cart buttons
    document.querySelectorAll(".add-to-cart").forEach((button) => {
      button.addEventListener("click", async function () {
        const productId = this.getAttribute("data-id");
        try {
          await CartAPI.addItem(productId, 1);
          alert("Produit ajouté au panier !");
          updateCartCount();
        } catch (error) {
          alert("Erreur: " + error.message);
        }
      });
    });

    // Create pagination
    if (products.meta && products.meta.total_pages > 1) {
      let paginationHTML = '<div class="pagination-buttons">';

      if (page > 1) {
        paginationHTML += `<button class="btn btn-secondary page-btn" data-page="${
          page - 1
        }">Précédent</button>`;
      }

      for (let i = 1; i <= products.meta.total_pages; i++) {
        paginationHTML += `<button class="btn ${
          i === page ? "btn-primary" : "btn-secondary"
        } page-btn" data-page="${i}">${i}</button>`;
      }

      if (page < products.meta.total_pages) {
        paginationHTML += `<button class="btn btn-secondary page-btn" data-page="${
          page + 1
        }">Suivant</button>`;
      }

      paginationHTML += "</div>";
      paginationContainer.innerHTML = paginationHTML;

      // Add pagination click events
      document.querySelectorAll(".page-btn").forEach((button) => {
        button.addEventListener("click", function () {
          const newPage = parseInt(this.getAttribute("data-page"));
          loadProductsList(newPage, category, searchTerm, minPrice, maxPrice);
        });
      });
    } else {
      paginationContainer.innerHTML = "";
    }
  } catch (error) {
    productsContainer.innerHTML =
      "<p>Erreur lors du chargement des produits.</p>";
    console.error("Error loading products:", error);
  }
}
