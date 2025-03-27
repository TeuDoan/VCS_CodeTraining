<?php
session_start();
require "./config.php";

// if user is already logged in, redirect to index.php
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true) {
    header('location:./index.php');
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    // Fetch user by username
    $sql = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($conn, $sql);

    if (!$result) {
        die("Query Failed: " . mysqli_error($conn));
    }

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);

        // Verify hashed password
        if (password_verify($password, $user["password"])) {
            $_SESSION['uuid'] = $user['uuid'];
            $_SESSION['is_teacher'] = $user['is_teacher'];
            $_SESSION['logged_in'] = true;

            // Redirect to index.php
            header("Location: ./index.php");
            exit();
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "User not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="./css/style.css">

</head>

<body>
    <h1>VAP - VCS Academic Portal</h1>
    <div class="login-container">
        <?php if (isset($error)) {
            echo "<p style='color:red;'>$error</p>";
        } ?>

        <form action="" method="post">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <p></p>
            <button type="submit" name="login">Login</button>
        </form>
    </div>
</body>

</html>