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
            color: white;
            font-size: 18px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 80vh;
        }
        .search-bar {
            width: 50%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .search-btn {
            padding: 10px 15px;
            margin-left: 10px;
            font-size: 16px;
            border: none;
            background-color: #007bff;
            color: white;
            cursor: pointer;
            border-radius: 5px;
        }
        .login-btn {
            padding: 8px 12px;
            font-size: 16px;
            border: none;
            background-color: #0056b3;
            color: white;
            cursor: pointer;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <span>Job Advertisement Portal</span>
        <a href="login.php"><button class="login-btn">Log In</button></a>
    </div>
    <div class="container">
        <h1>Find Your Dream Job</h1>
        <input type="text" class="search-bar" placeholder="Search for jobs...">
        <button class="search-btn">Search</button>
    </div>
</body>
</html>