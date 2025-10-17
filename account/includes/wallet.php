<?php
require_once 'connect.php';

// Expose common session values for pages that include this file
$name = isset($_SESSION['name']) ? $_SESSION['name'] : '';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : '';

// Load wallet data for the logged-in user if available
if (isset($_SESSION['user_id'])) {
    $user_id = (int) $_SESSION['user_id'];
    $result = $con->query("SELECT * FROM wallet WHERE customer_id = $user_id");
}
?>
