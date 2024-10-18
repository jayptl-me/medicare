<?php
session_start();

// Check if user is logged in using cookies
if (!isset($_COOKIE['user_id'])) {
    header("Location: /register");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Medicare - Home</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        header {
            background-color: #4CAF50;
            color: white;
            padding: 20px;
            text-align: center;
        }
        h1 {
            margin: 0;
        }
        .container {
            display: flex;
            justify-content: space-around;
            margin: 40px 0;
        }
        .card {
            background-color: #f1f1f1;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: center;
            width: 300px;
        }
        .card h2 {
            margin-top: 0;
        }
        .card p {
            margin-bottom: 20px;
        }
        .btn {
            background-color: #4CAF50;
            border: none;
            border-radius: 4px;
            color: white;
            cursor: pointer;
            padding: 10px 20px;
            text-decoration: none;
        }
        .btn:hover {
            background-color: #45a049;
        }
        .logout {
            background-color: #f44336;
            position: absolute;
            top: 20px;
            right: 20px;
        }
        .logout:hover {
            background-color: #d32f2f;
        }
    </style>
</head>
<body>
    <header>
        <h1>Medicare</h1>
        <a href="/logout" class="btn logout">Logout</a>
    </header>
    <div class="container">
        <div class="card">
            <h2>Your Schedule</h2>
            <p>View and manage your upcoming appointments.</p>
            <a href="/schedule" class="btn">View Schedule</a>
        </div>
        <div class="card">
            <h2>Book an Appointment</h2>
            <p>Schedule a new appointment with our healthcare providers.</p>
            <a href="/schedule" class="btn">Book Appointment</a>
        </div>
    </div>
</body>
</html>