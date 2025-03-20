<?php
require 'config/config.php';
require 'src/models/User.php';
require 'session/session.php';

$userModel = new User($pdo);

// Test login
$user = $userModel->login('jdoe@example.com', '123456');

if ($user) {
    Session::setUserSession($user);
    echo "Login successful! Welcome, " . $_SESSION['username'];
} else {
    echo "Invalid email or password.";
}
?>
