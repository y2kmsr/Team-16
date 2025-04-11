<?php
session_start();
include("connect.php");

// Ensure $is_admin is false by default and only true if set
$is_admin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true ? true : false;

// Fetch user ID if logged in
$user_id = null;
if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $user_id = $row['id'];
        $_SESSION['user_id'] = $user_id; // Store user_id in session for later use
    }
    $stmt->close();
}

// Get filter values from URL
$searchTerm = isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '';
$locationTerm = isset($_GET['location']) ? htmlspecialchars($_GET['location']) : '';
$payRange = isset($_GET['pay']) ? htmlspecialchars($_GET['pay']) : '';

// Fetch job listings from the database with filters applied
$sql = "SELECT * FROM job_listings WHERE admin_approval = 1 AND status = 1";
$conditions = [];
$params = [];
$types = "";

// Apply search term filter
if (!empty($searchTerm)) {
    $conditions[] = "(title LIKE ? OR description LIKE ?)";
    $searchWild = "%$searchTerm%";
    $params[] = $searchWild;
    $params[] = $searchWild;
    $types .= "ss";
}

// Apply location filter
if (!empty($locationTerm)) {
    $conditions[] = "location = ?";
    $params[] = $locationTerm;
    $types .= "s";
}

// Apply pay range filter
if (!empty($payRange)) {
    $payRanges = [
        '5-10' => [5, 10],
        '10-20' => [10, 20],
        '20-70' => [20, 70],
        '70-200' => [70, 200],
        '200-1000' => [200, 1000]
    ];
    if (isset($payRanges[$payRange])) {
        $min = $payRanges[$payRange][0];
        $max = $payRanges[$payRange][1];
        // Extract numeric part of salary for comparison
        $conditions[] = "CAST(REPLACE(salary, '£', '') AS DECIMAL) BETWEEN ? AND ?";
        $params[] = $min;
        $params[] = $max;
        $types .= "ii";
    }
}

// Build the final query
if (!empty($conditions)) {
    $sql .= " AND " . implode(" AND ", $conditions);
}
$sql .= " ORDER BY job_id DESC";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$jobListings = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $row['requirements'] = json_decode($row['requirements'], true); 
        $jobListings[] = $row;
    }
}
$stmt->close();

// Predefined list of locations
$locations = [
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Side Jobs Search Results</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="search_results_style.css">
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
        <li><a href="search_results.php" class="secondary-nav-link active">Search for Jobs</a></li>
        <li><a href="AboutUs.php" class="secondary-nav-link">About Us</a></li>
        <li><a href="ContactUs.php" class="secondary-nav-link">Contact Us</a></li>
    </ul>
</div>

<div class="container">
    <div id="jobPostingForm" style="display: none;" class="job-posting-form">
        <h2>Post a New Job</h2>
        <form method="POST" action="post_job.php" onsubmit="return validateDescription('description')">
            <input type="text" name="title" placeholder="Job Title" required>
            <select name="location" required>
                <option value="" disabled selected>Select Location</option>
                <?php foreach ($locations as $loc) { ?>
                    <option value="<?php echo htmlspecialchars($loc); ?>"><?php echo htmlspecialchars($loc); ?></option>
                <?php } ?>
            </select>
            <textarea name="description" id="description" placeholder="Description (max 400 words)" required oninput="updateWordCount(this, 'wordCount')"></textarea>
            <div id="wordCount" class="word-count">0/400 words</div>
            <input type="text" name="salary" placeholder="Salary (e.g., £500)" required>
            <textarea name="requirements" placeholder="Requirements (comma-separated)" required></textarea>
            <input type="hidden" name="lister_id" value="<?php echo $user_id ?? 2; ?>">
            <button type="submit" class="apply-btn">Submit Job</button>
        </form>
    </div>

    <div class="results-header">
        <div class="results-title">
            <?php
            echo "Search Results";
            if (!empty($searchTerm) || !empty($locationTerm) || !empty($payRange)) {
                echo " for";
                if (!empty($searchTerm)) {
                    echo " \"$searchTerm\"";
                }
                if (!empty($locationTerm)) {
                    echo " in $locationTerm";
                }
                if (!empty($payRange)) {
                    echo " with pay range $payRange";
                }
            }
            ?>
        </div>
        <div class="header-buttons">
            <?php if (isset($_SESSION['email']) && !$is_admin) { ?>
                <button class="floating-post-btn" onclick="showJobPostingForm()">Post a Job</button>
            <?php } ?>
            <a href="index.php"><button class="back-btn">Back to Search</button></a>
        </div>
    </div>

    <div class="filter-container">
        <div class="filter-group">
            <label for="locationFilter">Location:</label>
            <select id="locationFilter" onchange="applyFilters()">
                <option value="">All Locations</option>
                <?php foreach ($locations as $loc) { ?>
                    <option value="<?php echo htmlspecialchars($loc); ?>" <?php echo $locationTerm === $loc ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($loc); ?>
                    </option>
                <?php } ?>
            </select>
        </div>
        <div class="filter-group">
            <label for="payFilter">Pay Range:</label>
            <select id="payFilter" onchange="applyFilters()">
                <option value="">All Pay Ranges</option>
                <option value="5-10" <?php echo $payRange === '5-10' ? 'selected' : ''; ?>>£5 - £10</option>
                <option value="10-20" <?php echo $payRange === '10-20' ? 'selected' : ''; ?>>£10 - £20</option>
                <option value="20-70" <?php echo $payRange === '20-70' ? 'selected' : ''; ?>>£20 - £70</option>
                <option value="70-200" <?php echo $payRange === '70-200' ? 'selected' : ''; ?>>£70 - £200</option>
                <option value="200-1000" <?php echo $payRange === '200-1000' ? 'selected' : ''; ?>>£200 - £1000</option>
            </select>
        </div>
    </div>

    <div class="search-container">
        <form action="search_results.php" method="GET">
            <input type="text" class="search-bar" id="job-search" name="search" placeholder="Search for side jobs..." value="<?php echo $searchTerm; ?>">
            <input type="hidden" name="location" value="<?php echo $locationTerm; ?>">
            <input type="hidden" name="pay" value="<?php echo $payRange; ?>">
            <button class="search-btn" type="submit">Search</button>
        </form>
    </div>
    
    <div class="results-container" id="results">
        <?php
        $resultsFound = false;
        
        foreach ($jobListings as $job) {
            $resultsFound = true;
            ?>
            <div class="job-card" data-location="<?php echo htmlspecialchars($job['location']); ?>" data-salary="<?php echo htmlspecialchars($job['salary']); ?>">
                <div class="job-title" onclick="toggleJobDetails(this)">
                    <span><?php echo htmlspecialchars($job['title']); ?></span>
                    <span class="expand-icon">▼</span>
                </div>
                <div class="job-location">Location: <?php echo htmlspecialchars($job['location']); ?></div>
                <div class="job-description"><?php echo htmlspecialchars($job['description']); ?></div>
                <div class="job-details">
                    <h3>Job Details</h3>
                    <p><strong>Payment:</strong> <?php echo htmlspecialchars($job['salary']); ?></p>
                    <p><strong>Requirements:</strong></p>
                    <ul>
                        <?php foreach ($job['requirements'] as $requirement): ?>
                            <li><?php echo htmlspecialchars($requirement); ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <button class="apply-btn" onclick="applyForJob('<?php echo addslashes($job['title']); ?>', '<?php echo addslashes($job['location']); ?>', <?php echo $job['job_id']; ?>)">Apply Now</button>
                </div>
            </div>
            <?php
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
    
    function applyForJob(title, location, jobId) {
        <?php if (!isset($_SESSION['email'])) { ?>
            alert("You must sign in or create an account");
        <?php } else { ?>
            if (confirm(`Are you sure you want to apply for the ${title} position in ${location}?`)) {
                var xhr = new XMLHttpRequest();
                xhr.open("POST", "apply_job.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        alert("Application submitted!");
                        location.reload();
                    }
                };
                xhr.send("job_id=" + jobId + "&user_id=" + <?php echo json_encode($user_id ?? 0); ?>);
            }
        <?php } ?>
    }

    function showJobPostingForm() {
        const form = document.getElementById('jobPostingForm');
        if (form.style.display === 'block') {
            form.style.display = 'none';
        } else {
            form.style.display = 'block';
        }
    }

    function updateWordCount(textarea, counterId) {
        const text = textarea.value;
        const words = text.trim().split(/\s+/).filter(word => word.length > 0);
        const wordCount = words.length;
        const counter = document.getElementById(counterId);
        counter.textContent = `${wordCount}/400 words`;
        if (wordCount > 400) {
            counter.classList.add('error');
        } else {
            counter.classList.remove('error');
        }
    }

    function validateDescription(fieldId) {
        const textarea = document.getElementById(fieldId);
        const text = textarea.value;
        const words = text.trim().split(/\s+/).filter(word => word.length > 0);
        const wordCount = words.length;
        if (wordCount > 400) {
            alert('Description exceeds 400 words. Please shorten it.');
            return false;
        }
        return true;
    }

    function applyFilters() {
        const locationFilter = document.getElementById('locationFilter').value;
        const payFilter = document.getElementById('payFilter').value;
        const searchTerm = document.getElementById('job-search').value;

        // Update the URL with the current search term and filters
        const params = new URLSearchParams();
        if (searchTerm) params.set('search', searchTerm);
        if (locationFilter) params.set('location', locationFilter);
        if (payFilter) params.set('pay', payFilter);

        // Reload the page with the updated URL
        window.location.href = `search_results.php?${params.toString()}`;
    }
</script>
</body>
</html>
