<?php
    $page = "./pages/home.php"; // default
    $p = "home";
    $hero = true;
    $navbar = true;
    $footer = true;

    if (isset($_GET['p'])) {
        $p = $_GET['p'];
        switch ($p) {
            case "shop":
                $page = "./pages/products.php";
                $hero = false;
                break;
            case "shop-detail":
                $page = "./pages/productDetail.php";
                $hero = false;
                break;
            case "cart":
                $page = "./pages/cart.php";
                $hero = false;
                break;
            case "checkout":
                $page = "./pages/checkout.php";
                $hero = false;
                break;
            case "login":
                $page = "./pages/login.php";
                $hero = false;
                $navbar = false;
                $footer= false;
                break;
            case "contact":
                $page = "./pages/contact.php";
                $hero = false;
                break;
            case "register":
                $page = "./pages/register.php";
                $hero = false;
                break;
            default:
                $page = "./pages/404.php";
                $hero = false;
                break;
        }

    }
?>
<!DOCTYPE html>
<html lang="en">
<?php include "./includes/head.php"; ?>
<body>

    <?php include "./components/navbar.php"; ?>
    <?php include "./components/searchModal.php"; ?>

    <?php if ($hero) include "./components/heroSection.php"; ?>

    <main>
        <?php include $page; ?>
    </main>

    <?php include "./components/footer.php"; ?>
    <?php include "./includes/footer-scripts.php"; ?>
</body>
</html>
