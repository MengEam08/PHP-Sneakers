<?php
  @include '../admin/conf.php';

  // Fetch total products
  $product_count = $conn->query("SELECT COUNT(*) FROM product")->fetchColumn();

  // Fetch total customers
  $customer_count = $conn->query("SELECT COUNT(*) FROM users WHERE role = 'customer'")->fetchColumn();
  $sales_count = $conn->query("SELECT COUNT(*) FROM sales")->fetchColumn();

  // Fetch total earnings where payment_status is 'Paid'
  $earnings = $conn->query("SELECT SUM(amount) FROM payment WHERE payment_status = 'Paid'")->fetchColumn();
  $earnings = $earnings ? $earnings : 0; // Avoid null display
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

                    <div class="iconBx">
                    <ion-icon name="cube-outline"></ion-icon>
                    </div>
                </div>

                <div class="card">
                    <div>
                      <div class="numbers"><?php echo $sales_count; ?></div>
                      <div class="cardName">Sales</div>
                    </div>

                    <div class="iconBx">
                    <ion-icon name="cart-outline"></ion-icon>
                    </div>
                </div>

                <div class="card">
                    <div>
                      <div class="numbers"><?php echo $customer_count; ?></div>
                      <div class="cardName">Customers</div>

                    </div>

                    <div class="iconBx">
                    <ion-icon name="chatbubbles-outline"></ion-icon>
                    </div>
                </div>

                <div class="card">
                    <div>
                      <div class="numbers">$<?php echo number_format($earnings, 2); ?></div>
                      <div class="cardName">Earning</div>
                    </div>

                    <div class="iconBx">
                    <ion-icon name="cash-outline"></ion-icon>
                    </div>
                </div>
            </div>
            <div class="details">
          <div class="recentOrders">
            <div class="cardHeader">
              <h2>Recent Orders</h2>
              <a href="#" class="btn">View All</a>
            </div>

            <table>
              <thead>
                <tr>
                  <td>Name</td>
                  <td>Price</td>
                  <td>Payment</td>
                  <td>Status</td>
                </tr>
              </thead>

              <tbody>
                <tr>
                  <td>Star Refrigerator</td>
                  <td>$1200</td>
                  <td>Paid</td>
                  <td><span class="status delivered">Delivered</span></td>
                </tr>

                <tr>
                  <td>Dell Laptop</td>
                  <td>$110</td>
                  <td>Due</td>
                  <td><span class="status pending">Pending</span></td>
                </tr>

                <tr>
                  <td>Apple Watch</td>
                  <td>$1200</td>
                  <td>Paid</td>
                  <td><span class="status return">Return</span></td>
                </tr>

                
              </tbody>
            </table>
          </div>

          <!-- ================= New Customers ================ -->
          <div class="recentCustomers">
            <div class="cardHeader">
              <h2>Recent Customers</h2>
            </div>

            <table>
              <tr>
                <td width="60px">
                  <div class="imgBx">
                    <img src="imgs/customer02.jpg" alt="" />
                  </div>
                </td>
                <td>
                  <h4>
                    David <br />
                    <span>Italy</span>
                  </h4>
                </td>
              </tr>

              

              <tr>
                <td width="60px">
                  <div class="imgBx">
                    <img src="imgs/customer01.jpg" alt="" />
                  </div>
                </td>
                <td>
                  <h4>
                    Amit <br />
                    <span>India</span>
                  </h4>
                </td>
              </tr>

              <tr>
                <td width="60px">
                  <div class="imgBx">
                    <img src="imgs/customer01.jpg" alt="" />
                  </div>
                </td>
                <td>
                  <h4>
                    David <br />
                    <span>Italy</span>
                  </h4>
                </td>
              </tr>

              <tr>
                <td width="60px">
                  <div class="imgBx">
                    <img src="imgs/customer02.jpg" alt="" />
                  </div>
                </td>
                <td>
                  <h4>
                    Amit <br />
                    <span>India</span>
                  </h4>
                </td>
              </tr>
            </table>
          </div>
        </div>
    </div>
</div>