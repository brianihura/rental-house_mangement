<?php
// includes/db.php — replace this with your real DB connection
$host = "localhost";
$user = "root";          // your MySQL username
$pass = "";              // your MySQL password
$db   = "household_system";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Handle Add Tenant
if (isset($_POST['add_tenant'])) {
    $name = $_POST['name'];
    $account_number = $_POST['account_number'];
    $phone = $_POST['phone'];
    $balance = $_POST['balance'];

    $query = "INSERT INTO tenants (name, account_number, phone, balance) 
              VALUES ('$name', '$account_number', '$phone', '$balance')";
    mysqli_query($conn, $query);
    header('Location: tenants.php');
}

// Handle Edit Tenant
if (isset($_POST['edit_tenant'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $account_number = $_POST['account_number'];
    $phone = $_POST['phone'];
    $balance = $_POST['balance'];

    $query = "UPDATE tenants SET name='$name', account_number='$account_number', 
              phone='$phone', balance='$balance' WHERE id='$id'";
    mysqli_query($conn, $query);
    header('Location: tenants.php');
}

// Handle Delete Tenant
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $query = "DELETE FROM tenants WHERE id='$id'";
    mysqli_query($conn, $query);
    header('Location: tenants.php');
}

// Handle Add Payment
if (isset($_POST['add_payment'])) {
    $tenant_id = $_POST['tenant_id'];
    $amount = $_POST['amount'];
    $payment_date = $_POST['payment_date'];
    $payment_method = $_POST['payment_method'];
    $reference = $_POST['reference'];

    $query = "INSERT INTO payments (tenant_id, amount, payment_date, payment_method, reference) 
              VALUES ('$tenant_id', '$amount', '$payment_date', '$payment_method', '$reference')";
    
    if(mysqli_query($conn, $query)) {
        // Update tenant balance
        $update_balance = "UPDATE tenants SET balance = balance - $amount WHERE id = $tenant_id";
        mysqli_query($conn, $update_balance);
    }
    header('Location: tenants.php?view=' . $tenant_id);
}

// Fetch tenants from database
$query = "SELECT * FROM tenants ORDER BY id DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Tenant Management</title>
  <link rel="stylesheet" href="tenant.css" />
</head>
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
<body>
  <div class="container">
    <?php if(!isset($_GET['view']) && !isset($_GET['edit']) && !isset($_GET['add'])): ?>
    <!-- Main tenant list view -->
    <div class="actions">
      <a href="tenants.php?add=1" class="btn-primary">Add New Tenant</a>
    </div>
    
    <h2>Tenant List</h2>
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Name</th>
          <th>Account No.</th>
          <th>Phone</th>
          <th>Balance (KSH)</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $count = 1;
        while ($row = mysqli_fetch_assoc($result)) {
          echo "<tr>";
          echo "<td>{$count}</td>";
          echo "<td>{$row['name']}</td>";
          echo "<td>{$row['account_number']}</td>";
          echo "<td>{$row['phone']}</td>";
          echo "<td>{$row['balance']}</td>";
          echo "<td>
                  <a href='tenants.php?view={$row['id']}' class='btn-small view'>View</a>
                  <a href='tenants.php?edit={$row['id']}' class='btn-small'>Edit</a>
                  <a href='tenants.php?delete={$row['id']}' class='btn-small danger' onclick='return confirm(\"Are you sure?\")'>Delete</a>
                </td>";
          echo "</tr>";
          $count++;
        }
        ?>
      </tbody>
    </table>
    <?php endif; ?>
    
    <!-- Add Tenant Form -->
    <?php if(isset($_GET['add'])): ?>
    <div class="card">
      <div class="card-header">
        <h2>Add New Tenant</h2>
        <a href="tenants.php" class="btn-small back">Back to List</a>
      </div>
      <form action="tenants.php" method="POST">
        <label for="name">Name:</label>
        <input type="text" name="name" required />

        <label for="account_number">Account Number:</label>
        <input type="text" name="account_number" required />

        <label for="phone">Phone:</label>
        <input type="text" name="phone" required />

        <label for="balance">Balance (KSH):</label>
        <input type="number" name="balance" required />

        <button type="submit" name="add_tenant">Add Tenant</button>
      </form>
    </div>
    <?php endif; ?>

    <!-- View Tenant Payment History -->
    <?php
    if (isset($_GET['view'])) {
        $tenant_id = $_GET['view'];
        
        // Get tenant details
        $tenant_query = "SELECT * FROM tenants WHERE id='$tenant_id'";
        $tenant_result = mysqli_query($conn, $tenant_query);
        $tenant = mysqli_fetch_assoc($tenant_result);
        
        // Get payment history
        $payment_query = "SELECT * FROM payments WHERE tenant_id='$tenant_id' ORDER BY payment_date DESC";
        $payment_result = mysqli_query($conn, $payment_query);
    ?>
    <div class="tenant-details">
      <div class="card-header">
        <h2>Tenant Details: <?php echo $tenant['name']; ?></h2>
        <div class="header-actions">
          <a href="tenants.php" class="btn-small back">Back to List</a>
          <a href="tenants.php?add_payment=<?php echo $tenant_id; ?>" class="btn-small success">Add Payment</a>
        </div>
      </div>
      
      <div class="details-card">
        <p><strong>Account Number:</strong> <?php echo $tenant['account_number']; ?></p>
        <p><strong>Phone:</strong> <?php echo $tenant['phone']; ?></p>
        <p><strong>Current Balance:</strong> KSH <?php echo $tenant['balance']; ?></p>
      </div>
      
      <h3>Payment History</h3>
      <?php if(mysqli_num_rows($payment_result) > 0): ?>
      <table class="payment-history">
        <thead>
          <tr>
            <th>Date</th>
            <th>Amount (KSH)</th>
            <th>Method</th>
            <th>Reference</th>
          </tr>
        </thead>
        <tbody>
          <?php while($payment = mysqli_fetch_assoc($payment_result)): ?>
          <tr>
            <td><?php echo date('d M Y', strtotime($payment['payment_date'])); ?></td>
            <td><?php echo $payment['amount']; ?></td>
            <td><?php echo $payment['payment_method']; ?></td>
            <td><?php echo $payment['reference']; ?></td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
      <?php else: ?>
        <p class="no-records">No payment records found for this tenant.</p>
      <?php endif; ?>
    </div>
    <?php
    }
    ?>
    
    <!-- Add Payment Form -->
    <?php if(isset($_GET['add_payment'])): 
        $tenant_id = $_GET['add_payment'];
        $tenant_query = "SELECT name FROM tenants WHERE id='$tenant_id'";
        $tenant_result = mysqli_query($conn, $tenant_query);
        $tenant = mysqli_fetch_assoc($tenant_result);
    ?>
    <div class="card">
      <div class="card-header">
        <h2>Add Payment for <?php echo $tenant['name']; ?></h2>
        <a href="tenants.php?view=<?php echo $tenant_id; ?>" class="btn-small back">Back to Details</a>
      </div>
      <form action="tenants.php" method="POST" class="payment-form">
        <input type="hidden" name="tenant_id" value="<?php echo $tenant_id; ?>" />
        
        <div class="form-row">
          <div class="form-group">
            <label for="amount">Amount (KSH):</label>
            <input type="number" name="amount" required />
          </div>
          
          <div class="form-group">
            <label for="payment_date">Payment Date:</label>
            <input type="date" name="payment_date" value="<?php echo date('Y-m-d'); ?>" required />
          </div>
        </div>
        
        <div class="form-row">
          <div class="form-group">
            <label for="payment_method">Payment Method:</label>
            <select name="payment_method" required>
              <option value="Cash">Cash</option>
              <option value="M-Pesa">M-Pesa</option>
              <option value="Bank Transfer">Bank Transfer</option>
              <option value="Cheque">Cheque</option>
            </select>
          </div>
          
          <div class="form-group">
            <label for="reference">Reference/Transaction ID:</label>
            <input type="text" name="reference" />
          </div>
        </div>
        
        <button type="submit" name="add_payment" class="btn-success">Record Payment</button>
      </form>
    </div>
    <?php endif; ?>

    <!-- Edit Tenant Form -->
    <?php
    if (isset($_GET['edit'])) {
        $id = $_GET['edit'];
        $query = "SELECT * FROM tenants WHERE id='$id'";
        $result = mysqli_query($conn, $query);
        $tenant = mysqli_fetch_assoc($result);
    ?>
    <div class="card">
      <div class="card-header">
        <h2>Edit Tenant</h2>
        <a href="tenants.php" class="btn-small back">Back to List</a>
      </div>
      <form action="tenants.php" method="POST">
        <input type="hidden" name="id" value="<?php echo $tenant['id']; ?>" />
        <label for="name">Name:</label>
        <input type="text" name="name" value="<?php echo $tenant['name']; ?>" required />

        <label for="account_number">Account Number:</label>
        <input type="text" name="account_number" value="<?php echo $tenant['account_number']; ?>" required />

        <label for="phone">Phone:</label>
        <input type="text" name="phone" value="<?php echo $tenant['phone']; ?>" required />

        <label for="balance">Balance (KSH):</label>
        <input type="number" name="balance" value="<?php echo $tenant['balance']; ?>" required />

        <button type="submit" name="edit_tenant">Update Tenant</button>
      </form>
    </div>
    <?php
    }
    ?>
  </div>
</body>
</html>