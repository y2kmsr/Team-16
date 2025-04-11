<?php
include 'connect.php';
session_start();

// Redirect to login if not an admin
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: login.php");
    exit();
}

// Retrieve the admin's ID
$admin_id = null;
if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];
    $stmt = $conn->prepare("SELECT id FROM admins WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $admin_id = $row['id'];
    }
    $stmt->close();
}

// Redirect to login if admin_id couldn't be retrieved
if ($admin_id === null) {
    header("Location: login.php");
    exit();
}

// Function to get the user table HTML
function generateUserTable() {
    global $conn;

    $sql = "SELECT id, firstName, lastName FROM users";
    $result = $conn->query($sql);

    $usersList = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $usersList[] = $row;
        }
    }

    $tableHtml = '<table class="user-table">';
    $tableHtml .= '<thead><tr><th>ID</th><th>First Name</th><th>Last Name</th></tr></thead>';
    $tableHtml .= '<tbody>';

    if ($usersList && is_array($usersList)) {
        foreach ($usersList as $user) {
            $tableHtml .= '<tr>';
            $tableHtml .= '<td>' . htmlspecialchars($user['id']) . '</td>';
            $tableHtml .= '<td>' . htmlspecialchars($user['firstName']) . '</td>';
            $tableHtml .= '<td>' . htmlspecialchars($user['lastName']) . '</td>';
            $tableHtml .= '</tr>';
        }
    } else {
        $tableHtml .= '<tr><td colspan="3">No users found.</td></tr>';
    }

    $tableHtml .= '</tbody></table>';
    return $tableHtml;
}

// Function to add a new admin
function addAdmin() {
    global $conn;

    $message = '';

    if (isset($_POST['addAdmin'])) {
        $firstName = $_POST['firstName'];
        $lastName = $_POST['lastName'];
        $email = $_POST['email'];
        $password = $_POST['password'];

        $checkEmail = "SELECT * FROM admins WHERE email = ?";
        $stmt = $conn->prepare($checkEmail);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $message = "<p class='error-message'>Error: Email address already exists.</p>";
        } else {
            $insertQuery = "INSERT INTO admins (firstName, lastName, email, password) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($insertQuery);
            $stmt->bind_param("ssss", $firstName, $lastName, $email, $password);

            if ($stmt->execute()) {
                $message = "<p class='success-message'>Admin added successfully!</p>";
            } else {
                $message = "<p class='error-message'>Error adding admin: " . $stmt->error . "</p>";
            }
        }

        $stmt->close();
    }

    $form = '
        <h3>Add New Admin</h3>
        <form method="post" class="add-admin-form">
            <input type="text" name="firstName" placeholder="First Name" required><br>
            <input type="text" name="lastName" placeholder="Last Name" required><br>
            <input type="email" name="email" placeholder="Email" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <button class="admin-btn" type="submit" name="addAdmin">Add Admin</button>
        </form>
    ';

    return $message . $form;
}

// Function to generate the enquiries table
function generateEnquiriesTable() {
    global $conn;

    $sql = "SELECT * FROM enquiries WHERE Resolved = 0";
    $result = $conn->query($sql);

    $tableHtml = '<form method="post" class="update-form">';
    $tableHtml .= '<table class="enquiries-table">';
    $tableHtml .= '<thead><tr><th>ID</th><th>Enquiry</th><th>Email</th><th>Phone Number</th><th>Enquiry Handled</th></tr></thead>';
    $tableHtml .= '<tbody>';

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $tableHtml .= '<tr>';
            $tableHtml .= '<td>' . htmlspecialchars($row['EnquiryID']) . '</td>';
            $tableHtml .= '<td>' . htmlspecialchars($row['Enquiry']) . '</td>';
            $tableHtml .= '<td>' . htmlspecialchars($row['Email']) . '</td>';
            $tableHtml .= '<td>' . htmlspecialchars($row['PhoneNumber']) . '</td>';
            $tableHtml .= '<td><input type="checkbox" name="enquiries[]" value="' . $row['EnquiryID'] . '"></td>';
            $tableHtml .= '</tr>';
        }
    } else {
        $tableHtml .= '<tr><td colspan="5">No unresolved enquiries found.</td></tr>';
    }

    $tableHtml .= '</tbody></table>';
    $tableHtml .= '<button class="admin-btn" type="submit" name="updateEnquiries">Update List</button>';
    $tableHtml .= '</form>';

    return $tableHtml;
}

// Function to generate the complaints table
function generateComplaintsTable() {
    global $conn;

    $sql = "SELECT * FROM complaints WHERE Resolved = 0";
    $result = $conn->query($sql);

    $tableHtml = '<form method="post" class="update-form">';
    $tableHtml .= '<table class="complaints-table">';
    $tableHtml .= '<thead><tr><th>ID</th><th>Description</th><th>Date</th><th>Type</th><th>Report Handled</th></tr></thead>';
    $tableHtml .= '<tbody>';

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $tableHtml .= '<tr>';
            $tableHtml .= '<td>' . htmlspecialchars($row['Complaint_id']) . '</td>';
            $tableHtml .= '<td>' . htmlspecialchars($row['description']) . '</td>';
            $tableHtml .= '<td>' . htmlspecialchars($row['ComplaintDate']) . '</td>';
            $tableHtml .= '<td>' . htmlspecialchars($row['TypeOfComplaint']) . '</td>';
            $tableHtml .= '<td><input type="checkbox" name="complaints[]" value="' . $row['Complaint_id'] . '"></td>';
            $tableHtml .= '</tr>';
        }
    } else {
        $tableHtml .= '<tr><td colspan="5">No unresolved complaints found.</td></tr>';
    }

    $tableHtml .= '</tbody></table>';
    $tableHtml .= '<button class="admin-btn" type="submit" name="updateComplaints">Update List</button>';
    $tableHtml .= '</form>';

    return $tableHtml;
}

// Function to generate the listings approval table
function generateListingsApprovalTable() {
    global $conn;

    $sql = "SELECT * FROM job_listings WHERE admin_approval = 0";
    $result = $conn->query($sql);

    $tableHtml = '<form method="post" class="update-form">';
    $tableHtml .= '<table class="user-table">';
    $tableHtml .= '<thead><tr><th>Job ID</th><th>Title</th><th>Location</th><th>Approve</th></tr></thead>';
    $tableHtml .= '<tbody>';

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $tableHtml .= '<tr>';
            $tableHtml .= '<td>' . htmlspecialchars($row['job_id']) . '</td>';
            $tableHtml .= '<td>' . htmlspecialchars($row['title']) . '</td>';
            $tableHtml .= '<td>' . htmlspecialchars($row['location']) . '</td>';
            $tableHtml .= '<td><input type="checkbox" name="listings[]" value="' . $row['job_id'] . '"></td>';
            $tableHtml .= '</tr>';
        }
    } else {
        $tableHtml .= '<tr><td colspan="4">No unapproved listings found.</td></tr>';
    }

    $tableHtml .= '</tbody></table>';
    $tableHtml .= '<button class="admin-btn" type="submit" name="approveListings">Approve Listings</button>';
    $tableHtml .= '</form>';

    return $tableHtml;
}

// Function to generate the delete listings table
function generateDeleteListingsTable() {
    global $conn;

    $sql = "SELECT j.job_id, j.lister_id, j.title, u.firstName, u.lastName 
            FROM job_listings j 
            LEFT JOIN users u ON j.lister_id = u.id";
    $result = $conn->query($sql);

    $tableHtml = '<form method="post" class="update-form">';
    $tableHtml .= '<table class="user-table">';
    $tableHtml .= '<thead><tr><th>Job ID</th><th>Title</th><th>Lister ID</th><th>First Name</th><th>Last Name</th><th>Delete</th></tr></thead>';
    $tableHtml .= '<tbody>';

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $tableHtml .= '<tr>';
            $tableHtml .= '<td>' . htmlspecialchars($row['job_id']) . '</td>';
            $tableHtml .= '<td>' . htmlspecialchars($row['title']) . '</td>';
            $tableHtml .= '<td>' . htmlspecialchars($row['lister_id']) . '</td>';
            $tableHtml .= '<td>' . htmlspecialchars($row['firstName']) . '</td>';
            $tableHtml .= '<td>' . htmlspecialchars($row['lastName']) . '</td>';
            $tableHtml .= '<td><input type="checkbox" name="listings[]" value="' . $row['job_id'] . '"></td>';
            $tableHtml .= '</tr>';
        }
    } else {
        $tableHtml .= '<tr><td colspan="6">No listings found.</td></tr>';
    }

    $tableHtml .= '</tbody></table>';
    $tableHtml .= '<button class="admin-btn" type="submit" name="deleteListings">Delete Listings</button>';
    $tableHtml .= '</form>';

    return $tableHtml;
}

// Handle enquiries update
if (isset($_POST['updateEnquiries'])) {
    $selectedEnquiries = isset($_POST['enquiries']) ? $_POST['enquiries'] : [];
    if (!empty($selectedEnquiries)) {
        $ids = implode(',', array_map('intval', $selectedEnquiries));
        $sql = "UPDATE enquiries SET Resolved = 1 WHERE EnquiryID IN ($ids)";
        $conn->query($sql);
    }
    header("Location: admin.php?section=enquiries");
    exit();
}

// Handle complaints update
if (isset($_POST['updateComplaints'])) {
    $selectedComplaints = isset($_POST['complaints']) ? $_POST['complaints'] : [];
    if (!empty($selectedComplaints)) {
        $ids = implode(',', array_map('intval', $selectedComplaints));
        $sql = "UPDATE complaints SET Resolved = 1 WHERE Complaint_id IN ($ids)";
        $conn->query($sql);
    }
    header("Location: admin.php?section=complaints");
    exit();
}

// Handle listings approval
if (isset($_POST['approveListings'])) {
    $selectedListings = isset($_POST['listings']) ? $_POST['listings'] : [];
    if (!empty($selectedListings)) {
        $ids = implode(',', array_map('intval', $selectedListings));
        $sql = "UPDATE job_listings SET admin_approval = 1 WHERE job_id IN ($ids)";
        $conn->query($sql);
    }
    header("Location: admin.php?section=listings_approval");
    exit();
}

// Handle listings deletion
if (isset($_POST['deleteListings'])) {
    $selectedListings = isset($_POST['listings']) ? $_POST['listings'] : [];
    if (!empty($selectedListings)) {
        $ids = implode(',', array_map('intval', $selectedListings));
        $sql = "DELETE FROM job_listings WHERE job_id IN ($ids)";
        $conn->query($sql);
    }
    header("Location: admin.php?section=delete_listing");
    exit();
}

// Handle moving resolved items
if (isset($_POST['moveResolved'])) {
    if ($admin_id === null) {
        $pageContent .= "<p class='error-message'>Error: Admin ID not found. Please ensure you are logged in as a valid admin.</p>";
    } else {
        $stmt = $conn->prepare("INSERT INTO resolved (type, original_id, data, admin_id) 
                                SELECT 'enquiry', EnquiryID, JSON_OBJECT('Enquiry', Enquiry, 'Email', Email, 'PhoneNumber', PhoneNumber), ? 
                                FROM enquiries WHERE Resolved = 1");
        $stmt->bind_param("i", $admin_id);
        $stmt->execute();
        $stmt->close();
        $conn->query("DELETE FROM enquiries WHERE Resolved = 1");

        $stmt = $conn->prepare("INSERT INTO resolved (type, original_id, data, admin_id) 
                                SELECT 'complaint', Complaint_id, JSON_OBJECT('description', description, 'ComplaintDate', ComplaintDate, 'TypeOfComplaint', TypeOfComplaint), ? 
                                FROM complaints WHERE Resolved = 1");
        $stmt->bind_param("i", $admin_id);
        $stmt->execute();
        $stmt->close();
        $conn->query("DELETE FROM complaints WHERE Resolved = 1");

        $pageContent .= "<p class='success-message'>Resolved items moved successfully.</p>";
    }
}

// Check which page to show
$section = isset($_GET['section']) ? $_GET['section'] : 'dashboard';
$pageContent = '';
$deleteMessage = '';

if ($section === 'dashboard') {
    $enquiriesCount = $conn->query("SELECT COUNT(*) FROM enquiries WHERE Resolved = 0")->fetch_row()[0];
    $complaintsCount = $conn->query("SELECT COUNT(*) FROM complaints WHERE Resolved = 0")->fetch_row()[0];
    $totalUnresolved = $enquiriesCount + $complaintsCount;

    $pageContent = "<p>Welcome to the Job Portal Admin Dashboard.</p>";
    $pageContent .= "<p class='alert'>There are $totalUnresolved unresolved items (Enquiries: $enquiriesCount, Complaints: $complaintsCount).</p>";
    $pageContent .= '<form method="post"><button class="admin-btn" type="submit" name="moveResolved">Move All Resolved to Resolved Table</button></form>';
} elseif ($section === 'view_users') {
    $pageContent = generateUserTable();
} elseif ($section === 'delete_users') {
    $pageContent = '
        <h3>Delete User</h3>
        <form method="post" class="delete-form" id="deleteForm">
            <input type="text" name="userId" placeholder="User ID" required><br>
            <input type="text" name="firstName" placeholder="First Name" required><br>
            <input type="text" name="lastName" placeholder="Last Name" required><br>
            <button class="admin-btn" type="submit" name="deleteUser">Delete User</button>
        </form>
    ';
} elseif ($section === 'create_admin') {
    $pageContent = addAdmin();
} elseif ($section === 'enquiries') {
    $pageContent = generateEnquiriesTable();
} elseif ($section === 'complaints') {
    $pageContent = generateComplaintsTable();
} elseif ($section === 'listings_approval') {
    $pageContent = generateListingsApprovalTable();
} elseif ($section === 'delete_listing') {
    $pageContent = generateDeleteListingsTable();
} else {
    $pageContent = '<p>Welcome to the Job Portal Admin Dashboard.</p>';
}

// Handle user deletion
if (isset($_POST['deleteUser'])) {
    $userId = $_POST['userId'];
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];

    $stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND firstName = ? AND lastName = ?");
    $stmt->bind_param("iss", $userId, $firstName, $lastName);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            $deleteMessage = "<p class='success-message'>User deleted successfully!</p>";
        } else {
            $deleteMessage = "<p class='error-message'>No user found with the provided details.</p>";
        }
    } else {
        $deleteMessage = "<p class='error-message'>Error deleting user: " . $stmt->error . "</p>";
    }

    $stmt->close();

    if ($section === 'delete_users') {
        $pageContent = '
            <h3>Delete User</h3>
            ' . $deleteMessage . '
            <form method="post" class="delete-form" id="deleteForm">
                <input type="text" name="userId" placeholder="User ID" required><br>
                <input type="text" name="firstName" placeholder="First Name" required><br>
                <input type="text" name="lastName" placeholder="Last Name" required><br>
                <button class="admin-btn" type="submit" name="deleteUser">Delete User</button>
            </form>
        ';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Portal Admin</title>
    <link rel="stylesheet" href="admin_style.css">
</head>

<body>
    <div class="top-bar">
        Admin Centre
    </div>
    <div class="admin-container">
        <div class="sidebar">
            <h2>Navigation</h2>
            <ul>
                <li><a href="?section=dashboard">Dashboard</a></li>
                <li>
                    <h3>Users</h3>
                    <ul>
                        <li><a href="?section=view_users">View Users</a></li>
                        <li><a href="?section=delete_users">Delete Users</a></li>
                        <li><a href="?section=listings_approval">Listings Approval</a></li>
                        <li><a href="?section=delete_listing">Delete Listing</a></li>
                        <li><a href="?section=enquiries">Enquiries</a></li>
                        <li><a href="?section=complaints">Complaints</a></li>
                    </ul>
                </li>
                <li>
                    <h3>Admins</h3>
                    <ul>
                        <li><a href="?section=create_admin">Create Admin</a></li>
                    </ul>
                </li>
                <li>
                    <h3>Other</h3>
                    <ul>
                        <li><a href="index.php" target="_blank" class="go-home-link">Go to Home</a></li>
                    </ul>
                </li>
            </ul>
        </div>
        <div class="main-content">
            <?php echo $pageContent; ?>
        </div>
    </div>
</body>

</html>
