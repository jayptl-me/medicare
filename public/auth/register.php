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
<html lang="en">
<head>
    <title>Patient Registration</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/assets/css/main.css">
    <link rel="stylesheet" href="/assets/css/components.css">
</head>
<body>
    <?php require_once __DIR__ . '/../components/header.php'; ?>
    <div class="container">
        <h1>Patient Registration</h1>
        <?php require_once __DIR__ . '/../components/alert.php'; ?>
        <?php
        if (!empty($errors)) {
            foreach ($errors as $error) {
                render_alert($error, 'error');
            }
        }
        ?>
        <?php require_once __DIR__ . '/../components/form.php'; ?>
        <?php
        $fields = [
            [
                'label' => 'Name',
                'type' => 'text',
                'id' => 'name',
                'name' => 'name',
                'placeholder' => 'Name',
                'required' => true,
                'value' => $_POST['name'] ?? '',
                'pattern' => ''
            ],
            [
                'label' => 'Email',
                'type' => 'email',
                'id' => 'email',
                'name' => 'email',
                'placeholder' => 'Email',
                'required' => true,
                'value' => $_POST['email'] ?? '',
                'pattern' => ''
            ],
            [
                'label' => 'Password',
                'type' => 'password',
                'id' => 'password',
                'name' => 'password',
                'placeholder' => 'Password',
                'required' => true,
                'value' => '',
                'pattern' => ''
            ],
            [
                'label' => 'Phone',
                'type' => 'tel',
                'id' => 'phone',
                'name' => 'phone',
                'placeholder' => 'Phone (10 digits)',
                'required' => true,
                'value' => $_POST['phone'] ?? '',
                'pattern' => '[0-9]{10}'
            ],
            [
                'label' => 'Address',
                'type' => 'textarea',
                'id' => 'address',
                'name' => 'address',
                'placeholder' => 'Address',
                'required' => true,
                'value' => $_POST['address'] ?? '',
                'pattern' => ''
            ]
        ];
        render_form($fields, '', 'post', 'Register');
        ?>
    </div>
    <script>
    // Password visibility toggle (minimal JS)
    document.addEventListener('DOMContentLoaded', function() {
      var pwd = document.getElementById('password');
      if (pwd) {
        var toggle = document.createElement('span');
        toggle.textContent = '👁️';
        toggle.style.cursor = 'pointer';
        toggle.style.marginLeft = '8px';
        toggle.onclick = function() {
          pwd.type = pwd.type === 'password' ? 'text' : 'password';
        };
        pwd.parentNode.insertBefore(toggle, pwd.nextSibling);
      }
    });
    </script>
</body>
</html>