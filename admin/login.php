<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Login / Register</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
  <link rel="icon" type="image/png" href="imgs/admin-logo.png" />
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: "Poppins", sans-serif;
    }

    body {
      background: linear-gradient(to right, rgb(242, 209, 163), #c9d6ff);
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .container {
      background: #fff;
      width: 400px;
      padding: 2rem;
      border-radius: 10px;
      box-shadow: 0 15px 30px rgba(223, 167, 108, 0.1);
      text-align: center;
    }

    .form-title {
      font-size: 1.8rem;
      font-weight: bold;
      margin-bottom: 1rem;
      color: #333;
    }

    .input-group {
      margin-bottom: 1.5rem;
      position: relative;
    }

    .input-group i {
      position: absolute;
      left: 12px;
      top: 50%;
      transform: translateY(-50%);
      color: #757575;
    }

    input {
      width: 100%;
      padding: 12px 12px 12px 40px;
      border: 1px solid #ddd;
      border-radius: 5px;
      font-size: 16px;
      color: #333;
      transition: border-color 0.3s;
    }

    input:focus {
      border-color: #e89d2e;
      outline: none;
    }

    input::placeholder {
      color: #aaa;
    }

    label {
      position: absolute;
      top: -8px;
      left: 10px;
      font-size: 12px;
      color: #757575;
      background: #fff;
      padding: 0 4px;
    }

    .btn {
      width: 100%;
      padding: 12px;
      background-color: #e89d2e;
      color: white;
      border: none;
      border-radius: 5px;
      font-size: 1.1rem;
      cursor: pointer;
      transition: background-color 0.3s;
    }

    .btn:hover {
      background-color: rgb(230, 179, 113);
    }

    .or {
      font-size: 1rem;
      margin: 1.5rem 0;
      color: #777;
    }

    .icons {
      text-align: center;
      margin: 1rem 0;
    }

    .icons i {
      color: #e89d2e;
      font-size: 1.5rem;
      margin: 0 10px;
      cursor: pointer;
      transition: all 0.3s;
    }

    .icons i:hover {
      transform: scale(1.1);
      color: #470f76;
    }

    .links {
      font-size: 1rem;
      display: flex;
      justify-content: space-between;
      margin-top: 1rem;
      font-weight: bold;
      color: #555;
    }

    .links a {
      text-decoration: none;
      color: #e89d2e;
    }

    .links a:hover {
      text-decoration: underline;
    }

    button {
      background: none;
      border: none;
      color: #e89d2e;
      font-weight: bold;
      cursor: pointer;
    }

    button:hover {
      text-decoration: underline;
    }

    .recover {
      margin-bottom: 1.5rem;
      text-align: right;
    }

    .recover a {
      color: #e89d2e;
      font-weight: 600;
      text-decoration: none;
    }

    .recover a:hover {
      text-decoration: underline;
    }
  </style>
</head>


<?php
session_start();
require_once '../admin/conf.php'; // Your DB connection file

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Fetch user from DB
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && $password === $user['password']) { // use password_verify if hashed
        if ($user['role'] === 'admin') {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            header("Location: index.php"); // Admin dashboard
            exit();
        } else {
            $_SESSION['customer_logged_in'] = true;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            header("Location: index.php"); // Customer-facing site
            exit();
        }
    } else {
        header("Location: login.php?error=1");
        exit();
    }
}
?>



<body>
  <!-- REGISTER FORM -->
  <div class="container" id="signup" style="display:none;">
    <h1 class="form-title">Register</h1>
    <form method="post" action="register.php">
      <div class="input-group">
        <i class="fas fa-user"></i>
        <input type="text" name="fName" id="fName-signup" placeholder="First Name" required />
        <label for="fName-signup">First Name</label>
      </div>
      <div class="input-group">
        <i class="fas fa-user"></i>
        <input type="text" name="lName" id="lName-signup" placeholder="Last Name" required />
        <label for="lName-signup">Last Name</label>
      </div>
      <div class="input-group">
        <i class="fas fa-envelope"></i>
        <input type="email" name="email" id="email-signup" placeholder="Email" required />
        <label for="email-signup">Email</label>
      </div>
      <div class="input-group">
        <i class="fas fa-lock"></i>
        <input type="password" name="password" id="password-signup" placeholder="Password" required />
        <label for="password-signup">Password</label>
      </div>
      <input type="submit" class="btn" value="Sign Up" name="signUp" />
    </form>
    <p class="or">----------or----------</p>
    <div class="icons">
      <i class="fab fa-google"></i>
      <i class="fab fa-facebook"></i>
    </div>
    <div class="links">
      <p>Already Have Account ?</p>
      <button id="signInButton">Sign In</button>
    </div>
  </div>

  <!-- SIGN IN FORM -->
  <div class="container" id="signIn">
    <h1 class="form-title">Sign In</h1>
    <form method="post" action="login.php">
      <div class="input-group">
        <i class="fas fa-envelope"></i>
        <input type="email" name="email" id="email-signin" placeholder="Email" required />
        <label for="email-signin">Email</label>
      </div>
      <div class="input-group">
        <i class="fas fa-lock"></i>
        <input type="password" name="password" id="password-signin" placeholder="Password" required />
        <label for="password-signin">Password</label>
      </div>
      <p class="recover">
        <a href="#">Recover Password</a>
      </p>
      <input type="submit" class="btn" value="Sign In" name="signIn" />
    </form>
    <p class="or">----------or----------</p>
    <div class="icons">
      <i class="fab fa-google"></i>
      <i class="fab fa-facebook"></i>
    </div>
    <div class="links">
      <p>Don't have an account yet?</p>
      <button id="signUpButton">Sign Up</button>
    </div>
  </div>

  <script>
    const signUpButton = document.getElementById("signUpButton");
    const signInButton = document.getElementById("signInButton");
    const signInForm = document.getElementById("signIn");
    const signUpForm = document.getElementById("signup");

    signUpButton.addEventListener("click", function (e) {
      e.preventDefault();
      signInForm.style.display = "none";
      signUpForm.style.display = "block";
    });

    signInButton.addEventListener("click", function (e) {
      e.preventDefault();
      signInForm.style.display = "block";
      signUpForm.style.display = "none";
    });
  </script>
</body>
</html>
