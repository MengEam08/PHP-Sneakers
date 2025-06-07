<?php
session_start();
require_once __DIR__ . '/../admin/conf.php';

$user_id = $_SESSION['user_id'] ?? 1;

$stmt = $conn->prepare("SELECT SUM(quantity) AS cart_count FROM cart WHERE user_id = ?");
$stmt->execute([$user_id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$cart_count = (int)($row['cart_count'] ?? 0);

header('Content-Type: application/json');
echo json_encode(['cart_count' => $cart_count]);
