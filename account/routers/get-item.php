<?php
session_start();
include '../includes/connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['admin_sid']) || $_SESSION['admin_sid'] != session_id()) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$itemId = isset($_GET['item_id']) ? (int)$_GET['item_id'] : 0;

if ($itemId <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid item id']);
    exit();
}

try {
    $stmt = mysqli_prepare($con, "SELECT id, name, description, price, status, category_id, TO_BASE64(image) AS image FROM items WHERE id = ? AND deleted = 0");
    if (!$stmt) { throw new Exception('Prepare failed'); }
    mysqli_stmt_bind_param($stmt, 'i', $itemId);
    if (!mysqli_stmt_execute($stmt)) { throw new Exception('Execute failed'); }
    if (!mysqli_stmt_bind_result($stmt, $id, $name, $description, $price, $status, $categoryId, $imageBase64)) { throw new Exception('Bind result failed'); }
    if (!mysqli_stmt_fetch($stmt)) {
        echo json_encode(['success' => false, 'message' => 'Item not found']);
        mysqli_stmt_close($stmt);
        exit();
    }
    mysqli_stmt_close($stmt);

    echo json_encode([
        'success' => true,
        'item' => [
            'id' => (int)$id,
            'name' => $name,
            'description' => $description,
            'price' => (float)$price,
            'status' => $status,
            'category_id' => isset($categoryId) ? (int)$categoryId : null,
            'image' => $imageBase64 ?: null
        ]
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
?>


