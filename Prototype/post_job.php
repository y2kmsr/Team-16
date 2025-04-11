<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

// Predefined list of locations (same as in search_results.php and profile.php)
$allowed_locations = [
    "London",
    "Manchester",
    "Birmingham",
    "Leeds",
    "Sheffield",
    "Bristol",
    "Liverpool",
    "Newcastle",
    "Glasgow",
    "Edinburgh"
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $location = $_POST['location'];
    $description = $_POST['description'];
    $salary = $_POST['salary'];
    $requirements = json_encode(explode(',', $_POST['requirements']));
    $lister_id = $_POST['lister_id'];
    $applicant_id = NULL;
    $status = 1; // Active but pending approval
    $admin_approval = 0; 

    // Validate location against the predefined list
    if (!in_array($location, $allowed_locations)) {
        echo "Error: Invalid location selected.";
        exit();
    }

    // Validate description word count (max 400 words)
    $words = str_word_count($description);
    if ($words > 400) {
        echo "Error: Description exceeds 400 words.";
        exit();
    }

    $sql = "INSERT INTO job_listings (title, location, description, salary, requirements, lister_id, applicant_id, status, admin_approval) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssiiii", $title, $location, $description, $salary, $requirements, $lister_id, $applicant_id, $status, $admin_approval);

    if ($stmt->execute()) {
        header("Location: search_results.php?success=1");
    } else {
        echo "Error: " . $conn->error;
    }
    $stmt->close();
}

$conn->close();
?>
