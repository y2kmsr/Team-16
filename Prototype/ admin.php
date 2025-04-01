<?php
include 'connect.php';

// Function to get the user table HTML
function generateUserTable() {
    global $conn;

    $sql = "SELECT id, firstName, lastName FROM users"; // Grab user data
    $result = $conn->query($sql);

    $usersList = []; // Changed variable name for variety
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

// Placeholder for adding an admin
function addAdmin() {
    return "Add Admin functionality will be implemented here.";
}

// Check which page to show
$section = isset($_GET['section']) ? $_GET['section'] : 'dashboard';
$pageContent = ''; // Changed variable name

if ($section === 'view_users') {
    $pageContent = generateUserTable();
} elseif ($section === 'delete_users') {
    $pageContent = '
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
        $pageContent .= "<p>User deleted successfully!</p>";
    } else {
        $pageContent .= "<p>Oops, error deleting user: " . $stmt->error . "</p>";
    }

    $stmt->close();
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
    <div class="top-bar">Job Portal Admin</div>
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
            </ul>
        </div>
        <div class="main-content">
            <?php echo $pageContent; ?>
        </div>
    </div>
</body>

</html>
