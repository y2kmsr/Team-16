<?php
session_start();
include("connect.php");

// Determine if the user is an admin
$is_admin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true ? true : false;

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submitEnquiry'])) {
    $enquiry = $_POST['enquiry'];
    $email = $_POST['email'];
    $phoneNumber = $_POST['phoneNumber'];

    // Server-side validation: ensure all fields are filled
    if (empty($enquiry) || empty($email) || empty($phoneNumber)) {
        $errorMessage = "Please fill in all fields.";
    } else {
        // Insert the enquiry into the Enquiries table
        $insertQuery = "INSERT INTO Enquiries (Enquiry, Email, PhoneNumber) VALUES ('$enquiry', '$email', '$phoneNumber')";
        if ($conn->query($insertQuery) === TRUE) {
            $successMessage = "Thank you! Your enquiry has been submitted. We'll get back to you soon.";
        } else {
            $errorMessage = "Oops! Something went wrong: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Job Advertisement Portal</title>
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
            <li><a href="ContactUs.php" class="secondary-nav-link active">Contact Us</a></li>
        </ul>
    </div>

    <!-- Main Content Area -->
    <div class="container">
        <h1>Contact Us</h1>

        <!-- Success/Error Messages -->
        <?php if (isset($successMessage)) { ?>
            <div class="success-message"><?php echo $successMessage; ?></div>
        <?php } ?>
        <?php if (isset($errorMessage)) { ?>
            <div class="error-message"><?php echo $errorMessage; ?></div>
        <?php } ?>

        <!-- Contact Information -->
        <div class="contact-info">
            <p>Contact us at <a href="mailto:jobportalhelp@gmail.com">jobportalhelp@gmail.com</a> or call us at <a href="tel:+442012345678">+44 20 1234 5678</a>.</p>
            <p>We operate 5 days a week, Monday to Friday, from 9:00 AM to 5:00 PM.</p>
            <p>If you would like to make a report, please click on the following <a href="reports.php">link</a>.</p>
        </div>

        <!-- Contact Form -->
        <div class="contact-form">
            <h2>Contact Us Here</h2>
            <form method="POST" action="" onsubmit="return validateContactForm()">
                <div class="form-group">
                    <label for="enquiry">Your Message</label>
                    <textarea id="enquiry" name="enquiry" rows="5" placeholder="What would you like to ask us about?" required></textarea>
                </div>
                <div class="form-group">
                    <label for="email">Your Email</label>
                    <input type="email" id="email" name="email" placeholder="e.g., your.email@example.com" required>
                </div>
                <div class="form-group">
                    <label for="phoneNumber">Your Phone Number</label>
                    <input type="tel" id="phoneNumber" name="phoneNumber" placeholder="e.g., +44 20 1234 5678" required>
                </div>
                <button type="submit" name="submitEnquiry" class="submit-btn">Send Message</button>
            </form>
        </div>
    </div>

    <script>
        // Client-side form validation
        function validateContactForm() {
            const enquiry = document.getElementById('enquiry').value.trim();
            const email = document.getElementById('email').value.trim();
            const phoneNumber = document.getElementById('phoneNumber').value.trim();

            if (!enquiry || !email || !phoneNumber) {
                alert("Please fill in all fields before submitting.");
                return false;
            }

            // Basic email format validation
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailPattern.test(email)) {
                alert("Please enter a valid email address.");
                return false;
            }

            // Basic phone number format validation (allows + and numbers)
            const phonePattern = /^\+?[0-9\s-]+$/;
            if (!phonePattern.test(phoneNumber)) {
                alert("Please enter a valid phone number.");
                return false;
            }

            return true;
        }
    </script>
</body>
</html>
