<?php
session_start();

//Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ./login.php');
    exit();
}

require "./config.php";

// Check if the user is authorized
if (!isset($_GET['uuid']) || empty($_GET['uuid'])) {
    die("Invalid request.");
}

$uuid = mysqli_real_escape_string($conn, $_GET['uuid']);
$sql = "SELECT * FROM users WHERE uuid = '$uuid'";
$result = mysqli_query($conn, $sql);

if (!$result || mysqli_num_rows($result) == 0) {
    die("User not found.");
}

$user = mysqli_fetch_assoc($result);
$isOwner = ($_SESSION['uuid'] == $uuid);
$isTeacher = ($_SESSION['is_teacher'] == 1);


// Prevent unauthorized access
if (!$isOwner && !$isTeacher) {
    die("You do not have permission to edit this profile.");
}

// ** Handle Profile Update on Form Submission **
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phonenumber = mysqli_real_escape_string($conn, $_POST['phonenumber']);
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_BCRYPT) : null;

    $update_sql = "UPDATE users SET email='$email', phonenumber='$phonenumber'";

    // ** Only teachers can update username and fullname **
    if ($isTeacher) {
        $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
        $new_username = mysqli_real_escape_string($conn, $_POST['username']); // Input from form

        // Only check username availability if it has changed
        if ($new_username !== $user['username']) {
            $checkUsernameSQL = "SELECT username FROM users WHERE username = '$new_username'";
            $checkResult = mysqli_query($conn, $checkUsernameSQL);

            if (mysqli_num_rows($checkResult) > 0) {
                echo "Error: Username already taken!";
                exit;
            } else {
                $update_sql .= ", username='$new_username'";
            }
        }

        $update_sql .= ", fullname='$fullname'";
    }



    if ($password) {
        $update_sql .= ", password='$password'";
    }

    // Handle profile picture upload
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == 0) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (in_array($_FILES['avatar']['type'], $allowedTypes)) {
            $fileName = $username . "_" . time() . "." . pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
            $targetDir = "./uploads/img/";
            $targetFilePath = $targetDir . $fileName;

            // Delete old avatar if it exists
            if (!empty($user['avatar_url']) && file_exists($targetDir . $user['avatar_url'])) {
                unlink($targetDir . $user['avatar_url']);
            }

            if (move_uploaded_file($_FILES['avatar']['tmp_name'], $targetFilePath)) {
                $update_sql .= ", avatar_url='$fileName'";
                $user['avatar_url'] = $fileName; // Update the variable to show the new image immediately
            } else {
                echo "Error uploading file.";
                exit;
            }
        } else {
            echo "Invalid file type. Only JPG, PNG, and GIF are allowed.";
            exit;
        }
    }

    $update_sql .= " WHERE uuid='$uuid'";

    if (mysqli_query($conn, $update_sql)) {
        header("Location: edit_profile.php?uuid=$uuid&success=1");
        exit;
    } else {
        echo "Error updating profile: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="./css/style.css">
</head>

<body>
    <h1>VAP - VCS Academic Portal</h1>
    <nav>
        <a href="./index.php">Home</a>
        <a href="./profile.php?uuid=<?php echo htmlspecialchars($_SESSION['uuid']); ?>">Profile</a>
        <a href="./homework.php">Homework</a>
        <a href="./logout.php">Logout</a>
    </nav>
    <div class="edit-profile-container">
    <h2>Edit Profile</h2>

    <?php if (isset($_GET['success']))
        echo "<p style='color: green;'>Profile updated successfully!</p>"; ?>

    <form action="edit_profile.php?uuid=<?php echo htmlspecialchars($uuid); ?>" method="post"
        enctype="multipart/form-data">
        <label>Profile Picture:</label>
        <img src="./uploads/img/<?php echo !empty($user['avatar_url']) ? htmlspecialchars($user['avatar_url']) : 'default.png'; ?>"
            alt="Profile Picture" width="150" height="150">

        <label>Change Profile Picture:</label>
        <input type="file" name="avatar">

        <!-- ** Only Teachers Can Edit Fullname & Username ** -->
        <?php if ($isTeacher) { ?>
            <label>Username:</label>
            <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>

            <label>Full name:</label>
            <input type="text" name="fullname" value="<?php echo htmlspecialchars($user['fullname']); ?>">
        <?php } else { ?>
            <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
            <p><strong>Full Name:</strong> <?php echo htmlspecialchars($user['fullname']); ?></p>
        <?php } ?>

        <label>Email:</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

        <label>Password:</label>
        <input type="password" name="password" placeholder="Enter new password (leave blank to keep current)">

        <label>Phone Number:</label>
        <input type="text" name="phonenumber" value="<?php echo htmlspecialchars($user['phonenumber']); ?>" required>

        <button type="submit" name="submit">Save Changes</button>
    </form>
    </div>
</body>

</html>