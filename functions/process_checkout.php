<?php
session_start();
require_once __DIR__ . '/../admin/conf.php'; // uses $conn as PDO

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $mobile = trim($_POST['mobile'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $user_id = $_SESSION['user_id'] ?? null;  // <---- Add this line

    if (!$user_id) {
        die("User not logged in.");
    }

    $payment_method = $_POST['payment_method'] ?? 'Unknown';

    // Determine payment status based on method
    $payment_status = (strcasecmp($payment_method, 'Paypal') === 0) ? 'Paid' : 'Pending';

    try {
        $conn->beginTransaction();

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
            throw new Exception("Cart is empty.");
        }

        $totalAmount = 0;

        // Prepare sales insert statement
        $insert_sale = $conn->prepare("
            INSERT INTO sales 
            (user_id, product_id, quantity, total_price, sale_date, status, mobile, email)
            VALUES (:user_id, :product_id, :quantity, :total_price, NOW(), :status, :mobile, :email)
        ");

        // Insert each cart item as a sale, set status depending on payment method
        foreach ($cart_items as $item) {
            $product_id = $item['product_id'];
            $quantity = $item['quantity'];
            $price = $item['price'];
            $total_price = $price * $quantity;
            $totalAmount += $total_price;

            $insert_sale->execute([
                'user_id' => $user_id,
                'product_id' => $product_id,
                'quantity' => $quantity,
                'total_price' => $total_price,
                'status' => $payment_status,  // here is the key
                'mobile' => $mobile,
                'email' => $email
            ]);
        }

        // Get the last inserted sale_id (optional)
        $last_sale_id = $conn->lastInsertId();

        // Insert payment record
        $insert_payment = $conn->prepare("
            INSERT INTO payment 
            (sale_id, amount, payment_method, payment_status, payment_date)
            VALUES (:sale_id, :amount, :payment_method, :payment_status, NOW())
        ");
        $insert_payment->execute([
            'sale_id' => $last_sale_id ?: null,
            'amount' => $totalAmount,
            'payment_method' => $payment_method,
            'payment_status' => $payment_status
        ]);

        // Clear the cart
        $delete_cart = $conn->prepare("DELETE FROM cart WHERE user_id = :user_id");
        $delete_cart->execute(['user_id' => $user_id]);

        $conn->commit();

        header("Location: ../dashboard.php?checkout=success");
        exit;

    } catch (Exception $e) {
        $conn->rollBack();
        die("Checkout failed: " . $e->getMessage());
    }

} else {
    die("Invalid request");
}
