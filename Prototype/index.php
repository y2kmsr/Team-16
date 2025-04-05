<?php
session_start();
include("connect.php");

// Ensure $is_admin is false by default and only true if set
$is_admin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true ? true : false;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Main Menu</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
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

<div class="secondary-nav">
    <ul class="secondary-nav-list">
        <li><a href="index.php" class="secondary-nav-link">Home</a></li>
        <li><a href="search_results.php" class="secondary-nav-link">Search for Jobs</a></li>
        <li><a href="AboutUs.php" class="secondary-nav-link">About Us</a></li>
        <li><a href="ContactUs.php" class="secondary-nav-link">Contact Us</a></li>
    </ul>
</div>

<div class="container">
    <h1>Find a job today</h1>

    <div class="search-container">
    <form action="search_results.php" method="GET">
        <input type="text" class="search-bar" id="job-search" name="search" placeholder="Search for jobs...">
        <select class="location-filter" id="location-filter" name="location">
            <option value="">All Locations</option>
            <option value="London">London</option>
            <option value="Manchester">Manchester</option>
            <option value="Leeds">Leeds</option>
            <option value="Birmingham">Birmingham</option>
            <option value="Bradford">Bradford</option>
            <option value="sheffield">sheffield</option>
        </select>
        <button class="search-btn" type="submit">Search</button>
    </form>
</div>

    <div class="results-container" id="results">
        <div class="job-card" data-title="Dog Walker" data-location="London">
            <div class="job-title" onclick="toggleJobDetails(this)">
                <span>Dog walker</span>
                <span class="expand-icon">▼</span>
            </div>
            <div class="job-location">Location: London</div>
            <div class="job-description">I am looking for a patient dog walker that can handle my energetic dog..</div>
            <div class="job-details">
                <h3>Job Details</h3>
                <p><strong>Salary:</strong> £15 per hour</p>
                <p><strong>Hours:</strong> Part-time, 10-15 hours per week</p>
                <p><strong>Requirements:</strong></p>
                <ul>
                    <li>Experience with energetic dogs</li>
                    <li>Able to walk dogs in all weather conditions</li>
                    <li>Reliable and punctual</li>
                    <li>Lives in London or surrounding areas</li>
                </ul>
                <p><strong>Description:</strong> We have a 3-year-old Siberian Husky who needs daily walks. The ideal candidate will have experience with high-energy breeds and be able to provide consistent exercise and companionship. The schedule is flexible but must include weekday afternoons.</p>
                <button class="apply-btn" onclick="applyForJob('Dog Walker', 'London')">Apply Now</button>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleJobDetails(element) {
        const card = element.parentElement;
        const details = card.querySelector('.job-details');

        if (details.style.display === 'block') {
            details.style.display = 'none';
            card.classList.remove('expanded');
        } else {
            details.style.display = 'block';
            card.classList.add('expanded');
        }
    }

    function applyForJob(title, location) {
        alert(`You are applying for the ${title} position in ${location}. This functionality will be implemented in the final project.`);
    }

    function searchJobs() {
        const searchTerm = document.getElementById('job-search').value.toLowerCase();
        const locationTerm = document.getElementById('location-filter').value;
        const jobCards = document.getElementsByClassName('job-card');
        let resultsFound = false;

        for (let i = 0; i < jobCards.length; i++) {
            const card = jobCards[i];
            const jobTitle = card.getAttribute('data-title').toLowerCase();
            const jobLocation = card.getAttribute('data-location');

            const titleMatch = jobTitle.includes(searchTerm);
            const locationMatch = locationTerm === '' || jobLocation === locationTerm;

            if (titleMatch && locationMatch) {
                card.style.display = 'block';
                resultsFound = true;
            } else {
                card.style.display = 'none';
            }
        }

        if (!resultsFound) {
            const resultsContainer = document.getElementById('results');

            const existingMessage = document.getElementById('no-results-message');
            if (existingMessage) {
                existingMessage.remove();
            }

            const message = document.createElement('p');
            message.id = 'no-results-message';
            message.textContent = 'No jobs found matching your criteria.';
            message.style.textAlign = 'center';
            resultsContainer.appendChild(message);
        } else {
            const existingMessage = document.getElementById('no-results-message');
            if (existingMessage) {
                existingMessage.remove();
            }
        }
    }
</script>
</body>
</html>
