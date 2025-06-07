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
    img{
        width: 500px;
        height: 500px;
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
                    <a href="#" class="btn btn-primary">Add to Cart</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
