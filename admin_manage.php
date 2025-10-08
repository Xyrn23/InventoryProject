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

$error = "";
$success = "";

// Handle form submissions
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["action"])) {
        // Register new admin
        if ($_POST["action"] === "register") {
            $username = trim($_POST["username"] ?? "");
            $password = $_POST["password"] ?? "";
            $role = $_POST["role"] ?? "cashier";

            if (empty($username) || empty($password)) {
                $error = "Username and password are required.";
            } elseif (strlen($username) < 3) {
                $error = "Username must be at least 3 characters long.";
            } elseif (strlen($password) < 6) {
                $error = "Password must be at least 6 characters long.";
            } else {
                // Check if username already exists
                $stmt = $db->prepare(
                    "SELECT COUNT(*) FROM users WHERE username = ?",
                );
                $stmt->execute([$username]);
                $exists = $stmt->fetchColumn();

                if ($exists > 0) {
                    $error = "Username already exists.";
                } else {
                    // Hash password and insert new user
                    $hashedPassword = password_hash(
                        $password,
                        PASSWORD_DEFAULT,
                    );
                    $stmt = $db->prepare(
                        "INSERT INTO users (username, password, role) VALUES (?, ?, ?)",
                    );
                    $stmt->execute([$username, $hashedPassword, $role]);

                    $success = "User <b>{$username}</b> registered successfully as {$role}!";
                }
            }
        }

        // Update user role
        elseif ($_POST["action"] === "update_role") {
            $userId = $_POST["user_id"];
            $newRole = $_POST["new_role"];

            // Prevent changing own role
            $stmt = $db->prepare("SELECT username FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $targetUser = $stmt->fetchColumn();

            if ($targetUser === $_SESSION["user"]) {
                $error = "You cannot change your own role.";
            } else {
                $stmt = $db->prepare("UPDATE users SET role = ? WHERE id = ?");
                $stmt->execute([$newRole, $userId]);
                $success = "User role updated successfully!";
            }
        }

        // Delete user
        elseif ($_POST["action"] === "delete") {
            $userId = $_POST["user_id"];

            // Prevent deleting own account
            $stmt = $db->prepare("SELECT username FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $targetUser = $stmt->fetchColumn();

            if ($targetUser === $_SESSION["user"]) {
                $error = "You cannot delete your own account.";
            } else {
                $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
                $stmt->execute([$userId]);
                $success = "User deleted successfully!";
            }
        }
    }
}

// Get all users
$users = $db
    ->query("SELECT id, username, role FROM users ORDER BY role, username")
    ->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <link rel="icon" type="image/svg+xml" href="assets/logo.svg">
    <link rel="stylesheet" href="styles/admin_manage.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="assets/logo.svg" alt="Logo" class="logo">
            <h1>User Management</h1>
            <p>Manage system users and permissions</p>
        </div>

        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="success"><?php echo $success; ?></div>
        <?php endif; ?>

        <div class="content-wrapper">
            <div class="card">
                <h2>Register New User</h2>
                <form method="POST" action="">
                    <input type="hidden" name="action" value="register">

                    <div class="form-group">
                        <label for="username">Username</label>
                        <input
                            type="text"
                            id="username"
                            name="username"
                            placeholder="Enter username"
                            required
                            minlength="3"
                        >
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Enter password"
                            required
                            minlength="6"
                        >
                    </div>

                    <div class="form-group">
                        <label for="role">Role</label>
                        <select id="role" name="role" required>
                            <option value="cashier">Cashier (POS Only)</option>
                            <option value="admin">Administrator (Full Access)</option>
                        </select>
                    </div>

                    <button type="submit">Register User</button>
                </form>
            </div>

            <div class="card">
                <h2>Existing Users</h2>
                <table class="users-table">
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Role</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td>
                                    <?php echo htmlspecialchars(
                                        $user["username"],
                                    ); ?>
                                    <?php if (
                                        $user["username"] === $_SESSION["user"]
                                    ): ?>
                                        <span class="badge current-user">You</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge badge-<?php echo $user[
                                        "role"
                                    ]; ?>">
                                        <?php echo ucfirst($user["role"]); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="actions">
                                        <?php if (
                                            $user["username"] !==
                                            $_SESSION["user"]
                                        ): ?>
                                            <form method="POST" class="action-form">
                                                <input type="hidden" name="action" value="update_role">
                                                <input type="hidden" name="user_id" value="<?php echo $user[
                                                    "id"
                                                ]; ?>">
                                                <select name="new_role" onchange="this.form.submit()" style="width: auto; padding: 5px;">
                                                    <option value="">Change Role</option>
                                                    <option value="admin" <?php echo $user[
                                                        "role"
                                                    ] === "admin"
                                                        ? "disabled"
                                                        : ""; ?>>Admin</option>
                                                    <option value="cashier" <?php echo $user[
                                                        "role"
                                                    ] === "cashier"
                                                        ? "disabled"
                                                        : ""; ?>>Cashier</option>
                                                </select>
                                            </form>

                                            <form method="POST" class="action-form" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="user_id" value="<?php echo $user[
                                                    "id"
                                                ]; ?>">
                                                <button type="submit" class="btn-danger">Delete</button>
                                            </form>
                                        <?php else: ?>
                                            <span style="color: rgba(255, 255, 255, 0.5);">-</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="nav-links">
            <a href="dashboard.php">Dashboard</a>
            <a href="inventory.php">Inventory</a>
            <a href="pos.php">Point of Sale</a>
            <a href="logout.php" style="background: #ff0000;">Logout</a>
        </div>
    </div>

    <script src="scripts/vanilla-tilt.js"></script>
    <script>
        VanillaTilt.init(document.querySelectorAll(".card"), {
            max: 5,
            speed: 400,
            glare: true,
            "max-glare": 0.1,
        });
    </script>
</body>
</html>
