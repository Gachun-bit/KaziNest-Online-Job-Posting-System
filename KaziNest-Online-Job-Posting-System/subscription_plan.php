<?php
session_start();
require 'config.php'; // database connection

if (!isset($_SESSION['user_id'])) {
    header("Location: register.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$errors = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $plan = $_POST['plan'];
    $mpesa_ref = trim($_POST['mpesa_reference']);
    $start_date = date('Y-m-d');

    // Calculate end date
    if ($plan == 'monthly') {
        $end_date = date('Y-m-d', strtotime('+1 month'));
    } elseif ($plan == 'yearly') {
        $end_date = date('Y-m-d', strtotime('+1 year'));
    } else {
        $errors = 'Invalid subscription plan selected.';
    }

    if (empty($mpesa_ref)) {
        $errors = 'Please enter the Mpesa reference code.';
    }

    if (empty($errors)) {
        // Update user table with subscription
        $sql = "UPDATE users SET 
                    subscription_plan = ?, 
                    mpesa_reference = ?, 
                    subscription_start_date = ?, 
                    subscription_end_date = ?, 
                    subscription_status = 'active' 
                WHERE id = ? AND role = 'company'";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $plan, $mpesa_ref, $start_date, $end_date, $user_id);

        if ($stmt->execute()) {
            $success = "Subscription activated successfully!";
            header("Location: company_page.php"); // redirect after success
            exit();
        } else {
            $errors = "Failed to update subscription. Try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Select Subscription Plan</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f9f9f9; }
        .box { background: white; padding: 30px; max-width: 500px; margin: auto; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        h2 { margin-bottom: 20px; }
        input, select { width: 100%; padding: 10px; margin-top: 10px; }
        .error { color: red; margin-top: 10px; }
        .success { color: green; margin-top: 10px; }
        button { margin-top: 20px; padding: 10px 20px; background: teal; color: white; border: none; cursor: pointer; }
    </style>
</head>
<body>
<div class="box">
    <h2>CHOOSE YOUR SUBSCRIPTION PLAN</h2>
    <?php if ($errors): ?><div class="error"><?php echo $errors; ?></div><?php endif; ?>
    <?php if ($success): ?><div class="success"><?php echo $success; ?></div><?php endif; ?>

    <form method="POST" action="">
        <label for="plan"><strong>Select Plan:<strong></label>
        <select name="plan" required>
            <option value="">-- Choose Plan --</option>
            <option value="monthly">Monthly - KES 3000</option>
            <option value="yearly">Yearly - KES 28000</option>
        </select><br><br>

        <label for="mpesa_reference"><strong>Enter Mpesa Reference Code:<strong></label>
        <input type="text" name="mpesa_reference" placeholder="e.g. QED7NS52G2" required>

        <p><strong>Payment Methods:</strong></p>
        <ul>
            <li>Mpesa Paybill: 123456</li>
            <li>Account Number: KaziNest</li>
            <!-- Add other methods if needed -->
        </ul>

        <button type="submit">Submit Payment</button>
    </form>
</div>
</body>
</html>
