<?php
// Database connection
$host = "localhost";
$user = "root";
$pass = ""; // or your MySQL password
$db   = "household_system";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Get current page for active state
$current_page = basename($_SERVER['PHP_SELF']);

// Fetch dashboard stats
$totalTenants = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM tenants"))['total'];
$overdueTenants = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS overdue FROM tenants WHERE balance > 5000"))['overdue'];

// Get current month's collected payments
$currentMonth = date('Y-m'); // Format: 2025-05
$paymentQuery = mysqli_query($conn, "SELECT SUM(amount) AS total FROM payments WHERE DATE_FORMAT(payment_date, '%Y-%m') = '$currentMonth'");
$collectedAmount = mysqli_fetch_assoc($paymentQuery)['total'] ?? 0;

$tenants = mysqli_query($conn, "SELECT * FROM tenants ORDER BY id ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="style.css" />
  <style>
    .status.ok {
      color: green;
      font-weight: bold;
    }
    .status.overdue {
      color: red;
      font-weight: bold;
    }
  </style>
</head>
<body>
  <!-- Header Navigation - This will stay consistent across pages -->
  <header class="main-header">
    <div class="logo">
      <h2>Household Management</h2>
    </div>
    <nav>
      <ul>
        <li><a href="dashboard.php" class="<?php echo $current_page == 'dashboard.php' ? 'active' : ''; ?>">Dashboard</a></li>
        <li><a href="tenants.php" class="<?php echo $current_page == 'tenants.php' ? 'active' : ''; ?>">Tenants</a></li>
        <li><a href="payments.php" class="<?php echo $current_page == 'payments.php' ? 'active' : ''; ?>">Payments</a></li>
      </ul>
    </nav>
  </header>

  <div class="container">
    <h1>Admin Dashboard</h1>

    <!-- Summary Cards -->
    <div class="cards">
      <div class="card">
        <h2>Total Tenants</h2>
        <p><?php echo $totalTenants; ?></p>
      </div>
      <div class="card">
        <h2>Collected This Month</h2>
        <p>KSH <?php echo number_format($collectedAmount); ?></p>
      </div>
      <div class="card">
        <h2>Overdue Tenants</h2>
        <p><?php echo $overdueTenants; ?></p>
      </div>
    </div>

    <!-- Tenant Balance Table -->
    <div class="table-container">
      <h2>Tenant Balances</h2>
      <table>
        <thead>
          <tr>
            <th>#</th>
            <th>Name</th>
            <th>Balance (KSH)</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $i = 1;
          while ($row = mysqli_fetch_assoc($tenants)) {
              $status = $row['balance'] > 5000 ? 'Overdue' : 'OK';
              $class = $row['balance'] > 5000 ? 'overdue' : 'ok';
              echo "<tr>
                      <td>{$i}</td>
                      <td>{$row['name']}</td>
                      <td>" . number_format($row['balance'], 2) . "</td>
                      <td><span class='status {$class}'>{$status}</span></td>
                    </tr>";
              $i++;
          }
          ?>
        </tbody>
      </table>
    </div>
  </div>

</body>
</html>