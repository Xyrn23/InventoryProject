<?php
// Migration script to add createdAt column and generate random dates for existing products
try {
    $dbPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . "inventory.db";
    $db = new PDO("sqlite:" . $dbPath);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Connected to database successfully.\n";

    // Check if createdAt column already exists
    $stmt = $db->query("PRAGMA table_info(products)");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $hasCreatedAt = false;

    foreach ($columns as $column) {
        if ($column["name"] === "createdAt") {
            $hasCreatedAt = true;
            break;
        }
    }

    if (!$hasCreatedAt) {
        // Add createdAt column
        $db->exec("ALTER TABLE products ADD COLUMN createdAt DATETIME");
        echo "Added createdAt column to products table.\n";

        // Get all existing products
        $products = $db
            ->query("SELECT code FROM products")
            ->fetchAll(PDO::FETCH_ASSOC);

        if (count($products) > 0) {
            echo "Found " . count($products) . " existing products.\n";

            // Generate random dates for existing products
            // Dates will be randomly distributed over the past 2 years
            $currentTime = time();
            $twoYearsAgo = $currentTime - 2 * 365 * 24 * 60 * 60; // 2 years in seconds

            $updateStmt = $db->prepare(
                "UPDATE products SET createdAt = ? WHERE code = ?",
            );

            foreach ($products as $index => $product) {
                // Generate a random timestamp between 2 years ago and now
                // Products are distributed somewhat evenly but with some clustering
                $randomTime = rand($twoYearsAgo, $currentTime);

                // Add some clustering - 30% chance to be within last 30 days
                if (rand(1, 100) <= 30) {
                    $thirtyDaysAgo = $currentTime - 30 * 24 * 60 * 60;
                    $randomTime = rand($thirtyDaysAgo, $currentTime);
                }

                $dateTime = date("Y-m-d H:i:s", $randomTime);
                // Format as datetime string
                $updateStmt->execute([$dateTime, $product["code"]]);
                echo "Updated product {$product["code"]} with date: {$dateTime}\n";
            }

            echo "Successfully updated all existing products with random dates.\n";
        } else {
            echo "No existing products found.\n";
        }
    } else {
        echo "createdAt column already exists. Checking for products without dates...\n";

        // Update any products that don't have a createdAt date
        $productsWithoutDate = $db
            ->query("SELECT code FROM products WHERE createdAt IS NULL")
            ->fetchAll(PDO::FETCH_ASSOC);

        if (count($productsWithoutDate) > 0) {
            echo "Found " .
                count($productsWithoutDate) .
                " products without dates.\n";

            $currentTime = time();
            $twoYearsAgo = $currentTime - 2 * 365 * 24 * 60 * 60;

            $updateStmt = $db->prepare(
                "UPDATE products SET createdAt = ? WHERE code = ?",
            );

            foreach ($productsWithoutDate as $product) {
                $randomTime = rand($twoYearsAgo, $currentTime);

                // Add some clustering - 30% chance to be within last 30 days
                if (rand(1, 100) <= 30) {
                    $thirtyDaysAgo = $currentTime - 30 * 24 * 60 * 60;
                    $randomTime = rand($thirtyDaysAgo, $currentTime);
                }

                $dateTime = date("Y-m-d H:i:s", $randomTime);
                $updateStmt->execute([$dateTime, $product["code"]]);
                echo "Updated product {$product["code"]} with date: {$dateTime}\n";
            }

            echo "Successfully updated products without dates.\n";
        } else {
            echo "All products already have dates.\n";
        }
    }

    // Also add soldQuantity column for POS functionality
    $stmt = $db->query("PRAGMA table_info(products)");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $hasSoldQuantity = false;

    foreach ($columns as $column) {
        if ($column["name"] === "soldQuantity") {
            $hasSoldQuantity = true;
            break;
        }
    }

    if (!$hasSoldQuantity) {
        $db->exec(
            "ALTER TABLE products ADD COLUMN soldQuantity INTEGER DEFAULT 0",
        );
        echo "Added soldQuantity column to products table.\n";
    }

    echo "\nMigration completed successfully!\n";
} catch (PDOException $e) {
    die("Migration failed: " . $e->getMessage() . "\n");
}
