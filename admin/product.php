<?php
@include 'conf.php';

$message = [];

// Add Product
if (isset($_POST['add_product'])) {
    $product_name = $_POST['product_name'];
    $product_price = $_POST['product_price'];
    $product_image = $_FILES['product_image']['name'];
    $product_image_tmp_name = $_FILES['product_image']['tmp_name'];
    $product_image_folder = 'uploaded_img/' . $product_image;
    $category_id = !empty($_POST['category_id']) ? $_POST['category_id'] : null;

    if (empty($product_name) || empty($product_price) || empty($product_image)) {
        $message[] = 'Please fill out all required fields for the product';
    } else {
        try {
            $stmt = $conn->prepare("INSERT INTO product (name, price, image, category_id) VALUES (:name, :price, :image, :category_id)");

            $stmt->bindParam(':name', $product_name);
            $stmt->bindParam(':price', $product_price);
            $stmt->bindParam(':image', $product_image);
            $stmt->bindParam(':category_id', $category_id);

            $stmt->execute();
            move_uploaded_file($product_image_tmp_name, $product_image_folder);
            $message[] = 'New product added successfully';
        } catch (PDOException $e) {
            $message[] = 'Could not add the product: ' . $e->getMessage();
        }
    }
}
?>


<div class="container">
    <div class="main" style="margin-left: 10px;">
        <!-- Add Product Form -->
        <div class="admin-product-form-container">
            <form action="" method="post" enctype="multipart/form-data">
                <h3>Add a New Product</h3>
                <input type="text" placeholder="Enter product name" name="product_name" class="box">
                <input type="number" step="0.01" placeholder="Enter product price" name="product_price" class="box">
                <input type="file" accept="image/png, image/jpeg, image/jpg" name="product_image" class="box">

                <select name="category_id" class="box" style="padding: 10px; border-radius: 3px;">
                    <option value="">Select Category (Optional)</option>
                    <?php
                    $stmt = $conn->query("SELECT * FROM category");
                    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($categories as $row) {
                        echo '<option value="' . $row['id'] . '">' . $row['name'] . '</option>';
                    }
                    ?>
                </select>

                <input type="submit" class="btn" style="margin-top: 10px;" name="add_product" value="Add Product">
            </form>
        </div>

        <!-- Product List -->
        <div class="product-list">
            <h3>Product List</h3>
            <table>
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Price</th>
                        <th>Image</th>
                        <th>Category</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $stmt = $conn->query("SELECT * FROM product");
                    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    foreach ($products as $row) {
                        $category_name = 'No Category';
                        if ($row['category_id']) {
                            $cat_stmt = $conn->prepare("SELECT name FROM category WHERE id = ?");
                            $cat_stmt->execute([$row['category_id']]);
                            $cat = $cat_stmt->fetch(PDO::FETCH_ASSOC);
                            if ($cat) {
                                $category_name = $cat['name'];
                            }
                        }
                        ?>
                        <tr>
                           
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td><?= htmlspecialchars($row['price']) ?></td>
                            <td><img src="uploaded_img/<?= htmlspecialchars($row['image']) ?>" width="50" height="50"></td>
                            <td><?= htmlspecialchars($category_name) ?></td>
                            <td><a href="admin_page.php?delete=<?= $row['id'] ?>" class="delete-btn"><ion-icon name="trash-bin-outline"></ion-icon></a></td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
