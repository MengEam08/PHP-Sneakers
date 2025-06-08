paypal.Buttons({
    createOrder: function (data, actions) {
        // Set the amount dynamically (you could also pass PHP value via dataset or hidden input)
        return actions.order.create({
            purchase_units: [
                {
                    amount: {
                        value: '<?= number_format($grandTotal, 2, '.', '') ?>', // Use PHP to inject grand total
                        currency_code: 'USD',
                    },
                },
            ],
        });
    },
    onApprove: function (data, actions) {
        return actions.order.capture().then(function (details) {
            const transaction = details.purchase_units[0].payments.captures[0];

            // Send data to the server to store in DB
            fetch('/PHP-Sneakers/functions/paypal_processor.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    transaction_id: transaction.id,
                    status: transaction.status,
                    payer_email: details.payer.email_address,
                    amount: transaction.amount.value,
                    currency: transaction.amount.currency_code,
                }),
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    showToast("Payment successful. Transaction ID: " + transaction.id);
                    setTimeout(() => {
                        window.location.href = '/PHP-Sneakers/success.php';
                    }, 3000);
                } else {
                    showToast("Payment captured, but saving failed.");
                }
            })
            .catch(error => {
                console.error("Error processing PayPal payment:", error);
                showToast("An error occurred during payment processing.");
            });
        });
    },
    onError: function (err) {
        console.error("PayPal error:", err);
        showToast("Payment failed. Try again.");
    }
}).render('#paypal-button-container');
