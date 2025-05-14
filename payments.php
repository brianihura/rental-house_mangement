<?php
// Database connection
$host = "localhost";
$user = "root";
$pass = "";
$db   = "household_system";

$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Handle new payment form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tenant_id = $_POST['tenant_id'];
    $amount = $_POST['amount'];

    // Insert payment record
    $insert = "INSERT INTO payments (tenant_id, amount) VALUES ('$tenant_id', '$amount')";
    mysqli_query($conn, $insert);

    // Update tenant balance
    $update = "UPDATE tenants SET balance = balance - $amount WHERE id = $tenant_id";
    mysqli_query($conn, $update);

    header("Location: payments.php");
    exit();
}

// Fetch tenants for the dropdown
$tenants = mysqli_query($conn, "SELECT * FROM tenants");

// Fetch payment history
$payments = mysqli_query($conn, "
  SELECT payments.*, tenants.name 
  FROM payments 
  JOIN tenants ON payments.tenant_id = tenants.id 
  ORDER BY payments.payment_date DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Payment Management</title>
  <link rel="stylesheet" href="style.css" />
  <style>
    .form-group { margin-bottom: 15px; }
    label { display: block; margin-bottom: 5px; }
    input, select { padding: 8px; width: 100%; }
    .btn { padding: 10px 20px; background: #007BFF; color: white; border: none; cursor: pointer; }
    .btn:hover { background: #0056b3; }
    table { width: 100%; border-collapse: collapse; margin-top: 30px; }
    th, td { padding: 12px; border: 1px solid #ddd; text-align: center; }
  </style>
</head>
<body>

<div class="container">
  <h1>Payment Management</h1>

  <!-- New Payment Form -->
  <form method="POST" action="payments.php">
    <div class="form-group">
      <label for="tenant_id">Select Tenant</label>
      <select name="tenant_id" required>
        <option value="">-- Choose --</option>
        <?php while ($tenant = mysqli_fetch_assoc($tenants)): ?>
          <option value="<?= $tenant['id']; ?>"><?= htmlspecialchars($tenant['name']); ?></option>
        <?php endwhile; ?>
      </select>
    </div>
    <div class="form-group">
      <label for="amount">Payment Amount</label>
      <input type="number" name="amount" step="0.01" required />
    </div>
    <button type="submit" class="btn">Record Payment</button>
  </form>

  <!-- Payment History Table -->
  <h2>Payment History</h2>
  <table>
    <thead>
      <tr>
        <th>#</th>
        <th>Tenant</th>
        <th>Amount (KSH)</th>
        <th>Date</th>
      </tr>
    </thead>
    <tbody>
      <?php $i = 1; while ($row = mysqli_fetch_assoc($payments)): ?>
        <tr>
          <td><?= $i++; ?></td>
          <td><?= htmlspecialchars($row['name']); ?></td>
          <td><?= number_format($row['amount'], 2); ?></td>
          <td><?= date("d M Y H:i", strtotime($row['payment_date'])); ?></td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>

</body>
</html>
