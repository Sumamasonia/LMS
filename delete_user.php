<?php
session_start();
include 'db.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { header("Location: login.php"); exit(); }

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $conn->query("DELETE FROM users WHERE id=$id");
}

// Redirect back to the previous page safely
$referer = $_SERVER['HTTP_REFERER'];
header("Location: $referer");
exit();
?>