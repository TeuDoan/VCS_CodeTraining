<?php
session_start();
if (isset($_SESSION["logged_in"])) {
  header('location:./index.php');
}
?>

<!DOCTYPE html>
<html>

<head>
  <title>Login</title>
</head>

<body>
  <form action="./login_action.php" , method="post">
    <input type="username" name="username" placeholder="Username" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit" name="login">Login</button>
  </form>
  </form>
</body>

</html>