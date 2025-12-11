<?php
session_start();

// If already logged in, redirect based on role
if (isset($_SESSION["user"])) {
    if (isset($_SESSION["role"]) && $_SESSION["role"] === "admin") {
        header("Location: dashboard.php");
    } else {
        header("Location: pos.php");
    }
    exit();
}

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        $dbPath = __DIR__ . DIRECTORY_SEPARATOR . "inventory.db";
        $db = new PDO("sqlite:$dbPath");
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $username = trim($_POST["username"] ?? "");
        $password = $_POST["password"] ?? "";
        $confirmPassword = $_POST["confirm_password"] ?? "";

        // Validation
        if (empty($username) || empty($password) || empty($confirmPassword)) {
            $error = "All fields are required.";
        } elseif (strlen($username) < 3) {
            $error = "Username must be at least 3 characters long.";
        } elseif (strlen($password) < 6) {
            $error = "Password must be at least 6 characters long.";
        } elseif ($password !== $confirmPassword) {
            $error = "Passwords do not match.";
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
                // Hash password and insert new user as cashier
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $db->prepare(
                    "INSERT INTO users (username, password, role) VALUES (?, ?, ?)",
                );
                $stmt->execute([$username, $hashedPassword, "cashier"]);

                $success =
                    "Registration successful! You can now <a href='index.php' style='color: #67a6ff;'>login</a>.";
            }
        }
    } catch (PDOException $e) {
        $error = "Database error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/styles/login.css" />
    <link rel="icon" type="image/svg+xml" href="assets/logo.svg">
    <title>Register - POS System</title>
    <style>
        .error {
            background: rgba(244, 67, 54, 0.2);
            border: 1px solid rgba(244, 67, 54, 0.5);
            color: white;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            text-align: center;
            font-size: 0.9rem;
        }

        .success {
            background: rgba(76, 175, 80, 0.2);
            border: 1px solid rgba(76, 175, 80, 0.5);
            color: white;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            text-align: center;
            font-size: 0.9rem;
        }

        .password-requirements {
            margin-top: 5px;
            font-size: 0.75rem;
            color: rgba(255, 255, 255, 0.6);
        }

        .password-strength {
            height: 4px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 2px;
            margin-top: 8px;
            overflow: hidden;
            display: none;
        }

        .password-strength-bar {
            height: 100%;
            transition: all 0.3s ease;
            border-radius: 2px;
        }

        .strength-weak {
            width: 33%;
            background: #f44336;
        }

        .strength-medium {
            width: 66%;
            background: #ff9800;
        }

        .strength-strong {
            width: 100%;
            background: #4caf50;
        }

        #matchMessage {
            margin-top: 5px;
            font-size: 0.75rem;
        }

        .__logo {
            height: 200px;
            width: 200px;
            position: absolute;
            top: -6rem;
            /*right: 1rem;*/
            left: 15rem;
        }

        form {
            width: 340px;
        }
    </style>
</head>
<body>
    <form method="POST" action="" data-tilt data-tilt-glare>
        <img src="assets/logo.svg" class="__logo" alt="Logo">

        <h2 class="login__title">Create Account</h2>

        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="success"><?php echo $success; ?></div>
        <?php else: ?>
            <div class="login__field">
                <input
                    type="text"
                    id="username"
                    name="username"
                    class="login__input"
                    placeholder=" "
                    value="<?php echo htmlspecialchars(
                        $_POST["username"] ?? "",
                    ); ?>"
                    required
                    minlength="3"
                    autocomplete="username"
                >
                <label for="username" class="login__label">Username</label>
            </div>

            <div class="login__field">
                <input
                    type="password"
                    id="password"
                    name="password"
                    class="login__input"
                    placeholder=" "
                    required
                    minlength="6"
                    autocomplete="new-password"
                    onkeyup="checkPasswordStrength()"
                >
                <label for="password" class="login__label">Password</label>
                <div class="password-strength" id="passwordStrength">
                    <div class="password-strength-bar" id="strengthBar"></div>
                </div>
                <div class="password-requirements">
                    Minimum 6 characters required
                </div>
            </div>

            <div class="login__field">
                <input
                    type="password"
                    id="confirm_password"
                    name="confirm_password"
                    class="login__input"
                    placeholder=" "
                    required
                    autocomplete="new-password"
                    onkeyup="checkPasswordMatch()"
                >
                <label for="confirm_password" class="login__label">Confirm Password</label>
                <div id="matchMessage" class="password-requirements"></div>
            </div>

            <button type="submit">Register</button>

            <div style="text-align: center; margin-top: 20px;">
                <span style="color: rgba(255, 255, 255, 0.7);">Already have an account?</span>
                <br>
                <a href="index.php" style="color: #67a6ff; text-decoration: none; font-weight: 500;">Login here</a>
            </div>
        <?php endif; ?>
    </form>

    <script>
        function checkPasswordStrength() {
            const password = document.getElementById('password').value;
            const strengthDiv = document.getElementById('passwordStrength');
            const strengthBar = document.getElementById('strengthBar');

            if (password.length === 0) {
                strengthDiv.style.display = 'none';
                return;
            }

            strengthDiv.style.display = 'block';

            // Simple strength calculation
            let strength = 0;
            if (password.length >= 6) strength++;
            if (password.length >= 10) strength++;
            if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^a-zA-Z0-9]/.test(password)) strength++;

            strengthBar.className = 'password-strength-bar';

            if (strength <= 2) {
                strengthBar.classList.add('strength-weak');
            } else if (strength <= 3) {
                strengthBar.classList.add('strength-medium');
            } else {
                strengthBar.classList.add('strength-strong');
            }
        }

        function checkPasswordMatch() {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const matchMessage = document.getElementById('matchMessage');

            if (confirmPassword.length === 0) {
                matchMessage.textContent = '';
                return;
            }

            if (password === confirmPassword) {
                matchMessage.style.color = '#4caf50';
                matchMessage.textContent = '✓ Passwords match';
            } else {
                matchMessage.style.color = '#f44336';
                matchMessage.textContent = '✗ Passwords do not match';
            }
        }
    </script>
    <script type="text/javascript" src="scripts/vanilla-tilt.js"></script>
</body>
</html>
