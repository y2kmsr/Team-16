<?php
session_start();
include 'connect.php';

// Check if the user is logged in
if (!isset($_SESSION['email']) || !isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in.']);
    exit();
}

$user_id = $_SESSION['user_id'];

// Get the job ID from the query parameter
$job_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($job_id <= 0) {
    echo json_encode(['error' => 'Invalid job ID.']);
    exit();
}

// Fetch the job listing details
$sql = "SELECT job_id, title, location, description, salary, requirements 
        FROM job_listings 
        WHERE job_id = ? AND lister_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $job_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $listing = $result->fetch_assoc();
    $listing['requirements'] = $listing['requirements'] ?? '[]';
    echo json_encode($listing);
} else {
    echo json_encode(['error' => 'Listing not found or you do not have permission to edit this listing.']);
}

$stmt->close();
$conn->close();
exit();
?>
