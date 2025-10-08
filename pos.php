<?php
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: index.php");
    exit();
}

try {
    $dbPath = __DIR__ . DIRECTORY_SEPARATOR . "inventory.db";
    $db = new PDO("sqlite:$dbPath");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Handle sale transaction
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["action"])) {
    header("Content-Type: application/json");

    if ($_POST["action"] === "process_sale") {
        $cart = json_decode($_POST["cart"], true);
        $success = true;
        $message = "";

        $db->beginTransaction();

        try {
            foreach ($cart as $item) {
                $code = $item["code"];
                $quantity = $item["quantity"];

                // Check current stock
                $stmt = $db->prepare(
                    "SELECT quantity, soldQuantity FROM products WHERE code = ?",
                );
                $stmt->execute([$code]);
                $product = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$product) {
                    throw new Exception("Product not found: $code");
                }

                if ($product["quantity"] < $quantity) {
                    throw new Exception(
                        "Insufficient stock for product: $code",
                    );
                }

                // Update quantities
                $newQuantity = $product["quantity"] - $quantity;
                $newSoldQuantity = $product["soldQuantity"] + $quantity;

                $stmt = $db->prepare(
                    "UPDATE products SET quantity = ?, soldQuantity = ? WHERE code = ?",
                );
                $stmt->execute([$newQuantity, $newSoldQuantity, $code]);
            }

            $db->commit();
            $message = "Sale processed successfully!";
        } catch (Exception $e) {
            $db->rollBack();
            $success = false;
            $message = $e->getMessage();
        }

        echo json_encode(["success" => $success, "message" => $message]);
        exit();
    }

    if ($_POST["action"] === "check_stock") {
        $code = $_POST["code"];
        $stmt = $db->prepare("SELECT quantity FROM products WHERE code = ?");
        $stmt->execute([$code]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        echo json_encode([
            "success" => true,
            "stock" => $product ? $product["quantity"] : 0,
        ]);
        exit();
    }
}

// Get all products with stock
$products = $db
    ->query("SELECT * FROM products WHERE quantity > 0 ORDER BY name ASC")
    ->fetchAll(PDO::FETCH_ASSOC);

// Get out of stock products
$outOfStock = $db
    ->query("SELECT * FROM products WHERE quantity = 0 ORDER BY name ASC")
    ->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Point of Sale</title>
    <link rel="stylesheet" href="styles/pos.css">
    <link rel="icon" type="image/svg+xml" href="assets/logo.svg">
</head>
<body>
    <div class="pos-container">
        <img src="assets/logo.svg" class="pos-logo">
        <div class="pos-header">
            <h2>Available Products</h2>
            <div class="pagination-controls">
                <button class="nav-button prev-btn" id="prevBtn" disabled>&lt;</button>
                <span id="pageInfo">Page 1 of 1</span>
                <button class="nav-button next-btn" id="nextBtn" disabled>&gt;</button>
            </div>

            <div class="search-container">
                <input type="text" id="searchInput" placeholder="Search products..." onkeyup="filterProducts()">
            </div>
            <div class="header-actions">
                <?php if (
                    isset($_SESSION["role"]) &&
                    $_SESSION["role"] === "admin"
                ): ?>
                    <a href="inventory.php" class="btn-secondary">Inventory</a>
                    <a href="dashboard.php" class="btn-secondary">Dashboard</a>
                <?php endif; ?>
                <a href="logout.php" class="btn-logout">Logout</a>
            </div>
        </div>

        <div class="pos-main">
            <div class="products-section">
                <div class="section-header">

                </div>

                <div class="products-container">
                    <div class="products-wrapper">
                        <div class="products-grid" id="productsGrid">
                        <?php foreach ($products as $p): ?>
                    <div class="product-card" data-code="<?php echo htmlspecialchars(
                        $p["code"],
                    ); ?>"
                         data-name="<?php echo htmlspecialchars($p["name"]); ?>"
                         data-price="<?php echo $p["price"]; ?>"
                         data-stock="<?php echo $p["quantity"]; ?>">
                        <div class="product-image">
                            <?php if ($p["image1"]): ?>
                                <img src="<?php echo htmlspecialchars(
                                    $p["image1"],
                                ); ?>" alt="Product" onclick="openProductImage('<?php echo htmlspecialchars(
    $p["image1"],
); ?>')">
                            <?php else: ?>
                                <img src="assets/placeholder.svg" alt="Product">
                            <?php endif; ?>
                        </div>
                        <div class="product-info">
                            <h4><?php echo htmlspecialchars($p["name"]); ?></h4>
                            <p class="product-code"><?php echo htmlspecialchars(
                                $p["code"],
                            ); ?></p>
                            <p class="product-price">₱<?php echo number_format(
                                $p["price"],
                                2,
                            ); ?></p>
                            <p class="product-stock">Stock: <?php echo $p[
                                "quantity"
                            ]; ?></p>
                            <button onclick="addToCart('<?php echo htmlspecialchars(
                                $p["code"],
                            ); ?>', '<?php echo htmlspecialchars(
    $p["name"],
); ?>', <?php echo $p["price"]; ?>, <?php echo $p["quantity"]; ?>)"
                                    class="btn-add-cart">Add to Cart</button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                        </div>
                    </div>

                </div>

                <?php if (count($outOfStock) > 0): ?>
                <div class="out-of-stock-section" id="outOfStockSection" style="display: none;">
                    <h3>Out of Stock (<span id="outOfStockCount"><?php echo count(
                        $outOfStock,
                    ); ?></span>)</h3>
                    <div class="out-of-stock-grid">
                        <?php foreach ($outOfStock as $p): ?>
                        <div class="product-card out-of-stock">
                            <div class="product-image">
                                <?php if ($p["image1"]): ?>
                                    <img src="<?php echo htmlspecialchars(
                                        $p["image1"],
                                    ); ?>" alt="Product" onclick="openProductImage('<?php echo htmlspecialchars(
    $p["image1"],
); ?>')">
                                <?php else: ?>
                                    <img src="assets/placeholder.svg" alt="Product">
                                <?php endif; ?>
                                <div class="out-of-stock-overlay">SOLD OUT</div>
                            </div>
                            <div class="product-info">
                                <h4><?php echo htmlspecialchars(
                                    $p["name"],
                                ); ?></h4>
                                <p class="product-code"><?php echo htmlspecialchars(
                                    $p["code"],
                                ); ?></p>
                                <p class="product-price">₱<?php echo number_format(
                                    $p["price"],
                                    2,
                                ); ?></p>
                                <p class="product-stock sold-out">Out of Stock</p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <div class="cart-section">
                <div class="cart-header">
                    <h2>Shopping Cart</h2>
                    <button onclick="clearCart()" class="btn-clear">Clear Cart</button>
                </div>

                <div class="cart-items" id="cartItems">
                    <div class="empty-cart">
                        <p>Cart is empty</p>
                    </div>
                </div>

                <div class="cart-summary">
                    <div class="summary-row">
                        <span>Subtotal:</span>
                        <span id="subtotal">₱0.00</span>
                    </div>
                    <div class="summary-row">
                        <span>Tax (12%):</span>
                        <span id="tax">₱0.00</span>
                    </div>
                    <div class="summary-row total">
                        <span>Total:</span>
                        <span id="total">₱0.00</span>
                    </div>

                    <div class="payment-section">
                        <label for="payment">Payment:</label>
                        <input type="number" id="payment" placeholder="Enter amount" step="0.01" min="0" onkeyup="calculateChange()">

                        <div class="change-row">
                            <span>Change:</span>
                            <span id="change">₱0.00</span>
                        </div>
                    </div>

                    <button onclick="processSale()" class="btn-checkout" id="checkoutBtn" disabled>
                        Process Sale
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Image Modal -->
    <div id="imageModal" class="modal">
        <div class="modal-content image-modal">
            <span class="close" onclick="closeImageModal()">&times;</span>
            <img id="fullImage" src="" alt="Full Image" style="display: none;">
        </div>
    </div>

    <!-- Success Modal -->
    <div id="successModal" class="modal">
        <div class="modal-content success-modal">
            <span class="close" onclick="closeSuccessModal()">&times;</span>
            <div class="success-icon">✓</div>
            <h2>Sale Completed!</h2>
            <p id="successMessage"></p>
            <button onclick="closeSuccessModal()" class="btn-ok">OK</button>
        </div>
    </div>

    <!-- Error Modal -->
    <div id="errorModal" class="modal">
        <div class="modal-content error-modal">
            <span class="close" onclick="closeErrorModal()">&times;</span>
            <h2>Error</h2>
            <p id="errorMessage"></p>
            <button onclick="closeErrorModal()" class="btn-ok">OK</button>
        </div>
    </div>

    <script src="scripts/pos.js"></script>
    <script src="scripts/vanilla-tilt.js"></script>
    <script>
        VanillaTilt.init(document.querySelectorAll(".product-card"), {
            max: 5,
            speed: 400,
            glare: true,
            "max-glare": 0.2,
        });

        // Image modal functionality
        function openProductImage(imageSrc) {
            const modal = document.getElementById('imageModal');
            const fullImage = document.getElementById('fullImage');

            fullImage.src = imageSrc;
            fullImage.style.display = 'block';
            modal.style.display = 'block';
        }

        function closeImageModal() {
            const modal = document.getElementById('imageModal');
            const fullImage = document.getElementById('fullImage');

            modal.style.display = 'none';
            fullImage.style.display = 'none';
        }

        // Close modal on outside click
        window.addEventListener('click', function(event) {
            const modal = document.getElementById('imageModal');
            if (event.target === modal) {
                closeImageModal();
            }
        });

        // Close modal on ESC key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeImageModal();
            }
        });
    </script>
</body>
</html>
