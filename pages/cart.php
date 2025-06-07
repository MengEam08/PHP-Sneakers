<?php
session_start();
require_once __DIR__ . '/../admin/conf.php';

$user_id = $_SESSION['user_id'] ?? 1;

$stmt = $conn->prepare("
    SELECT cart.id, product.name, product.image, product.price, cart.quantity 
    FROM cart 
    JOIN product ON cart.product_id = product.id 
    WHERE cart.user_id = ?
");
$stmt->execute([$user_id]);
$cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total = 0;
foreach ($cartItems as $item) {
    $total += $item['price'] * $item['quantity'];
}

$shipping = 3.00; // Example flat shipping rate
$grandTotal = $total + $shipping;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Your Cart</title>
    <link rel="stylesheet" href="your-bootstrap.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        .quantity input {
            width: 60px;
        }
        .quantity .btn {
            padding: 0.25rem 0.5rem;
        }
        .table td, .table th {
            vertical-align: middle;
        }
        #toast {
        position: fixed;
        top: 20px;
        right: 20px;
        background-color: #81C408;
        color: white;
        padding: 15px 25px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        opacity: 0;
        pointer-events: none;
        transform: translateY(-20px);
        transition: opacity 0.3s ease, transform 0.3s ease;
        font-family: Arial, sans-serif;
        font-weight: 600;
        z-index: 9999;
        min-width: 250px;
        max-width: 300px;
    }
    #toast.show {
        opacity: 1;
        pointer-events: auto;
        transform: translateY(0);
    }
    </style>
</head>
<body>
    <div class="container-fluid py-5">
        <div class="container py-5">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                      <tr>
                        <th scope="col">Products</th>
                        <th scope="col">Name</th>
                        <th scope="col">Price</th>
                        <th scope="col">Quantity</th>
                        <th scope="col">Total</th>
                        <th scope="col">Handle</th>
                      </tr>
                    </thead>
                    <tbody>
                        <?php if (count($cartItems) > 0): ?>
                            <?php foreach ($cartItems as $item): ?>
                            <tr data-cart-id="<?= $item['id'] ?>">
                                <th scope="row">
                                    <div class="d-flex align-items-center">
                                        <img src="admin/uploaded_img/<?= htmlspecialchars($item['image']) ?>" 
                                             class="img-fluid me-5 rounded-circle" 
                                             style="width: 80px; height: 80px;" 
                                             alt="<?= htmlspecialchars($item['name']) ?>">
                                    </div>
                                </th>
                                <td>
                                    <p class="mb-0 mt-4"><?= htmlspecialchars($item['name']) ?></p>
                                </td>
                                <td>
                                    <p class="mb-0 mt-4">$<?= number_format($item['price'], 2) ?></p>
                                </td>
                                <td>
                                    <div class="input-group quantity mt-4" style="width: 120px;">
                                        <button class="btn btn-sm btn-minus rounded-circle bg-light border" type="button" onclick="changeQuantity(<?= $item['id'] ?>, -1)">
                                            <i class="fa fa-minus"></i>
                                        </button>
                                        <input type="text" class="form-control form-control-sm text-center border-0" 
                                               id="quantity-<?= $item['id'] ?>" 
                                               value="<?= $item['quantity'] ?>" 
                                               readonly>
                                        <button class="btn btn-sm btn-plus rounded-circle bg-light border" type="button" onclick="changeQuantity(<?= $item['id'] ?>, 1)">
                                            <i class="fa fa-plus"></i>
                                        </button>
                                    </div>
                                </td>
                                <td>
                                    <p class="mb-0 mt-4" id="subtotal-<?= $item['id'] ?>">
                                        $<?= number_format($item['price'] * $item['quantity'], 2) ?>
                                    </p>
                                </td>
                                <td>
                                    <button class="btn btn-md rounded-circle bg-light border mt-4" type="button" onclick="removeItem(<?= $item['id'] ?>)">
                                        <i class="fa fa-times text-danger"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center">Your cart is empty.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if (count($cartItems) > 0): ?>
            <div class="row g-4 justify-content-end">
                <div class="col-8"></div>
                <div class="col-sm-8 col-md-7 col-lg-6 col-xl-4">
                    <div class="bg-light rounded">
                        <div class="p-4">
                            <h1 class="display-6 mb-4">Cart <span class="fw-normal">Total</span></h1>
                            <div class="d-flex justify-content-between mb-4">
                                <h5 class="mb-0 me-4">Subtotal:</h5>
                                <p class="mb-0" id="cart-subtotal">$<?= number_format($total, 2) ?></p>
                            </div>
                            <div class="d-flex justify-content-between">
                                <h5 class="mb-0 me-4">Shipping</h5>
                                <div>
                                    <p class="mb-0">Flat rate: $<?= number_format($shipping, 2) ?></p>
                                </div>
                            </div>
                           
                        </div>
                        <div class="py-4 mb-4 border-top border-bottom d-flex justify-content-between">
                            <h5 class="mb-0 ps-4 me-4">Total</h5>
                            <p class="mb-0 pe-4" id="cart-total">$<?= number_format($grandTotal, 2) ?></p>
                        </div>
                        <a href="index.php?p=checkout" class="btn border-secondary rounded-pill px-4 py-3 text-primary text-uppercase mb-4 ms-4" type="button">Proceed Checkout</a>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <!-- Toast container -->
    <div id="toast"></div>


    <script>
        function changeQuantity(cartId, delta) {
            const qtyInput = document.getElementById('quantity-' + cartId);
            let quantity = parseInt(qtyInput.value);
            quantity += delta;
            if (quantity < 1) return;

            // Update quantity input immediately for user feedback
            qtyInput.value = quantity;

            // Send ajax request to update quantity in cart (create updateCart.php accordingly)
            fetch('/PHP-Sneakers/functions/updateCart.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ cart_id: cartId, quantity: quantity })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // Update subtotal and totals
                    document.getElementById('subtotal-' + cartId).textContent = '$' + (data.item_subtotal).toFixed(2);
                    document.getElementById('cart-subtotal').textContent = '$' + (data.cart_subtotal).toFixed(2);
                    document.getElementById('cart-total').textContent = '$' + (data.cart_total).toFixed(2);
                } else {
                    alert('Failed to update quantity.');
                }
            })
            .catch(() => alert('Error updating quantity.'));
        }

        function showToast(message, duration = 3000) {
            const toast = document.getElementById('toast');
            toast.textContent = message;
            toast.classList.add('show');
            setTimeout(() => {
                toast.classList.remove('show');
            }, duration);
        }

function removeItem(cartId) {
    if (!confirm('Are you sure you want to remove this item?')) return;

    fetch('/PHP-Sneakers/functions/removeCartItem.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ cart_id: cartId })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const row = document.querySelector('tr[data-cart-id="' + cartId + '"]');
            if (row) row.remove();

            if (data.cart_empty) {
                location.reload();
            } else {
                document.getElementById('cart-subtotal').textContent = '$' + (data.cart_subtotal).toFixed(2);
                document.getElementById('cart-total').textContent = '$' + (data.cart_total).toFixed(2);
            }

            showToast('Item removed successfully!', 'success');
        } else {
            showToast('Failed to remove item.', 'danger');
        }
    })
    .catch(() => showToast('Error removing item.', 'danger'));
}

    </script>
</body>
</html>
