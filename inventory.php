<?php
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: index.php");
    exit();
}

// Check if user is admin
if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
    header("Location: pos.php");
    exit();
}

try {
    $dbPath = __DIR__ . DIRECTORY_SEPARATOR . "inventory.db";
    $db = new PDO("sqlite:{$dbPath}");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

if (isset($_GET["delete"])) {
    $code = $_GET["delete"];

    $stmt = $db->prepare("SELECT image1, image2 FROM products WHERE code = ?");
    $stmt->execute([$code]);
    $images = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt = $db->prepare("DELETE FROM products WHERE code = ?");
    $stmt->execute([$code]);

    foreach (["image1", "image2"] as $img) {
        if (!empty($images[$img]) && file_exists($images[$img])) {
            unlink($images[$img]);
        }
    }

    header("Location: inventory.php");
    exit();
}

$products = $db
    ->query("SELECT * FROM products ORDER BY createdAt DESC")
    ->fetchAll(PDO::FETCH_ASSOC);
function compressImage($source, $destination, $quality = 70)
{
    $info = getimagesize($source);

    if ($info["mime"] === "image/jpeg") {
        $image = imagecreatefromjpeg($source);
        imagejpeg($image, $destination, $quality);
    } elseif ($info["mime"] === "image/png") {
        $image = imagecreatefrompng($source);
        $bg = imagecreatetruecolor(imagesx($image), imagesy($image));
        $white = imagecolorallocate($bg, 255, 255, 255);
        imagefill($bg, 0, 0, $white);
        imagecopy($bg, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));
        imagejpeg($bg, $destination, $quality);
        imagedestroy($bg);
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["original_code"])) {
    $original_code = $_POST["original_code"];
    $newCode = $_POST["code"];
    $name = $_POST["name"];
    $description = $_POST["description"];
    $price = $_POST["price"];
    $quantity = $_POST["quantity"];

    if ((float) $price < 0 || (int) $quantity < 0) {
        die("Error: Price and Quantity cannot be negative.");
    }

    $stmt = $db->prepare("SELECT image1, image2 FROM products WHERE code = ?");
    $stmt->execute([$original_code]);
    $current = $stmt->fetch(PDO::FETCH_ASSOC);

    $uploadDir = "uploads/";
    $imagePaths = [$current["image1"], $current["image2"]];
    $allowedExts = ["jpg", "jpeg", "png"];
    $maxSize = 2 * 1024 * 1024;

    for ($i = 1; $i <= 2; $i++) {
        if (
            isset($_FILES["image$i"]) &&
            $_FILES["image$i"]["error"] === UPLOAD_ERR_OK
        ) {
            $fileTmp = $_FILES["image$i"]["tmp_name"];
            $fileName = basename($_FILES["image$i"]["name"]);
            $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $fileSize = $_FILES["image$i"]["size"];

            if (!in_array($ext, $allowedExts) || $fileSize > $maxSize) {
                continue;
            }

            if (
                !empty($imagePaths[$i - 1]) &&
                file_exists($imagePaths[$i - 1])
            ) {
                unlink($imagePaths[$i - 1]);
            }

            $newName = uniqid("img{$i}_") . ".jpg";
            $destPath = "{$uploadDir}{$newName}";

            if (move_uploaded_file($fileTmp, $destPath)) {
                compressImage($destPath, $destPath);
                $imagePaths[$i - 1] = $destPath;
            }
        }
    }

    $stmt = $db->prepare("UPDATE products
                          SET code=?, name=?, description=?, price=?, quantity=?, image1=?, image2=?
                          WHERE code=?");
    $stmt->execute([
        $newCode,
        $name,
        $description,
        $price,
        $quantity,
        $imagePaths[0],
        $imagePaths[1],
        $original_code,
    ]);

    header("Location: inventory.php");
    exit();
}

if (isset($_GET["action"])) {
    header("Content-Type: application/json");

    if (
        $_GET["action"] === "delete_image" &&
        isset($_POST["code"]) &&
        isset($_POST["slot"])
    ) {
        $code = $_POST["code"];
        $slot = $_POST["slot"];
        $imageField = $slot === "1" ? "image1" : "image2";

        $stmt = $db->prepare("SELECT $imageField FROM products WHERE code = ?");
        $stmt->execute([$code]);
        $current = $stmt->fetch(PDO::FETCH_ASSOC);
        $imagePath = $current[$imageField];

        $stmt = $db->prepare(
            "UPDATE products SET $imageField = ? WHERE code = ?",
        );
        $stmt->execute(["", $code]);

        if (!empty($imagePath) && file_exists($imagePath)) {
            unlink($imagePath);
        }

        echo json_encode([
            "success" => true,
            "message" => "Image deleted successfully",
        ]);
        exit();
    }

    if (
        $_GET["action"] === "replace_image" &&
        isset($_POST["code"]) &&
        isset($_POST["slot"]) &&
        isset($_FILES["new_image"])
    ) {
        $code = $_POST["code"];
        $slot = $_POST["slot"]; // 1 or 2
        $imageField = $slot === "1" ? "image1" : "image2";
        $file = $_FILES["new_image"];

        if ($file["error"] !== UPLOAD_ERR_OK) {
            echo json_encode(["success" => false, "message" => "Upload error"]);
            exit();
        }

        $allowedExts = ["jpg", "jpeg", "png"];
        $maxSize = 2 * 1024 * 1024;
        $fileName = basename($file["name"]);
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $fileSize = $file["size"];

        if (!in_array($ext, $allowedExts) || $fileSize > $maxSize) {
            echo json_encode([
                "success" => false,
                "message" => "Invalid file type or size",
            ]);
            exit();
        }

        $stmt = $db->prepare("SELECT $imageField FROM products WHERE code = ?");
        $stmt->execute([$code]);
        $current = $stmt->fetch(PDO::FETCH_ASSOC);
        $oldImagePath = $current[$imageField];
        if (!empty($oldImagePath) && file_exists($oldImagePath)) {
            unlink($oldImagePath);
        }

        // Upload and compress new image
        $uploadDir = "uploads/";
        $newName = uniqid("img{$slot}_") . ".jpg";
        $destPath = "{$uploadDir}{$newName}";

        if (move_uploaded_file($file["tmp_name"], $destPath)) {
            compressImage($destPath, $destPath);

            $stmt = $db->prepare(
                "UPDATE products SET $imageField = ? WHERE code = ?",
            );
            $stmt->execute([$destPath, $code]);

            echo json_encode([
                "success" => true,
                "message" => "Image replaced successfully",
                "new_path" => $destPath,
            ]);
        } else {
            echo json_encode([
                "success" => false,
                "message" => "Upload failed",
            ]);
        }
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Inventory</title>
    <link rel="stylesheet" href="styles/inventory.css">
  <link rel="icon" type="image/svg+xml" href="assets/logo.svg">

</head>
<body>
        <img src="assets/logo.svg" class="logo fade-target" id="headerLogo">
    <h1>Product Inventory</h1>

    <div class="search-sort-container">
        <div class="search-container">
            <input type="text" id="searchInput" placeholder="Search by code or name...">
            <button onclick="searchProducts()">Search</button>
        </div>
        <div class="sort-container">
            <label for="sortSelect">Sort by:</label>
            <select id="sortSelect" onchange="sortProducts()">
                <option value="default">Default</option>
                <option value="date-desc">Most Recent Added</option>
                <option value="date-asc">Least Recent Added</option>
                <option value="name-asc">Name (A-Z)</option>
                <option value="name-desc">Name (Z-A)</option>
                <option value="price-asc">Price (Low to High)</option>
                <option value="price-desc">Price (High to Low)</option>
                <option value="quantity-asc">Quantity (Low to High)</option>
                <option value="quantity-desc">Quantity (High to Low)</option>
            </select>
        </div>
    </div>
<div class="product-container">
    <div class="grid" id="productsGrid">
<?php foreach ($products as $p): ?>
    <div class="card">
        <strong class="code-name">Code: <?php echo htmlspecialchars(
            $p["code"],
        ); ?></strong><br>
        <?php if (!empty($p["createdAt"])): ?>
            <small style="color: #888; font-size: 0.85em;">Added: <?php echo date(
                "M d, Y",
                strtotime($p["createdAt"]),
            ); ?></small><br>
        <?php endif; ?>
<div class="product-images">
    <?php if ($p["image1"]): ?>
        <img src="<?php echo htmlspecialchars($p["image1"]); ?>" alt="Image 1"
             data-code="<?php echo htmlspecialchars(
                 $p["code"],
             ); ?>" data-slot="1"
             onclick="openImageModal(this)">
    <?php else: ?>
        <img src="assets/placeholder.svg" alt="Placeholder Image"
             data-code="<?php echo htmlspecialchars(
                 $p["code"],
             ); ?>" data-slot="1"
             style="cursor: pointer;">
    <?php endif; ?>
    <?php if ($p["image2"]): ?>
        <img src="<?php echo htmlspecialchars($p["image2"]); ?>" alt="Image 2"
             data-code="<?php echo htmlspecialchars(
                 $p["code"],
             ); ?>" data-slot="2"
             onclick="openImageModal(this)">
    <?php else: ?>
        <img src="assets/placeholder.svg" alt="Placeholder Image"
             data-code="<?php echo htmlspecialchars(
                 $p["code"],
             ); ?>" data-slot="2"
             style="cursor: pointer;">
    <?php endif; ?>
</div>
        <h3><?php echo htmlspecialchars($p["name"]); ?></h3>
        <p class="price">₱<?php echo number_format($p["price"], 2); ?></p>
        <p>Stock: <?php echo htmlspecialchars($p["quantity"]); ?></p>
        <?php if (isset($p["soldQuantity"]) && $p["soldQuantity"] > 0): ?>
            <p style="color: #4caf50; font-size: 0.85em;">Sold: <?php echo htmlspecialchars(
                $p["soldQuantity"],
            ); ?> units</p>
        <?php endif; ?>
        <?php if (!empty($p["description"])): ?>
            <p class="description"><?php echo nl2br(
                htmlspecialchars($p["description"]),
            ); ?></p>
        <?php endif; ?>
        <div class="card-actions">
            <button onclick="openEditModal('<?php echo htmlspecialchars(
                $p["code"],
            ); ?>',
                                            '<?php echo htmlspecialchars(
                                                $p["name"],
                                            ); ?>',
                                            '<?php echo htmlspecialchars(
                                                $p["description"],
                                            ); ?>',
                                            '<?php echo htmlspecialchars(
                                                $p["price"],
                                            ); ?>',
                                            '<?php echo htmlspecialchars(
                                                $p["quantity"],
                                            ); ?>')"
                    class="edit">Edit</button>
            <button onclick="openDeleteModal('<?php echo htmlspecialchars(
                $p["code"],
            ); ?>')"
                    class="delete">Delete</button>
        </div>
    </div>
<?php endforeach; ?>
    </div>
</div>
    <div id="imageModal" class="modal">
              <div class="modal-content image-modal">
                  <span class="close" onclick="closeImageModal()">&times;</span>
                  <img id="fullImage" src="" alt="Full Image" style="display: none;">
                  <div class="image-actions" id="imageActions" style="display: none;">
                      <input type="file" id="replaceInput" accept="image/jpeg,image/png">
          <button class="replace-btn" onclick="document.getElementById('replaceInput').click();">
            Replace
          </button>
                      <button class="delete-btn" onclick="deleteImage()">Delete</button>
                  </div>
              </div>
          </div>
<div class="pagination-controls">
    <button class="nav-button prev-btn" id="prevBtn" disabled>&lt;</button>
    <span id="pageInfo">Page 1 of 1</span>
    <button class="nav-button next-btn" id="nextBtn" disabled>&gt;</button>
</div>
  <div class="actions">
        <a href="dashboard.php" class="back">Back to Dashboard</a>
        <a href="pos.php" class="back">Point of Sale</a>
        <a href="logout.php" class="logout">Logout</a>
    </div>
<div id="editModal" class="modal">
  <div class="modal-content">
    <h2>Edit Product</h2>
    <button type="button" class="close" onclick="closeEditModal()">&times;</button>
    <form method="post" enctype="multipart/form-data" id="editForm">
      <input type="hidden" name="original_code" id="original_code">

      <label>Code:</label>
      <input type="text" name="code" id="edit_code" required>

      <label>Name:</label>
      <input type="text" name="name" id="edit_name" required>

      <label>Description:</label>
      <textarea name="description" id="edit_description"></textarea>

      <div id="edit_price_wrapper">
          <label>Price:</label>
          <input type="number" step="1" name="price" id="edit_price" required min="0" placeholder="Php XXX">
      </div>

      <label>Quantity:</label>
      <input type="number" name="quantity" id="edit_quantity" required min="0">

      <label>Replace Image 1:</label>
      <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
        <input type="file" name="image1" id="edit_image1" accept="image/jpeg,image/png" onchange="previewEditImage(event, 'edit_preview1')" style="flex: 1;">
        <img id="edit_preview1" src="assets/placeholder.svg" alt="Preview" style="width: 60px; height: 60px; object-fit: cover; border-radius: 4px; border: 1px solid rgba(255,255,255,0.3); display: none;">
      </div>

      <label>Replace Image 2:</label>
      <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
        <input type="file" name="image2" id="edit_image2" accept="image/jpeg,image/png" onchange="previewEditImage(event, 'edit_preview2')" style="flex: 1;">
        <img id="edit_preview2" src="assets/placeholder.svg" alt="Preview" style="width: 60px; height: 60px; object-fit: cover; border-radius: 4px; border: 1px solid rgba(255,255,255,0.3); display: none;">
      </div>

      <button type="submit">Save</button>
      <button type="button" onclick="closeEditModal()">Cancel</button>
    </form>
  </div>
</div>

<script src="scripts/inventory.js" ></script>
<script src="scripts/vanilla-tilt.js"></script>


<div id="deleteModal" class="modal">
  <div class="modal-content delete-modal-content">
    <h2>Confirm Deletion</h2>
    <button type="button" class="close" onclick="closeDeleteModal()">&times;</button>
    <p>Are you sure you want to delete this product?</p>
    <div class="delete-actions">
      <a href="#" id="confirmDelete" class="confirm-delete">Delete</a>
      <button type="button" onclick="closeDeleteModal()">Cancel</button>
    </div>
  </div>
</div>

<script>
    VanillaTilt.init(document.querySelectorAll(".card"), {
        max: 5,
        speed: 400,
        glare: true,
        "max-glare": 0.2,
    });

    VanillaTilt.init(document.querySelectorAll(".modal-content"), {
        max: 3,
        speed: 400,
        glare: true,
        "max-glare": 0.1,
    });

    function previewEditImage(event, previewId) {
        const file = event.target.files[0];
        const preview = document.getElementById(previewId);

        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            preview.style.display = 'none';
            preview.src = '';
        }
    }

    // Reset previews when closing edit modal
    function closeEditModal() {
        document.getElementById('editModal').style.display = 'none';
        document.getElementById('edit_image1').value = '';
        document.getElementById('edit_image2').value = '';
        document.getElementById('edit_preview1').style.display = 'none';
        document.getElementById('edit_preview2').style.display = 'none';
        document.getElementById('edit_preview1').src = '';
        document.getElementById('edit_preview2').src = '';
    }
</script>
</body>
</html>
