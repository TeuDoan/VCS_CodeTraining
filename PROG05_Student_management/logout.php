<?php
session_start();
if (!isset($_SESSION["logged_in"])) {
    header('location:./login.php');
}
// Destroy session and unset all session variables
session_unset();
session_destroy();

// Redirect to login page
header("Location: ./login.php"); // Change this to your login page path
exit;
?>