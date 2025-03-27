<?php
session_start();
require './config.php';

// Redirect if not logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ./login.php');
    exit();
}

// Ensure uuid is set
$user_uuid = $_SESSION['uuid'] ?? '';

// Prepare a single query to fetch both students & teachers
$sql = "SELECT username, fullname, uuid, is_teacher FROM users ORDER BY is_teacher ASC, username ASC";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Separate students & teachers
$students = [];
$teachers = [];

while ($row = mysqli_fetch_assoc($result)) {
    if ($row['is_teacher']) {
        $teachers[] = $row;
    } else {
        $students[] = $row;
    }
}

mysqli_stmt_close($stmt);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <title>VAP - VCS Academic Portal</title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link rel='stylesheet' type='text/css' media='screen' href='./css/style.css'>
    <script src='main.js'></script>
</head>

<body>
    <h1>VAP - VCS Academic Portal</h1>
    <nav>
        <a href="./index.php">Home</a>
        <a href="./profile.php?uuid=<?php echo htmlspecialchars($user_uuid); ?>">Profile</a>
        <a href="./homework.php">Homework</a>
        <a href="./submission.php">Homework submission</a>
        <a href="./logout.php">Logout</a>
    </nav>

    <?php
    function renderTable($title, $data) {
        echo "<h2>{$title}</h2>";
        echo "<table>";
        echo "<tr><th>#</th><th>Username</th><th>Full Name</th></tr>";

        if (count($data) > 0) {
            foreach ($data as $index => $row) {
                $safeUsername = htmlspecialchars($row['username']);
                $safeFullname = htmlspecialchars($row['fullname']);
                $safeUuid = htmlspecialchars($row['uuid']);

                echo "<tr onclick=\"window.location='profile.php?uuid={$safeUuid}'\" style='cursor: pointer;'>";
                echo "<td>" . ($index + 1) . "</td>";
                echo "<td>{$safeUsername}</td>";
                echo "<td>{$safeFullname}</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='3'>No users found.</td></tr>";
        }
        echo "</table>";
    }

    renderTable("Student List", $students);
    renderTable("Teacher List", $teachers);
    ?>
</body>

</html>
