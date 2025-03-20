<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: /Student_management/login_system/index.php");
    exit();
}
require '../login_system/config.php';

// Determine which user's profile to show
if (isset($_GET['user'])) {
    $username = $_GET['user']; // Get username from URL
} else {
    $username = $_SESSION['username']; // Default to logged-in user
}

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

$isOwnerStudent = ($username == $_SESSION['username']) && ($user['role'] == 'student');
$isTeacher = $user['role'] == 'teacher';
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

<?php if ($isOwnerStudent): ?>
    <a href="student_edit_profile.php?user=<?= urlencode($_SESSION['username'])?>">Edit profile</a>
<?php endif; ?>

<table border="1">
    <tr>
        <th>Full name</th>
        <td><?= htmlspecialchars($user['fullname']) ?></td>
    </tr>
    <tr>
        <th>Username</th>
        <td><?= htmlspecialchars($user['username']) ?></td>
    </tr>
    <tr>
        <th>Email</th>
        <td><?= htmlspecialchars($user['email']) ?></td>
    </tr>
    <tr>
        <th>Birthdate</th>
        <td><?= htmlspecialchars($user['birthdate']) ?></td>
    </tr>
    <tr>
        <th>Gender</th>
        <td><?= htmlspecialchars($user['gender']) ?></td>
    </tr>
    <tr>
        <th>Role</th>
        <td><?= htmlspecialchars($user['role']) ?></td>
    </tr>
    <tr>
        <th>Phone Number</th>
        <td><?= htmlspecialchars($user['phonenumber']) ?></td>
    </tr>
    <tr>
        <th>Address</th>
        <td><?= htmlspecialchars($user['address']) ?></td>
    </tr>
    <tr>
        <th>Id card</th>
        <td><?= htmlspecialchars($user['id_card']) ?></td>
    </tr>
</table>

</body>
</html>