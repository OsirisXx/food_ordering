<?php
include '../includes/connect.php';
include '../includes/logger.php';

if (!isset($_SESSION['admin_sid']) || $_SESSION['admin_sid'] !== session_id()) {
    header('HTTP/1.1 403 Forbidden');
    exit('Forbidden');
}

$user_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($user_id <= 0) {
    header('Location: ../users-admin.php?error=invalid');
    exit();
}

$name = mysqli_real_escape_string($con, $_POST['name'] ?? '');
$email = mysqli_real_escape_string($con, $_POST['email'] ?? '');
$contact = mysqli_real_escape_string($con, $_POST['contact'] ?? '');
$role = $_POST['role'] ?? 'customer';

// Map UI role to DB values
$dbRole = ($role === 'admin') ? 'Administrator' : 'Customer';

$stmt = mysqli_prepare($con, "UPDATE users SET name = ?, email = ?, contact = ?, role = ? WHERE id = ?");
mysqli_stmt_bind_param($stmt, 'ssssi', $name, $email, $contact, $dbRole, $user_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

header('Location: ../users-admin.php?updated=1');
logActivity($con, "Updated user #$user_id: $name");
exit();
?>


