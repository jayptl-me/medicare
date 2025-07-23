<?php
session_start();

// Check if user is logged in using cookies
if (!isset($_COOKIE['user_id'])) {
    header("Location: /register");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Medicare - Home</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/assets/css/main.css">
    <link rel="stylesheet" href="/assets/css/components.css">
</head>
<body>
    <?php require_once __DIR__ . '/../components/header.php'; ?>
    <div class="container" style="max-width:900px;display:flex;gap:24px;flex-wrap:wrap;justify-content:center;">
        <?php require_once __DIR__ . '/../components/card.php'; ?>
        <?php require_once __DIR__ . '/../components/button.php'; ?>
        <?php
        render_card('Your Schedule',
            '<p>View and manage your upcoming appointments.</p>' .
            render_button('View Schedule', 'primary', '/schedule', 'style="margin-top:8px;"')
        );
        render_card('Book an Appointment',
            '<p>Schedule a new appointment with our healthcare providers.</p>' .
            render_button('Book Appointment', 'secondary', '/schedule', 'style="margin-top:8px;"')
        );
        ?>
    </div>
</body>
</html>