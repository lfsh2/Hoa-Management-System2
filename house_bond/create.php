<?php
include '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userid = $_POST['userid'];
    $category = $_POST['category'];

    if ($category === 'Internal Construction Bond') {
        $funds = 1000;
    } else if ($category === 'External Construction Bond') {
        $funds = 5000;
    }

    $sql = "INSERT INTO housebond (userid, category, funds) VALUES ('$userid', '$category', $funds)";
    if ($conn->query($sql) === TRUE) {
        $transaction_type = "Construction Bond ($category)";
        $description = "Construction bond for user ID $userid";
        $transaction_date = date('Y-m-d');
        
        $transaction_sql = "INSERT INTO financial_transactions (transaction_type, amount, transaction_date) 
                            VALUES ('$transaction_type', $funds, '$transaction_date')";
        $conn->query($transaction_sql);

        header("Location: index.php");
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$sql_users = "SELECT id, firstname, lastname FROM users";
$result_users = $conn->query($sql_users);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Construction Bond</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-3">
        <h2>Add New Construction Bond</h2>
        <form method="POST">
            <div class="form-group">
                <label for="userid">Homeowner</label>
                <select name="userid" id="userid" class="form-control" required>
                    <option value="">Select Homeowner</option>
                    <?php while($user = $result_users->fetch_assoc()): ?>
                        <option value="<?= $user['id'] ?>"><?= $user['firstname'] . ' ' . $user['lastname'] ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="category">Category</label>
                <select name="category" id="category" class="form-control" required>
                    <option value="Internal Construction Bond">Internal Construction Bond - ₱1000</option>
                    <option value="External Construction Bond">External Construction Bond - ₱5000</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Add Statement</button>
        </form>
    </div>
</body>
</html>
