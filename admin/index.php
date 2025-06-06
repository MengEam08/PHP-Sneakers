<?php
session_start();

// Redirect to login if admin is not logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

// Default page settings
$page = "dashboard.php";
$p = "dashboard";

// Handle routing
if (isset($_GET['p'])) {
    $p = $_GET['p'];
    switch ($p) {
        case "customer":
            $page = "customer.php";
            break;
        case "product":
            $page = "product.php";
            break;
        case "category":
            $page = "category.php";
            break;
        case "logout":
            $page = "logout.php";
            break;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
  <?php include "includes/head.php"; ?>

  <body>
    <div class="container">
      <?php include "includes/sidebar.php"; ?>
      <?php include "$page"; ?>
    </div>

    <!-- Scripts -->
    <script src="js/main.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
  </body>
</html>
