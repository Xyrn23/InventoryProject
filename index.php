<?php

session_start();
if (isset($_SESSION["user"])) {
    header("Location: dashboard.php");
    exit();
}

$pageTitle = "Login";
$pageCss = "login.css";
require_once 'components/header.php';
?>

<div class="main-content">
    <?php require_once 'features/authentication/login_form.php'; ?>
</div>

<?php require_once 'components/footer.php'; ?>
