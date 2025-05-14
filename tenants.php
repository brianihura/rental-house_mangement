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
}

// Handle Delete Tenant
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $query = "DELETE FROM tenants WHERE id='$id'";
    mysqli_query($conn, $query);
    header('Location: tenants.php');
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
<body>
  <div class="container">
    <h1>Tenant Management</h1>

    <!-- Add Tenant Form -->
    <h2>Add New Tenant</h2>
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

    <!-- Tenant List Table -->
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
                  <a href='tenants.php?edit={$row['id']}' class='btn-small'>Edit</a>
                  <a href='tenants.php?delete={$row['id']}' class='btn-small danger' onclick='return confirm(\"Are you sure?\")'>Delete</a>
                </td>";
          echo "</tr>";
          $count++;
        }
        ?>
      </tbody>
    </table>

    <!-- Edit Tenant Form (only visible when editing a tenant) -->
    <?php
    if (isset($_GET['edit'])) {
        $id = $_GET['edit'];
        $query = "SELECT * FROM tenants WHERE id='$id'";
        $result = mysqli_query($conn, $query);
        $tenant = mysqli_fetch_assoc($result);
    ?>
    <h2>Edit Tenant</h2>
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
    <?php
    }
    ?>
  </div>
</body>
</html>
