<?php
//Add submitted message to the database
session_start();

//Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ./login.php');
    exit();
}
  
require "./config.php";

// Handle initial message sending
if (isset($_POST['submit'])) {
    $message = mysqli_real_escape_string($conn, $_POST['message']);
    $receiver_uuid = mysqli_real_escape_string($conn, $_POST['receiver_uuid']);
    $sender_uuid = mysqli_real_escape_string($conn, $_SESSION['uuid']);
    $query = "INSERT INTO messages (message, receiver_uuid, sender_uuid) VALUES ('$message', '$receiver_uuid', '$sender_uuid')";
    $result = mysqli_query($conn, $query);
    if ($result) {
        echo "Message sent successfully.";
    } else {
        echo "Failed to send message.";
    }
    // Redirect to profile page
    header("Location: ./profile.php?uuid=$receiver_uuid");
    exit();
}

// Handle message edit and update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_SESSION['uuid'])) {
        echo "Unauthorized";
        exit();
    }

    $messageId = mysqli_real_escape_string($conn, $_POST['id']);
    $newMessage = mysqli_real_escape_string($conn, $_POST['message']);
    $userUUID = $_SESSION['uuid'];

    // Check if the user is the sender of the message
    $checkQuery = "SELECT * FROM messages WHERE id='$messageId' AND sender_uuid='$userUUID'";
    $checkResult = mysqli_query($conn, $checkQuery);

    if (mysqli_num_rows($checkResult) > 0) {
        // Update message
        $updateQuery = "UPDATE messages SET message='$newMessage' WHERE id='$messageId'";
        if (mysqli_query($conn, $updateQuery)) {
            echo "Success";
        } else {
            echo "Failed to update message.";
        }
    } else {
        echo "Unauthorized";
    }
}

// Handle message deletion
if (isset($_GET['delete_id'])) {
    require "./config.php"; 
    session_start();

    $messageId = mysqli_real_escape_string($conn, $_GET['delete_id']);
    $userUUID = $_SESSION['uuid'];

    // Ensure only the sender can delete the message & get receiver_uuid
    $checkQuery = "SELECT receiver_uuid FROM messages WHERE id='$messageId' AND sender_uuid='$userUUID'";
    $checkResult = mysqli_query($conn, $checkQuery);

    if ($row = mysqli_fetch_assoc($checkResult)) {
        $receiver_uuid = $row['receiver_uuid']; // Get the receiver's UUID

        // Delete the message
        $deleteQuery = "DELETE FROM messages WHERE id='$messageId'";
        if (mysqli_query($conn, $deleteQuery)) {
            header("Location: ./profile.php?uuid=$receiver_uuid");
            exit();
        } else {
            echo "Failed to delete message.";
        }
    } else {
        echo "Unauthorized to delete this message.";
    }
}
