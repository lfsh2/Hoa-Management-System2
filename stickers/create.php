<?php
include '../db.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userid = $_POST['userid'];
    $category = $_POST['category'];

    $fees = 0;
    switch ($category) {
        case 'Motorcycle':
            $fees = 100;
            break;
        case 'E-bike':
            $fees = 100;
            break;
        case 'Car':
            $fees = 150;
            break;
    }

    $sql = "INSERT INTO stickers (userid, category, fees) VALUES ('$userid', '$category', '$fees')";

    if ($conn->query($sql) === TRUE) {
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
    <title>Add New Vehicle Sticker</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-3">
        <h2>Add New Vehicle Sticker</h2>
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
                <label for="category">Vehicle Type</label>
                <select name="category" id="category" class="form-control" required>
                    <option value="Car">Car</option>
                    <option value="Motorcycle">Motorcycle</option>
                    <option value="E-bike">E-bike</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <div class="form-group">
                <label for="payment_mode">Payment Mode</label>
                <select name="payment_mode" id="payment_mode" class="form-control" required>
                    <option value="Cash">Cash</option>
                    <option value="Gcash">Gcash</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Add Sticker</button>
        </form>
    </div>
</body>
</html>
