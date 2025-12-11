// POS System JavaScript

let cart = [];
let products = [];
let allCards = [];
let displayList = [];
let currentPage = 0;
let cardsPerPage = 8; // Show 2 rows of 4 cards to prevent overflow
let outOfStockCards = [];

// Initialize cart from localStorage if exists
function initializeCart() {
  const savedCart = localStorage.getItem("posCart");
  if (savedCart) {
    cart = JSON.parse(savedCart);
    updateCartDisplay();
  }
}

// Add product to cart
function addToCart(code, name, price, stock) {
  // Check if product already in cart
  const existingItem = cart.find((item) => item.code === code);

  if (existingItem) {
    // Check stock limit
    if (existingItem.quantity < stock) {
      existingItem.quantity++;
      updateCartDisplay();
      showNotification("Product quantity updated", "success");
    } else {
      showNotification("Maximum stock reached", "warning");
    }
  } else {
    // Add new item to cart
    cart.push({
      code: code,
      name: name,
      price: price,
      quantity: 1,
      maxStock: stock,
    });
    updateCartDisplay();
    showNotification("Product added to cart", "success");
  }

  // Save to localStorage
  localStorage.setItem("posCart", JSON.stringify(cart));
}

// Remove item from cart
function removeFromCart(code) {
  cart = cart.filter((item) => item.code !== code);
  updateCartDisplay();
  localStorage.setItem("posCart", JSON.stringify(cart));
}

// Update item quantity
function updateQuantity(code, change) {
  const item = cart.find((item) => item.code === code);
  if (item) {
    const newQuantity = item.quantity + change;

    if (newQuantity <= 0) {
      removeFromCart(code);
    } else if (newQuantity <= item.maxStock) {
      item.quantity = newQuantity;
      updateCartDisplay();
      localStorage.setItem("posCart", JSON.stringify(cart));
    } else {
      showNotification("Maximum stock reached", "warning");
    }
  }
}

// Clear entire cart
function clearCart() {
  if (cart.length === 0) {
    showNotification("Cart is already empty", "info");
    return;
  }

  // NOTE: Confirm replacement
  if (confirm("Are you sure you want to clear the cart?")) {
    cart = [];
    updateCartDisplay();
    localStorage.removeItem("posCart");
    showNotification("Cart cleared", "info");
  }
}

// Update cart display
function updateCartDisplay() {
  const cartItems = document.getElementById("cartItems");
  const checkoutBtn = document.getElementById("checkoutBtn");

  if (cart.length === 0) {
    cartItems.innerHTML = '<div class="empty-cart"><p>Cart is empty</p></div>';
    checkoutBtn.disabled = true;
  } else {
    let html = "";
    cart.forEach((item) => {
      const subtotal = item.price * item.quantity;
      html += `
                <div class="cart-item">
                    <div class="cart-item-header">
                        <div class="cart-item-name">${escapeHtml(item.name)}</div>
                        <button class="cart-item-remove" onclick="removeFromCart('${item.code}')">×</button>
                    </div>
                    <div class="cart-item-code">${escapeHtml(item.code)}</div>
                    <div class="cart-item-details">
                        <div class="quantity-controls">
                            <button onclick="updateQuantity('${item.code}', -1)">−</button>
                            <span class="quantity-display">${item.quantity}</span>
                            <button onclick="updateQuantity('${item.code}', 1)">+</button>
                        </div>
                        <div class="cart-item-price">₱${subtotal.toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</div>
                    </div>
                </div>
            `;
    });
    cartItems.innerHTML = html;
    checkoutBtn.disabled = false;
  }

  // 1. Always recalculate totals when the cart changes.
  const { total } = calculateTotals();

  // 2. After calculating totals, update the change display based on the payment currently entered.
  calculateChange(total);
}

// Calculate totals (No longer calls calculateChange)
function calculateTotals() {
  let subtotal = 0;

  cart.forEach((item) => {
    subtotal += item.price * item.quantity;
  });

  const tax = subtotal * 0.12; // 12% VAT
  const total = subtotal + tax;

  document.getElementById("subtotal").textContent = "₱" + subtotal.toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2});
document.getElementById("tax").textContent = "₱" + tax.toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2});
document.getElementById("total").textContent = "₱" + total.toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2});

  return { subtotal, tax, total };
}

// Calculate change (Handles real-time payment input)
// Calculate change (Handles real-time payment input)
function calculateChange(totalAmountFromCart) {
  const paymentInput = document.getElementById("payment");
  const changeElement = document.getElementById("change");
  const checkoutBtn = document.getElementById("checkoutBtn");

  let total = totalAmountFromCart || 0;

  if (total === 0 && cart.length > 0) {
    const totalText = document
      .getElementById("total")
      .textContent.replace("₱", "")
      .replace(",", "");
    total = parseFloat(totalText) || 0;
  }

  const payment = parseFloat(paymentInput.value) || 0;

  // If the cart is empty or payment is zero
  if (total <= 0) {
    changeElement.textContent = "₱0.00";
    changeElement.style.color = "white"; 
    checkoutBtn.disabled = true;
    return;
  }

  // Calculate difference
  const difference = payment - total;

  if (difference >= 0) {
    // Change due
    changeElement.textContent = "₱" + difference.toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    
    if (difference === 0) {
      changeElement.style.color = "white";
    } else {
      changeElement.style.color = "#044107ff"; // Green
    }
    
    checkoutBtn.disabled = false;
  } else {
    // Shortage / Amount due
   changeElement.textContent = "-₱" + Math.abs(difference).toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    changeElement.style.color = "#a30b00ff"; // Red
    checkoutBtn.disabled = true;
  }
}

// Process sale
async function processSale() {
  const paymentInput = document.getElementById("payment");
  const payment = parseFloat(paymentInput.value) || 0;
  const { subtotal, tax, total } = calculateTotals(); // Get all values including subtotal and tax

  if (payment < total) {
    showErrorModal("Insufficient payment amount");
    return;
  }

  if (cart.length === 0) {
    showErrorModal("Cart is empty");
    return;
  }

  // Calculate change
  const change = payment - total;

  // Disable checkout button during processing
  const checkoutBtn = document.getElementById("checkoutBtn");
  checkoutBtn.disabled = true;
  checkoutBtn.textContent = "Processing...";

  try {
    const formData = new FormData();
    formData.append("action", "process_sale");
    formData.append("cart", JSON.stringify(cart));
    // ADD THESE LINES - Send payment data to PHP
    formData.append("subtotal", subtotal.toFixed(2));
    formData.append("tax", tax.toFixed(2));
    formData.append("total", total.toFixed(2));
    formData.append("payment", payment.toFixed(2));
    formData.append("change", change.toFixed(2));

    const response = await fetch("pos.php", {
      method: "POST",
      body: formData,
    });

    const result = await response.json();

    if (result.success) {
      showSuccessModal(
        `Sale completed successfully!<br>` +
          `Total: ₱${total.toFixed(2)}<br>` +
          `Payment: ₱${payment.toFixed(2)}<br>` +
          `Change: ₱${change.toFixed(2)}`,
      );

      // Clear cart after successful sale
      cart = [];
      updateCartDisplay();
      localStorage.removeItem("posCart");

      // Reset payment field
      paymentInput.value = "";
      document.getElementById("change").textContent = "₱0.00";

      // Reload page to update product stocks
      setTimeout(() => {
        location.reload();
      }, 3000);
    } else {
      showErrorModal(result.message || "Failed to process sale");
    }
  } catch (error) {
    showErrorModal("Network error: " + error.message);
  } finally {
    checkoutBtn.disabled = false;
    checkoutBtn.textContent = "Process Sale";
  }
}

// Update display list based on search
function updateDisplayList(searchTerm = "") {
  const grid = document.getElementById("productsGrid");

  const matches = (card) => {
    if (!searchTerm) return true;
    const name = (card.dataset.name || "").toLowerCase();
    const code = (card.dataset.code || "").toLowerCase();
    return name.includes(searchTerm) || code.includes(searchTerm);
  };

  // Filter cards
  let filtered = allCards.filter(matches);
  let nonFiltered = allCards.filter((card) => !matches(card));

  // Clear and rebuild grid
  grid.innerHTML = "";
  nonFiltered.forEach((card) => {
    grid.appendChild(card);
    card.style.display = "none";
  });

  displayList = filtered;
  filtered.forEach((card) => {
    grid.appendChild(card);
    card.style.display = "none";
  });
}

// Update pagination display
function updatePagination() {
  const pageInfo = document.getElementById("pageInfo");
  const prevBtn = document.getElementById("prevBtn");
  const nextBtn = document.getElementById("nextBtn");

  const totalPages = Math.ceil(displayList.length / cardsPerPage);

  // Hide all cards first
  displayList.forEach((card) => {
    card.style.display = "none";
    card.style.visibility = "hidden";
  });

  if (totalPages === 0) {
    pageInfo.textContent = "No results";
    prevBtn.disabled = true;
    nextBtn.disabled = true;
    return;
  }

  // Show cards for current page
  const start = currentPage * cardsPerPage;
  const end = Math.min(start + cardsPerPage, displayList.length);
  for (let i = start; i < end; i++) {
    if (displayList[i]) {
      displayList[i].style.display = "block";
      displayList[i].style.visibility = "visible";
    }
  }

  // Update pagination controls
  prevBtn.disabled = currentPage === 0;
  nextBtn.disabled = currentPage >= totalPages - 1;
  pageInfo.textContent = `Page ${currentPage + 1} of ${totalPages}`;
}

// Filter products with pagination
function filterProducts() {
  const searchInput = document
    .getElementById("searchInput")
    .value.toLowerCase();
  currentPage = 0; // Reset to first page when searching
  updateDisplayList(searchInput);
  updatePagination();

  // Handle out of stock section visibility
  const outOfStockSection = document.getElementById("outOfStockSection");
  if (outOfStockSection) {
    if (searchInput) {
      // Filter out of stock items too
      outOfStockCards.forEach((card) => {
        const name = (card.dataset.name || "").toLowerCase();
        const code = (card.dataset.code || "").toLowerCase();
        card.style.display =
          name.includes(searchInput) || code.includes(searchInput)
            ? "block"
            : "none";
      });
    } else {
      // Show all out of stock items when not searching
      outOfStockCards.forEach((card) => (card.style.display = "block"));
    }
  }
}

// Initialize pagination
function initPagination() {
  const prevBtn = document.getElementById("prevBtn");
  const nextBtn = document.getElementById("nextBtn");

  if (prevBtn) {
    prevBtn.addEventListener("click", () => {
      if (currentPage > 0) {
        currentPage--;
        updatePagination();
      }
    });
  }

  if (nextBtn) {
    nextBtn.addEventListener("click", () => {
      const totalPages = Math.ceil(displayList.length / cardsPerPage);
      if (currentPage < totalPages - 1) {
        currentPage++;
        updatePagination();
      }
    });
  }

  updatePagination();
}

// Show success modal
function showSuccessModal(message) {
  const modal = document.getElementById("successModal");
  const messageElement = document.getElementById("successMessage");
  messageElement.innerHTML = message;
  modal.style.display = "block";
}

// Close success modal
function closeSuccessModal() {
  document.getElementById("successModal").style.display = "none";
}

// Show error modal
function showErrorModal(message) {
  const modal = document.getElementById("errorModal");
  const messageElement = document.getElementById("errorMessage");
  messageElement.innerHTML = message;
  modal.style.display = "block";
}

// Close error modal
function closeErrorModal() {
  document.getElementById("errorModal").style.display = "none";
}

// Show notification (toast-like)
function showNotification(message, type = "info") {
  // Remove existing notification if any
  const existing = document.querySelector(".notification-toast");
  if (existing) {
    existing.remove();
  }

  const notification = document.createElement("div");
  notification.className = `notification-toast notification-${type}`;
  notification.innerHTML = message;
  notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 12px 20px;
        background: ${
          type === "success"
            ? "rgba(76, 175, 80, 0.9)"
            : type === "warning"
              ? "rgba(255, 152, 0, 0.9)"
              : type === "error"
                ? "rgba(244, 67, 54, 0.9)"
                : "rgba(33, 150, 243, 0.9)"
        };
        color: white;
        border-radius: 6px;
        z-index: 9999;
        animation: slideIn 0.3s ease;
        backdrop-filter: blur(5px);
    `;

  document.body.appendChild(notification);

  // Remove after 3 seconds
  setTimeout(() => {
    notification.style.animation = "slideOut 0.3s ease";
    setTimeout(() => notification.remove(), 300);
  }, 3000);
}

// Escape HTML to prevent XSS
function escapeHtml(text) {
  const map = {
    "&": "&amp;",
    "<": "&lt;",
    ">": "&gt;",
    '"': "&quot;",
    "'": "&#039;",
  };
  return text.replace(/[&<>"']/g, (m) => map[m]);
}

// Add CSS for notifications
const style = document.createElement("style");
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);

// Initialize on page load
document.addEventListener("DOMContentLoaded", function () {
  initializeCart();

  // Initialize pagination
  const availableCards = document.querySelectorAll(
    ".products-grid .product-card:not(.out-of-stock)",
  );
  allCards = Array.from(availableCards);
  displayList = [...allCards];

  // Adjust cards per page based on viewport
  const viewportHeight = window.innerHeight;
  if (viewportHeight < 800) {
    cardsPerPage = 4; // Show only 1 row on smaller screens
  }

  // Get out of stock cards
  const outOfStockGrid = document.querySelector(".out-of-stock-grid");
  if (outOfStockGrid) {
    outOfStockCards = Array.from(
      outOfStockGrid.querySelectorAll(".product-card"),
    );
    const outOfStockSection = document.getElementById("outOfStockSection");
    if (outOfStockSection && outOfStockCards.length > 0) {
      outOfStockSection.style.display = "block";
    }
  }

  updateDisplayList();
  initPagination();

  // Handle payment input
  const paymentInput = document.getElementById("payment");
  if (paymentInput) {
    // When payment changes, just recalculate the change (passing null or nothing
    // means it will read the Total from the DOM element, which is already updated
    // by updateCartDisplay or calculateTotals).
    paymentInput.addEventListener("input", calculateChange);

    paymentInput.addEventListener("keypress", function (e) {
      if (e.key === "Enter") {
        processSale();
      }
    });
  }

  // Close modals when clicking outside
  window.addEventListener("click", function (event) {
    const successModal = document.getElementById("successModal");
    const errorModal = document.getElementById("errorModal");

    if (event.target === successModal) {
      closeSuccessModal();
    }
    if (event.target === errorModal) {
      closeErrorModal();
    }
  });

  // Handle ESC key for modals
  document.addEventListener("keydown", function (e) {
    if (e.key === "Escape") {
      closeSuccessModal();
      closeErrorModal();
    }
  });

  // Search functionality
  const searchInput = document.getElementById("searchInput");
  if (searchInput) {
    // Trigger filter on typing (with debounce for better performance)
    let searchTimeout;
    searchInput.addEventListener("input", function (e) {
      clearTimeout(searchTimeout);
      searchTimeout = setTimeout(() => {
        filterProducts();
      }, 300);
    });

    searchInput.addEventListener("keyup", function (e) {
      if (e.key === "Enter") {
        clearTimeout(searchTimeout);
        filterProducts();
      }
    });
  }
});
