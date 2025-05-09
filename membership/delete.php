<?php
include '../db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    $get_userid_sql = "SELECT userid FROM membership WHERE id = $id";
    $userid_result = $conn->query($get_userid_sql);
    if ($userid_result && $userid_row = $userid_result->fetch_assoc()) {
        $userid = $userid_row['userid'];
        
        $delete_transactions_sql = "DELETE FROM financial_transactions WHERE userid = $userid";
        $conn->query($delete_transactions_sql);
        
        $delete_membership_sql = "DELETE FROM membership WHERE id = $id";
        if ($conn->query($delete_membership_sql) === TRUE) {
            header("Location: index.php");
            exit;
        } else {
            echo "Error deleting membership record: " . $conn->error;
        }
    } else {
        echo "Membership not found.";
    }
} else {
    echo "No ID provided.";
}
?>