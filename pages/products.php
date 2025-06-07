<?php
require_once './admin/conf.php';

try {
    $stmt = $conn->prepare("SELECT * FROM product");
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
<style>
    .shoes-item {
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        border-radius: 0.5rem;
        overflow: hidden;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
        transition: transform 0.3s ease;
    }

    .shoes-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px rgba(0,0,0,0.2);
    }

    .shoes-img {
        height: 400px;
        overflow: hidden;
        flex-shrink: 0;
    }

    .shoes-img img {
        height: 400px;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .shoes-img img:hover {
        transform: scale(1.05);
    }

    .p-4.border {
        flex-grow: 1;
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

<!-- Toast -->
<div id="toast"></div>

<!-- Shoes Shop Start -->
<div class="container-fluid shoes py-5">
    <div class="container py-5">
        <h1 class="mb-4">Shoes shop</h1>
        <div class="row g-4">
            <div class="col-lg-12">
                <div class="row g-4 justify-content-center">
                    <?php foreach ($products as $product): ?>
                        <div class="col-md-6 col-lg-6 col-xl-4">
                            <div class="rounded position-relative shoes-item">
                                <a href="index.php?p=shop-detail&id=<?= $product['id'] ?>" class="text-decoration-none text-dark">
                                    <div class="shoes-img">
                                        <img src="admin/uploaded_img/<?= htmlspecialchars($product['image']) ?>" 
                                             class="img-fluid w-100 rounded-top" 
                                             alt="<?= htmlspecialchars($product['name']) ?>">
                                    </div>
                                    <div class="text-white bg-secondary px-3 py-1 rounded position-absolute" style="top: 10px; left: 10px;">
                                        <?= htmlspecialchars($product['category'] ?? 'Shoes') ?>
                                    </div>
                                </a>
                                <div class="p-4 border border-secondary border-top-0 rounded-bottom">
                                    <h4><?= htmlspecialchars($product['name']) ?></h4>
                                    <p><?= htmlspecialchars($product['description'] ?? 'No description available.') ?></p>
                                    <div class="d-flex justify-content-between flex-lg-wrap align-items-center">
                                        <p class="text-dark fs-5 fw-bold mb-0">$<?= number_format($product['price'], 2) ?> / kg</p>
                                        
                                        <!-- Add to Cart Form -->
                                        <form class="addToCartForm" method="POST">
                                            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                            <button type="submit" class="btn border border-secondary rounded-pill px-3 text-primary">
                                                <i class="fa fa-shopping-bag me-2 text-primary"></i> Add to cart
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <?php if (count($products) === 0): ?>
                        <p class="text-center text-muted">No products found.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Shoes Shop End -->

<script>
    // Toast Notification
    function showToast(message, duration = 3000) {
        const toast = document.getElementById('toast');
        toast.textContent = message;
        toast.classList.add('show');
        setTimeout(() => {
            toast.classList.remove('show');
        }, duration);
    }

    // Handle Add to Cart via AJAX
    document.querySelectorAll('.addToCartForm').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch('/PHP-Sneakers/functions/addCart.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.text())
            .then(data => {
                if (data.trim() === 'success') {
                    showToast('Added to cart successfully!');
                    updateCartCount();
                } else {
                    showToast('Failed to add to cart.', 4000);
                }
            })
            .catch(err => {
                console.error('Add to cart error:', err);
                showToast('Failed to add to cart.', 4000);
            });
        });
    });

    // Update Cart Count (if you display it somewhere)
    function updateCartCount() {
        fetch('/PHP-Sneakers/functions/getCartCount.php')
            .then(response => response.json())
            .then(data => {
                const countSpan = document.querySelector('a[href="index.php?p=cart"] span');
                if (countSpan && data.cart_count !== undefined) {
                    countSpan.textContent = data.cart_count;
                }
            })
            .catch(err => {
                console.error('Failed to update cart count:', err);
            });
    }
</script>
