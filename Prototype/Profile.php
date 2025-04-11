<?php
session_start();
include 'connect.php';

// If user isn't logged in then redirects to login page
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

// User data is collected from database
$email = $_SESSION['email'];
$sql = "SELECT * FROM users WHERE email=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $_SESSION['user_id'] = $user['id']; // Ensure user_id is in session
} else {
    echo "User not found.";
    exit();
}
$stmt->close();

// Handle user details update
if (isset($_POST['updateDetails'])) {
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $postalCode = $_POST['postalCode'];

    $updateQuery = "UPDATE users SET firstName=?, lastName=?, phoneNumber=?, firstLineAddress=?, postalCode=? WHERE email=?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("ssssss", $firstName, $lastName, $phone, $address, $postalCode, $email);
    if ($stmt->execute()) {
        $sql = "SELECT * FROM users WHERE email=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $successMessage = "Details updated successfully!";
    } else {
        $errorMessage = "Error updating details: " . $conn->error;
    }
    $stmt->close();
}

// Handle password change
if (isset($_POST['changePassword'])) {
    $currentPassword = $_POST['currentPassword'];
    $newPassword = $_POST['newPassword'];
    $confirmPassword = $_POST['confirmPassword'];

    $checkPassword = "SELECT password FROM users WHERE email=?";
    $stmt = $conn->prepare($checkPassword);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $passwordResult = $stmt->get_result();
    $row = $passwordResult->fetch_assoc();

    if ($row['password'] !== $currentPassword) {
        $errorMessage = "Current password is incorrect.";
    } elseif ($newPassword !== $confirmPassword) {
        $errorMessage = "New password and confirm password do not match.";
    } elseif (strlen($newPassword) < 8) {
        $errorMessage = "New password must be at least 8 characters long.";
    } else {
        $updatePasswordQuery = "UPDATE users SET password=? WHERE email=?";
        $stmt = $conn->prepare($updatePasswordQuery);
        $stmt->bind_param("ss", $newPassword, $email);
        if ($stmt->execute()) {
            $successMessage = "Password changed successfully!";
        } else {
            $errorMessage = "Error changing password: " . $conn->error;
        }
    }
    $stmt->close();
}

// Handle job completion
if (isset($_POST['completeJobs'])) {
    $completedJobs = isset($_POST['completedJobs']) ? $_POST['completedJobs'] : [];
    if (!empty($completedJobs)) {
        $placeholders = implode(',', array_fill(0, count($completedJobs), '?'));
        $types = str_repeat('i', count($completedJobs));
        $sql = "UPDATE job_listings SET job_status = 'completed' WHERE job_id IN ($placeholders) AND lister_id = ?";
        $types .= 'i';
        $params = array_merge($completedJobs, [$user['id']]);
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        if ($stmt->execute()) {
            $successMessage = "Selected jobs marked as completed!";
        } else {
            $errorMessage = "Error marking jobs as completed: " . $conn->error;
        }
        $stmt->close();
    }
}

// Fetch recent applications
$sql = "SELECT title, location, status, job_status, applicant_id 
        FROM job_listings 
        WHERE applicant_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user['id']);
$stmt->execute();
$result = $stmt->get_result();
$recent_applications = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $status_text = '';
        if ($row['job_status'] === 'completed') {
            $status_text = 'Completed';
        } elseif ($row['status'] == 1) {
            $status_text = 'Pending'; // Job is active, user is the applicant
        } elseif ($row['status'] == 0 && $row['applicant_id'] == $user['id']) {
            $status_text = 'Accepted'; // Job is taken, and user is the accepted applicant
        } elseif ($row['status'] == 0 && $row['applicant_id'] != $user['id']) {
            $status_text = 'Rejected'; // Job is taken, but another user was accepted
        }
        $row['status_text'] = $status_text;
        $recent_applications[] = $row;
    }
}
$stmt->close();

// Predefined list of locations (not needed anymore for the edit form, but keeping for consistency)
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
    <title>Profile - Job Advertisement Portal</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="profile_style.css">
</head>
<body>
    <!-- Top Navbar -->
    <div class="navbar">
        <div class="navbar-title">Job Advertisement Portal</div>
        <div class="navbar-links">
            <a href="index.php" class="nav-link">Home</a>
            <a href="logout.php" class="nav-link">Logout</a>
        </div>
    </div>

    <!-- Main Layout -->
    <div class="d-flex">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <h3><?php echo htmlspecialchars($user['firstName'] . ' ' . $user['lastName']); ?></h3>
            </div>
            <ul class="sidebar-nav">
                <li><a href="#" id="dashboardLink" class="active">Dashboard</a></li>
                <li><a href="#" id="manageListingsLink">Manage Listings</a></li>
                <li><a href="#" id="acceptApplicantLink">Applicants</a></li>
                <li><a href="#" id="completeJobLink">Complete Job</a></li>
                <li class="nav-item dropdown">
                    <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown">Settings</a>
                    <ul class="dropdown-menu">
                        <li><a href="#" class="dropdown-item" id="updateDetailsLink">Update Personal Details</a></li>
                        <li><a href="#" class="dropdown-item" id="changePasswordLink">Change Password</a></li>
                    </ul>
                </li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Success/Error Messages -->
            <?php if (isset($_GET['success'])) { ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($_GET['success']); ?></div>
            <?php } elseif (isset($successMessage)) { ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($successMessage); ?></div>
            <?php } ?>
            <?php if (isset($_GET['error'])) { ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($_GET['error']); ?></div>
            <?php } elseif (isset($errorMessage)) { ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($errorMessage); ?></div>
            <?php } ?>

            <!-- Dashboard Section -->
            <div class="content-section" id="dashboardSection">
                <h2>Welcome <?php echo htmlspecialchars($user['firstName'] . ' ' . $user['lastName']); ?></h2>
                <!-- Recent Applications Section -->
                <div class="mt-4">
                    <h3>Recent Applications</h3>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Job Title</th>
                                <th>Location</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recent_applications)) { ?>
                                <tr>
                                    <td colspan="3" class="text-center">No applications yet.</td>
                                </tr>
                            <?php } else { ?>
                                <?php foreach ($recent_applications as $application) { ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($application['title']); ?></td>
                                        <td><?php echo htmlspecialchars($application['location']); ?></td>
                                        <td><?php echo htmlspecialchars($application['status_text']); ?></td>
                                    </tr>
                                <?php } ?>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Manage Listings Section -->
            <div class="content-section" id="manageListingsSection" style="display: none;">
                <h2>Manage Listings</h2>
                <?php
                $user_id = $user['id'];
                $sql = "SELECT * FROM job_listings WHERE lister_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $status_text = '';
                        if ($row['job_status'] === 'completed') {
                            $status_text = 'Completed';
                            $disabled = 'disabled';
                        } elseif ($row['status'] == 1) {
                            $status_text = 'Active';
                            $disabled = '';
                        } else {
                            $status_text = 'Taken';
                            $disabled = 'disabled';
                        }
                        echo '<div class="job-card">';
                        echo '<h3>' . htmlspecialchars($row['title']) . '</h3>';
                        echo '<p>Status: ' . $status_text . '</p>';
                        echo '<button class="btn btn-danger" onclick="deleteListing(' . $row['job_id'] . ')" ' . $disabled . '>Delete Listing</button>';
                        echo '</div>';
                    }
                } else {
                    echo '<p>No listings found.</p>';
                }
                $stmt->close();
                ?>
            </div>

            <!-- Accept Applicant Section -->
            <div class="content-section" id="acceptApplicantSection" style="display: none;">
                <h2>Accept Applicant</h2>
                <?php
                $sql = "SELECT j.job_id, j.title, u.firstName, u.lastName 
                        FROM job_listings j 
                        LEFT JOIN users u ON j.applicant_id = u.id 
                        WHERE j.lister_id = ? AND j.applicant_id IS NOT NULL AND j.status = 1 AND j.job_status != 'completed'";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo '<div class="job-card">';
                        echo '<h3>' . htmlspecialchars($row['title']) . '</h3>';
                        echo '<p>Applicant: ' . htmlspecialchars($row['firstName'] . ' ' . $row['lastName']) . '</p>';
                        echo '<button class="btn btn-success me-2" onclick="acceptApplicant(' . $row['job_id'] . ')">Accept</button>';
                        echo '<button class="btn btn-danger" onclick="rejectApplicant(' . $row['job_id'] . ')">Reject</button>';
                        echo '</div>';
                    }
                } else {
                    echo '<p>No applicants found.</p>';
                }
                $stmt->close();
                ?>
            </div>

            <!-- Complete Job Section -->
            <div class="content-section" id="completeJobSection" style="display: none;">
                <h2>Complete Job</h2>
                <form id="completeJobForm" method="POST" action="">
                    <?php
                    $sql = "SELECT j.job_id, j.title, j.description, j.job_status, u.firstName, u.lastName 
                            FROM job_listings j 
                            LEFT JOIN users u ON j.applicant_id = u.id 
                            WHERE j.lister_id = ? AND j.status = 0 AND j.job_status = 'taken'";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $user_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo '<div class="job-card">';
                            echo '<h3>' . htmlspecialchars($row['title']) . '</h3>';
                            echo '<p><strong>Description:</strong> ' . htmlspecialchars($row['description']) . '</p>';
                            echo '<p><strong>Taken By:</strong> ' . htmlspecialchars($row['firstName'] . ' ' . $row['lastName']) . '</p>';
                            echo '<label><input type="checkbox" name="completedJobs[]" value="' . $row['job_id'] . '"> Mark as Completed</label>';
                            echo '</div>';
                        }
                        echo '<button type="submit" name="completeJobs" class="btn btn-primary mt-3">Update</button>';
                    } else {
                        echo '<p>No taken jobs to complete.</p>';
                    }
                    $stmt->close();
                    ?>
                </form>
            </div>

            <!-- Update Personal Details Section -->
            <div class="content-section" id="updateDetailsSection" style="display: none;">
                <h2>Update Personal Details</h2>
                <form id="profileForm" method="POST" action="">
                    <div class="mb-3">
                        <label for="firstName" class="form-label">First Name</label>
                        <input type="text" id="firstName" name="firstName" class="form-control" value="<?php echo htmlspecialchars($user['firstName']); ?>" placeholder="Your First Name">
                    </div>
                    <div class="mb-3">
                        <label for="lastName" class="form-label">Last Name</label>
                        <input type="text" id="lastName" name="lastName" class="form-control" value="<?php echo htmlspecialchars($user['lastName']); ?>" placeholder="Your Last Name">
                    </div>
                    <div class="mb-3">
                        <label for="userEmail" class="form-label">Email</label>
                        <input type="email" id="userEmail" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="userPhone" class="form-label">Phone</label>
                        <input type="text" id="userPhone" name="phone" class="form-control" value="<?php echo htmlspecialchars($user['phoneNumber']); ?>" placeholder="Your Phone">
                    </div>
                    <div class="mb-3">
                        <label for="userAddress" class="form-label">Address</label>
                        <input type="text" id="userAddress" name="address" class="form-control" value="<?php echo htmlspecialchars($user['firstLineAddress']); ?>" placeholder="Your Address">
                    </div>
                    <div class="mb-3">
                        <label for="postalCode" class="form-label">Postal Code</label>
                        <input type="text" id="postalCode" name="postalCode" class="form-control" value="<?php echo htmlspecialchars($user['postalCode']); ?>" placeholder="Your Postal Code">
                    </div>
                    <button type="submit" name="updateDetails" class="btn btn-primary w-100">Update Details</button>
                </form>
            </div>

            <!-- Change Password Section -->
            <div class="content-section" id="changePasswordSection" style="display: none;">
                <h2>Change Password</h2>
                <form id="passwordForm" method="POST" action="">
                    <div class="mb-3">
                        <label for="currentPassword" class="form-label">Current Password</label>
                        <input type="password" id="currentPassword" name="currentPassword" class="form-control" placeholder="Current Password">
                    </div>
                    <div class="mb-3">
                        <label for="newPassword" class="form-label">New Password</label>
                        <input type="password" id="newPassword" name="newPassword" class="form-control" placeholder="New Password">
                    </div>
                    <div class="mb-3">
                        <label for="confirmPassword" class="form-label">Confirm New Password</label>
                        <input type="password" id="confirmPassword" name="confirmPassword" class="form-control" placeholder="Confirm New Password">
                    </div>
                    <button type="submit" name="changePassword" class="btn btn-primary w-100">Change Password</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Function to show the Dashboard section
        function showDashboard() {
            document.getElementById('dashboardSection').style.display = 'block';
            document.getElementById('updateDetailsSection').style.display = 'none';
            document.getElementById('changePasswordSection').style.display = 'none';
            document.getElementById('manageListingsSection').style.display = 'none';
            document.getElementById('acceptApplicantSection').style.display = 'none';
            document.getElementById('completeJobSection').style.display = 'none';
            updateActiveLink('dashboardLink');
        }

        // Function to show the Update Personal Details section
        function showUpdateDetails() {
            document.getElementById('dashboardSection').style.display = 'none';
            document.getElementById('updateDetailsSection').style.display = 'block';
            document.getElementById('changePasswordSection').style.display = 'none';
            document.getElementById('manageListingsSection').style.display = 'none';
            document.getElementById('acceptApplicantSection').style.display = 'none';
            document.getElementById('completeJobSection').style.display = 'none';
            updateActiveLink('updateDetailsLink');
        }

        // Function to show the Change Password section
        function showChangePassword() {
            document.getElementById('dashboardSection').style.display = 'none';
            document.getElementById('updateDetailsSection').style.display = 'none';
            document.getElementById('changePasswordSection').style.display = 'block';
            document.getElementById('manageListingsSection').style.display = 'none';
            document.getElementById('acceptApplicantSection').style.display = 'none';
            document.getElementById('completeJobSection').style.display = 'none';
            updateActiveLink('changePasswordLink');
        }

        // Function to show the Manage Listings section
        function showManageListings() {
            document.getElementById('dashboardSection').style.display = 'none';
            document.getElementById('updateDetailsSection').style.display = 'none';
            document.getElementById('changePasswordSection').style.display = 'none';
            document.getElementById('manageListingsSection').style.display = 'block';
            document.getElementById('acceptApplicantSection').style.display = 'none';
            document.getElementById('completeJobSection').style.display = 'none';
            updateActiveLink('manageListingsLink');
        }

        // Function to show the Accept Applicant section
        function showAcceptApplicant() {
            document.getElementById('dashboardSection').style.display = 'none';
            document.getElementById('updateDetailsSection').style.display = 'none';
            document.getElementById('changePasswordSection').style.display = 'none';
            document.getElementById('manageListingsSection').style.display = 'none';
            document.getElementById('acceptApplicantSection').style.display = 'block';
            document.getElementById('completeJobSection').style.display = 'none';
            updateActiveLink('acceptApplicantLink');
        }

        // Function to show the Complete Job section
        function showCompleteJob() {
            document.getElementById('dashboardSection').style.display = 'none';
            document.getElementById('updateDetailsSection').style.display = 'none';
            document.getElementById('changePasswordSection').style.display = 'none';
            document.getElementById('manageListingsSection').style.display = 'none';
            document.getElementById('acceptApplicantSection').style.display = 'none';
            document.getElementById('completeJobSection').style.display = 'block';
            updateActiveLink('completeJobLink');
        }

        // Function to update the active link styling
        function updateActiveLink(linkId) {
            const links = document.querySelectorAll('.sidebar-nav a');
            links.forEach(link => link.classList.remove('active'));
            document.getElementById(linkId).classList.add('active');
        }

        // Function to delete a listing
        function deleteListing(jobId) {
            if (confirm('Are you sure you want to delete this listing?')) {
                window.location.href = 'delete_listing.php?id=' + jobId;
            }
        }

        // Function to accept an applicant
        function acceptApplicant(jobId) {
            if (confirm('Are you sure you want to accept this applicant?')) {
                window.location.href = 'accept_applicant.php?id=' + jobId;
            }
        }

        // Function to reject an applicant
        function rejectApplicant(jobId) {
            if (confirm('Are you sure you want to reject this applicant? This will reopen the job for new applications.')) {
                window.location.href = 'reject_applicant.php?id=' + jobId;
            }
        }

        // Attach event listeners after the DOM is fully loaded
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('dashboardLink').addEventListener('click', function(e) {
                e.preventDefault();
                showDashboard();
            });

            document.getElementById('manageListingsLink').addEventListener('click', function(e) {
                e.preventDefault();
                showManageListings();
            });

            document.getElementById('acceptApplicantLink').addEventListener('click', function(e) {
                e.preventDefault();
                showAcceptApplicant();
            });

            document.getElementById('completeJobLink').addEventListener('click', function(e) {
                e.preventDefault();
                showCompleteJob();
            });

            document.getElementById('updateDetailsLink').addEventListener('click', function(e) {
                e.preventDefault();
                showUpdateDetails();
            });

            document.getElementById('changePasswordLink').addEventListener('click', function(e) {
                e.preventDefault();
                showChangePassword();
            });

            // Show dashboard by default
            showDashboard();
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
