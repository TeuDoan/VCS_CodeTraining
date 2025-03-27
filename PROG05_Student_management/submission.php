<!-- This page show the list of submissions corresponding to a homework-->

<?php
session_start();
require "./config.php";

//Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ./login.php');
    exit();
}

if ($_SESSION['is_teacher'] == 0) {
    echo 'You do not have permission to view this page.';
    exit();
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <title>Homework Submission</title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link rel='stylesheet' type='text/css' media='screen' href='./css/style.css'>
    <script src='main.js'></script>
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
    <div class="homework-container">
        <h2>Homework Submissions</h2>
        <table>
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Homework</th>
                    <th>Submission</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT users.fullname, homeworks.title, submissions.file_path FROM submissions JOIN users ON submissions.student_uuid = users.uuid JOIN homeworks ON submissions.homework_id = homeworks.id";
                $result = mysqli_query($conn, $sql);
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['fullname']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['title']) . "</td>";
                        echo "<td><a href='./download.php?file=" . urlencode(basename($row['file_path'])). "&type=submission'>Download</a></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>No submissions found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>

</html>