<?php
require_once 'conf.php';
$messages = [];

// Handle POST request for status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'], $_POST['sale_id'])) {
    $sale_id = $_POST['sale_id'];
    $new_status = $_POST['update_status'];

    $stmt = $conn->prepare("UPDATE sales SET status = :status WHERE id = :id");
    if ($stmt->execute([':status' => $new_status, ':id' => $sale_id])) {
        $messages[] = ['type' => 'success', 'text' => "Status updated successfully for Sale ID $sale_id."];
    } else {
        $messages[] = ['type' => 'danger', 'text' => "Failed to update status."];
    }
}

// Fetch all sales
$stmt = $conn->query("SELECT * FROM sales ORDER BY sale_date DESC");
$sales = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Sales List</title>
  <style>
    h4 {
      margin-bottom: 20px;
      color: #333;
    }

    .alert {
      padding: 15px;
      border-radius: 5px;
      margin-bottom: 20px;
    }

    .alert-success { background-color: #d4edda; color: #155724; }
    .alert-danger { background-color: #f8d7da; color: #721c24; }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 30px;
    }

    th, td {
      padding: 12px;
      border: 1px solid #ccc;
      text-align: left;
    }

    th {
      background-color: #FEB424;
      color: white;
    }

    .form-select {
      padding: 6px 10px;
      border-radius: 3px;
      font-size: 14px;
    }

    .btn-submit {
      padding: 6px 10px;
      background-color: #FEB424;
      color: white;
      border: none;
      border-radius: 3px;
      cursor: pointer;
    }

    .btn-submit:hover {
      background-color: #0056b3;
    }
     .toast-container {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
  }

  .toast {
    display: flex;
    align-items: center;
    background-color: #333;
    color: #fff;
    padding: 12px 18px;
    margin-bottom: 10px;
    border-radius: 5px;
    min-width: 250px;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
    opacity: 0.95;
    animation: slideIn 0.3s ease, fadeOut 0.5s ease 3s forwards;
  }

  .toast-success { background-color: #28a745; }
  .toast-danger { background-color: #dc3545; }

  @keyframes slideIn {
    from { transform: translateX(100%); }
    to { transform: translateX(0); }
  }

  @keyframes fadeOut {
    to { opacity: 0; transform: translateX(100%); }
  }
  </style>
</head>
<body>

<div class="container">
  <div class="main">
    <h4>Sales List</h4>

    <!-- Alerts -->
    <?php foreach ($messages as $msg): ?>
      <div class="alert alert-<?php echo $msg['type']; ?>">
        <?php echo $msg['text']; ?>
      </div>
    <?php endforeach; ?>

    <!-- Sales Table -->
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>User ID</th>
          <th>Product ID</th>
          <th>Quantity</th>
          <th>Total Price</th>
          <th>Sale Date</th>
          <th>Status</th>
          <th>Mobile</th>
          <th>Email</th>
        </tr>
      </thead>
      <tbody style="background-color: #E7F2CE;">
        <?php foreach ($sales as $sale): ?>
          <tr>
            <td><?php echo $sale['id']; ?></td>
            <td><?php echo $sale['user_id']; ?></td>
            <td><?php echo $sale['product_id']; ?></td>
            <td><?php echo $sale['quantity']; ?></td>
            <td>$<?php echo number_format($sale['total_price'], 2); ?></td>
            <td><?php echo $sale['sale_date']; ?></td>
            <td>
              <form method="POST" style="display: flex; gap: 5px; align-items: center;">
                <input type="hidden" name="sale_id" value="<?php echo $sale['id']; ?>">
                <select name="update_status" class="form-select">
                  <option value="Pending" <?php echo $sale['status'] === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                  <option value="Paid" <?php echo $sale['status'] === 'Paid' ? 'selected' : ''; ?>>Paid</option>
                </select>
                <button type="submit" class="btn-submit">Update</button>
              </form>
            </td>
            <td><?php echo htmlspecialchars($sale['mobile']); ?></td>
            <td><?php echo htmlspecialchars($sale['email']); ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

  </div>
</div>
<?php if (!empty($messages)): ?>
  <div class="toast-container" id="toastContainer">
    <?php foreach ($messages as $msg): ?>
      <div class="toast toast-<?php echo $msg['type']; ?>">
        <?php echo json_encode($msg['text']); ?>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>
<script>
  setTimeout(() => {
    const container = document.getElementById('toastContainer');
    if (container) container.remove();
  }, 4000); // remove after 4s
</script>

</body>
</html>
