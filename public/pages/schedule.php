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
<html lang="en">
<head>
    <title>Schedule</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/assets/css/main.css">
    <link rel="stylesheet" href="/assets/css/components.css">
</head>
<body>
    <?php require_once __DIR__ . '/../components/header.php'; ?>
    <div class="container" style="max-width:700px;">
        <h1>Schedule</h1>
        <?php require_once __DIR__ . '/../components/alert.php'; ?>
        <?php
        if (!empty($errors)) {
            foreach ($errors as $error) {
                render_alert($error, 'error');
            }
        }
        if ($success) {
            render_alert('Appointment booked successfully!', 'success');
        }
        require_once __DIR__ . '/../components/form.php';
        $fields = [
            [
                'label' => 'Date',
                'type' => 'date',
                'id' => 'date',
                'name' => 'date',
                'placeholder' => '',
                'required' => true,
                'value' => $_POST['date'] ?? '',
                'pattern' => ''
            ],
            [
                'label' => 'Time',
                'type' => 'time',
                'id' => 'time',
                'name' => 'time',
                'placeholder' => '',
                'required' => true,
                'value' => $_POST['time'] ?? '',
                'pattern' => ''
            ],
            [
                'label' => 'Provider',
                'type' => 'text',
                'id' => 'provider',
                'name' => 'provider',
                'placeholder' => 'Provider',
                'required' => true,
                'value' => $_POST['provider'] ?? '',
                'pattern' => ''
            ]
        ];
        render_form($fields, '', 'post', 'Book Appointment');
        ?>
        <h2>Your Scheduled Appointments</h2>
        <?php require_once __DIR__ . '/../components/card.php'; ?>
        <?php
        if (!empty($appointments)) {
            echo '<div style="overflow-x:auto;">';
            echo '<table style="width:100%;border-collapse:collapse;">';
            echo '<tr><th>Date</th><th>Time</th><th>Provider</th></tr>';
            foreach ($appointments as $appointment) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($appointment['date']) . '</td>';
                echo '<td>' . htmlspecialchars($appointment['time']) . '</td>';
                echo '<td>' . htmlspecialchars($appointment['provider']) . '</td>';
                echo '</tr>';
            }
            echo '</table>';
            echo '</div>';
        } else {
            render_card('', '<p>You have no appointments scheduled.</p>');
        }
        ?>
    </div>
</body>
</html>