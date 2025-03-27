<?php
session_start();
require "./config.php";

//Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ./login.php');
    exit();
}

$sql_homework = "SELECT * FROM homeworks";
$result_homework = mysqli_query($conn, $sql_homework);

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
        <a href="./submission.php">Homework submission</a>
        <a href="./logout.php">Logout</a>
    </nav>
    <div class="homework-container">
        <!-- If teacher then show upload homework form -->
        <?php if ($_SESSION['is_teacher'] == 1) { ?>
            <h2>Upload Assignment</h2>
            <form action="upload.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="upload_type" value="assignment">
                <input type="text" name="title" placeholder="Title" required>
                <input type="text" name="description" placeholder="Description">
                <input type="file" name="fileToUpload" id="homework_file" required>
                <button type="submit" name="homework_upload">Upload</button>
            </form>'

        <?php } ?>
        <?php if ($_SESSION['is_teacher'] == 0) { ?>
            <h2>Submit Homework</h2>
            <form action="upload.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="upload_type" value="submission">
                <select name="homework_id" required>
                    <option value="">Select Assignment</option>
                    <?php
                    $query = "SELECT id, title FROM homeworks";
                    $result = mysqli_query($conn, $query);
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<option value='" . $row['id'] . "'>" . htmlspecialchars($row['title']) . "</option>";
                    }
                    ?>
                </select>
                <input type="file" name="fileToUpload" required>
                <button type="submit">Submit Homework</button>
            </form>
        <?php } ?>

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
                    if ($row['homework_file'] != null) {
                        echo "<td><a href='download.php?file=" . urlencode(basename($row['homework_file'])) . "&type=homework'>Download</a></td>";
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