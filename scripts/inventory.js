let allCards = [];
let displayList = [];
let currentPage = 0;
let cardsPerPage = 4;
let searchTerm = "";
let currentSort = "default";
let fileToUpload = null;
let currentCode = "";
let currentSlot = "";
let currentImgSrc = "";

const DOM = {
  grid: document.getElementById("productsGrid"),
  prevBtn: document.getElementById("prevBtn"),
  nextBtn: document.getElementById("nextBtn"),
  pageInfo: document.getElementById("pageInfo"),
  searchInput: document.getElementById("searchInput"),
  sortSelect: document.getElementById("sortSelect"),
  replaceInput: document.getElementById("replaceInput"),
  imageModal: document.getElementById("imageModal"),
  fullImage: document.getElementById("fullImage"),
  imageActions: document.getElementById("imageActions"),
  editModal: document.getElementById("editModal"),
  deleteModal: document.getElementById("deleteModal"),
};

function getText(el, selector, fallback = "") {
  const s = el.querySelector(selector);
  return s ? s.textContent.trim() : fallback;
}

function parsePrice(el) {
  const txt = getText(el, ".price", "0").replace("₱", "").replace(/,/g, "");
  return parseFloat(txt) || 0;
}

function parseQuantity(el) {
  const txt = getText(el, "p:nth-of-type(2)", "0").replace("Stock:", "").trim();
  return parseInt(txt, 10) || 0;
}

function parseDate(el) {
  const s = el.querySelector("small");
  if (!s) return 0;
  const d = new Date(s.textContent.replace("Added:", "").trim());
  return isNaN(d.getTime()) ? 0 : d.getTime();
}

const sorters = {
  default: (a, b) =>
    getText(b, "strong")
      .replace("Code:", "")
      .trim()
      .toLowerCase()
      .localeCompare(
        getText(a, "strong").replace("Code:", "").trim().toLowerCase(),
      ),
  "date-desc": (a, b) => parseDate(b) - parseDate(a),
  "date-asc": (a, b) => parseDate(a) - parseDate(b),
  "name-asc": (a, b) =>
    getText(a, "h3")
      .toLowerCase()
      .localeCompare(getText(b, "h3").toLowerCase()),
  "name-desc": (a, b) =>
    getText(b, "h3")
      .toLowerCase()
      .localeCompare(getText(a, "h3").toLowerCase()),
  "price-asc": (a, b) => parsePrice(a) - parsePrice(b),
  "price-desc": (a, b) => parsePrice(b) - parsePrice(a),
  "quantity-asc": (a, b) => parseQuantity(a) - parseQuantity(b),
  "quantity-desc": (a, b) => parseQuantity(b) - parseQuantity(a),
};

function updateDisplayList() {
  const grid = DOM.grid;
  currentSort = DOM.sortSelect ? DOM.sortSelect.value : currentSort;
  const matches = (card) => {
    const code = getText(card, "strong").toLowerCase();
    const name = getText(card, "h3").toLowerCase();
    return code.includes(searchTerm) || name.includes(searchTerm);
  };
  const filtered = allCards.filter(matches);
  const nonFiltered = allCards.filter((card) => !matches(card));
  const sorter = sorters[currentSort] || (() => 0);
  filtered.sort(sorter);
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

function updatePagination() {
  const totalPages = Math.ceil(displayList.length / cardsPerPage);
  displayList.forEach((card) => (card.style.display = "none"));
  if (totalPages === 0) {
    DOM.pageInfo.textContent = "No results";
    DOM.prevBtn.disabled = true;
    DOM.nextBtn.disabled = true;
    return;
  }
  const start = currentPage * cardsPerPage;
  const end = Math.min(start + cardsPerPage, displayList.length);
  for (let i = start; i < end; i++) displayList[i].style.display = "block";
  DOM.prevBtn.disabled = currentPage === 0;
  DOM.nextBtn.disabled = currentPage >= totalPages - 1;
  DOM.pageInfo.textContent = `Page ${currentPage + 1} of ${totalPages}`;
}

function searchProducts() {
  searchTerm = (DOM.searchInput ? DOM.searchInput.value : "").toLowerCase();
  currentPage = 0;
  updateDisplayList();
  updatePagination();
}

function sortProducts() {
  currentPage = 0;
  updateDisplayList();
  updatePagination();
}

function openEditModal(code, name, description, price, quantity) {
  document.getElementById("original_code").value = code;
  document.getElementById("edit_code").value = code;
  document.getElementById("edit_name").value = name;
  document.getElementById("edit_description").value = description;
  document.getElementById("edit_price").value = price;
  document.getElementById("edit_quantity").value = quantity;
  DOM.editModal.style.display = "block";
}

function closeEditModal() {
  DOM.editModal.style.display = "none";
}

function openDeleteModal(code) {
  document.getElementById("confirmDelete").href =
    `inventory.php?delete=${encodeURIComponent(code)}`;
  DOM.deleteModal.style.display = "block";
}

function closeDeleteModal() {
  DOM.deleteModal.style.display = "none";
}

function showPlaceholderMessage(container, referenceNode) {
  removePlaceholderMessage();
  const message = document.createElement("div");
  message.id = "placeholderMessage";
  message.textContent = "No image available. Click 'Replace' to add one.";
  message.style.cssText =
    "color: rgba(255,255,255,0.7); text-align:center; padding:20px; font-style:italic;";
  container.insertBefore(message, referenceNode);
}

function removePlaceholderMessage() {
  const e = document.getElementById("placeholderMessage");
  if (e) e.remove();
}

function restoreOriginalActions() {
  const actions = DOM.imageActions;
  const original = actions.getAttribute("data-original-buttons");
  if (original) {
    actions.innerHTML = original;
    actions.removeAttribute("data-original-buttons");
    bindReplaceInput();
  }
}

function showReplaceConfirmButtons() {
  const actions = DOM.imageActions;
  if (!actions.hasAttribute("data-original-buttons"))
    actions.setAttribute("data-original-buttons", actions.innerHTML);
  actions.innerHTML = `<button class="replace-btn" onclick="confirmReplace()">Confirm Replace</button><button class="close-btn" onclick="cancelReplace()">Cancel</button>`;
}

function addPreviewIndicator(parent) {
  const existing = document.getElementById("previewIndicator");
  if (existing) existing.remove();
  const indicator = document.createElement("div");
  indicator.id = "previewIndicator";
  indicator.textContent = "Preview - Click 'Confirm' to save";
  indicator.style.cssText =
    "position:absolute; top:10px; left:50%; transform:translateX(-50%); background: rgba(76,175,80,0.9); color:white; padding:8px 16px; border-radius:4px; font-weight:bold; z-index:10;";
  parent.appendChild(indicator);
}

function removePreviewIndicator() {
  const ind = document.getElementById("previewIndicator");
  if (ind) ind.remove();
}

function closeImageModal() {
  DOM.imageModal.style.display = "none";
  if (DOM.fullImage) DOM.fullImage.style.display = "none";
  if (DOM.replaceInput) DOM.replaceInput.value = "";
  fileToUpload = null;
  removePreviewIndicator();
  restoreOriginalActions();
  currentCode = "";
  currentSlot = "";
  currentImgSrc = "";
}

async function deleteImage() {
  if (!currentCode || !currentSlot) {
    console.error("Error: No product selected.");
    return;
  }
  try {
    const res = await fetch(`inventory.php?action=delete_image`, {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: `code=${encodeURIComponent(currentCode)}&slot=${currentSlot}`,
    });
    const data = await res.json();
    if (data.success) {
      const imgSelector = `[data-code="${currentCode}"][data-slot="${currentSlot}"]`;
      const targetImg = document.querySelector(imgSelector);
      if (targetImg) {
        targetImg.src = "assets/placeholder.svg";
        targetImg.style.cursor = "pointer";
        targetImg.alt = "Placeholder Image";
      }
      updateDisplayList();
      updatePagination();
      closeImageModal();
      console.log("Image deleted successfully!");
    } else console.error("Error deleting image: " + data.message);
  } catch (e) {
    console.error("Delete error:", e);
  }
}

async function confirmReplace() {
  if (!fileToUpload) {
    console.error("No file selected to upload.");
    return;
  }
  const formData = new FormData();
  formData.append("code", currentCode);
  formData.append("slot", currentSlot);
  formData.append("new_image", fileToUpload);
  try {
    const res = await fetch(`inventory.php?action=replace_image`, {
      method: "POST",
      body: formData,
    });
    const data = await res.json();
    if (data.success) {
      const imgSelector = `[data-code="${currentCode}"][data-slot="${currentSlot}"]`;
      const targetImg = document.querySelector(imgSelector);
      if (targetImg) {
        targetImg.src = data.new_path + "?t=" + new Date().getTime();
        targetImg.style.cursor = "pointer";
        targetImg.alt = "Product Image";
      }
      updateDisplayList();
      updatePagination();
      closeImageModal();
      console.log("Image replaced successfully!");
    } else console.error("Error replacing image: " + data.message);
  } catch (e) {
    console.error("Replace error:", e);
  }
}

function cancelReplace() {
  if (DOM.replaceInput) DOM.replaceInput.value = "";
  fileToUpload = null;
  if (DOM.fullImage) DOM.fullImage.src = currentImgSrc;
  const isPlaceholder =
    currentImgSrc && currentImgSrc.includes("placeholder.svg");
  if (isPlaceholder && DOM.fullImage) {
    DOM.fullImage.style.display = "none";
    showPlaceholderMessage(DOM.fullImage.parentElement, DOM.imageActions);
  } else if (DOM.fullImage) {
    DOM.fullImage.style.display = "block";
    removePlaceholderMessage();
  }
  removePreviewIndicator();
  restoreOriginalActions();
}

function initPagination() {
  DOM.prevBtn.addEventListener("click", () => {
    if (currentPage > 0) {
      currentPage--;
      updatePagination();
    }
  });
  DOM.nextBtn.addEventListener("click", () => {
    const totalPages = Math.ceil(displayList.length / cardsPerPage);
    if (currentPage < totalPages - 1) {
      currentPage++;
      updatePagination();
    }
  });
  updatePagination();
}

function handleReplaceInput(event) {
  const file = event.target.files[0];
  if (!file || !currentCode || !currentSlot) {
    console.error("Error: No product selected or invalid file.");
    return;
  }
  fileToUpload = file;
  const reader = new FileReader();
  reader.onload = function (e) {
    if (!DOM.fullImage) return;
    DOM.fullImage.parentElement.style.position = "relative";
    removePlaceholderMessage();
    DOM.fullImage.src = e.target.result;
    DOM.fullImage.style.display = "block";
    addPreviewIndicator(DOM.fullImage.parentElement);
    showReplaceConfirmButtons();
  };
  reader.readAsDataURL(file);
}

function bindReplaceInput() {
  DOM.replaceInput = document.getElementById("replaceInput");
  if (DOM.replaceInput) {
    DOM.replaceInput.value = "";
    DOM.replaceInput.onchange = handleReplaceInput;
  }
}

function openImageModal(img) {
  currentCode = img.dataset.code;
  currentSlot = img.dataset.slot;
  currentImgSrc = img.src || "";
  const isPlaceholder = currentImgSrc.includes("placeholder.svg");
  restoreOriginalActions();
  removePlaceholderMessage();
  if (isPlaceholder) {
    if (DOM.fullImage) DOM.fullImage.style.display = "none";
    const del = DOM.imageActions.querySelector(".delete-btn");
    if (del) del.style.display = "none";
    showPlaceholderMessage(DOM.fullImage.parentElement, DOM.imageActions);
  } else {
    if (DOM.fullImage) {
      DOM.fullImage.src = currentImgSrc;
      DOM.fullImage.style.display = "block";
    }
    const del = DOM.imageActions.querySelector(".delete-btn");
    if (del) del.style.display = "inline-block";
  }
  DOM.imageActions.style.display = "flex";
  DOM.imageModal.style.display = "block";
  bindReplaceInput();
}

document.addEventListener("DOMContentLoaded", function () {
  allCards = Array.from(document.querySelectorAll(".card"));
  displayList = [...allCards];
  if (DOM.sortSelect) DOM.sortSelect.value = "default";
  updateDisplayList();
  initPagination();
  if (DOM.grid) DOM.grid.style.opacity = 1;
  if (DOM.searchInput)
    DOM.searchInput.addEventListener("keyup", function (e) {
      if (e.key === "Enter") searchProducts();
    });
  bindReplaceInput();
  if (DOM.grid) {
    DOM.grid.addEventListener("click", function (event) {
      const img = event.target.closest("img[data-code]");
      if (!img || !img.dataset.code || !img.dataset.slot) return;
      openImageModal(img);
    });
  }
});

window.confirmReplace = confirmReplace;
window.cancelReplace = cancelReplace;
window.deleteImage = deleteImage;
window.closeImageModal = closeImageModal;
window.openEditModal = openEditModal;
window.closeEditModal = closeEditModal;
window.openDeleteModal = openDeleteModal;
window.closeDeleteModal = closeDeleteModal;
window.searchProducts = searchProducts;
window.sortProducts = sortProducts;

window.addEventListener("click", function (event) {
  if (event.target === DOM.imageModal) closeImageModal();
});

document.addEventListener("keydown", function (event) {
  if (event.key === "Escape") {
    if (DOM.imageModal && DOM.imageModal.style.display === "block")
      closeImageModal();
    if (DOM.editModal && DOM.editModal.style.display === "block")
      closeEditModal();
    if (DOM.deleteModal && DOM.deleteModal.style.display === "block")
      closeDeleteModal();
  }
});
