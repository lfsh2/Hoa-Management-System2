<?php
include '../db.php';

$id = $_GET['id'];
$sql = "SELECT s.id, s.userid, u.firstname, u.lastname, s.category, s.fees 
        FROM stickers s 
        JOIN users u ON s.userid = u.id 
        WHERE s.id = $id";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

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
        case 'Other':
            $fees = 0;
            break;
    }

    $sql = "UPDATE stickers SET userid='$userid', category='$category', fees='$fees' WHERE id=$id";

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
    <title>Edit Vehicle Sticker</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-3">
        <h2>Edit Vehicle Sticker</h2>
        <form method="POST" action="">
            <div class="form-group">
                <label for="userid">Homeowner</label>
                <select name="userid" id="userid" class="form-control" required>
                    <?php while($user = $users_result->fetch_assoc()): ?>
                        <option value="<?= $user['id'] ?>" <?= $user['id'] == $row['userid'] ? 'selected' : '' ?>>
                            <?= $user['firstname'] . ' ' . $user['lastname'] ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="category">Vehicle Type</label>
                <select name="category" id="category" class="form-control" required>
                    <option value="Car" <?= $row['category'] == 'Car' ? 'selected' : '' ?>>Car</option>
                    <option value="Motorcycle" <?= $row['category'] == 'Motorcycle' ? 'selected' : '' ?>>Motorcycle</option>
                    <option value="E-bike" <?= $row['category'] == 'E-bike' ? 'selected' : '' ?>>E-bike</option>
                    <option value="Other" <?= $row['category'] == 'Other' ? 'selected' : '' ?>>Other</option>
                </select>
            </div>
            <div class="form-group">
                <label for="payment_mode">Payment Mode</label>
                <select name="payment_mode" id="payment_mode" class="form-control" required>
                    <option value="Cash">Cash</option>
                    <option value="Gcash">Gcash</option>
                </select>
            </div>
            <button type="submit" class="btn btn-success">Update</button>
        </form>
    </div>
</body>
</html>
