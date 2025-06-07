<?php
session_start();
require_once __DIR__ . '/../admin/conf.php';

$user_id = $_SESSION['user_id'] ?? 1;

if (!isset($_POST['product_id']) || !is_numeric($_POST['product_id'])) {
    http_response_code(400);
    echo 'Invalid product ID.';
    exit;
}

$product_id = (int)$_POST['product_id'];
$quantity = 1;

$stmt = $conn->prepare("SELECT * FROM cart WHERE user_id = ? AND product_id = ?");
$stmt->execute([$user_id, $product_id]);
$existing = $stmt->fetch();

if ($existing) {
    $stmt = $conn->prepare("UPDATE cart SET quantity = quantity + ? WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$quantity, $user_id, $product_id]);
} else {
    $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
    $stmt->execute([$user_id, $product_id, $quantity]);
}

echo 'success';
