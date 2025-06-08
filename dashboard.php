<?php
session_start();
require_once './admin/conf.php'; // adjust path if needed

if (!isset($_SESSION['customer_logged_in']) || !$_SESSION['customer_logged_in']) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['user_id'];

$updateMessage = '';

// Handle profile update form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newName = trim($_POST['name'] ?? '');
    $newEmail = trim($_POST['email'] ?? '');
    $newPassword = trim($_POST['password'] ?? '');

    if ($newName === '' || $newEmail === '') {
        $updateMessage = "Name and Email cannot be empty.";
    } else {
        try {
            if ($newPassword !== '') {
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE users SET name = :name, email = :email, password = :password WHERE id = :id");
                $stmt->execute([
                    ':name' => $newName,
                    ':email' => $newEmail,
                    ':password' => $hashedPassword,
                    ':id' => $userId
                ]);
            } else {
                $stmt = $conn->prepare("UPDATE users SET name = :name, email = :email WHERE id = :id");
                $stmt->execute([
                    ':name' => $newName,
                    ':email' => $newEmail,
                    ':id' => $userId
                ]);
            }
            $updateMessage = "Profile updated successfully!";
        } catch (PDOException $e) {
            $updateMessage = "Error updating profile: " . $e->getMessage();
        }
    }
}

// Fetch current user data
try {
    $stmt = $conn->prepare("SELECT name, email FROM users WHERE id = :id");
    $stmt->bindParam(':id', $userId);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching user: " . $e->getMessage());
}

// Fetch user sales history
try {
    $stmt = $conn->prepare("
        SELECT 
            s.product_id,
            s.quantity,
            s.total_price,
            s.status,
            s.mobile,
            s.email,
            s.sale_date,
            p.name AS product_name
        FROM sales s
        JOIN product p ON s.product_id = p.id
        WHERE s.user_id = :user_id
        ORDER BY s.sale_date DESC
    ");
    $stmt->bindParam(':user_id', $userId);
    $stmt->execute();
    $sales = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching sales: " . $e->getMessage());
}
?>

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />

<style>
  body {
    background: #f7f7ff;
    margin-top: 20px;
  }
  .card {
    background-color: #fff;
    border-radius: .25rem;
    margin-bottom: 1.5rem;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
  }
</style>

<?php include "./includes/head.php"; ?>
<body>
<?php include "./components/navbar.php"; ?>

<div class="container">
  <div class="main-body" style="margin-top: 200px;">
    <?php if ($updateMessage): ?>
      <div class="alert alert-info"><?= htmlspecialchars($updateMessage) ?></div>
    <?php endif; ?>

    <div class="row">
      <!-- Profile card -->
      <div class="col-lg-4">
        <div class="card">
          <div class="card-body text-center">
            <img src="https://bootdey.com/img/Content/avatar/avatar6.png" alt="User" class="rounded-circle p-1 bg-primary" width="110">
            <div class="mt-3">
              <h4><?= htmlspecialchars($user['name']) ?></h4>
              <p class="text-secondary mb-1"><?= htmlspecialchars($user['email']) ?></p>
              <form action="/PHP-Sneakers/pages/logout.php" method="POST">
                <button type="submit" class="btn btn-danger">Logout</button>
              </form>
            </div>
          </div>
        </div>
      </div>

      <!-- Profile update form -->
      <div class="col-lg-8">
        <div class="card">
          <div class="card-body">
            <form method="POST">
              <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Full Name</label>
                <div class="col-sm-9">
                  <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($user['name']) ?>" required>
                </div>
              </div>
              <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Email</label>
                <div class="col-sm-9">
                  <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
                </div>
              </div>
              <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Password</label>
                <div class="col-sm-9">
                  <input type="password" name="password" class="form-control" placeholder="Enter new password if changing">
                </div>
              </div>
              <div class="row">
                <div class="col-sm-3"></div>
                <div class="col-sm-9">
                  <button type="submit" class="btn btn-primary px-4">Save Changes</button>
                </div>
              </div>
            </form>
          </div>
        </div>

        <!-- Sales History Table -->
        <div class="card mt-4">
          <div class="card-body">
            <h5 class="mb-3">Purchase History</h5>
            <?php if (!empty($sales)): ?>
              <div class="table-responsive">
                <table class="table table-bordered table-striped">
                  <thead style="background-color: #FEB424;">
                    <tr>
                      <th>Product Name</th>
                      <th>Quantity</th>
                      <th>Price</th>
                      <th>Status</th>
                      <th>Mobile</th>
                      <th>Email</th>
                      <th>Date</th>
                    </tr>
                  </thead>
                  <tbody class="table-primary">
                    <?php foreach ($sales as $sale): ?>
                      <tr>
                        <td><?= htmlspecialchars($sale['product_name']) ?></td>
                        <td><?= htmlspecialchars($sale['quantity']) ?></td>
                        <td>$<?= number_format($sale['total_price'], 2) ?></td>
                        <td><?= htmlspecialchars($sale['status']) ?></td>
                        <td><?= htmlspecialchars($sale['mobile']) ?></td>
                        <td><?= htmlspecialchars($sale['email']) ?></td>
                        <td><?= date('Y-m-d', strtotime($sale['sale_date'])) ?></td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            <?php else: ?>
              <p class="text-muted">You haven't made any purchases yet.</p>
            <?php endif; ?>
          </div>
        </div>

      </div> <!-- End Right Column -->
    </div> <!-- End Row -->
  </div>
</div>

<?php include "./components/footer.php"; ?>
<?php include "./includes/footer-scripts.php"; ?>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
