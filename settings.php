<?php
// DB connection
$conn = mysqli_connect("localhost", "root", "", "household_system");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch current settings from DB
$query = "SELECT * FROM settings";
$result = mysqli_query($conn, $query);
$settings = [];
while ($row = mysqli_fetch_assoc($result)) {
    $settings[$row['setting_name']] = $row['setting_value'];
}

// Update settings (on form submission)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $rent_amount = $_POST['rent_amount'];
    $sms_api_key = $_POST['sms_api_key'];
    $sms_username = $_POST['sms_username'];
    $mpesa_api_key = $_POST['mpesa_api_key'];
    $mpesa_shortcode = $_POST['mpesa_shortcode'];

    // Update settings in database
    $updateQuery = "UPDATE settings SET setting_value = CASE 
                        WHEN setting_name = 'rent_amount' THEN '$rent_amount'
                        WHEN setting_name = 'sms_api_key' THEN '$sms_api_key'
                        WHEN setting_name = 'sms_username' THEN '$sms_username'
                        WHEN setting_name = 'mpesa_api_key' THEN '$mpesa_api_key'
                        WHEN setting_name = 'mpesa_shortcode' THEN '$mpesa_shortcode'
                    END
                    WHERE setting_name IN ('rent_amount', 'sms_api_key', 'sms_username', 'mpesa_api_key', 'mpesa_shortcode')";
    mysqli_query($conn, $updateQuery);
    
    echo "Settings updated successfully!";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Settings</title>
  <link rel="stylesheet" href="style.css">
  <style>
    .form-group { margin-bottom: 15px; }
    label { display: block; margin-bottom: 5px; }
    input { padding: 8px; width: 100%; }
    button { padding: 10px 20px; background: #007BFF; color: white; border: none; cursor: pointer; }
    button:hover { background: #0056b3; }
  </style>
</head>
<body>

<div class="container">
  <h1>Settings</h1>
  
  <form method="POST" action="settings.php">
    <div class="form-group">
      <label for="rent_amount">Rent Amount (KSH)</label>
      <input type="number" name="rent_amount" value="<?= $settings['rent_amount'] ?? '5000'; ?>" required />
    </div>
    
    <h3>SMS API Configuration</h3>
    <div class="form-group">
      <label for="sms_api_key">SMS API Key</label>
      <input type="text" name="sms_api_key" value="<?= $settings['sms_api_key'] ?? ''; ?>" required />
    </div>
    <div class="form-group">
      <label for="sms_username">SMS API Username</label>
      <input type="text" name="sms_username" value="<?= $settings['sms_username'] ?? ''; ?>" required />
    </div>
    
    <h3>Payment API Configuration</h3>
    <div class="form-group">
      <label for="mpesa_api_key">M-PESA API Key</label>
      <input type="text" name="mpesa_api_key" value="<?= $settings['mpesa_api_key'] ?? ''; ?>" required />
    </div>
    <div class="form-group">
      <label for="mpesa_shortcode">M-PESA Shortcode</label>
      <input type="text" name="mpesa_shortcode" value="<?= $settings['mpesa_shortcode'] ?? ''; ?>" required />
    </div>
    
    <button type="submit">Save Settings</button>
  </form>
</div>

</body>
</html>
