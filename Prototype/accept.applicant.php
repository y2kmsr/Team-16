<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $job_id = $_GET['id'];
    $sql = "UPDATE job_listings SET status = 0 WHERE job_id = ? AND lister_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $job_id, $_SESSION['user_id']);
    if ($stmt->execute()) {
        header("Location: profile.php");
    } else {
        echo "Error: " . $conn->error;
    }
    $stmt->close();
}
  // PHP File to allow users to accept aplicants
?>
