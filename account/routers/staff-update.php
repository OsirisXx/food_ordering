<?php
include '../includes/connect.php';
include '../includes/logger.php';

if (!isset($_SESSION['admin_sid']) || $_SESSION['admin_sid'] !== session_id()) {
    header('Location: ../staffs-admin.php?error=auth');
    exit();
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { header('Location: ../staffs-admin.php?error=invalid'); exit(); }

$name = mysqli_real_escape_string($con, $_POST['name'] ?? '');
$email = mysqli_real_escape_string($con, $_POST['email'] ?? '');
$contact = mysqli_real_escape_string($con, $_POST['contact'] ?? '');
$role = mysqli_real_escape_string($con, $_POST['role'] ?? '');
$status = mysqli_real_escape_string($con, $_POST['status'] ?? 'active');
$hire_date = mysqli_real_escape_string($con, $_POST['hire_date'] ?? '');

$sql = $hire_date ? "UPDATE staff SET name=?, email=?, contact=?, role=?, status=?, hire_date=? WHERE id=?" : "UPDATE staff SET name=?, email=?, contact=?, role=?, status=? WHERE id=?";
$stmt = mysqli_prepare($con, $sql);
if ($hire_date) {
    mysqli_stmt_bind_param($stmt, 'ssssssi', $name, $email, $contact, $role, $status, $hire_date, $id);
} else {
    mysqli_stmt_bind_param($stmt, 'sssssi', $name, $email, $contact, $role, $status, $id);
}
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

header('Location: ../staffs-admin.php');
logActivity($con, "Updated staff #$id: $name");
exit();
?>


