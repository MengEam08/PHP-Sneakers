<!-- navbar.php -->
<div class="container-fluid fixed-top">
    <div class="container topbar bg-primary d-none d-lg-block">
        <div class="d-flex justify-content-between">
            <div class="top-info ps-2">
               
            </div>
            
        </div>
    </div>
    <div class="container px-0">
        <nav class="navbar navbar-light bg-white navbar-expand-xl">
            <a href="index.php" class="navbar-brand">
                <h1 class="text-primary display-6">Sneakers</h1>
            </a>
            <button class="navbar-toggler py-2 px-3" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                <span class="fa fa-bars text-primary"></span>
            </button>
            <div class="collapse navbar-collapse bg-white" id="navbarCollapse">
                <div class="navbar-nav mx-auto">
                    <a href="index.php" class="nav-item nav-link <?= $p === 'home' ? 'active' : '' ?>">Home</a>
                    <a href="index.php?p=shop" class="nav-item nav-link <?= $p === 'shop' ? 'active' : '' ?>">Shop</a>
                    <a href="contact.php" class="nav-item nav-link">Contact</a>
                    <!-- <a href="index.php?p=404" class="dropdown-item">404 Page</a> -->
                </div>
                <div class="d-flex m-3 me-0">
                    
                    <a href="index.php?p=cart" class="position-relative me-4 my-auto">
                        <i class="fa fa-shopping-bag fa-2x"></i>
                        <span class="position-absolute bg-secondary rounded-circle d-flex align-items-center justify-content-center text-dark px-1"
                              style="top: -5px; left: 15px; height: 20px; min-width: 20px;">3</span>
                    </a>
                   
                    <a class="my-auto" href="<?= isset($_SESSION['customer_logged_in']) && $_SESSION['customer_logged_in'] ? 'dashboard.php' : 'index.php?p=login' ?>">
                        <i class="fas fa-user fa-2x"></i>
                    </a>

                </div>
            </div>
        </nav>
    </div>
</div>
