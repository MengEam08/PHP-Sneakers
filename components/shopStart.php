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
    .fruite-item {
  height: 100%; /* Ensure cards take full height of their container */
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  border-radius: 0.5rem; /* smooth corners */
  overflow: hidden;
  box-shadow: 0 0 10px rgba(0,0,0,0.1);
  transition: transform 0.3s ease;
}

.fruite-item:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 15px rgba(0,0,0,0.2);
}

.fruite-img {
  height: 300px; /* fixed height for all images */
  overflow: hidden;
  flex-shrink: 0;
}

.fruite-img img {
  height: 300px;
  object-fit: cover; /* keeps aspect ratio, fills area */
  transition: transform 0.3s ease;
}

.fruite-img img:hover {
  transform: scale(1.05);
}

.p-4.border {
  flex-grow: 1; /* so content fills remaining vertical space */
}

</style>
<div class="container-fluid fruite py-5">
    <div class="container py-5">
        <div class="tab-class text-center">
            <div class="row g-4">
                <div class="col-lg-4 text-start">
                    <h1>Our Products</h1>
                </div>
            </div>

            <div class="tab-content">
                <div id="tab-1" class="tab-pane fade show p-0 active">
                    <div class="row g-4">
                        <?php foreach ($products as $product): ?>
                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <a href="index.php?p=shop-detail&id=<?= htmlspecialchars($product['id']) ?>" class="text-decoration-none text-dark">
                                <div class="rounded position-relative fruite-item">
                                    <div class="fruite-img">
                                        <img src="admin/uploaded_img/<?= htmlspecialchars($product['image']) ?>" 
                                             class="img-fluid w-100 rounded-top" 
                                             alt="<?= htmlspecialchars($product['name']) ?>">
                                    </div>
                                    <div class="p-4 border border-secondary border-top-0 rounded-bottom">
                                        <h4><?= htmlspecialchars($product['name']) ?></h4>
                                        <p><!-- You can add description here if you have one --></p>
                                        <div class="d-flex justify-content-between flex-lg-wrap">
                                            <p class="text-dark fs-5 fw-bold mb-0">$<?= htmlspecialchars($product['price']) ?></p>
                                            <span class="btn border border-secondary rounded-pill px-3 text-primary">
                                                <i class="fa fa-shopping-bag me-2 text-primary"></i> Add to cart
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <!-- TODO: Add other tab content (Vegetables, Fruits, etc.) similarly -->
            </div>
        </div>      
    </div>
</div>
