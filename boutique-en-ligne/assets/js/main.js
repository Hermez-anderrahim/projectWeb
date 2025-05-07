// assets/js/main.js
document.addEventListener("DOMContentLoaded", function () {
  // Initialize global elements
  initializeGlobalElements();
});

// Function to initialize global elements
function initializeGlobalElements() {
  // Add mobile menu toggle
  const navLinks = document.querySelector(".nav-links");
  const menuToggle = document.createElement("button");
  menuToggle.classList.add("menu-toggle");
  menuToggle.innerHTML = '<i class="fas fa-bars"></i>';

  const navbar = document.querySelector(".navbar");
  if (navbar && !document.querySelector(".menu-toggle")) {
    navbar.insertBefore(menuToggle, navLinks);

    menuToggle.addEventListener("click", function () {
      navLinks.classList.toggle("active");

      // Change toggle icon
      if (navLinks.classList.contains("active")) {
        menuToggle.innerHTML = '<i class="fas fa-times"></i>';
      } else {
        menuToggle.innerHTML = '<i class="fas fa-bars"></i>';
      }
    });
  }

  // Close mobile menu when clicking outside
  document.addEventListener("click", function (event) {
    if (
      navLinks &&
      navLinks.classList.contains("active") &&
      !event.target.closest(".navbar")
    ) {
      navLinks.classList.remove("active");
      if (menuToggle) menuToggle.innerHTML = '<i class="fas fa-bars"></i>';
    }
  });

  // Update cart count on page load
  updateCartCount();
}

// Global cart count updater (used across pages)
async function updateCartCount() {
  const cartCountElement = document.getElementById("cart-count");
  if (!cartCountElement) return;

  try {
    const response = await CartAPI.getContents();
    const cart = response.data;
    const count = cart.items.reduce((total, item) => total + item.quantite, 0);
    cartCountElement.textContent = count;
  } catch (error) {
    console.error("Erreur lors de la récupération du panier:", error);
  }
}
