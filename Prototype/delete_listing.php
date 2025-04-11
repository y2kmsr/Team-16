<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $job_id = $_GET['id'];
    $sql = "DELETE FROM job_listings WHERE job_id = ? AND lister_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $job_id, $_SESSION['user_id']);
    if ($stmt->execute()) {
        header("Location: profile.php");
    } else {
        echo "Error: " . $conn->error;
    }
    $stmt->close();
}

  //PHP file to allow users to delete job listings
?>
