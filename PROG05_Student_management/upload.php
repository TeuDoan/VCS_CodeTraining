<?php
session_start();
require "./config.php";

// Redirect if not logged in
if (!isset($_SESSION['logged_in'])) {
    header("Location: ./login.php");
    exit();
}

// Determine upload type (assignment or submission)
$uploadType = $_POST['upload_type'] ?? null;
$target_dir = $uploadType === "assignment" ? "./uploads/homework/" : "./uploads/submissions/";
$fileUploaded = false;
$uploadOk = 1;

// File Upload Logic
if (!empty($_FILES["fileToUpload"]["name"])) {
    $fileName = basename($_FILES["fileToUpload"]["name"]);
    $target_file = $target_dir . $fileName;

    // Prevent duplicate filenames
    if (file_exists($target_file)) {
        $fileNameWithoutExt = pathinfo($target_file, PATHINFO_FILENAME);
        $fileExtension = pathinfo($target_file, PATHINFO_EXTENSION);
        $counter = 1;
        while (file_exists($target_file)) {
            $newFileName = $fileNameWithoutExt . "-" . $counter . "." . $fileExtension;
            $target_file = $target_dir . $newFileName;
            $counter++;
        }
    }

    // Allowed file types
    $allowedTypes = ['pdf', 'doc', 'docx', 'txt'];
    if (!in_array(strtolower(pathinfo($target_file, PATHINFO_EXTENSION)), $allowedTypes)) {
        die("Invalid file type.");
    }

    // Move uploaded file
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
        $fileUploaded = true;
    } else {
        die("File upload failed.");
    }
}

// Insert into the correct table
if ($uploadType === "assignment" && $_SESSION['is_teacher'] == 1) {
    // Handle teacher assignment upload
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $teacher_uuid = mysqli_real_escape_string($conn, $_SESSION['uuid']);
    $file_path = mysqli_real_escape_string($conn, $target_file);

    $query = "INSERT INTO homeworks (title, description, teacher_uuid, homework_file) VALUES ('$title', '$description', '$teacher_uuid', '$file_path')";
    $result = mysqli_query($conn, $query);
    if (!$result) {
        die("Database error: " . mysqli_error($conn));
    }
    header("Location: ./homework.php");
    exit();

} elseif ($uploadType === "submission" && $_SESSION['is_teacher'] == 0) {
    // Handle student submission
    $homework_id = mysqli_real_escape_string($conn, $_POST['homework_id']);
    $student_uuid = mysqli_real_escape_string($conn, $_SESSION['uuid']);
    $file_path = mysqli_real_escape_string($conn, $target_file);

    $query = "INSERT INTO submissions (homework_id, student_uuid, file_path) VALUES ('$homework_id', '$student_uuid', '$file_path')";
    $result = mysqli_query($conn, $query);
    if (!$result) {
        die("Database error: " . mysqli_error($conn));
    }
    header("Location: ./homework.php");
    exit();
} else {
    die("Unauthorized access.");
}
?>
