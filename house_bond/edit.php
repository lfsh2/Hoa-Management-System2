<?php
include '../db.php';

$id = $_GET['id'];
$sql = "SELECT h.id, h.userid, u.firstname, u.lastname, h.category, h.funds 
        FROM housebond h 
        JOIN users u ON h.userid = u.id 
        WHERE h.id = $id";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userid = $_POST['userid'];
    $category = $_POST['category'];
    $funds = $_POST['funds'];

    $sql = "UPDATE housebond SET userid='$userid', category='$category', funds='$funds' WHERE id=$id";

    if ($conn->query($sql) === TRUE) {
        header('Location: index.php');
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Construction Bond</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-3">
        <h2>Edit Construction Bond</h2>
        <form method="POST">
            <div class="form-group">
                <label for="userid">User ID</label>
                <input type="number" class="form-control" id="userid" name="userid" value="<?= $row['userid'] ?>" required>
            </div>
            <div class="form-group">
                <label for="category">Category</label>
                <input type="text" class="form-control" id="category" name="category" value="<?= $row['category'] ?>" required>
            </div>
            <div class="form-group">
                <label for="funds">Funds</label>
                <input type="number" class="form-control" id="funds" name="funds" value="<?= $row['funds'] ?>" required>
            </div>
            <button type="submit" class="btn btn-success">Update</button>
        </form>
    </div>
</body>
</html>
