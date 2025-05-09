<?php
include '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $status = $_POST['status'];

    $sql = "UPDATE monthly_dues SET status = '$status' WHERE id = $id";
    if ($conn->query($sql) === TRUE) {
        $due_sql = "SELECT userid, funds, month FROM monthly_dues WHERE id = $id";
        $due_result = $conn->query($due_sql);
        $due = $due_result->fetch_assoc();
        
        $userid = $due['userid'];
        $funds = $due['funds'];
        $month = $due['month'];
        $transaction_date = date('Y-m-d');

        if ($status == 'Paid') {
            $transaction_type = "Monthly Dues Payment";
            $description = "Payment for monthly dues for month $month, user ID $userid";
            
            $financial_sql = "INSERT INTO financial_transactions (userid, transaction_type, amount, transaction_date, description) 
                              VALUES ($userid, '$transaction_type', $funds, '$transaction_date', '$description')";
            $conn->query($financial_sql);
        } else if ($status == 'Unpaid') {
            $transaction_type = "Monthly Dues Payment";
            $financial_sql = "UPDATE financial_transactions 
                              SET description = CONCAT(description, ' (Voided)') 
                              WHERE userid = $userid AND transaction_type = '$transaction_type' AND amount = $funds AND transaction_date = '$transaction_date'";
            $conn->query($financial_sql);
        }

        header("Location: index.php");
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
