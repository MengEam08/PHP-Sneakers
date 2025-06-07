<?php
session_start();
require_once __DIR__ . '/../admin/conf.php'; // uses $conn as PDO

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mobile = trim($_POST['mobile']);
    $email = trim($_POST['email']);
    $user_id = $_SESSION['user_id'] ?? null;
    $status = "Pending";

    if (!$user_id) {
        die("User not logged in.");
    }

    // Get cart items for this user
    $cart_stmt = $conn->prepare("
        SELECT c.product_id, c.quantity, p.price
        FROM cart c
        JOIN product p ON c.product_id = p.id
        WHERE c.user_id = :user_id
    ");
    $cart_stmt->execute(['user_id' => $user_id]);
    $cart_items = $cart_stmt->fetchAll();

    if (empty($cart_items)) {
        die("Cart is empty.");
    }

    $totalAmount = 0;

    // Prepare the insert into sales
    $insert_stmt = $conn->prepare("
        INSERT INTO sales 
        (user_id, product_id, quantity, total_price, sale_date, status, mobile, email, total_amount)
        VALUES (:user_id, :product_id, :quantity, :total_price, NOW(), :status, :mobile, :email, :total_amount)
    ");

    foreach ($cart_items as $item) {
        $product_id = $item['product_id'];
        $quantity = $item['quantity'];
        $price = $item['price'];
        $total_price = $price * $quantity;
        $totalAmount += $total_price;

        $insert_stmt->execute([
            'user_id' => $user_id,
            'product_id' => $product_id,
            'quantity' => $quantity,
            'total_price' => $total_price,
            'status' => $status,
            'mobile' => $mobile,
            'email' => $email,
            'total_amount' => $totalAmount
        ]);
    }

    // Optional: clear cart
    $delete_cart = $conn->prepare("DELETE FROM cart WHERE user_id = :user_id");
    $delete_cart->execute(['user_id' => $user_id]);

    // Redirect after success
    header("Location: ../dashboard.php");
    exit;

} else {
    die("Invalid request");
}
