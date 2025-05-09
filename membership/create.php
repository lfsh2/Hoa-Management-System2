<?php
include '../db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userid = $_POST['userid'];
    $status = $_POST['status'];
    
    $sql = "INSERT INTO membership (userid, status) VALUES ('$userid', '$status')";
    
    if ($conn->query($sql) === TRUE) {
        if ($status == 'Paid') {
            $funds_sql = "UPDATE monthly_dues SET funds = funds + 300 WHERE userid = $userid";
            $conn->query($funds_sql);
        }
        header("Location: index.php");
        exit;
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
    <title>Add New Member</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-3">
        <h2>Add New Member</h2>
        <form method="POST" action="">
            <div class="form-group">
                <label for="userid">Homeowner</label>
                <select name="userid" id="userid" class="form-control" required>
                    <?php while($user = $users_result->fetch_assoc()): ?>
                        <option value="<?= $user['id'] ?>"><?= $user['firstname'] . ' ' . $user['lastname'] ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="status">Status</label>
                <select name="status" id="status" class="form-control" required>
                    <option value="To be Reviewed">To be Reviewed</option>
                    <option value="Paid">Paid</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Add Member</button>
        </form>
    </div>
</body>
</html>
