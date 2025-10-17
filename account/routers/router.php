<?php
session_start();
include '../includes/connect.php';

$success = false;
$username = $_POST['username'];
$password = $_POST['password'];

// Admin Login
$result = mysqli_query($con, "SELECT * FROM users WHERE username='$username' AND password='$password' AND role='Administrator' AND deleted=0");
while ($row = mysqli_fetch_array($result)) {
    $success = true;
    $user_id = $row['id'];
    $name = $row['name'];
    $role = $row['role'];
}

if ($success) {
    $_SESSION['admin_sid'] = session_id();
    $_SESSION['user_id'] = $user_id;
    $_SESSION['role'] = $role;
    $_SESSION['name'] = $name;
    header("location: ../dashboard.php");
    exit();
}

// Customer Login
$result = mysqli_query($con, "SELECT * FROM users WHERE username='$username' AND password='$password' AND role='Customer' AND deleted=0");
while ($row = mysqli_fetch_array($result)) {
    $success = true;
    $user_id = $row['id'];
    $name = $row['name'];
    $role = $row['role'];
}

if ($success) {
    $_SESSION['customer_sid'] = session_id();
    $_SESSION['user_id'] = $user_id;
    $_SESSION['role'] = $role;
    $_SESSION['name'] = $name;
    header("location: ../index.php");
    exit();
} else {
    header("location: ../login.php");
    exit();
}
?>
