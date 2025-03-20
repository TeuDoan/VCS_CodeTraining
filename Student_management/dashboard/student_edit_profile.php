<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: /Student_management/login_system/index.php");
    exit();
}
require '../login_system/config.php';

// Determine which user's profile to show
$username = isset($_GET['user']) ? $_GET['user'] : $_SESSION['username'];

// Use prepared statements to prevent SQL injection
$stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// If user not found, show an error
if (!$user) {
    echo "<h2>User not found.</h2>";
    exit();
}

$isOwner = ($username == $_SESSION['username']); // Check if it's the current user's profile

// Handle profile update (only for the owner)
if ($isOwner && $_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $birthdate = $_POST['birthdate'];
    $gender = $_POST['gender'];
    $phone = $_POST['phonenumber'];
    $address = $_POST['address'];
    $id_card = $_POST['id_card'];

    // Update user details
    $stmt = $conn->prepare("UPDATE users SET email=?, birthdate=?, gender=?, phonenumber=?, address=?, id_card=? WHERE username=?");
    $stmt->bind_param("sssssss", $email, $birthdate, $gender, $phone, $address, $id_card, $username);
    $stmt->execute();
    $stmt->close();

    // Reload page to show updated details
    header("Location: profile.php?user=" . urlencode($username));
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profile - <?= htmlspecialchars($user['fullname']) ?></title>
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>

<ul>
  <li><a href="index.php">Home</a></li>
  <li><a href="profile.php">Your Profile</a></li>
</ul> 

<div class="profile-container">
    <h2><?= htmlspecialchars($user['fullname']) ?>'s Profile</h2>

    <form method="post" action="profile.php?user=<?= htmlspecialchars($user['username']) ?>">
        <table border="1">
            <tr>
                <th>Full Name</th>
                <td><?= htmlspecialchars($user['fullname']) ?></td>
            </tr>
            <tr>
                <th>Username</th>
                <td><?= htmlspecialchars($user['username']) ?></td>
            </tr>
            <tr>
                <th>Email</th>
                <td>
                    <?php if ($isOwner): ?>
                        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>">
                    <?php else: ?>
                        <?= htmlspecialchars($user['email']) ?>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <th>Birthdate</th>
                <td>
                    <?php if ($isOwner): ?>
                        <input type="date" name="birthdate" value="<?= htmlspecialchars($user['birthdate']) ?>">
                    <?php else: ?>
                        <?= htmlspecialchars($user['birthdate']) ?>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <th>Gender</th>
                <td>
                    <?php if ($isOwner): ?>
                        <input type="text" name="gender" value="<?= htmlspecialchars($user['gender']) ?>">
                    <?php else: ?>
                        <?= htmlspecialchars($user['gender']) ?>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <th>Role</th>
                <td><?= htmlspecialchars($user['role']) ?></td>
            </tr>
            <tr>
                <th>Phone Number</th>
                <td>
                    <?php if ($isOwner): ?>
                        <input type="text" name="phonenumber" value="<?= htmlspecialchars($user['phonenumber']) ?>">
                    <?php else: ?>
                        <?= htmlspecialchars($user['phonenumber']) ?>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <th>Address</th>
                <td>
                    <?php if ($isOwner): ?>
                        <input type="text" name="address" value="<?= htmlspecialchars($user['address']) ?>">
                    <?php else: ?>
                        <?= htmlspecialchars($user['address']) ?>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <th>ID Card</th>
                <td>
                    <?php if ($isOwner): ?>
                        <input type="text" name="id_card" value="<?= htmlspecialchars($user['id_card']) ?>">
                    <?php else: ?>
                        <?= htmlspecialchars($user['id_card']) ?>
                    <?php endif; ?>
                </td>
            </tr>
        </table>

        <?php if ($isOwner): ?>
            <button type="submit">Update Profile</button>
        <?php endif; ?>
    </form>
</div>

</body>
</html>
