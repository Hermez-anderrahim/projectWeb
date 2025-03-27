/**
 * Main JavaScript for the Anon E-commerce Website
 */

document.addEventListener("DOMContentLoaded", function () {
  // Mobile menu toggle
  const mobileMenuToggle = document.querySelector(".mobile-menu-toggle");
  const navMenu = document.querySelector(".nav-menu");

  if (mobileMenuToggle) {
    mobileMenuToggle.addEventListener("click", function () {
      navMenu.classList.toggle("active");
      this.classList.toggle("active");
    });
  }

  // Dropdown menus
  const hasDropdown = document.querySelectorAll(".has-dropdown");

  hasDropdown.forEach((item) => {
    item.addEventListener("click", function (e) {
      if (window.innerWidth < 992) {
        e.preventDefault();
        this.classList.toggle("active");
        this.querySelector(".dropdown-menu").classList.toggle("show");
      }
    });
  });

  // Quantity controls on product pages
  const quantityControls = document.querySelectorAll(".quantity-control");

  quantityControls.forEach((control) => {
    const input = control.querySelector(".quantity-input");
    const minusBtn = control.querySelector(".minus");
    const plusBtn = control.querySelector(".plus");

    if (minusBtn && plusBtn && input) {
      minusBtn.addEventListener("click", function () {
        let value = parseInt(input.value);
        if (value > 1) {
          input.value = value - 1;
          // Trigger change event
          const event = new Event("change", { bubbles: true });
          input.dispatchEvent(event);
        }
      });

      plusBtn.addEventListener("click", function () {
        let value = parseInt(input.value);
        const max = parseInt(input.getAttribute("max") || 999);
        if (value < max) {
          input.value = value + 1;
          // Trigger change event
          const event = new Event("change", { bubbles: true });
          input.dispatchEvent(event);
        }
      });

      input.addEventListener("change", function () {
        let value = parseInt(this.value);
        const min = parseInt(this.getAttribute("min") || 1);
        const max = parseInt(this.getAttribute("max") || 999);

        if (value < min) {
          this.value = min;
        } else if (value > max) {
          this.value = max;
        }
      });
    }
  });

  // Back to top button
  const backToTop = document.querySelector(".back-to-top");

  if (backToTop) {
    window.addEventListener("scroll", function () {
      if (window.pageYOffset > 300) {
        backToTop.classList.add("show");
      } else {
        backToTop.classList.remove("show");
      }
    });

    backToTop.addEventListener("click", function (e) {
      e.preventDefault();
      window.scrollTo({
        top: 0,
        behavior: "smooth",
      });
    });
  }

  // Product tabs
  const tabButtons = document.querySelectorAll(".tab-btn");

  tabButtons.forEach((button) => {
    button.addEventListener("click", function () {
      // Remove active class from all buttons and panes
      document.querySelectorAll(".tab-btn").forEach((btn) => {
        btn.classList.remove("active");
      });
      document.querySelectorAll(".tab-pane").forEach((pane) => {
        pane.classList.remove("active");
      });

      // Add active class to clicked button and its pane
      this.classList.add("active");
      const tabId = this.getAttribute("data-tab");
      document.getElementById(tabId).classList.add("active");
    });
  });

  // Lightbox for product images
  const productImages = document.querySelectorAll(".product-gallery img");

  productImages.forEach((img) => {
    img.addEventListener("click", function () {
      const lightbox = document.createElement("div");
      lightbox.className = "lightbox";

      const lightboxImg = document.createElement("img");
      lightboxImg.src = this.src;

      const closeBtn = document.createElement("span");
      closeBtn.className = "lightbox-close";
      closeBtn.innerHTML = "&times;";

      lightbox.appendChild(lightboxImg);
      lightbox.appendChild(closeBtn);
      document.body.appendChild(lightbox);

      lightbox.addEventListener("click", function () {
        this.remove();
      });

      closeBtn.addEventListener("click", function (e) {
        e.stopPropagation();
        lightbox.remove();
      });
    });
  });

  // Sort by select
  const sortSelect = document.getElementById("sort-by");

  if (sortSelect) {
    sortSelect.addEventListener("change", function () {
      const url = new URL(window.location.href);
      url.searchParams.set("sort", this.value);
      window.location.href = url.toString();
    });

    // Set initial select value based on URL
    const urlParams = new URLSearchParams(window.location.search);
    const sortParam = urlParams.get("sort");
    if (sortParam) {
      sortSelect.value = sortParam;
    }
  }

  // Global notification function
  window.showNotification = function (message, type = "info") {
    const notification = document.createElement("div");
    notification.className = "notification " + type;
    notification.textContent = message;

    document.body.appendChild(notification);

    setTimeout(() => {
      notification.classList.add("show");
    }, 100);

    setTimeout(() => {
      notification.classList.remove("show");
      setTimeout(() => {
        notification.remove();
      }, 500);
    }, 3000);
  };

  // Handle validation errors
  const forms = document.querySelectorAll("form");

  forms.forEach((form) => {
    form.addEventListener("submit", function (e) {
      const requiredFields = form.querySelectorAll("[required]");
      let isValid = true;

      requiredFields.forEach((field) => {
        if (!field.value.trim()) {
          isValid = false;
          field.classList.add("error");
        } else {
          field.classList.remove("error");
        }
      });

      if (!isValid) {
        e.preventDefault();
        window.showNotification("Please fill in all required fields.", "error");

        // Scroll to first error field
        const firstError = form.querySelector(".error");
        if (firstError) {
          firstError.scrollIntoView({ behavior: "smooth", block: "center" });
        }
      }
    });
  });
});
