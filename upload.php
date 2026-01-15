<?php
session_start();
include 'db.php';

if (isset($_POST['upload']) && isset($_FILES['profile_pic'])) {
    $id = $_SESSION['student'];
    $fileName = $_FILES['profile_pic']['name'];
    $tmpName = $_FILES['profile_pic']['tmp_name'];
    $target = "uploads/" . basename($fileName);

    if (move_uploaded_file($tmpName, $target)) {
        mysqli_query($conn, "UPDATE users SET profile_pic='$fileName' WHERE id='$id'");
        echo "Profile picture updated!";
    } else {
        echo "Failed to upload!";
    }
}
?>
<a href="student_profile.php">Back to Profile</a>
