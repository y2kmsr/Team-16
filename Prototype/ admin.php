<?php
include 'connect.php';

function generateUserTable()
{
    global $conn;

    $sql = "SELECT id, firstName, lastName FROM users";
    $result = $conn->query($sql);
    // query to get the id, firstName and lastName from users table

    $users = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
    }
    //Array is created,checks to see if query returned any rows and adds each row to array
    //using a loop

    $table = '<table class="user-table">'; 
    $table .= '<thead><tr><th>ID</th><th>First Name</th><th>Last Name</th></tr></thead>';
    $table .= '<tbody>';
    // Table is created

    if ($users && is_array($users)) {
        foreach ($users as $user) {
            $table .= '<tr>';
            $table .= '<td>' . htmlspecialchars($user['id']) . '</td>';
            $table .= '<td>' . htmlspecialchars($user['firstName']) . '</td>';
            $table .= '<td>' . htmlspecialchars($user['lastName']) . '</td>';
            $table .= '</tr>';
        }
        // Users are added to the table
    } else {
        $table .= '<tr><td colspan="3">No users found.</td></tr>';
    }

    $table .= '</tbody></table>';
    return $table;
}

$userTable = ''; // Initialize

if (isset($_POST['runAll'])) {
    $userTable = generateUserTable();
    $buttonText = "Hide Users";
    $showTable = true;
} elseif (isset($_POST['hideAll'])) {
    $userTable = '';
    $buttonText = "Show Users";
    $showTable = false;
} else {
    $buttonText = "Show Users";
    $showTable = false;
}

function deleteUsers() {}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Portal</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
            color: #333;
            line-height: 1.6;
        }

        .admin-container {
            width: 90%;
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .admin-container p {
            font-size: 24px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
        }

        .admin-btn {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            margin: 10px;
            transition: background-color 0.3s ease;
        }

        .admin-btn:hover {
            background-color: #0056b3;
        }

        .user-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border-radius: 6px;
            overflow: hidden;
        }

        .user-table th,
        .user-table td {
            border: 1px solid #ddd;
            padding: 12px 15px;
            text-align: left;
        }

        .user-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .user-table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .user-table tbody tr:hover {
            background-color: #f0f0f0;
        }
    </style>
</head>

<body>
    <div class="admin-container">
        <p>Admin Portal</p>

        <form method="post">
            <button class="admin-btn" type="submit" name="<?php echo $showTable ? 'hideAll' : 'runAll'; ?>"><?php echo $buttonText; ?></button>
        </form>

        <?php if ($showTable) {
            echo $userTable;
        } ?>

        <button class="admin-btn" onclick="deleteUsers()">Delete Users</button>
        <button class="admin-btn" onclick="addAdmin()">Add Admin</button>
    </div>

    <script>
        function deleteUsers() {
            alert("Delete Users functionality will be implemented here.");
        }

        function addAdmin() {
            alert("Add admin functionality will be implemented here.");
        }
    </script>
</body>

</html>
