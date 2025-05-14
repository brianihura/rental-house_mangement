<?php
// DB connection
$conn = mysqli_connect("localhost", "root", "", "household_system");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Get overdue tenants (balance > 5000)
$query = "SELECT * FROM tenants WHERE balance > 5000";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Tenant Notifications</title>
  <link rel="stylesheet" href="style.css">
  <style>
    .container {
      max-width: 900px;
      margin: 30px auto;
      padding: 20px;
      background: #fff;
      border-radius: 10px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }
    th, td {
      padding: 12px;
      border: 1px solid #ccc;
      text-align: center;
    }
    th {
      background: #f4f4f4;
    }
    .status-overdue {
      color: red;
      font-weight: bold;
    }
    .btn-notify {
      padding: 6px 12px;
      background: crimson;
      color: white;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }
    .btn-notify:hover {
      background: darkred;
    }
  </style>
</head>
<body>

<div class="container">
  <h1>Overdue Tenant Notifications</h1>

  <?php if (mysqli_num_rows($result) > 0): ?>
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Tenant Name</th>
          <th>Phone</th>
          <th>Balance (KSH)</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php $i = 1; while ($row = mysqli_fetch_assoc($result)): ?>
          <tr>
            <td><?= $i++; ?></td>
            <td><?= htmlspecialchars($row['name']); ?></td>
            <td><?= htmlspecialchars($row['phone']); ?></td>
            <td><?= number_format($row['balance'], 2); ?></td>
            <td><span class="status-overdue">Overdue</span></td>
            <td><button class="btn-notify">Send Reminder</button></td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  <?php else: ?>
    <p>No overdue tenants at the moment.</p>
  <?php endif; ?>
</div>

</body>
</html>
