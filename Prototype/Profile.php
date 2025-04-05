<?php
session_start();
include 'connect.php';

// If user isnt logged in then redirects to login page
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

// User data is collected from database
$email = $_SESSION['email'];
$sql = "SELECT * FROM users WHERE email='$email'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    echo "User not found.";
    exit();
}

// Handle user details update
if (isset($_POST['updateDetails'])) {
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $postalCode = $_POST['postalCode'];

    $updateQuery = "UPDATE users SET firstName='$firstName', lastName='$lastName', phoneNumber='$phone', firstLineAddress='$address', postalCode='$postalCode' WHERE email='$email'";
    if ($conn->query($updateQuery) === TRUE) {
        // Refresh user data
        $sql = "SELECT * FROM users WHERE email='$email'";
        $result = $conn->query($sql);
        $user = $result->fetch_assoc();
        $successMessage = "Details updated successfully!";
    } else {
        $errorMessage = "Error updating details: " . $conn->error;
    }
}

// Handle password change
if (isset($_POST['changePassword'])) {
    $currentPassword = $_POST['currentPassword'];
    $newPassword = $_POST['newPassword'];
    $confirmPassword = $_POST['confirmPassword'];

    // Verify current password
    $checkPassword = "SELECT password FROM users WHERE email='$email'";
    $passwordResult = $conn->query($checkPassword);
    $row = $passwordResult->fetch_assoc();

    if ($row['password'] !== $currentPassword) {
        $errorMessage = "Current password is incorrect.";
    } elseif ($newPassword !== $confirmPassword) {
        $errorMessage = "New password and confirm password do not match.";
    } elseif (strlen($newPassword) < 8) {
        $errorMessage = "New password must be at least 8 characters long.";
    } else {
        $updatePasswordQuery = "UPDATE users SET password='$newPassword' WHERE email='$email'";
        if ($conn->query($updatePasswordQuery) === TRUE) {
            $successMessage = "Password changed successfully!";
        } else {
            $errorMessage = "Error changing password: " . $conn->error;
        }
    }
}

$recent_applications = [];
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
                <h3><?php echo $user['firstName'] . ' ' . $user['lastName']; ?></h3>
            </div>
            <ul class="sidebar-nav">
                <li><a href="profile.php" class="active">Dashboard</a></li>
                <li class="nav-item dropdown">
                    <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown">Settings</a>
                    <ul class="dropdown-menu">
                        <li><a href="#" class="dropdown-item" onclick="showUpdateDetails()">Update Personal Details</a></li>
                        <li><a href="#" class="dropdown-item" onclick="showChangePassword()">Change Password</a></li>
                    </ul>
                </li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Success/Error Messages -->
            <?php if (isset($successMessage)) { ?>
                <div class="alert alert-success"><?php echo $successMessage; ?></div>
            <?php } ?>
            <?php if (isset($errorMessage)) { ?>
                <div class="alert alert-danger"><?php echo $errorMessage; ?></div>
            <?php } ?>

            <!-- Dashboard Section -->
            <div class="content-section" id="dashboardSection">
                <h2>Welcome <?php echo $user['firstName'] . ' ' . $user['lastName']; ?></h2>
                <!-- Recent Applications Section -->
                <div class="mt-4">
                    <h3>Recent Applications</h3>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Job Title</th>
                                <th>Company</th>
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
                                        <td><?php echo $application['job_title']; ?></td>
                                        <td><?php echo $application['company']; ?></td>
                                        <td><?php echo $application['status']; ?></td>
                                    </tr>
                                <?php } ?>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Update Personal Details Section (Hidden by Default) -->
            <div class="content-section" id="updateDetailsSection" style="display: none;">
                <h2>Update Personal Details</h2>
                <form id="profileForm" method="POST" action="">
                    <div class="mb-3">
                        <label for="firstName" class="form-label">First Name</label>
                        <input type="text" id="firstName" name="firstName" class="form-control" value="<?php echo $user['firstName']; ?>" placeholder="Your First Name">
                    </div>
                    <div class="mb-3">
                        <label for="lastName" class="form-label">Last Name</label>
                        <input type="text" id="lastName" name="lastName" class="form-control" value="<?php echo $user['lastName']; ?>" placeholder="Your Last Name">
                    </div>
                    <div class="mb-3">
                        <label for="userEmail" class="form-label">Email</label>
                        <input type="email" id="userEmail" name="email" class="form-control" value="<?php echo $user['email']; ?>" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="userPhone" class="form-label">Phone</label>
                        <input type="text" id="userPhone" name="phone" class="form-control" value="<?php echo $user['phoneNumber']; ?>" placeholder="Your Phone">
                    </div>
                    <div class="mb-3">
                        <label for="userAddress" class="form-label">Address</label>
                        <input type="text" id="userAddress" name="address" class="form-control" value="<?php echo $user['firstLineAddress']; ?>" placeholder="Your Address">
                    </div>
                    <div class="mb-3">
                        <label for="postalCode" class="form-label">Postal Code</label>
                        <input type="text" id="postalCode" name="postalCode" class="form-control" value="<?php echo $user['postalCode']; ?>" placeholder="Your Postal Code">
                    </div>
                    <button type="submit" name="updateDetails" class="btn btn-primary w-100">Update Details</button>
                </form>
            </div>

            <!-- Change Password Section (Hidden by Default) -->
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
        }

        // Function to show the Update Personal Details section
        function showUpdateDetails() {
            document.getElementById('dashboardSection').style.display = 'none';
            document.getElementById('updateDetailsSection').style.display = 'block';
            document.getElementById('changePasswordSection').style.display = 'none';
        }

        // Function to show the Change Password section
        function showChangePassword() {
            document.getElementById('dashboardSection').style.display = 'none';
            document.getElementById('updateDetailsSection').style.display = 'none';
            document.getElementById('changePasswordSection').style.display = 'block';
        }

        // Show dashboard by default
        showDashboard();
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
