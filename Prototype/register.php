<?php
include 'connect.php';

session_start();

if (isset($_POST['signUp'])) {
    $firstName = $_POST['fname'];
    $lastName = $_POST['lname'];
    $email = $_POST['email'];
    $phoneNumber = $_POST['phonenumber'];
    $firstLineAddress = $_POST['address1'];
    $postalCode = $_POST['postalcode'];
    $password = $_POST['password'];

    $checkEmail = "SELECT * FROM users WHERE email='$email'";
    $result = $conn->query($checkEmail);
    if ($result->num_rows > 0) {
        echo "Email Address already exists";
    } else {
        $insertQuery = "INSERT INTO users (firstName, lastName, email, phoneNumber, firstLineAddress, postalCode, password) 
        VALUES ('$firstName', '$lastName', '$email', '$phoneNumber', '$firstLineAddress', '$postalCode', '$password')";
        if ($conn->query($insertQuery) === TRUE) {
            header("Location: index.php");
            exit();
        } else {
            echo "Error: " . $conn->error;
        }
    }
}

if (isset($_POST['signIn'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email='$email' AND password='$password'";
    $sq2 = "SELECT * FROM admins WHERE email='$email' AND password='$password'";
    $userResult = $conn->query($sql);
    $adminResult = $conn->query($sq2);
    if ($userResult->num_rows > 0) {
        $row = $userResult->fetch_assoc();
        $_SESSION['email'] = $row['email'];
        $_SESSION['is_admin'] = false;
        header("Location: index.php");
        exit();}
    if ($adminResult->num_rows > 0) {
        $row = $adminResult->fetch_assoc();
        $_SESSION['email'] = $row['email'];
        $_SESSION['is_admin'] = true;
        header("Location: admin.php");
        exit();}

        else {
        echo "not found incorrect email or password";
    }
}
?>
