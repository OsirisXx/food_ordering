<?php
include '../includes/connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['admin_sid']) || $_SESSION['admin_sid'] !== session_id()) {
    http_response_code(403);
    echo json_encode(['error' => 'Forbidden']);
    exit();
}

$user_id = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;
if ($user_id <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid user id']);
    exit();
}

$stmt = mysqli_prepare($con, "SELECT id, role, name, email, contact FROM users WHERE id = ? AND deleted = 0");
mysqli_stmt_bind_param($stmt, 'i', $user_id);
if (!mysqli_stmt_execute($stmt)) {
    http_response_code(500);
    echo json_encode(['error' => 'Query failed']);
    exit();
}
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$user) {
    http_response_code(404);
    echo json_encode(['error' => 'User not found']);
    exit();
}

// Map role to UI values
$role = strtolower($user['role']) === 'administrator' ? 'admin' : 'customer';

echo json_encode([
    'id' => (int)$user['id'],
    'name' => $user['name'],
    'email' => $user['email'],
    'contact' => $user['contact'],
    'role' => $role
]);
?>


