<?php
session_start();
require_once './admin/conf.php'; // adjust path if needed

if (!isset($_SESSION['customer_logged_in']) || !$_SESSION['customer_logged_in']) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['user_id'];

$updateMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get submitted data, trim whitespace
    $newName = trim($_POST['name'] ?? '');
    $newEmail = trim($_POST['email'] ?? '');
    $newPassword = trim($_POST['password'] ?? '');

    if ($newName === '' || $newEmail === '') {
        $updateMessage = "Name and Email cannot be empty.";
    } else {
        try {
            // If password is not empty, hash it and update; else keep old password
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
                // Update name and email only, keep password unchanged
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

// Fetch updated user data
try {
    $stmt = $conn->prepare("SELECT name, email FROM users WHERE id = :id");
    $stmt->bindParam(':id', $userId);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching user: " . $e->getMessage());
}
?>


<!-- Bootstrap 5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />

<!-- Ionicons -->
<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

<style>
  body {
    background: #f7f7ff;
    margin-top: 20px;
  }
  .card {
    background-color: #fff;
    border: 0 solid transparent;
    border-radius: .25rem;
    margin-bottom: 1.5rem;
    box-shadow: 0 2px 6px 0 rgb(218 218 253 / 65%), 0 2px 6px 0 rgb(206 206 238 / 54%);
  }
  .me-2 {
    margin-right: .5rem!important;
  }
</style>
</head>

<?php include "./includes/head.php"; ?>
<body>
  <?php include "./components/navbar.php"; ?>

  <div class="container">
    <div class="main-body" style="margin-top: 200px;">

      <?php if ($updateMessage): ?>
        <div class="alert alert-info"><?= htmlspecialchars($updateMessage) ?></div>
      <?php endif; ?>

      <div class="row">
        <div class="col-lg-4">
          <div class="card">
            <div class="card-body">
              <div class="d-flex flex-column align-items-center text-center">
                <img src="https://bootdey.com/img/Content/avatar/avatar6.png" alt="Admin" class="rounded-circle p-1 bg-primary" width="110">
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
        </div>

        <div class="col-lg-8">
          <div class="card">
            <div class="card-body">
              <form method="POST">
                <div class="row mb-3">
                  <div class="col-sm-3">
                    <h6 class="mb-0">Full Name</h6>
                  </div>
                  <div class="col-sm-9 text-secondary">
                    <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($user['name']) ?>" required>
                  </div>
                </div>

                <div class="row mb-3">
                  <div class="col-sm-3">
                    <h6 class="mb-0">Email</h6>
                  </div>
                  <div class="col-sm-9 text-secondary">
                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
                  </div>
                </div>

                <div class="row mb-3">
                  <div class="col-sm-3">
                    <h6 class="mb-0">Password</h6>
                  </div>
                  <div class="col-sm-9 text-secondary">
                    <input type="password" name="password" class="form-control" placeholder="Enter new password if you want to change">
                  </div>
                </div>

                <div class="row">
                  <div class="col-sm-3"></div>
                  <div class="col-sm-9 text-secondary">
                    <button type="submit" class="btn btn-primary px-4">Save Changes</button>
                  </div>
                </div>
              </form> <!-- end form -->
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>

  <?php include "./components/footer.php"; ?>
  <?php include "./includes/footer-scripts.php"; ?>
  
  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
