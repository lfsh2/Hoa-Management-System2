<?php
include '../db.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    $sql = "DELETE FROM stickers WHERE id = ?";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param('i', $id);

        if ($stmt->execute()) {
            header('Location: index.php');
            exit;
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
    }
} else {
    echo "No ID provided";
}

$conn->close();
?>
