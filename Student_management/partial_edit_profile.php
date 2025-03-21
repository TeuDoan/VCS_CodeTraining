<?php
session_start();
if ($_SESSION['logged_in'] != 1) {
  header('location:./login.php');
}
?>
<h1>Partial edit profile</h1>