<?php
include '../db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $status = $_POST['status'];

    $sql = "UPDATE membership SET status = '$status' WHERE id = $id";

    if ($conn->query($sql) === TRUE) {
        $userid_sql = "SELECT userid FROM membership WHERE id = $id";
        $userid_result = $conn->query($userid_sql);
        $userid_row = $userid_result->fetch_assoc();
        $userid = $userid_row['userid'];

        if ($status == 'Paid') {
            $funds_sql = "UPDATE monthly_dues SET funds = funds + 300 WHERE userid = $userid";
            $conn->query($funds_sql);
        } elseif ($status == 'To be Reviewed') {
            $funds_sql = "UPDATE monthly_dues SET funds = NULL WHERE userid = $userid";
            $conn->query($funds_sql);
        }

        header('Location: index.php');
        exit;
    } else {
        echo "Error updating record: " . $conn->error;
    }
}
?>
