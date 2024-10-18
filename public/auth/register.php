<?php
require_once __DIR__ . '/../../vendor/autoload.php';

session_start();

// Check if user is already logged in
if (isset($_COOKIE['user_name'])) {
    header("Location: /home");
    exit();
}

$supabaseUrl = getenv('SUPABASE_URL');
$supabaseKey = getenv('SUPABASE_KEY');

$errors = array();
$success = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $phone = preg_replace("/[^0-9]/", "", $_POST['phone']); // Remove non-digit characters
    $address = $_POST['address'];

    // Form Validation
    if (empty($name)) {
        $errors[] = 'Name is required.';
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Valid email is required.';
    }
    if (empty($password) || strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters long.';
    }
    if (empty($phone) || strlen($phone) != 10) {
        $errors[] = 'Phone number must be exactly 10 digits.';
    }
    if (empty($address)) {
        $errors[] = 'Address is required.';
    }

    if (empty($errors)) {
        // Generate a unique user ID
        $userId = bin2hex(random_bytes(16));

        $data = array(
            'user_id' => $userId,
            'name' => $name,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_BCRYPT),
            'phone' => (int)$phone,
            'address' => $address
        );

        $ch = curl_init($supabaseUrl . '/rest/v1/patients');
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
            setcookie('user_name', $name, time() + (86400 * 30), "/");
            setcookie('user_id', $userId, time() + (86400 * 30), "/");
            header("Location: /home");
            exit();
        } else {
            $errors[] = 'Error registering patient. HTTP Code: ' . $httpcode . '. Response: ' . $response;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Patient Registration</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            width: 300px;
            margin: auto;
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        input[type="text"], input[type="email"], input[type="password"], input[type="tel"], textarea {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        input[type="submit"] {
            width: 100%;
            padding: 10px;
            background: #4CAF50;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background: #45a049;
        }
        .error {
            color: red;
            margin-bottom: 10px;
        }
        .success {
            color: green;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Patient Registration</h1>
        <?php if (!empty($errors)): ?>
            <?php foreach ($errors as $error): ?>
                <div class="error"><?= htmlspecialchars($error) ?></div>
            <?php endforeach; ?>
        <?php endif; ?>
        <form action="" method="post">
            <input type="text" id="name" name="name" placeholder="Name" required>
            <input type="email" id="email" name="email" placeholder="Email" required>
            <input type="password" id="password" name="password" placeholder="Password" required>
            <input type="tel" id="phone" name="phone" placeholder="Phone (10 digits)" required pattern="[0-9]{10}">
            <textarea id="address" name="address" placeholder="Address" required></textarea>
            <input type="submit" value="Register">
        </form>
    </div>
</body>
</html>