<div class="container">
  
<div class="navigation">
        <ul>
          <li>
            <a href="index.php">
                <img src="imgs/admin-logo.png" class="admin-logo"alt="">
            </a>
          </li>

          <li class="<?=($p=="dashboard"?'active':'')?>">
            <a href="index.php">
              <span class="icon">
                <ion-icon name="home-outline"></ion-icon>
              </span>
              <span class="title">Dashboard</span>
            </a>
          </li>

          <li class="<?=($p=="customer"?'active':'') ?>">
            <a href="index.php?p=customer">
              <span class="icon">
                <ion-icon name="people-outline"></ion-icon>
              </span>
              <span class="title">Customers</span>
            </a>
          </li>

          <li class="<?=($p=="product"?'active':'')?>">
            <a href="index.php?p=product">
              <span class="icon">
              <ion-icon name="bag-remove-outline"></ion-icon> 
              </span>
              <span class="title">Products</span>
            </a>
          </li>

          <li class="<?=($p=="category"?'active':'')?>">
            <a href="index.php?p=category">
              <span class="icon">
              <ion-icon name="copy-outline"></ion-icon>
              </span>
              <span class="title">Category</span>
            </a>
          </li>
        </li>
          <li class="<?=($p=="category"?'active':'')?>">
            <a href="index.php?p=order">
              <span class="icon">
              <ion-icon name="copy-outline"></ion-icon>
              </span>
              <span class="title">Orders</span>
            </a>
          </li>

          <li>
            <a href="index.php?p=logout">
              <span class="icon">
                <ion-icon name="log-out-outline"></ion-icon>
              </span>
              <span class="title">Sign Out</span>
            </a>
          </li>
        </ul>
      </div>
</div>