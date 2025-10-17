<?php
include '../includes/connect.php';
include '../includes/logger.php';

if (!isset($_SESSION['admin_sid']) || $_SESSION['admin_sid'] !== session_id()) {
    header('Location: ../staffs-admin.php?error=auth');
    exit();
}

$name = mysqli_real_escape_string($con, $_POST['name'] ?? '');
$email = mysqli_real_escape_string($con, $_POST['email'] ?? '');
$contact = mysqli_real_escape_string($con, $_POST['contact'] ?? '');
$role = mysqli_real_escape_string($con, $_POST['role'] ?? '');
// Status/hire_date removed from the Add form per UI spec
$status = 'active';
$hire_date = '';

if ($name && $email && $contact && $role) {
    $stmt = mysqli_prepare($con, "INSERT INTO staff (name,email,contact,role,status) VALUES (?,?,?,?,?)");
    mysqli_stmt_bind_param($stmt, 'sssss', $name, $email, $contact, $role, $status);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

header('Location: ../staffs-admin.php');
logActivity($con, "Added staff: $name");
exit();
?>


