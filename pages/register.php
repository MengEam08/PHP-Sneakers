<?php
echo 'Current file: ' . __FILE__ . '<br>';
echo 'Current dir: ' . __DIR__ . '<br>';
echo 'Trying to include: ' . __DIR__ . '/../admin/conf.php<br>';
var_dump(file_exists(__DIR__ . '/../admin/conf.php'));
require_once __DIR__ . '/../admin/conf.php';


$messages = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $firstName = trim($_POST['first_name']);
    $lastName = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($firstName) || empty($lastName) || empty($email) || empty($password)) {
        $messages[] = ['type' => 'danger', 'text' => 'Please fill in all fields.'];
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $messages[] = ['type' => 'danger', 'text' => 'Invalid email format.'];
    } else {
        $fullName = $firstName . ' ' . $lastName;
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $role = 'customer';  // fixed role to customer

        try {
            // Check if email exists
            $stmtCheck = $conn->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
            $stmtCheck->bindParam(':email', $email);
            $stmtCheck->execute();

            if ($stmtCheck->fetchColumn() > 0) {
                $messages[] = ['type' => 'danger', 'text' => 'Email already registered.'];
            } else {
                $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (:name, :email, :password, :role)");
                $stmt->bindParam(':name', $fullName);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':password', $hashedPassword);
                $stmt->bindParam(':role', $role);
                $stmt->execute();

                $messages[] = ['type' => 'success', 'text' => 'Registration successful! You can now <a href="index.php?p=login">login</a>.'];
            }
        } catch (PDOException $e) {
            $messages[] = ['type' => 'danger', 'text' => 'Error: ' . $e->getMessage()];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Customer Registration</title>
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
      width: 100%;
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
    .main {
      max-width: 400px;
      margin: 30px auto;
      padding: 20px;
      box-shadow: 0 4px 10px rgb(0 0 0 / 0.1);
      border-radius: 10px;
      font-family: Arial, sans-serif;
    }

  </style>
</head>
<body>
  <div class="main ">
    <h4>Customer Registration</h4>

    <!-- Display messages -->
    <?php if (!empty($messages)): ?>
      <?php foreach ($messages as $msg): ?>
        <div class="alert alert-<?php echo $msg['type']; ?>">
          <?php echo $msg['text']; ?>
          <button type="button" class="btn-close" onclick="this.parentElement.style.display='none';">&times;</button>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>

    <form method="POST" action="" style="margin-top: 100px;">
      <label for="firstName" class="form-label">First Name</label>
      <input type="text" class="form-control" id="firstName" name="first_name" required />

      <label for="lastName" class="form-label">Last Name</label>
      <input type="text" class="form-control" id="lastName" name="last_name" required />

      <label for="email" class="form-label">Email Address</label>
      <input type="email" class="form-control" id="email" name="email" required />

      <label for="password" class="form-label">Password</label>
      <input type="password" class="form-control" id="password" name="password" required />

      <button type="submit" class="btn-success">Register</button>
    </form>
  </div>
</body>
</html>
