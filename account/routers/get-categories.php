<?php
session_start();
include '../includes/connect.php';

header('Content-Type: application/json');

// Check if user is admin
if (!isset($_SESSION['admin_sid']) || $_SESSION['admin_sid'] != session_id()) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

try {
    $stmt = mysqli_prepare($con, "SELECT id, name FROM categories WHERE deleted = 0 ORDER BY name ASC");
    if (!$stmt) { throw new Exception('Prepare failed'); }
    if (!mysqli_stmt_execute($stmt)) { throw new Exception('Execute failed'); }
    if (!mysqli_stmt_bind_result($stmt, $id, $name)) { throw new Exception('Bind result failed'); }

    $categories = [];
    while (mysqli_stmt_fetch($stmt)) {
        $categories[] = [ 'id' => (int)$id, 'name' => $name ];
    }
    mysqli_stmt_close($stmt);

    echo json_encode(['success' => true, 'categories' => $categories]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
?>
