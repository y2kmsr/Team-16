<?php
include 'connect.php';
session_start();

// Redirect to login if not an admin
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: login.php");
    exit();
}

// Function to get the user table HTML
function generateUserTable() {
    global $conn;

    $sql = "SELECT id, firstName, lastName FROM users"; // Grab user data
    $result = $conn->query($sql);

    $usersList = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $usersList[] = $row;    //stores it into an array
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

    // Handle form submission
    if (isset($_POST['addAdmin'])) {
        $firstName = $_POST['firstName'];
        $lastName = $_POST['lastName'];
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Check if email already exists
        $checkEmail = "SELECT * FROM admins WHERE email = ?";
        $stmt = $conn->prepare($checkEmail);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $message = "<p class='error-message'>Error: Email address already exists.</p>";
        } else {
            // Insert new admin into the database
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

    // Form for adding a new admin
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

// Check which page to show
$section = isset($_GET['section']) ? $_GET['section'] : 'dashboard';
$pageContent = '';
$deleteMessage = ''; // Variable for the error message or success

if ($section === 'view_users') {
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
