<?php
session_start();
require_once __DIR__ . '/../admin/conf.php';

// For demo, assume user ID is set in session, or fallback to 1
$user_id = $_SESSION['user_id'] ?? 1;

// Fetch cart items for the user
$stmt = $conn->prepare("
    SELECT cart.id, product.name, product.image, product.price, cart.quantity 
    FROM cart 
    JOIN product ON cart.product_id = product.id 
    WHERE cart.user_id = ?
");
$stmt->execute([$user_id]);
$cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate subtotal
$subtotal = 0;
foreach ($cartItems as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}

// Define shipping cost and grand total
$shipping = 3.00; // Flat shipping rate (not currently used for "Free Shipping")
$grandTotal = $subtotal + $shipping;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Checkout</title>
    <!-- Your Bootstrap CSS -->
    <link rel="stylesheet" href="your-bootstrap.css" />
    <style>
        .main { margin-top: 60px; }
        .form-label { font-weight: bold; }
        .table th, .table td { vertical-align: middle; }

        /* Toast style */
        #toast {
            visibility: hidden;
            min-width: 250px;
            margin-left: -125px;
            background-color: #333;
            color: #fff;
            text-align: center;
            border-radius: 2px;
            padding: 16px;
            position: fixed;
            z-index: 9999;
            left: 50%;
            bottom: 30px;
            font-size: 17px;
        }
        #toast.show {
            visibility: visible;
            animation: fadein 0.5s, fadeout 0.5s 2.5s;
        }
        @keyframes fadein { from {bottom: 0;opacity: 0;} to {bottom: 30px;opacity: 1;} }
        @keyframes fadeout { from {bottom: 30px;opacity: 1;} to {bottom: 0;opacity: 0;} }

        /* Hide PayPal button container by default */
        #paypal-button-container {
            display: none;
            margin-top: 15px;
        }
    </style>
</head>
<body>
<div class="container-fluid py-5 main">
    <div class="container py-5">
        <h1 class="mb-4">Billing Details</h1>
        <form id="checkoutForm" method="POST">
            <div class="row">
                <!-- Billing Information -->
                <div class="col-md-12 col-lg-6 col-xl-7">
                    <div class="row">
                        <div class="col-md-12 col-lg-6">
                            <div class="form-item w-100">
                                <label class="form-label my-3">First Name<sup>*</sup></label>
                                <input type="text" name="first_name" class="form-control" required />
                            </div>
                        </div>
                        <div class="col-md-12 col-lg-6">
                            <div class="form-item w-100">
                                <label class="form-label my-3">Last Name<sup>*</sup></label>
                                <input type="text" name="last_name" class="form-control" required />
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-item">
                        <label class="form-label my-3">Address<sup>*</sup></label>
                        <input type="text" name="address" class="form-control" placeholder="House Number Street Name" required />
                    </div>
                    <div class="form-item">
                        <label class="form-label my-3">Mobile<sup>*</sup></label>
                        <input type="tel" name="mobile" class="form-control" required />
                    </div>
                    <div class="form-item">
                        <label class="form-label my-3">Email Address<sup>*</sup></label>
                        <input type="email" name="email" class="form-control" required />
                    </div>
                    <hr />
                </div>

                <!-- Order Summary -->
                <div class="col-md-12 col-lg-6 col-xl-5">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Products</th>
                                    <th>Name</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cartItems as $item): ?>
                                    <tr>
                                        <td>
                                            <img src="admin/uploaded_img/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="img-fluid rounded-circle" style="width: 90px; height: 90px;" />
                                        </td>
                                        <td class="py-5"><?= htmlspecialchars($item['name']) ?></td>
                                        <td class="py-5">$<?= number_format($item['price'], 2) ?></td>
                                        <td class="py-5"><?= $item['quantity'] ?></td>
                                        <td class="py-5">$<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                <tr>
                                    <td colspan="3"></td>
                                    <td class="py-5"><strong>Subtotal</strong></td>
                                    <td class="py-5">$<?= number_format($subtotal, 2) ?></td>
                                </tr>
                                <tr>
                                    <td colspan="2"><strong>Shipping</strong></td>
                                    <td colspan="3">
                                        <div class="form-check text-start">
                                            <input type="checkbox" class="form-check-input bg-primary border-0" id="Shipping-1" name="shipping" value="Free Shipping" checked />
                                            <label class="form-check-label" for="Shipping-1">Free Shipping</label>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3"></td>
                                    <td class="py-5"><strong>Total</strong></td>
                                    <td class="py-5">$<?= number_format($grandTotal, 2) ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Payment Methods -->
                    <div class="row g-4 text-center align-items-center justify-content-center border-bottom py-3">
                        <h3>Payment Methods</h3>
                        <div class="col-12">
                            <div class="form-check text-start my-3">
                                <input type="radio" class="form-check-input bg-primary border-0" id="Delivery-1" name="payment_method" value="Cash On Delivery" required />
                                <label class="form-check-label" for="Delivery-1">Cash On Delivery</label>
                            </div>
                        </div>
                    </div>
                    <div class="row g-4 text-center align-items-center justify-content-center border-bottom py-3">
                        <div class="col-12">
                            <div class="form-check text-start my-3">
                                <input type="radio" class="form-check-input bg-primary border-0" id="Paypal-1" name="payment_method" value="Paypal" required />
                                <label class="form-check-label" for="Paypal-1">Paypal</label>
                            </div>
                            <!-- PayPal Button Container (hidden by default) -->
                            <div id="paypal-button-container"></div>
                        </div>
                    </div>

                    <!-- Place Order Button -->
                    <div class="row g-4 text-center align-items-center justify-content-center pt-4">
                        <button type="submit" class="btn border-secondary py-3 px-4 text-uppercase w-100 text-primary">Place Order</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Toast container -->
<div id="toast"></div>

<!-- PayPal SDK -->
<script src="https://www.paypal.com/sdk/js?client-id=AaxcE-zTLDsoivpKMOD2Riu57zXt6MYxhboJV1dFEjYOSrj29WfhXcL_UXqMUJnRMSyWIlvVeqLsu6Sy&currency=USD"></script>

<script>
// PayPal Buttons render only once and stay hidden until PayPal option selected
paypal.Buttons({
    createOrder: function(data, actions) {
        return actions.order.create({
            purchase_units: [{
                amount: {
                    value: '<?= number_format($grandTotal, 2, '.', '') ?>'
                }
            }]
        });
    },
    onApprove: function(data, actions) {
        return actions.order.capture().then(function(details) {
            alert('Transaction completed by ' + details.payer.name.given_name + '!');
            // Optionally submit your form or do AJAX to record payment here
            // e.g. document.getElementById('checkoutForm').submit();
        });
    }
}).render('#paypal-button-container');
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const paypalRadio = document.getElementById('Paypal-1');
    const codRadio = document.getElementById('Delivery-1');
    const paypalContainer = document.getElementById('paypal-button-container');
    const form = document.getElementById('checkoutForm');

    function togglePaypal() {
        paypalContainer.style.display = paypalRadio.checked ? 'block' : 'none';
    }

    paypalRadio.addEventListener('change', togglePaypal);
    codRadio.addEventListener('change', togglePaypal);

    togglePaypal();

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        // If PayPal selected, let PayPal handle the payment first
        if (paypalRadio.checked) {
            showToast("Please complete PayPal payment.");
            return; // Prevent direct form submission
        }

        // Proceed with regular form submit (e.g., Cash on Delivery)
        submitCheckoutForm(form);
    });
});

// Your existing form submit handling (AJAX)
document.getElementById('checkoutForm').addEventListener('submit', function(e) {
    e.preventDefault();

    // If PayPal is selected, you might want to prevent direct form submission
    // and rely on PayPal payment success event.
    // For demo, this example just sends form always.
    const formData = new FormData(this);

    fetch('/PHP-Sneakers/functions/process_checkout.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (response.redirected) {
            window.location.href = response.url;
        } else {
            return response.text();
        }
    })
    .then(data => {
        if (data) {
            showToast(data);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
});

    // Simple toast function
    function showToast(message) {
        const toast = document.getElementById('toast');
        toast.innerText = message;
        toast.className = 'show';
        setTimeout(() => { toast.className = toast.className.replace('show', ''); }, 3000);
    }
</script>
</body>
</html>
