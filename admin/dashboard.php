<?php
@include '../admin/conf.php';

// Fetch total counts
$product_count = $conn->query("SELECT COUNT(*) FROM product")->fetchColumn();
$customer_count = $conn->query("SELECT COUNT(*) FROM users WHERE role = 'customer'")->fetchColumn();
$sales_count = $conn->query("SELECT COUNT(*) FROM sales")->fetchColumn();
$earnings = $conn->query("SELECT SUM(amount) FROM payment WHERE payment_status = 'Paid'")->fetchColumn();
$earnings = $earnings ? $earnings : 0;

// Fetch recent sales (latest 5)
$recent_sales = $conn->query("
    SELECT s.*, p.name AS product_name
    FROM sales s
    JOIN product p ON s.product_id = p.id
    ORDER BY s.sale_date DESC
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

// Fetch recent customers (latest 5)
$recent_customers = $conn->query("
    SELECT * FROM users 
    WHERE role = 'customer' 
    ORDER BY created_at DESC 
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
  <div class="main">
    <div class="topbar">
      <div class="toggle">
        <ion-icon name="menu-outline"></ion-icon>
      </div>
    </div>

    <div class="cardBox">
      <div class="card">
        <div>
          <div class="numbers"><?php echo $product_count; ?></div>
          <div class="cardName">Products</div>
        </div>
        <div class="iconBx"><ion-icon name="cube-outline"></ion-icon></div>
      </div>

      <div class="card">
        <div>
          <div class="numbers"><?php echo $sales_count; ?></div>
          <div class="cardName">Sales</div>
        </div>
        <div class="iconBx"><ion-icon name="cart-outline"></ion-icon></div>
      </div>

      <div class="card">
        <div>
          <div class="numbers"><?php echo $customer_count; ?></div>
          <div class="cardName">Customers</div>
        </div>
        <div class="iconBx"><ion-icon name="people-outline"></ion-icon></div>
      </div>

      <div class="card">
        <div>
          <div class="numbers">$<?php echo number_format($earnings, 2); ?></div>
          <div class="cardName">Earnings</div>
        </div>
        <div class="iconBx"><ion-icon name="cash-outline"></ion-icon></div>
      </div>
    </div>

    <div class="details">
      <!-- ========== Recent Sales ========== -->
      <div class="recentOrders">
        <div class="cardHeader">
          <h2>Recent Sales</h2>
          <a href="#" class="btn">View All</a>
        </div>

        <table>
          <thead>
            <tr>
              <td>Product</td>
              <td>Price</td>
              <td>Payment</td>
              <td>Status</td>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($recent_sales as $sale): ?>
              <tr>
                <td><?php echo htmlspecialchars($sale['product_name']); ?></td>
                <td>$<?php echo number_format($sale['total_price'], 2); ?></td>
                <td><?php echo htmlspecialchars($sale['status']); ?></td>
                <td>
                  <span class="status <?php echo $sale['status'] === 'Paid' ? 'delivered' : 'pending'; ?>">
                    <?php echo $sale['status'] === 'Paid' ? 'Delivered' : 'Pending'; ?>
                  </span>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <!-- ========== Recent Customers ========== -->
      <div class="recentCustomers">
        <div class="cardHeader">
          <h2>Recent Customers</h2>
        </div>

        <table>
          <?php foreach ($recent_customers as $customer): ?>
            <tr>
              <td width="60px">
                <div class="imgBx">
                  <img src="https://bootdey.com/img/Content/avatar/avatar6.png" alt="User" />
                </div>
              </td>
              <td>
                <h4>
                  <?php echo htmlspecialchars($customer['name']); ?><br />
                  <span><?php echo htmlspecialchars($customer['email']); ?></span>
                </h4>
              </td>
            </tr>
          <?php endforeach; ?>
        </table>
      </div>
    </div>
  </div>
</div>
