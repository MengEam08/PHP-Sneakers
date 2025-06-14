<?php
require_once 'conf.php';

$messages = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_category'])) {
    $category_name = trim($_POST['category_name']);
    $category_image = $_FILES['category_image']['name'];
    $category_image_tmp = $_FILES['category_image']['tmp_name'];
    $category_image_folder = 'uploaded_img/' . $category_image;

    if (empty($category_name) || empty($category_image)) {
        $messages[] = ['type' => 'danger', 'text' => 'Please fill out all fields for the category.'];
    } else {
        try {
            $stmt = $conn->prepare("INSERT INTO category (name, image) VALUES (:name, :image)");
            $stmt->bindParam(':name', $category_name);
            $stmt->bindParam(':image', $category_image);

            if ($stmt->execute()) {
                move_uploaded_file($category_image_tmp, $category_image_folder);
                $messages[] = ['type' => 'success', 'text' => 'New category added successfully.'];
            } else {
                $messages[] = ['type' => 'danger', 'text' => 'Could not add the category.'];
            }
        } catch (PDOException $e) {
            $messages[] = ['type' => 'danger', 'text' => 'Database error: ' . $e->getMessage()];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Category</title>
  <style>
    h4 {
      margin-bottom: 20px;
      color: #333;
    }

    .form-label {
      font-weight: 600;
      margin-bottom: 5px;
      display: inline-block;
    }

    .form-control {
      width: 100%;
      padding: 10px;
      margin-bottom: 15px;
      border: 1px solid #ccc;
      border-radius: 5px;
      font-size: 1rem;
    }

    .btn-success {
      padding: 10px;
      font-size: 16px;
      border: none;
      border-radius: 5px;
      background-color: #e89d2e;
      color: white;
      cursor: pointer;
      transition: background-color 0.3s;
    }

    .btn-success:hover {
      background-color: #c97902;
    }

    .alert {
      padding: 15px;
      border-radius: 5px;
      margin-bottom: 20px;
      font-size: 14px;
    }

    .alert-success {
      background-color: #d4edda;
      color: #155724;
    }

    .alert-danger {
      background-color: #f8d7da;
      color: #721c24;
    }

    .main {
      margin: 20px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 30px;
    }

    th, td {
      padding: 12px;
      border: 1px solid #ccc;
      text-align: left;
    }

    th {
      background-color: #e89d2e;
      color: white;
    }

    img {
      border-radius: 5px;
    }

    .btn-delete {
      background-color: #dc3545;
      color: white;
      padding: 6px 10px;
      border: none;
      border-radius: 3px;
      cursor: pointer;
    }

    .btn-delete:hover {
      background-color: #b02a37;
    }
  </style>
</head>
<body>

<div class="container">
  <div class="main">
    <!-- Alert Messages -->
    <?php if (!empty($messages)): ?>
      <?php foreach ($messages as $msg): ?>
        <div class="alert alert-<?php echo $msg['type']; ?>">
          <?php echo $msg['text']; ?>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>

    <!-- Add Category Form -->
    <div class="card-body shadow" style="margin-right: 50px;">
      <h4>Add New Category</h4>
      <form action="" method="POST" enctype="multipart/form-data">
        <div class="mb-3">
          <label for="category_name" class="form-label">Category Name</label>
          <input type="text" class="form-control" name="category_name" required>
        </div>

        <div class="mb-3">
          <label for="category_image" class="form-label">Category Image</label>
          <input type="file" class="form-control" name="category_image" accept="image/png, image/jpeg, image/jpg" required>
        </div>

        <div class="d-grid">
          <button type="submit" name="add_category" class="btn btn-success">Add Category</button>
        </div>
      </form>
    </div>

    <!-- Category List -->
    <div class="mt-4">
      <h4>Category List</h4>
      <table>
        <thead>
          <tr>
            <th>Category Name</th>
            <th>Image</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $stmt = $conn->query("SELECT * FROM category ORDER BY id DESC");
          while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
              echo '<tr>';
              echo '<td>' . htmlspecialchars($row['name']) . '</td>';
              echo '<td><img src="uploaded_img/' . htmlspecialchars($row['image']) . '" width="50" height="50" alt="Category Image"></td>';
              echo '<td>
                      <a href="#" class="delete-btn" data-id="' . $row['id'] . '">
                        <ion-icon name="trash-bin-outline"></ion-icon>
                      </a>
                    </td>';
              echo '</tr>';
          }
          ?>
        </tbody>

      </table>
    </div>
  </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.delete-btn').forEach(btn => {
    btn.addEventListener('click', function (e) {
      e.preventDefault();

      if (!confirm("Are you sure you want to delete this category?")) return;

      const categoryId = this.dataset.id;

      fetch('delete_category.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'delete_id=' + encodeURIComponent(categoryId)
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          alert('Category deleted!');
          const row = this.closest('tr');
          if (row) row.remove();
        } else {
          alert(data.message || 'Failed to delete category.');
        }
      })
      .catch(() => alert('Something went wrong.'));
    });
  });
});
</script>

</body>
</html>
