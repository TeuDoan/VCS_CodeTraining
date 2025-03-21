<?php
session_start();
if ($_SESSION['logged_in'] != 1) {
    header('location:./login.php');
}
require "./config.php";


$username = $_SESSION['username'];
$sql = "SELECT * FROM users WHERE username = '$username'";


$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);


$isOwner = $user["username"] == $_SESSION['username'];


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <a href="index.php">Home</a> |
    <a href="logout.php" class="logout-btn">Logout</a>|


    <div class="profile-container">
        <h2>User Profile</h2>

        <img src="./uploads/img/<?php echo $user['avatar_url'] ?: 'default.png'; ?>" alt="Profile Picture">
        <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
        <p><strong>Full Name:</strong> <?php echo htmlspecialchars($user['fullname']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
        <p><strong>Phone Number:</strong> <?php echo htmlspecialchars($user['phonenumber']); ?></p>
        <p><strong>Role:</strong> <?php echo $user['is_teacher'] ? "Teacher" : "Student"; ?></p>

    </div>
    <?php
    if ($_SESSION['is_teacher'] == 1) { ?>
        <a href="full_edit_profile.php">Edit Profile</a>
    <?php } elseif ($isOwner) { ?>
        <a href="partial_edit_profile.php">Edit Profile</a>
    <?php } ?>

</body>

</html>