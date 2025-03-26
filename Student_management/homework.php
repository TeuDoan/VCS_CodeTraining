<?php
session_start();
//Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ./login.php');
    exit();
}

require "./config.php";


$sql_homework = "SELECT * FROM homeworks";
$result_homework = mysqli_query($conn, $sql_homework);
// Handle file upload
$allowedTypes = ['pdf', 'doc', 'docx', 'txt'];
if (isset($_POST['upload'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $teacher_uuid = $_SESSION['uuid'];
    $file = $_FILES['homework_file'];

    $fileName = $file['name'];
    $fileTmpName = $file['tmp_name'];
    $fileSize = $file['size'];
    $fileError = $file['error'];
    $fileType = $file['type'];

    $fileExt = explode('.', $fileName);
    $fileActualExt = strtolower(end($fileExt));

    if (in_array($fileActualExt, $allowedTypes)) {
        if ($fileError === 0) {
            $fileNameNew = uniqid('', true) . "." . $fileActualExt;
            $fileDestination = 'uploads/' . $fileNameNew;
            move_uploaded_file($fileTmpName, $fileDestination);

            $sql = "INSERT INTO homeworks (title, description, teacher_uuid, homework_file) VALUES ('$title', '$description', '$teacher_uuid', '$fileNameNew')";
            if (mysqli_query($conn, $sql)) {
                echo "Homework uploaded successfully.";
            } else {
                echo "Error: " . $sql . "<br>" . mysqli_error($conn);
            }
        } else {
            echo "Error uploading file.";
        }
    } else {
        echo "Invalid file type.";
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homework</title>
    <link rel="stylesheet" href="./css/style.css">
</head>

<body>
    <h1>Homework</h1>
    <nav>
        <a href="./index.php">Home</a>
        <a href="./profile.php?uuid=<?php echo htmlspecialchars($_SESSION['uuid']); ?>">Profile</a>
        <a href="./homework.php">Homework</a>
        <a href="./logout.php">Logout</a>
    </nav>
    <div class="homework-container">
    <!-- If teacher then show upload homework form -->
    <?php if ($_SESSION['is_teacher'] == 1) {
        echo '<h2>Upload Homework</h2>';
        echo '<form action="upload.php" method="post" enctype="multipart/form-data">';
        echo '<input type="text" name="title" placeholder="Title" required>';
        echo '<input type="text" name="description" placeholder="Description">';
        echo '<input type="file" name="homework_file" id="homework_file" required>';
        echo '<button type="submit" name="homework_upload">Upload</button>';
        echo '</form>';
    }
    ?>
    <h2>Homework list</h2>
    <table>
        <tr>
            <th>#</th>
            <th>Homework</th>
            <th>Teacher</th>
            <th>File(s)</th>
        </tr>
        <?php
        if (mysqli_num_rows($result_homework) > 0) {
            $index = 1; // Manual counter
            while ($row = mysqli_fetch_assoc($result_homework)) {
                $teacher_uuid = $row["teacher_uuid"];
                $teacher_name_sql = "SELECT fullname FROM users WHERE uuid = '$teacher_uuid'";
                $still_not_done = mysqli_query($conn, $teacher_name_sql);
                $teacher_name = mysqli_fetch_assoc($still_not_done)['fullname'];

                echo "<tr onclick=\"window.location='profile.php?uuid=" . $row['id'] . "'\" style='cursor: pointer;'>";
                echo "<td>" . $index . "</td>";
                echo "<td>" . htmlspecialchars($row['title']) . "</td>";
                echo "<td>" . htmlspecialchars($teacher_name) . "</td>";
                // download homework file button
                if ($row['homework_file'] != null){
                    echo "<td><a href='download.php?file=" . $row['homework_file'] . "'>Download</a></td>";
                } else {
                    echo "<td>N/A</td>";
                }
                echo "</tr>";
                $index++; // Increment index manually
            }
        } else {
            echo "<tr><td colspan='3'>No homeworks found.</td></tr>";
        }
        ?>
    </table>
    </div>

</html>