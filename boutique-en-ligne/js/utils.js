// js/utils.js
// Utility functions for the frontend

// Format price to currency
function formatPrice(price) {
  return parseFloat(price).toFixed(2) + " €";
}

// Format date to locale format
function formatDate(dateString) {
  const date = new Date(dateString);
  return date.toLocaleDateString("fr-FR");
}

// Show alert message
function showAlert(message, type = "success", container = null) {
  const alertElement = document.createElement("div");
  alertElement.className = `alert alert-${type}`;
  alertElement.textContent = message;

  if (container) {
    container.prepend(alertElement);
  } else {
    const mainContainer = document.querySelector("main") || document.body;
    mainContainer.prepend(alertElement);
  }

  // Auto-dismiss after 5 seconds
  setTimeout(() => {
    alertElement.remove();
  }, 5000);
}

// Create pagination
function createPagination(currentPage, totalPages, onPageChange) {
  const paginationElement = document.createElement("div");
  paginationElement.className = "pagination";

  // Previous button
  if (currentPage > 1) {
    const prevButton = document.createElement("button");
    prevButton.className = "btn btn-secondary";
    prevButton.textContent = "Précédent";
    prevButton.addEventListener("click", () => onPageChange(currentPage - 1));
    paginationElement.appendChild(prevButton);
  }

  // Page numbers
  const startPage = Math.max(1, currentPage - 2);
  const endPage = Math.min(totalPages, startPage + 4);

  for (let i = startPage; i <= endPage; i++) {
    const pageButton = document.createElement("button");
    pageButton.className = `btn ${
      i === currentPage ? "btn-primary" : "btn-secondary"
    }`;
    pageButton.textContent = i;
    pageButton.addEventListener("click", () => onPageChange(i));
    paginationElement.appendChild(pageButton);
  }

  // Next button
  if (currentPage < totalPages) {
    const nextButton = document.createElement("button");
    nextButton.className = "btn btn-secondary";
    nextButton.textContent = "Suivant";
    nextButton.addEventListener("click", () => onPageChange(currentPage + 1));
    paginationElement.appendChild(nextButton);
  }

  return paginationElement;
}

// Validate form input
function validateInput(value, type) {
  switch (type) {
    case "email":
      return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
    case "password":
      // At least 8 characters, containing letters and numbers
      return value.length >= 8 && /[A-Za-z]/.test(value) && /[0-9]/.test(value);
    case "number":
      return !isNaN(parseFloat(value)) && isFinite(value);
    case "zipcode":
      // French postal code format (5 digits)
      return /^[0-9]{5}$/.test(value);
    case "phone":
      // French phone number format
      return /^(?:(?:\+|00)33|0)\s*[1-9](?:[\s.-]*\d{2}){4}$/.test(value);
    default:
      return value.trim().length > 0;
  }
}
