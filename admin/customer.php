<?php
require_once '../admin/conf.php'; // Ensure this connects to your DB using PDO

$messages = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $firstName = trim($_POST['first_name']);
    $lastName = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    $allowed_roles = ['admin', 'customer'];
    if (!in_array($role, $allowed_roles)) {
        $messages[] = ['type' => 'danger', 'text' => 'Invalid role selected.'];
    } else {
        $fullName = $firstName . ' ' . $lastName;
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        try {
            $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (:name, :email, :password, :role)");
            $stmt->bindParam(':name', $fullName);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->bindParam(':role', $role);
            $stmt->execute();

            $messages[] = ['type' => 'success', 'text' => 'Customer created successfully.'];
        } catch (PDOException $e) {
            if ($e->errorInfo[1] == 1062) {
                $messages[] = ['type' => 'danger', 'text' => 'Email already exists.'];
            } else {
                $messages[] = ['type' => 'danger', 'text' => 'Error: ' . $e->getMessage()];
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Create Customer</title>
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

    .form-control,
    .form-select {
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

    .btn-close {
      float: right;
      background: none;
      border: none;
      font-size: 18px;
      cursor: pointer;
    }
    .main{
        margin-left: 20px;
        margin-top: 10px;
    }
  </style>
</head>
<body class="">
<div class="container">
    <div class="main" >
        <div class="ms-5">
      <!-- Alert Messages -->
      <?php if (!empty($messages)): ?>
        <?php foreach ($messages as $msg): ?>
          <div class="alert alert-<?php echo $msg['type']; ?> alert-dismissible fade show" role="alert">
            <?php echo $msg['text']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>

      <div class="" >
        <div class="">
          <h4 class="mb-0">Create New Customer</h4>
        </div>
        <div class="card-body shadow" style="margin-right: 50px;">
          <form action="customer.php" method="POST">
            <div class="mb-3">
              <label for="firstName" class="form-label">First Name</label>
              <input type="text" class="form-control" id="firstName" name="first_name" required>
            </div>

            <div class="mb-3">
              <label for="lastName" class="form-label">Last Name</label>
              <input type="text" class="form-control" id="lastName" name="last_name" required>
            </div>

            <div class="mb-3">
              <label for="email" class="form-label">Email Address</label>
              <input type="email" class="form-control" id="email" name="email" required>
            </div>

            <div class="mb-3">
              <label for="password" class="form-label">Password</label>
              <input type="password" class="form-control" id="password" name="password" required>
            </div>

            <div class="mb-3">
              <label for="role" class="form-label">Role</label>
              <select class="form-select" id="role" name="role" required>
                <option value="customer">Customer</option>
                <option value="admin">Admin</option>
              </select>
            </div>

            <div class="d-grid">
              <button type="submit" class="btn btn-success">Create Customer</button>
            </div>
          </form>
        </div>
      </div>

    </div>
    </div>
</div>
</body>
</html>