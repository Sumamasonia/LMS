<?php
session_start();
include 'db.php';

// Security Check: Only Faculty Focal Person can delete
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'faculty' || $_SESSION['faculty_type'] !== 'focal_person') {
    die("Access Denied. Only Focal Persons can perform this action.");
}
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Execute Delete Query
    if ($conn->query("DELETE FROM notices WHERE id=$id") === TRUE) {
        // Redirect back to noticeboard with success message
        header("Location: faculty_dashboard.php?view=noticeboard&msg=deleted");
    } else {
        echo "Error deleting record: " . $conn->error;
    }
} else {
    // If no ID provided, go back
    header("Location: faculty_dashboard.php?view=noticeboard");
}
exit();
?>