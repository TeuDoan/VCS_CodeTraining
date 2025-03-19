<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: /Student_management/login_system/index.php");
    exit();
}
?>
<h1>Chào mừng, <?php echo $_SESSION['username']; ?>!</h1>

<a href="/Student_management/login_system/logout.php">Đăng xuất</a>
