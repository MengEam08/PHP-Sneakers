<?php
require_once './admin/conf.php'; // your DB connection file

try {
    // Fetch 3 categories from the category table
    $stmt = $conn->prepare("SELECT id, name, image FROM category LIMIT 3");
    $stmt->execute();
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error fetching categories: " . $e->getMessage();
    $categories = [];
}
?>
<style>
    .service-item img {
    height: 400px;          /* fixed image height */
    object-fit: cover;      /* cover the image nicely */
    flex-shrink: 0;         /* don't allow shrinking */
    border-top-left-radius: 0.25rem;
    border-top-right-radius: 0.25rem;
}

</style>
<!-- Features Start -->
<div class="container-fluid service py-5">
    <div class="container py-5">
        <div class="row g-4 justify-content-center">
            <?php foreach ($categories as $category): ?>
                <div class="col-md-6 col-lg-4">
                    <a href="#">
                        <div class="service-item bg-secondary rounded border border-secondary d-flex flex-column" style="height: 500px;">
                            <img src="admin/uploaded_img/<?= htmlspecialchars($category['image']) ?>" class="img-fluid rounded-top w-100" alt="<?= htmlspecialchars($category['name']) ?>">
                            <div class="px-4 rounded-bottom flex-grow-1 d-flex flex-column justify-content-center">
                                <h5 class="text-white text-center"><?= htmlspecialchars($category['name']) ?></h5>
                                <h3 class="mb-0 text-center">Free delivery</h3>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<!-- Features End -->
