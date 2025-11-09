<?php
$servername = "localhost"; // Change if needed
$username = "root"; // Change if needed
$password = ""; // Change if needed
$dbname = "hack_detection_db";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
die("Connection failed: " . $conn->connect_error);
}
?>
