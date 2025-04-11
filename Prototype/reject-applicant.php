<?php
session_start();
include 'connect.php';

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

// Get the user ID from the session
$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    echo "User not found.";
    exit();
}

// Get the job ID from the query parameter
$job_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($job_id <= 0) {
    echo "Invalid job ID.";
    exit();
}

// Verify that the job belongs to the logged-in user (lister) and has an applicant
$sql = "SELECT lister_id, applicant_id FROM job_listings WHERE job_id = ? AND lister_id = ? AND applicant_id IS NOT NULL AND status = 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $job_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Job not found, or you do not have permission to reject applicants for this job.";
    $stmt->close();
    exit();
}
$stmt->close();

// Update the job listing: clear the applicant_id, set status to 1 (active), and job_status to 'pending'
$sql = "UPDATE job_listings SET applicant_id = NULL, status = 1, job_status = 'pending' WHERE job_id = ? AND lister_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $job_id, $user_id);

if ($stmt->execute()) {
    // Redirect back to Profile.php with a success message
    header("Location: Profile.php?success=" . urlencode("Applicant rejected successfully! The job is now open for new applications."));
} else {
    // Redirect back with an error message
    header("Location: Profile.php?error=" . urlencode("Error rejecting applicant: " . $conn->error));
}

$stmt->close();
$conn->close();
exit();
?>
