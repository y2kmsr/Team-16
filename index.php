<?php
session_start();
include("connect.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Main Menu</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            text-align: center;
        }
        .navbar {
            background-color: #007bff;
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: white;
        }
        .navbar-title {
            font-size: 18px;
            font-weight: bold;
        }
        .navbar-buttons {
            display: flex;
            gap: 10px;
        }
        .navbar-btn {
            background-color: white;
            color: #007bff;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s, color 0.3s;
        }
        .navbar-btn:hover {
            background-color: #f0f0f0;
        }
        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: calc(100vh - 100px);
        }
        .search-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px;
            width: 80%;
            margin-bottom: 20px;
        }
        .search-bar {
            flex-grow: 2;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
            min-width: 250px;
        }
        .location-filter {
            flex-grow: 1;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
            min-width: 200px;
            background-color: white;
        }
        .search-btn {
            padding: 10px 15px;
            font-size: 16px;
            border: none;
            background-color: #007bff;
            color: white;
            cursor: pointer;
            border-radius: 5px;
        }
        .results-container {
            width: 80%;
            text-align: left;
            margin-top: 20px;
        }
        .job-card {
            background-color: white;
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            display: none;
        }
        .job-title {
            font-weight: bold;
            color: #007bff;
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
        }
        .job-location {
            color: #666;
            font-style: italic;
        }
        .job-description {
            margin-top: 10px;
        }
        .job-details {
            display: none;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }
        .expand-icon {
            font-size: 20px;
            transition: transform 0.3s ease;
        }
        .expanded .expand-icon {
            transform: rotate(180deg);
        }
        .apply-btn {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
            transition: background-color 0.3s;
        }
        .apply-btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="navbar-title">Job Advertisement Portal</div>
        <div class="navbar-buttons">
            <button class="navbar-btn" onclick="openEmployerPortal()">For Employers</button>
             <a href="login.php"><button class="navbar-btn">Log In</button></a>
        </div>
    </div>

    <div class="container">
        <h1>Find a job today</h1>
        
        <div class="search-container">
            <input type="text" class="search-bar" id="job-search" placeholder="Search for jobs...">
            <select class="location-filter" id="location-filter">
                <option value="">All Locations</option>
                <option value="London">London</option>
                <option value="Manchester">Manchester</option>
                <option value="Leeds">Leeds</option>
                <option value="Birmingham">Birmingham</option>
                <option value="Bradford">Bradford</option>
                <option value="sheffield">sheffield</option>
            </select>
            <button class="search-btn" onclick="searchJobs()">Search</button>
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

        function openEmployerPortal() {
            alert('Employer portal and job posting functionality will be added towards the final project');
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
