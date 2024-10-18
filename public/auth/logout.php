<?php
// Destroy the cookies
setcookie('user_name', '', time() - 3600, "/");
setcookie('user_id', '', time() - 3600, "/");

// Redirect to the login page
header("Location: /register");
exit();