<?php

ini_set("display_errors", "1");
ini_set("display_startup_errors", "1");
error_reporting(E_ALL);
session_start();

require_once '../../lib/database.php';

$db = getDatabaseConnection();

$username = $_POST["username"] ?? "";
$password = $_POST["password"] ?? "";

$stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
$stmt->execute([$username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && password_verify($password, $user["password"])) {
    $_SESSION["user"] = $user["username"];
    $_SESSION["role"] = $user["role"] ?? "admin";

    if ($_SESSION["role"] === "admin") {
        header("Location: ../../dashboard.php");
    } else {
        header("Location: ../../pos.php");
    }
    exit();
} else {
    echo "Invalid credentials. <a href='../../index.php'>Try again</a>";
}
