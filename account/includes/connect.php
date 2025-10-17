<?php

// Database connection
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "food"; // Palitan ng actual DB name mo

$con = mysqli_connect($host, $user, $pass, $dbname);

if (!$con) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Start session kung hindi pa naka-start
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
