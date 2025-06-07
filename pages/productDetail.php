<?php
require_once __DIR__ . '/../admin/conf.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Invalid product ID.');
}

$id = (int)$_GET['id'];

$stmt = $conn->prepare("SELECT * FROM product WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    die('Product not found.');
}
?>
<style>
    img {
        width: 500px;
        height: 500px;
        object-fit: cover;
    }

    /* Toast styles */
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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($product['name']) ?> | Detail</title>
    <link rel="stylesheet" href="your-bootstrap.css">
</head>
<body>
    <div class="container-fluid py-5" style="margin-top: 100px;">
        <div class="container py-5">
            <div class="row g-4 mb-5">
                <div class="col-lg-6">
                    <img src="admin/uploaded_img/<?= htmlspecialchars($product['image']) ?>" class="img-fluid rounded" alt="<?= htmlspecialchars($product['name']) ?>">
                </div>
                <div class="col-lg-6">
                    <h4 class="fw-bold mb-3"><?= htmlspecialchars($product['name']) ?></h4>
                    <h5 class="fw-bold mb-3">$<?= number_format($product['price'], 2) ?></h5>
                    <form id="addToCartForm">
                        <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['id']) ?>">
                        <button type="submit" class="btn btn-primary">Add to Cart</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast container -->
    <div id="toast"></div>

    <script>
        function showToast(message, duration = 3000) {
            const toast = document.getElementById('toast');
            toast.textContent = message;
            toast.classList.add('show');
            setTimeout(() => {
                toast.classList.remove('show');
            }, duration);
        }

        document.getElementById('addToCartForm').addEventListener('submit', function(e) {
            e.preventDefault(); // prevent form submit navigation
            
            const formData = new FormData(this);

            fetch('/PHP-Sneakers/functions/addCart.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                if(data.trim() === 'success') {
                    showToast('Added to cart successfully!');
                    updateCartCount();
                } else {
                    showToast('Failed to add to cart.', 4000);
                }
            })
            .catch(err => {
                console.error('Error adding to cart:', err);
                showToast('Failed to add to cart.', 4000);
            });
        });

        function updateCartCount() {
            fetch('/PHP-Sneakers/functions/getCartCount.php')
                .then(response => response.json())
                .then(data => {
                    const countSpan = document.querySelector('a[href="index.php?p=cart"] span');
                    if(countSpan && data.cart_count !== undefined) {
                        countSpan.textContent = data.cart_count;
                    }
                })
                .catch(err => {
                    console.error('Failed to update cart count:', err);
                });
        }
    </script>
</body>
</html>
