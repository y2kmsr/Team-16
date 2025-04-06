<?php
session_start();
include("connect.php");

// Determine if the user is an admin (default to false if not set)
$is_admin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true ? true : false;

// Check if the user is logged in
$is_logged_in = isset($_SESSION['email']) && !$is_admin;

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submitReport'])) {
    // Check if the user is logged in
    if (!$is_logged_in) {
        $errorMessage = "Please register an account or log in to submit a report.";
    } else {
        $typeOfComplaint = $_POST['typeOfComplaint'];
        $description = $_POST['description'];

        // Server-side validation: ensure all fields are filled
        if (empty($typeOfComplaint) || empty($description)) {
            $errorMessage = "Please fill in all fields.";
        } else {
            // Get the user's ID from the users table
            $email = $_SESSION['email'];
            $userQuery = "SELECT id FROM users WHERE email='$email'";
            $userResult = $conn->query($userQuery);

            if ($userResult->num_rows > 0) {
                $userRow = $userResult->fetch_assoc();
                $userId = $userRow['id'];

                // Insert the report into the complaints table
                $insertQuery = "INSERT INTO complaints (id, description, TypeOfComplaint) VALUES ('$userId', '$description', '$typeOfComplaint')";
                if ($conn->query($insertQuery) === TRUE) {
                    $successMessage = "Thank you! Your report has been submitted.";
                } else {
                    $errorMessage = "Oops! Something went wrong: " . $conn->error;
                }
            } else {
                $errorMessage = "Error: User not found.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit a Report - Job Advertisement Portal</title>
    <link rel="stylesheet" href="contact_style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <!-- Primary Navbar -->
    <div class="navbar">
        <div class="navbar-title">Job Advertisement Portal</div>
        <div class="navbar-buttons">
            <?php if ($is_admin === true) { ?>
                <a href="admin.php"><button class="navbar-btn admin-portal-btn">Admin Portal</button></a>
            <?php } ?>
            <?php if (isset($_SESSION['email']) && !$is_admin) { ?>
                <a href="Profile.php"><button class="navbar-btn">My Profile</button></a>
            <?php } ?>
            <?php
            if (isset($_SESSION['email'])) {
                echo '<a href="logout.php"><button class="navbar-btn">Sign Out</button></a>';
            } else {
                echo '<a href="login.php"><button class="navbar-btn">Log In</button></a>';
            }
            ?>
        </div>
    </div>

    <!-- Secondary Navbar -->
    <div class="secondary-nav">
        <ul class="secondary-nav-list">
            <li><a href="index.php" class="secondary-nav-link">Home</a></li>
            <li><a href="search_results.php" class="secondary-nav-link">Search for Jobs</a></li>
            <li><a href="AboutUs.php" class="secondary-nav-link">About Us</a></li>
            <li><a href="ContactUs.php" class="secondary-nav-link">Contact Us</a></li>
        </ul>
    </div>

    <!-- Main Content Area -->
    <div class="container">
        <h1>Submit a Report</h1>

        <!-- Success/Error Messages -->
        <?php if (isset($successMessage)) { ?>
            <div class="success-message"><?php echo $successMessage; ?></div>
        <?php } ?>
        <?php if (isset($errorMessage)) { ?>
            <div class="error-message"><?php echo $errorMessage; ?></div>
        <?php } ?>

        <!-- Report Form -->
        <div class="contact-form">
            <h2>Submit Your Report</h2>
            <form method="POST" action="" onsubmit="return validateReportForm()">
                <div class="form-group">
                    <label for="typeOfComplaint">Type of Report</label>
                    <select id="typeOfComplaint" name="typeOfComplaint" required>
                        <option value="">Select a type</option>
                        <option value="Report against a person">Report against a person</option>
                        <option value="Report against a listing">Report against a listing</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="5" placeholder="Describe the issue..." required></textarea>
                </div>
                <button type="submit" name="submitReport" class="submit-btn">Submit Report</button>
            </form>
        </div>
    </div>

    <script>
        // Client-side form validation
        function validateReportForm() {
            const typeOfComplaint = document.getElementById('typeOfComplaint').value;
            const description = document.getElementById('description').value.trim();

            if (!typeOfComplaint || !description) {
                alert("Please fill in all fields before submitting.");
                return false;
            }

            return true;
        }
    </script>
</body>
</html>
