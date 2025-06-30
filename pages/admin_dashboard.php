<!--for checking if the user is logged in and has the correct role-->

<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header("Location: ../index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <!-- Link main CSS -->
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="../assets/css/base.css">
</head>
<body class="dashboard">

    <div class="dashboard-header clearfix">
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h1>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>

    <div class="dashboard-content">
        <h2>User Dashboard</h2>
        <p>This is the Admin dashboard.</p>
        <!-- Add more features below -->


        
    </div>

</body>
</html>
