<?php
session_start();
if ($_SESSION['logged_in'] != 1) {
  header('location:./login.php');
}
require './config.php';
$sql_student = "SELECT username,fullname FROM users WHERE is_teacher=0";
$sql_teacher = "SELECT username,fullname FROM users WHERE is_teacher=1";
$result_student = mysqli_query($conn, $sql_student);
$result_teacher = mysqli_query($conn, $sql_teacher);

?>

<!DOCTYPE html>
<html>

<head>
  <meta charset='utf-8'>
  <meta http-equiv='X-UA-Compatible' content='IE=edge'>
  <title>VAP - VCS Academic Portal</title>
  <meta name='viewport' content='width=device-width, initial-scale=1'>
  <link rel='stylesheet' type='text/css' media='screen' href='main.css'>
  <script src='main.js'></script>
</head>

<body>
  <h1>Welcome to Student Management System</h1>

  <a href="./profile.php">Profile</a>
  <a href="./logout.php">Logout</a>
  

  <h2>Student List</h2>
  <table>
    <tr>
      <th>#</th>
      <th>Username</th>
      <th>Full Name</th>
    </tr>
    <?php
    if (mysqli_num_rows($result_student) > 0) {
      $index = 1; // Manual counter
      while ($row = mysqli_fetch_assoc($result_student)) {
        echo "<tr>";
        echo "<td>" . $index . "</td>";
        echo "<td>" . htmlspecialchars($row['username']) . "</td>";
        echo "<td>" . htmlspecialchars($row['fullname']) . "</td>";
        echo "</tr>";
        $index++; // Increment index manually
      }
    } else {
      echo "<tr><td colspan='3'>No users found.</td></tr>";
    }
    ?>
  </table>
  <h2>Teacher List</h2>
  <table>
    <tr>
      <th>#</th>
      <th>Username</th>
      <th>Full Name</th>
    </tr>
    <?php
    if (mysqli_num_rows($result_teacher) > 0) {
      $index = 1; // Manual counter
      while ($row = mysqli_fetch_assoc($result_teacher)) {
        echo "<tr>";
        echo "<td>" . $index . "</td>";
        echo "<td>" . htmlspecialchars($row['username']) . "</td>";
        echo "<td>" . htmlspecialchars($row['fullname']) . "</td>";
        echo "</tr>";
        $index++; // Increment index manually
      }
    } else {
      echo "<tr><td colspan='3'>No users found.</td></tr>";
    }
    ?>

  </table>

</body>

</html>