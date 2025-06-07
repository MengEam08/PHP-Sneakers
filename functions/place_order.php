<?php
session_start();
require_once __DIR__ . '/../admin/conf.php'; 

// Check if required POST data is received
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user_id = $_SESSION['user_id'] ?? 1;
    $firstName = $_POST['first_name'] ?? '';
    $lastName = $_POST['last_name'] ?? '';
    $address = $_POST['address'] ?? '';
    $mobile = $_POST['mobile'] ?? '';
    $email = $_POST['email'] ?? '';
    $paymentMethod = $_POST['payment_method'] ?? '';
    $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
    $stmt->execute([$user_id]);
    header("Location: dashboard.php");
    exit;
} else {
    header("Location: checkout.php");
    exit;
}
