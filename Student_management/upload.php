<?php

require "./config.php";


// Handle initial homework upload
if (isset($_POST['submit'])) {
    $target_dir = "./uploads/homework/";
    $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
    $uploadOk = 1;
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $teacher_uuid = mysqli_real_escape_string($conn, $_SESSION['uuid']);
    $query = "INSERT INTO homeworks (title, description, teacher_uuid) VALUES ('$title', '$description', '$teacher_uuid')";
    $result = mysqli_query($conn, $query);
    if ($result) {
        echo "Message sent successfully.";
    } else {
        echo "Failed to send message.";
    }
    // Redirect to profile page
    header("Location: ./homework.php");
    exit();
}
