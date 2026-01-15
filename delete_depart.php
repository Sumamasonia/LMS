<?php
session_start();
include 'db.php';

// Security Check: Only Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Execute Delete Query
    if ($conn->query("DELETE FROM departments WHERE id=$id") === TRUE) {
        // Redirect back with success message
        header("Location: add_depart.php?msg=deleted");
    } else {
        echo "Error deleting record: " . $conn->error;
    }
} else {
    // If no ID, just go back
    header("Location: add_depart.php");
}
exit();
?>