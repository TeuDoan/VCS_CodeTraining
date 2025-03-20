<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: /Student_management/login_system/index.php");
    exit();
}
require '../login_system/config.php';
$result_student = mysqli_query($conn,"SELECT fullname, username FROM users where role = 'student'");
$result_teacher = mysqli_query($conn,"SELECT fullname, username FROM users where role = 'teacher'");
$data_student = $result_student->fetch_all(MYSQLI_ASSOC);
$data_teacher = $result_teacher->fetch_all(MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="en" >
<head>
  <meta charset="UTF-8">
  <title>VAP - VCS Academic Portal</title>
  <link rel="stylesheet" href="/Student_management/dashboard/css/style.css">

</head>
<body>

<ul>
  <li><a href="index.php">Home</a></li>
  <li><a href="profile.php">Your Profile</a></li>
  
</ul> 



<div class="table-container">
<table border="1">
  <tr>
    <th>#</th> <!-- Cột số thứ tự -->
    <th>Full name</th>
    <th>Username</th>
  </tr>
  <?php $index = 1; foreach($data_student as $row): ?>
  <tr>
    <td><?= $index++ ?></td> <!-- Tăng index sau mỗi lần lặp -->
    <td><?= htmlspecialchars($row['fullname']) ?></td>
    <td><a href="profile.php?user=<?= urlencode($row['username']) ?>"><?= htmlspecialchars($row['username']) ?></a></td>
  </tr>
  <?php endforeach ?>
</table>
</div>

<div class="table-container">
<table border="1">
  <tr>
    <th>#</th> 
    <th>Full name</th>
    <th>Username</th>
  </tr>
  <?php $index = 1; foreach($data_teacher as $row): ?>
  <tr>
    <td><?= $index++ ?></td> <!-- Tăng index sau mỗi lần lặp -->
    <td><?= htmlspecialchars($row['fullname']) ?></td>
    <td><a href="profile.php?user=<?= urlencode($row['username']) ?>"><?= htmlspecialchars($row['username']) ?></a></td>
    
  </tr>
  <?php endforeach ?>
</table>
</div>

<a href="/Student_management/login_system/logout.php">Đăng xuất</a>
</body>
</html>
