<?php
// Migration script to add role column to users table

try {
    $dbPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . "inventory.db";
    $db = new PDO("sqlite:" . $dbPath);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Connected to database successfully.\n";

    // Check if role column already exists
    $stmt = $db->query("PRAGMA table_info(users)");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $hasRole = false;

    foreach ($columns as $column) {
        if ($column['name'] === 'role') {
            $hasRole = true;
            break;
        }
    }

    if (!$hasRole) {
        // Add role column with default value 'admin'
        $db->exec("ALTER TABLE users ADD COLUMN role TEXT DEFAULT 'admin'");
        echo "Added role column to users table.\n";

        // Update existing users to have admin role explicitly
        $db->exec("UPDATE users SET role = 'admin' WHERE role IS NULL");
        echo "Set existing users to admin role.\n";
    } else {
        echo "Role column already exists.\n";
    }

    // Check if we need to create a sample cashier user
    $stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
    $stmt->execute(['cashier']);
    $cashierExists = $stmt->fetchColumn() > 0;

    if (!$cashierExists) {
        // Create a sample cashier user (password: cashier123)
        $hashedPassword = password_hash('cashier123', PASSWORD_DEFAULT);

        $stmt = $db->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        $stmt->execute(['cashier', $hashedPassword, 'cashier']);

        echo "Created sample cashier user:\n";
        echo "  Username: cashier\n";
        echo "  Password: cashier123\n";
        echo "  Role: cashier\n";
    } else {
        echo "Cashier user already exists.\n";
    }

    // Display all users and their roles
    echo "\nCurrent users in the system:\n";
    echo "-----------------------------\n";

    $users = $db->query("SELECT username, role FROM users")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($users as $user) {
        echo "  Username: {$user['username']}, Role: {$user['role']}\n";
    }

    echo "\nMigration completed successfully!\n";

} catch (PDOException $e) {
    die("Migration failed: " . $e->getMessage() . "\n");
}
