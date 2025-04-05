<?php
session_start();
include("connect.php");

// Determine if the user is an admin (default to false if not set)
$is_admin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true ? true : false;
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
            <li><a href="#" class="secondary-nav-link">Search for Jobs</a></li>
            <li><a href="AboutUs.php" class="secondary-nav-link">About Us</a></li>
            <li><a href="ContactUs.php" class="secondary-nav-link active">Contact Us</a></li>
        </ul>
    </div>

    <!-- Main Content Area -->
    <div class="container">
        <h1>Contact Us</h1>
    </div>

</body>
</html>
