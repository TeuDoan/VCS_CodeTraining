<?php
session_start();
require "./config.php";

//Login
if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    // Fetch user by username only (DO NOT check password in SQL)
    $sql = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($conn, $sql);

    if (!$result) {
        die("Query Failed: " . mysqli_error($conn));
    }

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);

        // Verify hashed password
        if (password_verify($password, $user["password"])) {
            $_SESSION['username'] = $user['username'];
            $_SESSION['is_teacher'] = $user['is_teacher'];
            $_SESSION['logged_in'] = 1;
                        //redirect to index.php
            header("Location: ./index.php");
        } else {
            echo "Invalid password.";
        }
    } else {
        echo "User not found.";
    }
}
?>