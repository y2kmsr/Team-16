<?php
session_start();

// Ensure $is_admin is false by default and only true if set
$is_admin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true ? true : false;
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us</title>
    <link rel="stylesheet" href="about_us_style.css"> 
</head>
<div class="navbar">
    <div class="navbar-title">Job Advertisement Portal</div>
    <div class="navbar-buttons">
        <?php if ($is_admin === true) { ?>
            <a href="admin.php"><button class="navbar-btn admin-portal-btn">Admin Portal</button></a>
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

<div class="secondary-nav">
    <ul class="secondary-nav-list">
        <li><a href="index.php" class="secondary-nav-link">Home</a></li>
        <li><a href="#" class="secondary-nav-link">Search for Jobs</a></li>
        <li><a href="AboutUs.php" class="secondary-nav-link">About Us</a></li>
        <li><a href="#" class="secondary-nav-link">Contact Us</a></li>
    </ul>
</div>

    <div class="container">
        <h1>About Us</h1>
        <p>Welcome to the Job Advertisement Portal, your go-to platform for finding and offering side jobs. Whether you need help with tasks or are looking for flexible work opportunities, we connect people with employers in a simple and reliable way.</p>
        <h2>What We Offer</h2>
        <ul>
            <li>Gardening</li>
            <li>Dog Walking</li>
            <li>House Cleaning</li>
            <li>Babysitting</li>
            <li>Handyman Services</li>
            <li>Tutoring</li>
        </ul>
        <h2>How It Works</h2>
        <p> People can browse available opportunities and apply for jobs that match their skills and interests. Employers can post jobs and find reliable workers to get tasks done efficiently. Our website has a login page allowing you to apply for jobs safe and easy. Also a job portal allowing you to view jobs within your area. we provide various methods of payments for example debit,credit and apple pay.  </p>
        <h2>Our Mission</h2>
        <p>We aim to make side jobs accessible to everyone by providing a safe, efficient, and user-friendly platform. Whether you're looking to earn extra income or find help for everyday tasks, we are here to help.</p>
        <h2>Why Choose Us?</h2>
        <ul>
            <li>Easy-to-use platform</li>
            <li>Flexible work opportunities</li>
            <li>Wide range of job categories</li>
            <li>Secure and trusted community</li>
        </ul>
    </div>
</body>
</html>
