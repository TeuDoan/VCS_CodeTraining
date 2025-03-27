<?php
session_start();

//Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ./login.php');
    exit();
}


require "./config.php";

// Check if 'uuid' is set
if (isset($_GET['uuid']) && !empty($_GET['uuid'])) {
    // Secure the input against SQL injection
    $uuid = mysqli_real_escape_string($conn, $_GET['uuid']);

    // Fetch user data from the database
    $query = "SELECT * FROM users WHERE uuid = '$uuid'";
    $result = mysqli_query($conn, $query);

    // Check if the user exists
    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
    } else {
        die("User not found.");
    }
} else {
    die("Invalid request.");
}
// Messages that owner get
$isOwner = ($_SESSION['uuid'] == $uuid);
if ($isOwner) {
    $messages_received_query = "SELECT messages.message, messages.timestamp, 
               users.fullname, users.username 
        FROM messages 
        JOIN users ON messages.sender_uuid = users.uuid 
        WHERE messages.receiver_uuid = '$_SESSION[uuid]' 
        ORDER BY messages.timestamp DESC";

    $messages_received_result = mysqli_query($conn, $messages_received_query);
    $sender_name = "SELECT fullname FROM users WHERE uuid = '$_SESSION[uuid]'";


} else if (!$isOwner) {
    // Messages that owner sent
    $messages_sent_query = "SELECT messages.id,messages.message, messages.timestamp, 
    users.fullname, users.username 
    FROM messages 
    JOIN users ON messages.receiver_uuid = users.uuid 
    WHERE messages.sender_uuid = '$_SESSION[uuid]' and messages.receiver_uuid = '$uuid'
    ORDER BY messages.timestamp DESC";

    $messages_sent_result = mysqli_query($conn, $messages_sent_query);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="./css/style.css">
    <script src="./js/edit_messages.js"></script>
</head>

<body>
    <h1>VAP - VCS Academic Portal</h1>
    <nav>
        <a href="./index.php">Home</a>
        <a href="./profile.php?uuid=<?php echo htmlspecialchars($_SESSION['uuid']); ?>">Profile</a>
        <a href="./homework.php">Homework</a>
        <a href="./submission.php">Homework submission</a>
        <a href="./logout.php">Logout</a>
    </nav>


    <div class="profile-container">
        <h2>User Profile</h2>

        <img src="./uploads/img/<?php echo $user['avatar_url'] ?: 'default.png'; ?>" alt="Profile Picture">
        <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
        <p><strong>Full Name:</strong> <?php echo htmlspecialchars($user['fullname']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
        <p><strong>Phone Number:</strong> <?php echo htmlspecialchars($user['phonenumber']); ?></p>
        <p><strong>UUID:</strong> <?php echo htmlspecialchars($user['uuid']); ?></p>
        <p><strong>Role:</strong> <?php echo $user['is_teacher'] ? "Teacher" : "Student"; ?></p>

    </div>
    <?php
    if ($_SESSION['is_teacher'] == 1 || $isOwner) {
        echo '<button class="edit-profile-btn" onclick="window.location.href=\'edit_profile.php?uuid=' . urlencode($user['uuid']) . '\'">Edit Profile</button>';
    }
    ?>


    <!-- Get messages sent to them if is profile owner -->
    <?php if ($isOwner) { ?>
        <h2>Messages received</h2>
        <table>
            <tr>
                <th>#</th>
                <th>Sender</th>
                <th>Messages</th>
                <th>Time</th>
            </tr>

            <?php
            if ($messages_received_result && mysqli_num_rows($messages_received_result) > 0) {
                $index = 1;
                while ($row = mysqli_fetch_assoc($messages_received_result)) {
                    echo "<tr>";
                    echo "<td>" . $index . "</td>";
                    echo "<td>" . htmlspecialchars($row['fullname']) . " (" . htmlspecialchars($row['username']) . ")</td>";
                    echo "<td>" . htmlspecialchars($row['message']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['timestamp']) . "</td>";
                    echo "</tr>";
                    $index++;
                }
            } else {
                // Display empty rows (adjust the number as needed)
                for ($i = 0; $i < 1; $i++) {
                    echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
                }
            }
            ?>
        </table>

    <?php } ?>

    <!-- Leave message for the current profile if is not profile owner -->
    <?php if (!$isOwner) { ?>
        <h2>Leave a Message for <?php echo $user['fullname'] ?></h2>
        <form action="message.php" method="post">
            <input type="hidden" name="receiver_uuid" value="<?php echo $user['uuid']; ?>">
            <textarea name="message"
                placeholder="Leave a message for <?php echo htmlspecialchars($user['username']); ?>"></textarea>
            <button type="submit" name="submit">Send Message</button>
        </form>
    <?php } ?>

    <script src="./js/edit_messages.js"></script>
    <!-- Display message owner sent to others -->
    <?php if (!$isOwner) { ?>
        <h2>Messages Sent</h2>
        <table>
            <tr>
                <th>#</th>
                <th>Messages</th>
                <th>Time</th>
                <th>Actions</th>
            </tr>
            <?php
            if ($messages_sent_result && mysqli_num_rows($messages_sent_result) > 0) {
                $index = 1;
                while ($row = mysqli_fetch_assoc($messages_sent_result)) {
                    echo "<tr>";
                    echo "<td>" . $index . "</td>";

                    // Message content with editable field
                    echo "<td> 
                        <span id='msg_test_{$row['id']}'>" . htmlspecialchars($row['message']) . "</span>
                        <input type='text' id='msg_edit_{$row['id']}' value='" . htmlspecialchars($row['message']) . "' style='display:none;'>
                    </td>";

                    echo "<td>" . htmlspecialchars($row['timestamp']) . "</td>";

                    // Actions: Edit, Save, Cancel, Delete
                    echo "<td>
                    <button onclick='editMessage({$row['id']})'>Edit</button>
                    <button onclick='saveMessage({$row['id']})' style='display:none;'>Save</button>
                    <button onclick='cancelEdit({$row['id']})' style='display:none;'>Cancel</button>
                    
                    <form action='message.php' method='get' style='display:inline;'>
                    <input type='hidden' name='delete_id' value='" . $row['id'] . "'>
                    <button type='submit' class='delete-btn' onclick='return confirm(\"Are you sure?\");'>Delete</button>
                    </form>

                    </td>";
                    echo "</tr>";
                    $index++;
                }
            } else {
                for ($i = 0; $i < 1; $i++) {
                    echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
                }
            }
            ?>
        </table>
    <?php } ?>

</body>

</html>