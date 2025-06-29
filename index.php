<?php
session_start();

// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['user_role'] == 'admin') {
        header("Location: pages/admin_dashboard.php");
    } else {
        header("Location: pages/user_dashboard.php");
    }
    exit();
}

// Redirect to login page
header("Location: ./pages/login.php");
exit();
?>