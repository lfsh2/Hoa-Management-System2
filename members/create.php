<?php
include '../db.php'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $mi = $_POST['mi'];
    $block = $_POST['block'];
    $lot = $_POST['lot'];
    $street = $_POST['street'];

    $sql = "INSERT INTO users (firstname, lastname, mi, block, lot, street) 
            VALUES ('$firstname', '$lastname', '$mi', '$block', '$lot', '$street')";

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
    <title>Add Homeowner</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-3">
        <h2>Add Homeowner</h2>
        <form method="POST">
            <div class="form-group">
                <label for="firstname">First Name</label>
                <input type="text" class="form-control" id="firstname" name="firstname" required>
            </div>
            <div class="form-group">
                <label for="lastname">Last Name</label>
                <input type="text" class="form-control" id="lastname" name="lastname" required>
            </div>
            <div class="form-group">
                <label for="mi">Middle Initial</label>
                <input type="text" class="form-control" id="mi" name="mi" maxlength="1">
            </div>
            <div class="form-group">
                <label for="block">Block</label>
                <input type="text" class="form-control" id="block" name="block" required>
            </div>
            <div class="form-group">
                <label for="lot">Lot</label>
                <input type="text" class="form-control" id="lot" name="lot" required>
            </div>
            <div class="form-group">
                <label for="street">Street</label>
                <input type="text" class="form-control" id="street" name="street" required>
            </div>
            <button type="submit" class="btn btn-success">Submit</button>
        </form>
    </div>
</body>
</html>
