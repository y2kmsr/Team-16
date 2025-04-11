<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['email'])) {
    echo "Please log in.";
    exit();
}

if (isset($_POST['job_id']) && isset($_POST['user_id'])) {
    $job_id = $_POST['job_id'];
    $user_id = $_POST['user_id'];

    $sql = "UPDATE job_listings SET applicant_id = ? WHERE job_id = ? AND applicant_id IS NULL";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $user_id, $job_id);
    if ($stmt->execute()) {
        echo "Success";
    } else {
        echo "Error: " . $conn->error;
    }
    $stmt->close();
}

  //PHP files connecting to the apply job button, allows users to apply for jobs
?>
