<?php
include '../db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userid = $_POST['userid'];
    $month_paid = $_POST['month_paid'];
    $payment_mode = $_POST['payment_mode'];
    $funds = 500;  

    $month_paid = date('Y-m-d', strtotime($month_paid));
    $sql = "INSERT INTO monthly_dues (userid, funds, month_paid) VALUES ('$userid', '$funds', '$month_paid')";
    
    if ($conn->query($sql) === TRUE) {
        $transaction_type = 'Monthly Dues';
        $transaction_date = date('Y-m-d'); 
        $details = "Payment for monthly dues for the month of $month_paid";

        $financial_sql = "INSERT INTO financial_transactions (transaction_type, userid, amount, transaction_date, details)
                          VALUES ('$transaction_type', '$userid', '$funds', '$transaction_date', '$details')";

        if ($conn->query($financial_sql) === TRUE) {
            header('Location: index.php');
        } else {
            echo "Error: " . $financial_sql . "<br>" . $conn->error;
        }
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$users_sql = "SELECT id, firstname, lastname FROM users";
$users_result = $conn->query($users_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Monthly Dues</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-3">
        <h2>Add Monthly Dues</h2>
        <form method="POST">
            <div class="form-group">
                <label for="userid">Homeowner</label>
                <select class="form-control" id="userid" name="userid" required>
                    <option value="">Select Homeowner</option>
                    <?php while($user = $users_result->fetch_assoc()): ?>
                        <option value="<?= $user['id'] ?>"><?= $user['firstname'] . ' ' . $user['lastname'] ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="month_paid">Month of Due</label>
                <input type="month" class="form-control" id="month_paid" name="month_paid" required>
            </div>
            <div class="form-group">
                <label for="payment_mode">Payment Mode</label>
                <select class="form-control" id="payment_mode" name="payment_mode" required>
                    <option value="Cash">Cash</option>
                    <option value="Gcash">Gcash</option>
                </select>
            </div>
            <button type="submit" class="btn btn-success">Submit</button>
        </form>
    </div>
</body>
</html>
