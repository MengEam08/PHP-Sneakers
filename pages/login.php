<?php
session_start();
require_once './admin/conf.php';

$messages = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $messages[] = ['type' => 'danger', 'text' => 'Please enter both email and password.'];
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $messages[] = ['type' => 'danger', 'text' => 'Invalid email format.'];
    } else {
        try {
            $stmt = $conn->prepare("SELECT id, name, email, password, role FROM users WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                if ($user['role'] === 'customer') {
                    $_SESSION['customer_logged_in'] = true;
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['name'];
                    header("Location: dashboard.php");
                    exit();
                } else {
                    $messages[] = ['type' => 'danger', 'text' => 'Access denied: not a customer.'];
                }
            } else {
                $messages[] = ['type' => 'danger', 'text' => 'Invalid email or password.'];
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
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Login - Tidi</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
<style>
   
    .main-content { width: 50%; border-radius: 20px; box-shadow: 0 5px 5px rgba(0,0,0,.4); margin: 5em auto; display: flex; overflow: hidden; }
    .company__info { background-color: #81C408; border-top-left-radius: 20px; border-bottom-left-radius: 20px; display: flex; flex-direction: column; justify-content: center; align-items: center; color: #fff; padding: 2em; width: 30%; }
    .fa-android { font-size: 3em; }
    .login_form { background-color: #fff; border-top-right-radius: 20px; border-bottom-right-radius: 20px; border-left: 1px solid #ccc; padding: 2em; width: 60%; }
    form { width: 100%; }
    .form__input { width: 100%; padding: 12px; margin-bottom: 15px; border-radius: 30px; border: 1px solid #ccc; }
    .form__input:focus { outline: none; border-color: #81C408; }
    .btn { width: 100%; padding: 12px; background-color: #81C408; color: #fff; border: none; border-radius: 30px; font-size: 16px; cursor: pointer; margin-top: 10px; }
    .btn:hover { background-color: #FEB999; }
    .message {
        text-align: center;
        margin-bottom: 10px;
        font-weight: bold;
    }
    .message.danger { color: red; }
    .message.success { color: green; }
    .footer { text-align: center; margin-top: 2em; font-size: 14px; }
    @media screen and (max-width: 640px) {
        .main-content { width: 90%; flex-direction: column; }
        .company__info { display: none; }
        .login_form { border-radius: 20px; width: 100%; }
    }
    @media screen and (min-width: 641px) and (max-width: 800px) {
        .main-content { width: 70%; }
    }
    h2 { color: #81C408; text-align: center; margin-bottom: 20px; }
    .card{
        margin-top: 100px;
    }
</style>
</head>
<body>
    <div class="card">
        <div class="main-content " >
    <div class="company__info">
    </div>
    <div class="login_form" >
        <h2>Log In</h2>

        <?php if (!empty($messages)): ?>
            <?php foreach ($messages as $msg): ?>
                <div class="message <?= htmlspecialchars($msg['type']) ?>">
                    <?= htmlspecialchars($msg['text']) ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>


        <form method="POST" action="">
            <input
                type="text"
                name="email"
                class="form__input"
                placeholder="Username or Email"
                required
            />
            <input
                type="password"
                name="password"
                class="form__input"
                placeholder="Password"
                required
            />
            <input type="hidden" name="csrf_token" />
            <button type="submit" class="btn">Log In</button>
        </form>
        <p style="text-align:center; margin-top:15px;">
            Don't have an account?
            <a href="index.php?p=register" style="color: #FEB999;">Register here</a>
        </p>
    </div>
</div>
</div>

</body>
</html>
