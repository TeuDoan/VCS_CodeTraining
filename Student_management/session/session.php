<?php
session_start();

class Session {
    // Set user session after login
    public static function setUserSession($user) {
        $_SESSION['id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
    }

    // Check if user is logged in
    public static function isLoggedIn() {
        return isset($_SESSION['id']);
    }

    // Get logged-in user data
    public static function getUser() {
        if (self::isLoggedIn()) {
            return [
                'id' => $_SESSION['id'],
                'username' => $_SESSION['username'],
                'role' => $_SESSION['role']
            ];
        }
        return null;
    }

    // Logout user
    public static function logout() {
        session_unset();
        session_destroy();
        header("Location: /login.php"); // Redirect to login page after logout
        exit;
    }
}
?>
