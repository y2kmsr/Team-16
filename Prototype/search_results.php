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
    <title>Side Jobs Search Results</title>
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
            width: 80%;
            margin: 20px auto;
            padding: 20px;
        }
        .search-container {
            display: flex;
            justify-content: center;
            width: 100%;
            margin-bottom: 20px;
        }
        .search-bar {
            width: 70%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px 0 0 5px;
        }
        .search-btn {
            padding: 10px 15px;
            font-size: 16px;
            border: none;
            background-color: #007bff;
            color: white;
            cursor: pointer;
            border-radius: 0 5px 5px 0;
        }
        .results-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .results-title {
            font-size: 24px;
            text-align: left;
        }
        .back-btn {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .back-btn:hover {
            background-color: #0056b3;
        }
        .job-card {
            background-color: white;
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            text-align: left;
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
        .no-results {
            text-align: center;
            margin-top: 40px;
            color: #666;
        }
        .secondary-nav {
         background-color: #0056b3; 
         padding: 10px 0;
         box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
         }

         .secondary-nav-list {
         list-style: none;
         margin: 0;
         padding: 0;
         display: flex;
         justify-content: center;
         gap: 30px;
         }

         .secondary-nav-list li {
         display: inline;
         }

         .secondary-nav-link {
         color: white;
         text-decoration: none;
         font-size: 16px;
         font-weight: 500;
         padding: 8px 15px;
         border-radius: 5px;
         transition: background-color 0.3s, color 0.3s;
         }

         .secondary-nav-link:hover {
          background-color: #003d82; 
          color: #f0f0f0;
         }
    </style>
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
        <div class="results-header">
            <div class="results-title">
                <?php
                $searchTerm = isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '';
                $locationTerm = isset($_GET['location']) ? htmlspecialchars($_GET['location']) : '';
                
                echo "Search Results";
                if (!empty($searchTerm) || !empty($locationTerm)) {
                    echo " for";
                    if (!empty($searchTerm)) {
                        echo " \"$searchTerm\"";
                    }
                    if (!empty($locationTerm)) {
                        echo " in $locationTerm";
                    }
                }
                ?>
            </div>
            <a href="index.php"><button class="back-btn">Back to Search</button></a>
        </div>
        
        <div class="search-container">
            <form action="search_results.php" method="GET">
                <input type="text" class="search-bar" id="job-search" name="search" placeholder="Search for side jobs..." value="<?php echo $searchTerm; ?>">
                <?php if (!empty($locationTerm)): ?>
                <input type="hidden" name="location" value="<?php echo $locationTerm; ?>">
                <?php endif; ?>
                <button class="search-btn" type="submit">Search</button>
            </form>
        </div>
        
        <div class="results-container" id="results">
            <?php
            // we would fetch job listings from the database
            // i simulated with static side job listings
            $jobListings = [
                [
                    'title' => 'Dog Walker',
                    'location' => 'London',
                    'description' => 'I am looking for a patient dog walker that can handle my energetic dog.',
                    'salary' => '£15 per hour',
                    'hours' => 'Weekdays, 1-2 hours between 12pm-3pm',
                    'requirements' => [
                        'Experience with energetic dogs',
                        'Able to walk dogs in all weather conditions',
                        'Reliable and punctual',
                        'Lives in London or surrounding areas'
                    ],
                    'full_description' => 'We have a 3-year-old Siberian Husky who needs daily walks. The ideal candidate will have experience with high-energy breeds and be able to provide consistent exercise and companionship. The schedule is flexible but must include weekday afternoons.'
                ],
                [
                    'title' => 'Babysitter',
                    'location' => 'Manchester',
                    'description' => 'Looking for a reliable babysitter for two children (5 and 8 years old) for evenings and occasional weekends.',
                    'salary' => '£12 per hour',
                    'hours' => 'Evenings and occasional weekends',
                    'requirements' => [
                        'Previous childcare experience',
                        'First aid certification preferred',
                        'Energetic and patient',
                        'Able to help with homework'
                    ],
                    'full_description' => 'Our family is looking for a responsible and caring individual to look after our two children. Duties include preparing meals, helping with homework, bedtime routines, and engaging in age-appropriate activities. References required.'
                ],
                [
                    'title' => 'Lawn Mowing & Garden Maintenance',
                    'location' => 'Birmingham',
                    'description' => 'Need someone to maintain our lawn and garden weekly during the growing season.',
                    'salary' => '£14 per hour or £25 per visit',
                    'hours' => 'Weekly, approximately 2 hours per visit',
                    'requirements' => [
                        'Own gardening equipment preferred',
                        'Knowledge of basic garden maintenance',
                        'Reliable and consistent',
                        'Available on weekends'
                    ],
                    'full_description' => 'We need help maintaining our medium-sized garden including lawn mowing, weeding, and basic pruning. The job requires approximately 2 hours per week, typically on weekends. We can provide some equipment but prefer if you have your own lawn mower.'
                ],
                [
                    'title' => 'Pet Sitting',
                    'location' => 'Leeds',
                    'description' => 'Looking for a pet sitter for our cat and two small dogs while we are on vacation.',
                    'salary' => '£20 per day',
                    'hours' => 'Two visits per day, 30 minutes each',
                    'requirements' => [
                        'Experience with cats and small dogs',
                        'Responsible and trustworthy',
                        'Lives nearby',
                        'Available for the last two weeks of August'
                    ],
                    'full_description' => 'We need someone to feed, walk, and spend time with our pets while we are away on vacation. The job involves visiting our home twice daily (morning and evening) to feed our animals, walk the dogs, and provide some companionship. Must be comfortable with administering medication to one of our dogs.'
                ],
                [
                    'title' => 'Grocery Shopping Assistant',
                    'location' => 'Sheffield',
                    'description' => 'Seeking help with weekly grocery shopping for an elderly couple.',
                    'salary' => '£12 per hour plus travel expenses',
                    'hours' => 'Once a week, approximately 2-3 hours',
                    'requirements' => [
                        'Driver\'s license and own vehicle',
                        'Patient and respectful',
                        'Attention to detail',
                        'Available on weekday mornings'
                    ],
                    'full_description' => 'An elderly couple needs assistance with their weekly grocery shopping. The job involves picking up a shopping list from their home, going to the supermarket, purchasing the items, and delivering them back to their home. Candidate must be patient, respectful, and have their own transportation.'
                ],
                [
                    'title' => 'Tutoring - Math & Science',
                    'location' => 'London',
                    'description' => 'Looking for a tutor for GCSE level Math and Science for my 15-year-old son.',
                    'salary' => '£20 per hour',
                    'hours' => 'Twice weekly, 1.5 hours per session',
                    'requirements' => [
                        'Strong knowledge of GCSE Math and Science curriculum',
                        'Previous tutoring experience preferred',
                        'Patient and encouraging teaching style',
                        'Available weekday evenings'
                    ],
                    'full_description' => 'We are looking for a knowledgeable and patient tutor to help our son improve his Math and Science grades for his upcoming GCSE exams. The ideal candidate will have experience with the current curriculum and be able to explain concepts clearly. Sessions would be held at our home.'
                ]
            ];
            
            $resultsFound = false;
            
            foreach ($jobListings as $index => $job) {
                $titleMatch = empty($searchTerm) || stripos($job['title'], $searchTerm) !== false || stripos($job['description'], $searchTerm) !== false;
                $locationMatch = empty($locationTerm) || stripos($job['location'], $locationTerm) !== false;
                
                if ($titleMatch && $locationMatch) {
                    $resultsFound = true;
                    ?>
                    <div class="job-card">
                        <div class="job-title" onclick="toggleJobDetails(this)">
                            <span><?php echo htmlspecialchars($job['title']); ?></span>
                            <span class="expand-icon">▼</span>
                        </div>
                        <div class="job-location">Location: <?php echo htmlspecialchars($job['location']); ?></div>
                        <div class="job-description"><?php echo htmlspecialchars($job['description']); ?></div>
                        <div class="job-details">
                            <h3>Job Details</h3>
                            <p><strong>Payment:</strong> <?php echo htmlspecialchars($job['salary']); ?></p>
                            <p><strong>Hours:</strong> <?php echo htmlspecialchars($job['hours']); ?></p>
                            <p><strong>Requirements:</strong></p>
                            <ul>
                                <?php foreach ($job['requirements'] as $requirement): ?>
                                    <li><?php echo htmlspecialchars($requirement); ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <p><strong>Description:</strong> <?php echo htmlspecialchars($job['full_description']); ?></p>
                            <button class="apply-btn" onclick="applyForJob('<?php echo addslashes($job['title']); ?>', '<?php echo addslashes($job['location']); ?>')">Apply Now</button>
                        </div>
                    </div>
                    <?php
                }
            }
            
            if (!$resultsFound) {
                echo '<div class="no-results">
                    <h2>No side jobs found matching your search.</h2>
                    <p>Try using different keywords or browse all available jobs.</p>
                </div>';
            }
            ?>
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
            alert('Job posting functionality will be added towards the final project');
        }
    </script>
</body>
</html>
