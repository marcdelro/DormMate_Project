<?php
// db connector 
$host = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "user_system"; // Fixed database name to match admin dashboard
$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
