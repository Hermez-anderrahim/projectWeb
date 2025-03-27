/**
 * Admin JavaScript for the Anon E-commerce Website
 */

document.addEventListener("DOMContentLoaded", function () {
  // Mobile sidebar toggle
  const sidebarToggle = document.querySelector(".sidebar-toggle");
  const adminSidebar = document.querySelector(".admin-sidebar");

  if (sidebarToggle && adminSidebar) {
    sidebarToggle.addEventListener("click", function () {
      adminSidebar.classList.toggle("show");
      document.body.classList.toggle("sidebar-open");
    });

    // Close sidebar when clicking outside
    document.addEventListener("click", function (e) {
      if (
        window.innerWidth < 992 &&
        adminSidebar.classList.contains("show") &&
        !adminSidebar.contains(e.target) &&
        !sidebarToggle.contains(e.target)
      ) {
        adminSidebar.classList.remove("show");
        document.body.classList.remove("sidebar-open");
      }
    });
  }

  // Dropdown menus
  const dropdownToggles = document.querySelectorAll(".dropdown-toggle");

  dropdownToggles.forEach((toggle) => {
    toggle.addEventListener("click", function (e) {
      e.preventDefault();
      this.parentElement.classList.toggle("show");

      // Close other dropdowns
      dropdownToggles.forEach((otherToggle) => {
        if (otherToggle !== this) {
          otherToggle.parentElement.classList.remove("show");
        }
      });
    });

    // Close dropdown when clicking outside
    document.addEventListener("click", function (e) {
      if (!toggle.contains(e.target)) {
        toggle.parentElement.classList.remove("show");
      }
    });
  });

  // Modal handling
  const modalTriggers = document.querySelectorAll("[data-modal]");
  const closeModalButtons = document.querySelectorAll(".close-modal");

  modalTriggers.forEach((trigger) => {
    trigger.addEventListener("click", function (e) {
      e.preventDefault();
      const modalId = this.getAttribute("data-modal");
      const modal = document.getElementById(modalId);

      if (modal) {
        modal.classList.add("show");
        document.body.classList.add("modal-open");
      }
    });
  });

  closeModalButtons.forEach((button) => {
    button.addEventListener("click", function () {
      const modal = this.closest(".modal");
      modal.classList.remove("show");
      document.body.classList.remove("modal-open");
    });
  });

  // Close modal when clicking outside
  document.addEventListener("click", function (e) {
    if (e.target.classList.contains("modal")) {
      e.target.classList.remove("show");
      document.body.classList.remove("modal-open");
    }
  });

  // Alerts auto-close
  const alerts = document.querySelectorAll(".alert");

  alerts.forEach((alert) => {
    if (!alert.classList.contains("persistent")) {
      setTimeout(() => {
        alert.classList.add("fade-out");
        setTimeout(() => {
          alert.remove();
        }, 500);
      }, 5000);
    }

    const closeBtn = alert.querySelector(".close-alert");
    if (closeBtn) {
      closeBtn.addEventListener("click", function () {
        alert.classList.add("fade-out");
        setTimeout(() => {
          alert.remove();
        }, 500);
      });
    }
  });

  // Image preview
  const imageInputs = document.querySelectorAll(
    'input[type="file"][accept*="image"]'
  );

  imageInputs.forEach((input) => {
    input.addEventListener("change", function () {
      const previewElement = document.querySelector(
        `#${this.getAttribute("data-preview")}`
      );

      if (previewElement && this.files && this.files[0]) {
        const reader = new FileReader();

        reader.onload = function (e) {
          previewElement.src = e.target.result;
        };

        reader.readAsDataURL(this.files[0]);
      }
    });
  });

  // Bulk action handling
  const bulkActionForm = document.querySelector(".bulk-action-form");

  if (bulkActionForm) {
    const selectAll = bulkActionForm.querySelector(".select-all");
    const checkboxes = bulkActionForm.querySelectorAll(".item-checkbox");
    const bulkActionSelect = bulkActionForm.querySelector(
      ".bulk-action-select"
    );
    const applyBtn = bulkActionForm.querySelector(".apply-bulk-action");

    // Select all checkboxes
    if (selectAll) {
      selectAll.addEventListener("change", function () {
        checkboxes.forEach((checkbox) => {
          checkbox.checked = this.checked;
        });

        updateBulkActionButton();
      });
    }

    // Update apply button state
    checkboxes.forEach((checkbox) => {
      checkbox.addEventListener("change", function () {
        updateBulkActionButton();

        // Update select all checkbox
        if (selectAll) {
          selectAll.checked = [...checkboxes].every((c) => c.checked);
          selectAll.indeterminate =
            !selectAll.checked && [...checkboxes].some((c) => c.checked);
        }
      });
    });

    // Apply bulk action
    bulkActionForm.addEventListener("submit", function (e) {
      const selectedAction = bulkActionSelect.value;
      const selectedItems = [...checkboxes]
        .filter((c) => c.checked)
        .map((c) => c.value);

      if (!selectedAction || selectedItems.length === 0) {
        e.preventDefault();
        alert("Please select an action and at least one item.");
        return;
      }

      if (
        selectedAction === "delete" &&
        !confirm("Are you sure you want to delete the selected items?")
      ) {
        e.preventDefault();
      }
    });

    function updateBulkActionButton() {
      const selectedCount = [...checkboxes].filter((c) => c.checked).length;
      applyBtn.disabled = selectedCount === 0;

      if (selectedCount > 0) {
        applyBtn.textContent = `Apply (${selectedCount})`;
      } else {
        applyBtn.textContent = "Apply";
      }
    }
  }

  // Sales chart (using Chart.js if available)
  const salesChartCanvas = document.getElementById("sales-chart");

  if (salesChartCanvas && typeof Chart !== "undefined") {
    // Sample data for demonstration
    const salesData = {
      labels: [
        "Jan",
        "Feb",
        "Mar",
        "Apr",
        "May",
        "Jun",
        "Jul",
        "Aug",
        "Sep",
        "Oct",
        "Nov",
        "Dec",
      ],
      datasets: [
        {
          label: "Revenue",
          backgroundColor: "rgba(255, 107, 107, 0.2)",
          borderColor: "rgba(255, 107, 107, 1)",
          borderWidth: 2,
          data: [
            1500, 2500, 1800, 3000, 2800, 3500, 4000, 3800, 4200, 4500, 5000,
            5500,
          ],
        },
      ],
    };

    const salesChart = new Chart(salesChartCanvas, {
      type: "line",
      data: salesData,
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              callback: function (value) {
                return "$" + value;
              },
            },
          },
        },
        plugins: {
          tooltip: {
            callbacks: {
              label: function (context) {
                return "$" + context.parsed.y;
              },
            },
          },
        },
      },
    });

    // Period selector
    const periodSelect = document.getElementById("sales-period");

    if (periodSelect) {
      periodSelect.addEventListener("change", function () {
        // In a real application, this would fetch new data from the server
        // For demonstration, we'll just update with random data
        const newData = salesData.datasets[0].data.map(
          () => Math.floor(Math.random() * 5000) + 1000
        );
        salesChart.data.datasets[0].data = newData;
        salesChart.update();
      });
    }
  }
});
