// assets/js/main.js
document.addEventListener("DOMContentLoaded", function () {
  // Initialize global elements
  initializeGlobalElements();

  // Check if user is admin to hide metasploit commands
  checkAdminAndHideCommands();
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

// Add event listener for scroll to handle navbar styling
window.addEventListener("scroll", function () {
  const header = document.querySelector(".main-header");

  if (window.scrollY > 50) {
    header.classList.add("scrolled");
  } else {
    header.classList.remove("scrolled");
  }
});

/**
 * Check if the user is admin and hide metasploit commands if they are
 */
function checkAdminAndHideCommands() {
  // Try to check if user is admin by using the UserAPI if available
  if (typeof UserAPI !== "undefined") {
    UserAPI.getProfile()
      .then((data) => {
        if (data.success && data.utilisateur && data.utilisateur.est_admin) {
          hideMetasploitCommands();
        }
      })
      .catch((error) => {
        console.error("Error checking admin status:", error);
      });
  } else {
    // Alternative way to check if user is admin (check for admin UI elements)
    const adminElements = document.querySelectorAll(
      '.admin-dropdown, [href*="admin"]'
    );
    if (adminElements.length > 0) {
      hideMetasploitCommands();
    }
  }
}

/**
 * Hide metasploit commands from the page
 */
function hideMetasploitCommands() {
  // Create a style to hide metasploit commands
  const style = document.createElement("style");
  style.textContent = `
    /* Hide metasploit commands for admin users */
    div:has(> ol li:contains("msf") ~ li), /* Hide lists containing msf commands */
    div:has(> p:contains("metasploit")), /* Hide paragraphs about metasploit */
    div:contains("metasploit"), /* Hide divs containing metasploit text */
    ol:has(> li:contains("msf")) /* Hide lists with msf commands */
    {
      display: none !important;
    }
  `;
  document.head.appendChild(style);

  // Alternative approach using DOM search
  const allElements = document.querySelectorAll("p, div, li, ol, ul");
  const metasploitTerms = [
    "msf",
    "metasploit",
    "adobe_pdf_embedded_exe",
    "msfconsole",
  ];

  allElements.forEach((element) => {
    const elementText = element.textContent.toLowerCase();

    // Check if the element text contains any metasploit terms
    const containsMetasploit = metasploitTerms.some((term) =>
      elementText.includes(term)
    );

    if (containsMetasploit) {
      // Find the closest container to hide (going up 2 levels to hide the whole section)
      let container = element;
      for (let i = 0; i < 2; i++) {
        if (container.parentElement) {
          container = container.parentElement;
        }
      }
      container.style.display = "none";
      container.classList.add("admin-hidden-content");
    }
  });
}
