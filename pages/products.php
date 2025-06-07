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
        height: 100%; /* Ensure cards take full height of their container */
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        border-radius: 0.5rem; /* smooth corners */
        overflow: hidden;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
        transition: transform 0.3s ease;
        }

        .shoes-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px rgba(0,0,0,0.2);
        }

        .shoes-img {
        height: 400px; /* fixed height for all images */
        overflow: hidden;
        flex-shrink: 0;
        }

        .shoes-img img {
        height: 400px;
        object-fit: cover; /* keeps aspect ratio, fills area */
        transition: transform 0.3s ease;
        }

        .shoes-img img:hover {
        transform: scale(1.05);
        }

        .p-4.border {
        flex-grow: 1; /* so content fills remaining vertical space */
        }
</style>
<!-- Fruits Shop Start-->
<div class="container-fluid shoes py-5">
    <div class="container py-5">
        <h1 class="mb-4">Shoes shop</h1>
        <div class="row g-4">
            <div class="col-lg-12">
                <div class="row g-4 justify-content-center">
                    <?php foreach ($products as $product): ?>
                        <div class="col-md-6 col-lg-6 col-xl-4">
                            <a href="index.php?p=shop-detail&id=<?= $product['id'] ?>" class="text-decoration-none text-dark">
                                <div class="rounded position-relative shoes-item">
                                    <div class="shoes-img">
                                        <img src="admin/uploaded_img/<?= htmlspecialchars($product['image']) ?>" 
                                            class="img-fluid w-100 rounded-top" 
                                            alt="<?= htmlspecialchars($product['name']) ?>">
                                    </div>
                                    <div class="text-white bg-secondary px-3 py-1 rounded position-absolute" style="top: 10px; left: 10px;">
                                        <?= htmlspecialchars($product['category'] ?? 'Shoes') ?>
                                    </div>
                                    <div class="p-4 border border-secondary border-top-0 rounded-bottom">
                                        <h4><?= htmlspecialchars($product['name']) ?></h4>
                                        <p><?= htmlspecialchars($product['description'] ?? 'No description available.') ?></p>
                                        <div class="d-flex justify-content-between flex-lg-wrap">
                                            <p class="text-dark fs-5 fw-bold mb-0">$<?= number_format($product['price'], 2) ?> / kg</p>
                                            <span class="btn border border-secondary rounded-pill px-3 text-primary">
                                                <i class="fa fa-shopping-bag me-2 text-primary"></i> Add to cart
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </a>
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
<!-- Fruits Shop End-->
