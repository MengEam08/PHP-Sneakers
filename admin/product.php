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

// Update Product
if (isset($_POST['update_product'])) {
    $update_id = $_POST['update_product_id'];
    $update_name = $_POST['update_product_name'];
    $update_price = $_POST['update_product_price'];
    $update_category_id = !empty($_POST['update_category_id']) ? $_POST['update_category_id'] : null;

    // Handle image update only if a new image is uploaded
    if (!empty($_FILES['update_product_image']['name'])) {
        $update_image = $_FILES['update_product_image']['name'];
        $update_image_tmp = $_FILES['update_product_image']['tmp_name'];
        $update_image_folder = 'uploaded_img/' . $update_image;
    } else {
        // Keep current image (fetch from DB)
        $stmt = $conn->prepare("SELECT image FROM product WHERE id = ?");
        $stmt->execute([$update_id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        $update_image = $product['image'];
    }

    if (empty($update_name) || empty($update_price)) {
        $message[] = 'Please fill out all required fields for updating product';
    } else {
        try {
            $stmt = $conn->prepare("UPDATE product SET name = :name, price = :price, image = :image, category_id = :category_id WHERE id = :id");
            $stmt->bindParam(':name', $update_name);
            $stmt->bindParam(':price', $update_price);
            $stmt->bindParam(':image', $update_image);
            $stmt->bindParam(':category_id', $update_category_id);
            $stmt->bindParam(':id', $update_id);

            $stmt->execute();

            // Move new image file if uploaded
            if (!empty($_FILES['update_product_image']['name'])) {
                move_uploaded_file($update_image_tmp, $update_image_folder);
            }

            $message[] = 'Product updated successfully';
        } catch (PDOException $e) {
            $message[] = 'Failed to update product: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Product Management</title>
<style>

    h3 {
        color: #2d3436;
        margin-bottom: 20px;
    }

    .box {
        padding: 12px;
        margin: 10px 0;
        width: 100%;
        max-width: 100%;
        border: 1px solid #ccc;
        border-radius: 5px;
        box-sizing: border-box;
    }

    .btn {
        padding: 10px 20px;
        cursor: pointer;
        background: #6c5ce7;
        color: white;
        border: none;
        border-radius: 5px;
        font-weight: 600;
        transition: background 0.3s ease;
    }

    .btn:hover {
        background: #341f97;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 30px;
    }

    th, td {
        padding: 12px;
        border: 1px solid #ddd;
        text-align: left;
    }

    th {
        background-color: #6c5ce7;
        color: white;
    }

    tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    .edit-btn, .delete-btn {
        cursor: pointer;
        font-size: 18px;
        display: inline-block;
        padding: 5px 10px;
    }

    .edit-btn {
        color: #0984e3;
    }

    .delete-btn {
        color: #d63031;
    }

    img {
        border-radius: 4px;
        object-fit: cover;
    }

    p {
        background-color: #dff9fb;
        padding: 10px 15px;
        border-left: 5px solid #6c5ce7;
        border-radius: 5px;
        margin-bottom: 15px;
        color: #222;
    }
</style>

<script src="https://unpkg.com/ionicons@5.5.2/dist/ionicons.js"></script>
</head>
<body>

<div class="container">
    <?php
    if (!empty($message)) {
        foreach ($message as $msg) {
            echo '<p style="color: green;">' . htmlspecialchars($msg) . '</p>';
        }
    }
    ?>

    <div class="main" style="margin-left: 10px;">
        <!-- Add Product Form -->
        <div class="admin-product-form-container">
            <form action="" method="post" enctype="multipart/form-data">
                <h3>Add a New Product</h3>
                <input type="text" placeholder="Enter product name" name="product_name" class="box" required>
                <input type="number" step="0.01" placeholder="Enter product price" name="product_price" class="box" required>
                <input type="file" accept="image/png, image/jpeg, image/jpg" name="product_image" class="box" required>

                <select name="category_id" class="box" style="padding: 10px; border-radius: 3px;">
                    <option value="">Select Category (Optional)</option>
                    <?php
                    $stmt = $conn->query("SELECT * FROM category");
                    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($categories as $row) {
                        echo '<option value="' . $row['id'] . '">' . htmlspecialchars($row['name']) . '</option>';
                    }
                    ?>
                </select>

                <input type="submit" class="btn" style="margin-top: 10px;" name="add_product" value="Add Product">
            </form>
        </div>

        <!-- Update Product Form (hidden initially) -->
        <div class="admin-product-form-container" id="update-product-form" style="display:none; margin-top: 30px;">
            <form action="" method="post" enctype="multipart/form-data">
                <h3>Update Product</h3>
                <input type="hidden" name="update_product_id" id="update_product_id">
                <input type="text" placeholder="Enter product name" name="update_product_name" id="update_product_name" class="box" required>
                <input type="number" step="0.01" placeholder="Enter product price" name="update_product_price" id="update_product_price" class="box" required>
                <input type="file" accept="image/png, image/jpeg, image/jpg" name="update_product_image" class="box">

                <select name="update_category_id" id="update_category_id" class="box" style="padding: 10px; border-radius: 3px;">
                    <option value="">Select Category (Optional)</option>
                    <?php
                    foreach ($categories as $row) {
                        echo '<option value="' . $row['id'] . '">' . htmlspecialchars($row['name']) . '</option>';
                    }
                    ?>
                </select>

                <input type="submit" class="btn" style="margin-top: 10px;" name="update_product" value="Update Product">
                <button type="button" class="btn" style="margin-top: 10px; background-color: gray;" id="cancel-update">Cancel</button>
            </form>
        </div>

        <!-- Product List -->
        <div class="product-list" style="margin-top: 40px;">
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
                            <td><img src="uploaded_img/<?= htmlspecialchars($row['image']) ?>" width="50" height="50" alt="Product Image"></td>
                            <td><?= htmlspecialchars($category_name) ?></td>
                            <td>
                                <a href="#" class="edit-btn" 
                                   data-id="<?= $row['id'] ?>"
                                   data-name="<?= htmlspecialchars($row['name']) ?>"
                                   data-price="<?= htmlspecialchars($row['price']) ?>"
                                   data-category="<?= $row['category_id'] ?>"
                                   data-image="<?= htmlspecialchars($row['image']) ?>"
                                   title="Edit Product"
                                   style="color: #0984e3; margin-right: 10px;">
                                   <ion-icon name="create-outline"></ion-icon>
                                </a>
                                <a href="#" class="delete-btn" data-id="<?= $row['id'] ?>" title="Delete Product" style="color: #d63031;">
                                    <ion-icon name="trash-bin-outline"></ion-icon>
                                </a>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  // Handle delete
  document.querySelectorAll('.delete-btn').forEach(btn => {
    btn.addEventListener('click', function (e) {
      e.preventDefault();

      if (!confirm("Are you sure you want to delete this product?")) return;

      const productId = this.dataset.id;

      fetch('delete_product.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'delete_id=' + encodeURIComponent(productId)
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          alert('Product deleted!');
          const row = this.closest('tr');
          if (row) row.remove();
        } else {
          alert(data.message || 'Failed to delete.');
        }
      })
      .catch(() => alert('Something went wrong.'));
    });
  });

  // Handle edit
  const updateForm = document.getElementById('update-product-form');
  const addForm = document.querySelector('.admin-product-form-container form');

  document.querySelectorAll('.edit-btn').forEach(btn => {
    btn.addEventListener('click', function (e) {
      e.preventDefault();

      // Get product data attributes
      const id = this.dataset.id;
      const name = this.dataset.name;
      const price = this.dataset.price;
      const category = this.dataset.category;

      // Fill update form
      document.getElementById('update_product_id').value = id;
      document.getElementById('update_product_name').value = name;
      document.getElementById('update_product_price').value = price;
      document.getElementById('update_category_id').value = category;

      // Show update form and hide add form
      updateForm.style.display = 'block';
      addForm.parentElement.style.display = 'none';

      // Scroll to update form
      updateForm.scrollIntoView({ behavior: 'smooth' });
    });
  });

  // Cancel update
  document.getElementById('cancel-update').addEventListener('click', () => {
    updateForm.style.display = 'none';
    addForm.parentElement.style.display = 'block';
  });
});
</script>

</body>
</html>
