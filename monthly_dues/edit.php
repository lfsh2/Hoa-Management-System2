<?php
include '../db.php';

$id = $_GET['id'];
$sql = "SELECT m.id, m.userid, u.firstname, u.lastname, m.funds, m.month_paid 
        FROM monthly_dues m 
        JOIN users u ON m.userid = u.id 
        WHERE m.id = $id";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userid = $_POST['userid'];
    $month_paid = $_POST['month_paid'];
    $funds = 500;  

    // Ensure date is in the correct format
    $month_paid = date('Y-m-d', strtotime($month_paid));

    $sql = "UPDATE monthly_dues SET userid='$userid', month_paid='$month_paid', funds='$funds' WHERE id=$id";

    if ($conn->query($sql) === TRUE) {
        header('Location: index.php');
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
    <title>Edit Monthly Dues</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-3">
        <h2>Edit Monthly Dues</h2>
        <form method="POST">
            <div class="form-group">
                <label for="userid">Homeowner</label>
                <select class="form-control" id="userid" name="userid" required>
                    <option value="">Select Homeowner</option>
                    <?php while($user = $users_result->fetch_assoc()): ?>
                        <option value="<?= $user['id'] ?>" <?= $user['id'] == $row['userid'] ? 'selected' : '' ?>>
                            <?= $user['firstname'] . ' ' . $user['lastname'] ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="month_paid">Month of Due</label>
                <input type="month" class="form-control" id="month_paid" name="month_paid" value="<?= date('Y-m', strtotime($row['month_paid'])) ?>" required>
            </div>
            <button type="submit" class="btn btn-success">Update</button>
        </form>
    </div>
</body>
</html>
