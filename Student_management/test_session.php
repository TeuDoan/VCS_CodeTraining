<?php
require 'session/session.php';

if (Session::isLoggedIn()) {
    $user = Session::getUser();
    echo "User is logged in: " . $user['username'];
} else {
    echo "No user logged in.";
}
?>
