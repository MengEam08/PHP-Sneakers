<?php
session_start();
require_once __DIR__ . '/../admin/conf.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['cart_id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

$cart_id = (int)$data['cart_id'];
$user_id = $_SESSION['user_id'] ?? 1;

// Delete the item from the cart
$deleteStmt = $conn->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
$deleteSuccess = $deleteStmt->execute([$cart_id, $user_id]);

if (!$deleteSuccess) {
    echo json_encode(['success' => false, 'message' => 'Failed to delete item']);
    exit;
}

// Check if cart is empty
$checkStmt = $conn->prepare("SELECT COUNT(*) FROM cart WHERE user_id = ?");
$checkStmt->execute([$user_id]);
$count = (int)$checkStmt->fetchColumn();

if ($count === 0) {
    echo json_encode(['success' => true, 'cart_empty' => true]);
    exit;
}

// Calculate updated totals
$cartStmt = $conn->prepare("
    SELECT SUM(product.price * cart.quantity) AS subtotal 
    FROM cart JOIN product ON cart.product_id = product.id 
    WHERE cart.user_id = ?
");
$cartStmt->execute([$user_id]);
$cartSubtotal = (float) $cartStmt->fetchColumn();

$shipping = 3.00;
$cartTotal = $cartSubtotal + $shipping;

echo json_encode([
    'success' => true,
    'cart_empty' => false,
    'cart_subtotal' => $cartSubtotal,
    'cart_total' => $cartTotal
]);
