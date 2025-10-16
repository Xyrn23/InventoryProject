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
    $db = new PDO("sqlite:$dbPath");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

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

function generateProductCode($db, $name)
{
    $prefix = strtoupper(substr(preg_replace("/[^A-Za-z]/", "", $name), 0, 3));
    if ($prefix === "") {
        $prefix = "PRD";
    }

    $stmt = $db->prepare(
        "SELECT code FROM products WHERE code LIKE ? ORDER BY code DESC LIMIT 1",
    );
    $stmt->execute(["{$prefix}-%"]);
    $lastCode = $stmt->fetchColumn();

    if ($lastCode) {
        $lastNumber = (int) substr($lastCode, 4);
        $newNumber = str_pad($lastNumber + 1, 6, "0", STR_PAD_LEFT);
    } else {
        $newNumber = "000001";
    }

    return "{$prefix}-{$newNumber}";
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST["name"] ?? "";
    $description = $_POST["description"] ?? "";
    $price = $_POST["price"] ?? 1;
    $quantity = $_POST["quantity"] ?? 1;

    if ((float) $price < 1 || (int) $quantity < 1) {
        $_SESSION["error"] =
            "Error: Price and quantity cannot be negative or zero.";
        header("Location: dashboard.php");
        exit();
    }

    $errors = [];

    $uploadDir = "uploads/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $allowedExts = ["jpg", "jpeg", "png"];
    $maxSize = 2 * 1024 * 1024;
    $imagePaths = [null, null];

    for ($i = 1; $i <= 2; $i++) {
        if (
            isset($_FILES["image$i"]) &&
            $_FILES["image$i"]["error"] === UPLOAD_ERR_OK
        ) {
            $fileTmp = $_FILES["image$i"]["tmp_name"];
            $fileName = basename($_FILES["image$i"]["name"]);
            $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $fileSize = $_FILES["image$i"]["size"];

            if (!in_array($ext, $allowedExts)) {
                $errors[] = "Image $i must be a JPEG or PNG.";
                continue;
            }
            if ($fileSize > $maxSize) {
                $errors[] = "Image $i exceeds the 2MB size limit.";
                continue;
            }

            $newName = uniqid("img{$i}_") . ".jpg";
            $destPath = "{$uploadDir}{$newName}";

            if (move_uploaded_file($fileTmp, $destPath)) {
                compressImage($destPath, $destPath);
                $imagePaths[$i - 1] = $destPath;
            } else {
                $errors[] = "Failed to upload Image $i.";
            }
        }
    }

    if (!empty($errors)) {
        $_SESSION["error"] = implode("<br>", $errors);
        header("Location: dashboard.php");
        exit();
    }

    $code = generateProductCode($db, $name);

    $stmt = $db->prepare("INSERT INTO products (code, name, description, price, quantity, image1, image2, createdAt)
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $code,
        $name,
        $description,
        $price,
        $quantity,
        $imagePaths[0],
        $imagePaths[1],
        date("Y-m-d H:i:s"),
    ]);

    $_SESSION["success"] = "Product <b>$name</b> added successfully!";
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<?php if (!empty($_SESSION["success"])): ?>
    <div class="notification"><?php echo $_SESSION["success"]; ?></div>
    <?php unset($_SESSION["success"]); ?>
<?php endif; ?>

<?php if (!empty($_SESSION["error"])): ?>
    <div class="notification error"><?php echo $_SESSION["error"]; ?></div>
    <?php unset($_SESSION["error"]); ?>
<?php endif; ?>

<script>
    setTimeout(() => {
        const notifications = document.querySelectorAll('.notification');
        notifications.forEach(el => el.style.display = 'none');
    }, 4000);
</script>


<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Dashboard - Add Products</title>
    <link rel="stylesheet" href="styles/dashboard.css">
    <link rel="icon" type="image/svg+xml" href="assets/logo.svg">

</head>

<body>

    <div class="wrapper">
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION["user"]); ?>!</h1>
        <img src="assets/logo.svg" class="__logo">
        <h3>Add New Product</h3>
        <form method="post" enctype="multipart/form-data">
            <div class="__product_name">
                <label for="name">Product Name</label>
                <input type="text" id="name" name="name" placeholder="Enter product name." required>
            </div>
            <div class="__description-field">
                <label for="description">Description</label>
                <textarea id="description" name="description" placeholder="Describe your product..."></textarea>
            </div>
            <div class="__product_price">
                <label for="price">Price</label>
                <input type="number" id="price" step="0.01" name="price" placeholder="₱xxx" required min="0">
            </div>
            <div class="__product_quantity">
                <label for="quantity">Quantity</label>
                <input type="number" id="quantity" name="quantity" placeholder="000" required min="0">
            </div>
            <div class="__product_image_1">
                <label for="image1" class="__image_label">
                    <div class="__image_wrapper">
                        <img src="/assets/placeholder.svg" alt="Upload Image 1" class="__upload_icon" id="preview1" />
                        <span class="__image_text">Image 1</span>
                    </div>
                </label>
                <input type="file" id="image1" name="image1" class="__image_upload" accept="image/jpeg,image/png"
                    hidden>
            </div>

            <div class="__product_image_2">
                <label for="image2" class="__image_label">
                    <div class="__image_wrapper">
                        <img src="/assets/placeholder.svg" alt="Upload Image 2" class="__upload_icon" id="preview2" />
                        <span class="__image_text">Image 2</span>
                    </div>
                </label>
                <input type="file" id="image2" name="image2" class="__image_upload" accept="image/jpeg,image/png"
                    hidden>
            </div>



            <button type="submit" class="__add_product_btn">Add Product</button>
        </form>

        <div class="actions">
            <a href="inventory.php" class="inventory">Show Inventory</a>
            <a href="pos.php" class="inventory">Point of Sale</a>
            <a href="report.php" class="inventory">Sales Report</a>
            <a href="admin_manage.php" class="inventory">User Management</a>
            <a href="logout.php" class="logout">Logout</a>
        </div>
        <script src="scripts/vanilla-tilt.js"></script>
    </div>
<script src="scripts/dashboard.js"></script>
</body>

</html>
