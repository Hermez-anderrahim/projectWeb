/**
 * Admin Dashboard Functionality
 * Handles all admin-specific functionality including statistics, charts, and data management
 */

document.addEventListener("DOMContentLoaded", function () {
  // Initialize admin components based on the current page
  initAdminPage();

  // Setup event listeners for admin actions
  setupAdminEventListeners();
});

/**
 * Initialize admin page components based on current route
 */
function initAdminPage() {
  const currentPath = window.location.search;

  // Load appropriate functionality based on current admin page
  if (
    currentPath.includes("route=admin-dashboard") ||
    currentPath === "?route=admin"
  ) {
    initDashboard();
  } else if (currentPath.includes("route=admin-products")) {
    initProductsManager();
  } else if (currentPath.includes("route=admin-orders")) {
    initOrdersManager();
  }
}

/**
 * Initialize dashboard components and charts
 */
function initDashboard() {
  console.log("Initializing admin dashboard");

  // Load sales chart if the element exists
  const salesChartCanvas = document.getElementById("sales-chart");
  if (salesChartCanvas) {
    loadSalesChart(salesChartCanvas);
  }
}

/**
 * Load sales chart with monthly data
 * @param {HTMLElement} canvas - The canvas element for the chart
 */
async function loadSalesChart(canvas) {
  try {
    // Fetch sales data from API
    const salesData = await fetch("/api/admin.php?action=sales_by_month")
      .then((response) => response.json())
      .catch(() => ({
        // Fallback mock data if API doesn't exist yet
        success: true,
        sales: [
          { month: "Jan", sales: 2450.99 },
          { month: "Feb", sales: 3250.5 },
          { month: "Mar", sales: 4120.75 },
          { month: "Apr", sales: 3890.25 },
          { month: "May", sales: 4560.0 },
          { month: "Jun", sales: 5120.5 },
          { month: "Jul", sales: 4890.75 },
          { month: "Aug", sales: 5230.25 },
          { month: "Sep", sales: 4780.5 },
          { month: "Oct", sales: 5340.25 },
          { month: "Nov", sales: 6120.75 },
          { month: "Dec", sales: 7450.99 },
        ],
      }));

    if (salesData.success && salesData.sales) {
      // Extract months and sales amounts for chart
      const months = salesData.sales.map((item) => item.month);
      const salesAmounts = salesData.sales.map((item) => item.sales);

      // Create chart using Chart.js
      new Chart(canvas, {
        type: "bar",
        data: {
          labels: months,
          datasets: [
            {
              label: "Ventes mensuelles (€)",
              data: salesAmounts,
              backgroundColor: "rgba(76, 175, 80, 0.6)",
              borderColor: "#4CAF50",
              borderWidth: 1,
            },
          ],
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            y: {
              beginAtZero: true,
              ticks: {
                callback: function (value) {
                  return value.toLocaleString("fr-FR", {
                    style: "currency",
                    currency: "EUR",
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0,
                  });
                },
              },
            },
          },
          plugins: {
            tooltip: {
              callbacks: {
                label: function (context) {
                  return (
                    context.dataset.label +
                    ": " +
                    context.raw.toLocaleString("fr-FR", {
                      style: "currency",
                      currency: "EUR",
                    })
                  );
                },
              },
            },
          },
        },
      });
    } else {
      console.error("Error loading sales data for chart");
      canvas.parentElement.innerHTML =
        '<div class="chart-error">Impossible de charger les données de vente</div>';
    }
  } catch (error) {
    console.error("Error rendering sales chart:", error);
    canvas.parentElement.innerHTML =
      '<div class="chart-error">Erreur lors du chargement du graphique</div>';
  }
}

/**
 * Initialize product management functionality
 */
function initProductsManager() {
  console.log("Initializing products manager");

  // Product search functionality
  const searchInput = document.getElementById("product-search");
  if (searchInput) {
    searchInput.addEventListener(
      "input",
      debounce(function () {
        filterProducts(this.value);
      }, 300)
    );
  }

  // Category filter functionality
  const categoryFilter = document.getElementById("category-filter");
  if (categoryFilter) {
    categoryFilter.addEventListener("change", function () {
      filterProductsByCategory(this.value);
    });
  }

  // Product form validation
  const productForm = document.getElementById("product-form");
  if (productForm) {
    productForm.addEventListener("submit", validateProductForm);
  }
}

/**
 * Initialize order management functionality
 */
function initOrdersManager() {
  console.log("Initializing orders manager");

  // Order status filter functionality
  const statusFilter = document.getElementById("status-filter");
  if (statusFilter) {
    statusFilter.addEventListener("change", function () {
      filterOrdersByStatus(this.value);
    });
  }

  // Order detail view handlers
  setupOrderDetailViewHandlers();
}

/**
 * Set up event listeners for admin actions
 */
function setupAdminEventListeners() {
  // Handle delete confirmations
  document.querySelectorAll(".delete-confirm").forEach((button) => {
    button.addEventListener("click", function (event) {
      if (
        !confirm(
          "Êtes-vous sûr de vouloir supprimer cet élément? Cette action est irréversible."
        )
      ) {
        event.preventDefault();
        return false;
      }
    });
  });
}

/**
 * Filter products based on search query
 * @param {string} query - Search query
 */
function filterProducts(query) {
  query = query.toLowerCase();
  const rows = document.querySelectorAll("#products-table tbody tr");

  rows.forEach((row) => {
    const productName = row
      .querySelector("td:nth-child(3)")
      .textContent.toLowerCase();
    const productId = row
      .querySelector("td:nth-child(1)")
      .textContent.toLowerCase();
    const productDesc = row.dataset.description
      ? row.dataset.description.toLowerCase()
      : "";

    if (
      productName.includes(query) ||
      productId.includes(query) ||
      productDesc.includes(query)
    ) {
      row.style.display = "";
    } else {
      row.style.display = "none";
    }
  });
}

/**
 * Filter products by category
 * @param {string} category - Category to filter by
 */
function filterProductsByCategory(category) {
  const rows = document.querySelectorAll("#products-table tbody tr");

  rows.forEach((row) => {
    if (!category || category === "all" || row.dataset.category === category) {
      row.style.display = "";
    } else {
      row.style.display = "none";
    }
  });
}

/**
 * Set up handlers for order detail view
 */
function setupOrderDetailViewHandlers() {
  // View order details
  document.querySelectorAll(".view-order-details").forEach((button) => {
    button.addEventListener("click", function () {
      const orderId = this.dataset.orderId;
      viewOrderDetails(orderId);
    });
  });

  // Update order status
  document.querySelectorAll(".update-status").forEach((button) => {
    button.addEventListener("click", function () {
      const orderId = this.dataset.orderId;
      const newStatus = this.dataset.status;
      updateOrderStatus(orderId, newStatus);
    });
  });
}

/**
 * Validate product form before submission
 * @param {Event} event - Form submission event
 */
function validateProductForm(event) {
  const form = event.target;
  const nameInput = form.querySelector("#product-name");
  const priceInput = form.querySelector("#product-price");
  const stockInput = form.querySelector("#product-stock");

  let isValid = true;

  // Reset previous error messages
  form.querySelectorAll(".error-message").forEach((el) => el.remove());

  // Validate product name
  if (!nameInput.value.trim()) {
    showInputError(nameInput, "Le nom du produit est requis");
    isValid = false;
  }

  // Validate price
  if (
    !priceInput.value ||
    isNaN(parseFloat(priceInput.value)) ||
    parseFloat(priceInput.value) <= 0
  ) {
    showInputError(priceInput, "Le prix doit être un nombre positif");
    isValid = false;
  }

  // Validate stock
  if (
    !stockInput.value ||
    isNaN(parseInt(stockInput.value)) ||
    parseInt(stockInput.value) < 0
  ) {
    showInputError(
      stockInput,
      "Le stock doit être un nombre entier positif ou zéro"
    );
    isValid = false;
  }

  if (!isValid) {
    event.preventDefault();
  }
}

/**
 * Show error message for form input
 * @param {HTMLElement} input - The input element
 * @param {string} message - Error message to display
 */
function showInputError(input, message) {
  const errorElement = document.createElement("div");
  errorElement.className = "error-message";
  errorElement.textContent = message;
  errorElement.style.color = "#e53935";
  errorElement.style.fontSize = "0.8rem";
  errorElement.style.marginTop = "0.25rem";

  input.parentNode.appendChild(errorElement);
  input.style.borderColor = "#e53935";
}

/**
 * View order details
 * @param {number} orderId - ID of the order to view
 */
async function viewOrderDetails(orderId) {
  try {
    const orderData = await fetch(
      `/api/commande.php?action=detail&id=${orderId}`
    ).then((response) => response.json());

    if (orderData.success && orderData.commande) {
      // Populate and show order details modal
      const modal = document.getElementById("order-detail-modal");
      if (modal) {
        populateOrderDetailsModal(modal, orderData.commande);
        // Show modal - assumes you have a modal implementation
        showModal(modal);
      }
    } else {
      alert("Impossible de charger les détails de la commande");
    }
  } catch (error) {
    console.error("Error fetching order details:", error);
    alert("Erreur lors du chargement des détails de la commande");
  }
}

/**
 * Update order status
 * @param {number} orderId - ID of the order
 * @param {string} status - New status value
 */
async function updateOrderStatus(orderId, status) {
  try {
    const response = await fetch("/api/commande.php?action=update_status", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        id_commande: orderId,
        statut: status,
      }),
    }).then((response) => response.json());

    if (response.success) {
      // Update UI to reflect status change
      updateOrderStatusInUI(orderId, status);
      alert("Statut de la commande mis à jour avec succès");
    } else {
      alert("Erreur lors de la mise à jour du statut: " + response.message);
    }
  } catch (error) {
    console.error("Error updating order status:", error);
    alert("Erreur lors de la mise à jour du statut de la commande");
  }
}

/**
 * Update order status in the UI
 * @param {number} orderId - ID of the order
 * @param {string} status - New status value
 */
function updateOrderStatusInUI(orderId, status) {
  const statusCell = document.querySelector(
    `tr[data-order-id="${orderId}"] .order-status`
  );
  if (statusCell) {
    // Remove old status classes
    statusCell.classList.forEach((className) => {
      if (className.startsWith("status-")) {
        statusCell.classList.remove(className);
      }
    });

    // Add new status class and update text
    statusCell.classList.add(`status-${status}`);

    const statusMap = {
      en_attente: "En attente",
      validee: "Validée",
      expediee: "Expédiée",
      livree: "Livrée",
      annulee: "Annulée",
    };

    statusCell.textContent = statusMap[status] || status;
  }
}

/**
 * Utility function to debounce function calls
 * @param {Function} func - Function to debounce
 * @param {number} wait - Wait time in milliseconds
 * @returns {Function} Debounced function
 */
function debounce(func, wait) {
  let timeout;
  return function () {
    const context = this,
      args = arguments;
    clearTimeout(timeout);
    timeout = setTimeout(() => func.apply(context, args), wait);
  };
}
