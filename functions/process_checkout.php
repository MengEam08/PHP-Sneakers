<?php
session_start();
require_once __DIR__ . '/../admin/conf.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $mobile = trim($_POST['mobile']);
    $email = trim($_POST['email']);
    $user_id = $_SESSION['user_id'] ?? null;
    $status = "Pending";

    if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
        die('Cart is empty.');
    }

    $totalAmount = 0;

    /** @var mysqli_stmt $stmt */
    $stmt = $conn->prepare("INSERT INTO sales 
        (user_id, product_id, quantity, total_price, sale_date, status, mobile, email, total_amount)
        VALUES (?, ?, ?, ?, NOW(), ?, ?, ?, ?)");

    foreach ($_SESSION['cart'] as $item) {
        $product_id = $item['product_id'];
        $quantity = $item['quantity'];
        $price = $item['price'];
        $total_price = $price * $quantity;
        $totalAmount += $total_price;

        $stmt->bind_param("iiidssssd", $user_id, $product_id, $quantity, $total_price, $status, $mobile, $email, $totalAmount);

        if (!$stmt->execute()) {
            die("Insert failed: " . $stmt->error);
        }
    }

    $stmt->close();
    unset($_SESSION['cart']);
    header("Location: thank_you.php");
    exit;

} else {
    die("Invalid request");
}
