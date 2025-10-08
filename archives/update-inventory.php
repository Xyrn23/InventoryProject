<?php

$dbPath = __DIR__ . DIRECTORY_SEPARATOR . "inventory.db";

try {
    $db = new PDO("sqlite:" . $dbPath);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

$db->exec("DROP TABLE IF EXISTS products");

$db->exec("CREATE TABLE products (
    code TEXT PRIMARY KEY,
    name TEXT NOT NULL,
    description TEXT,
    price REAL NOT NULL,
    quantity INTEGER NOT NULL,
    image1 TEXT,
    image2 TEXT
)");

$filename = "products_enhanced.csv";
if (($handle = fopen($filename, "r")) !== false) {
    $header = fgetcsv($handle);

    $insertStmt = $db->prepare(
        "INSERT INTO products (code, name, description, price, quantity, image1, image2) VALUES (?, ?, ?, ?, ?, ?, ?)",
    );
    $insertedCount = 0;
    $errorCount = 0;

    while (($data = fgetcsv($handle)) !== false) {
        if (count($data) < 7) {
            $errorCount++;
            continue;
        }

        $code = trim($data[0]);
        $name = trim($data[1]);
        $description = trim($data[2]);
        $price = (float) $data[3];
        $quantity = (int) $data[4];
        $image1 = trim($data[5]);
        $image2 = trim($data[6]);

        try {
            $insertStmt->execute([
                $code,
                $name,
                $description,
                $price,
                $quantity,
                $image1,
                $image2,
            ]);
            $insertedCount++;
        } catch (PDOException $e) {
            $errorCount++;
            echo "<p>Error inserting $code: " . $e->getMessage() . "</p>";
        }
    }

    fclose($handle);

    echo "<h2>Reset Complete!</h2>";
    echo "<p>Table dropped and recreated successfully.</p>";
    echo "<p>Inserted: $insertedCount products</p>";
    echo "<p>Errors: $errorCount (check CSV if >0)</p>";
    echo "<p><a href='inventory.php'>Go to Inventory</a> to see the new realistic products.</p>";
    echo "<p><em>Delete this file and products_enhanced.csv after verifying.</em></p>";

    $preview = $db
        ->query("SELECT * FROM products ORDER BY code ASC LIMIT 5")
        ->fetchAll(PDO::FETCH_ASSOC);
    echo "<h3>Preview (first 5 products):</h3><pre>";
    print_r($preview);
    echo "</pre>";
} else {
    die(
        "Failed to read $filename. Ensure it's in the project root and properly formatted."
    );
}
