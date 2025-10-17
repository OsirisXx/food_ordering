<?php
include '../includes/connect.php';
include '../includes/logger.php';

header('Content-Type: application/json');

// Allow either admin session id or Administrator role as fallback
$isAdmin = (isset($_SESSION['admin_sid']) && $_SESSION['admin_sid'] === session_id())
          || (isset($_SESSION['role']) && strcasecmp($_SESSION['role'], 'Administrator') === 0);
if (!$isAdmin) {
    echo json_encode(['success' => false, 'message' => 'Not authorized']);
    exit();
}

$user_id = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
if ($user_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid user id']);
    exit();
}

// Prevent deleting yourself (optional safety)
if (isset($_SESSION['user_id']) && (int)$_SESSION['user_id'] === $user_id) {
    echo json_encode(['success' => false, 'message' => 'Cannot delete current user']);
    exit();
}

$stmt = mysqli_prepare($con, "UPDATE users SET deleted = 1 WHERE id = ?");
mysqli_stmt_bind_param($stmt, 'i', $user_id);
$ok = mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

if ($ok && mysqli_affected_rows($con) > 0) {
    logActivity($con, "Deleted user #$user_id (soft delete)");
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'User not found']);
}
?>


