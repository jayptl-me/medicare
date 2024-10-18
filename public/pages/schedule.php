<?php
session_start();
require_once __DIR__ . '/../../vendor/autoload.php';

// Check if user is logged in using cookies
if (!isset($_COOKIE['user_id'])) {
    header("Location: /register");
    exit();
}

$supabaseUrl = getenv('SUPABASE_URL');
$supabaseKey = getenv('SUPABASE_KEY');

$errors = array();
$success = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $date = $_POST['date'];
    $time = $_POST['time'];
    $provider = $_POST['provider'];

    // Form Validation
    if (empty($date)) {
        $errors[] = 'Date is required.';
    }
    if (empty($time)) {
        $errors[] = 'Time is required.';
    }
    if (empty($provider)) {
        $errors[] = 'Provider is required.';
    }

    if (empty($errors)) {
        $data = array(
            'patient_id' => $_COOKIE['user_id'],
            'date' => $date,
            'time' => $time,
            'provider' => $provider
        );

        $ch = curl_init($supabaseUrl . '/rest/v1/appointments');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'apikey: ' . $supabaseKey,
            'Authorization: Bearer ' . $supabaseKey
        ));

        $response = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            $errors[] = 'Curl error: ' . curl_error($ch);
        }

        curl_close($ch);

        if ($httpcode == 201) {
            $success = true;
            $_SESSION['success_message'] = 'Appointment booked successfully';
        } else {
            $errors[] = 'Error booking appointment. HTTP Code: ' . $httpcode . '. Response: ' . $response;
        }
    }
}

// Fetch existing appointments
$ch = curl_init($supabaseUrl . '/rest/v1/appointments?patient_id=eq.' . urlencode($_COOKIE['user_id']) . '&order=date.asc,time.asc');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'apikey: ' . $supabaseKey,
    'Authorization: Bearer ' . $supabaseKey
));

$response = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

curl_close($ch);

if ($httpcode == 200) {
    $appointments = json_decode($response, true);
} else {
    $errors[] = 'Error fetching appointments. HTTP Code: ' . $httpcode . '. Response: ' . $response;
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Schedule</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        header {
            background-color: #4CAF50;
            color: white;
            padding: 20px;
            text-align: center;
        }
        h1 {
            margin: 0;
            color: #333;
        }
        .container {
            width: 80%;
            margin: auto;
            overflow: hidden;
            padding: 20px;
            background: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .error {
            color: red;
            margin-bottom: 10px;
        }
        .success {
            color: green;
            margin-bottom: 10px;
        }
        form {
            margin-bottom: 20px;
        }
        input[type="date"], input[type="time"], input[type="text"], input[type="submit"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <header>
        <h1>Schedule</h1>
    </header>
    <div class="container">
        <h1>Schedule</h1>
        <?php if (!empty($errors)): ?>
            <?php foreach ($errors as $error): ?>
                <div class="error"><?= htmlspecialchars($error) ?></div>
            <?php endforeach; ?>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="success">Appointment booked successfully!</div>
        <?php endif; ?>
        <form action="" method="post">
            <input type="date" id="date" name="date" required>
            <input type="time" id="time" name="time" required>
            <input type="text" id="provider" name="provider" placeholder="Provider" required>
            <input type="submit" value="Book Appointment">
        </form>

        <h2>Your Scheduled Appointments</h2>
        <?php if (!empty($appointments)): ?>
            <table>
                <tr>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Provider</th>
                </tr>
                <?php foreach ($appointments as $appointment): ?>
                    <tr>
                        <td><?= htmlspecialchars($appointment['date']) ?></td>
                        <td><?= htmlspecialchars($appointment['time']) ?></td>
                        <td><?= htmlspecialchars($appointment['provider']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>You have no appointments scheduled.</p>
        <?php endif; ?>
    </div>
</body>
</html>