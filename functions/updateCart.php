<?php
session_start();
require_once __DIR__ . '/../admin/conf.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['cart_id'], $data['quantity'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

$cart_id = (int)$data['cart_id'];
$quantity = (int)$data['quantity'];

if ($quantity < 1) {
    echo json_encode(['success' => false, 'message' => 'Quantity must be at least 1']);
    exit;
}

$user_id = $_SESSION['user_id'] ?? 1;

// Check if this cart item belongs to this user
$stmt = $conn->prepare("SELECT product.price FROM cart JOIN product ON cart.product_id = product.id WHERE cart.id = ? AND cart.user_id = ?");
$stmt->execute([$cart_id, $user_id]);
$item = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$item) {
    echo json_encode(['success' => false, 'message' => 'Cart item not found']);
    exit;
}

// Update the quantity in the database
$updateStmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?");
$updateSuccess = $updateStmt->execute([$quantity, $cart_id, $user_id]);

if (!$updateSuccess) {
    echo json_encode(['success' => false, 'message' => 'Failed to update quantity']);
    exit;
}

// Calculate the new subtotal for this item
$item_subtotal = $item['price'] * $quantity;

// Calculate the cart subtotal and grand total (assuming shipping is fixed)
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
    'item_subtotal' => $item_subtotal,
    'cart_subtotal' => $cartSubtotal,
    'cart_total' => $cartTotal
]);
