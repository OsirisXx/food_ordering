<?php
session_start();
include '../includes/connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['admin_sid']) || $_SESSION['admin_sid'] != session_id()) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$categoryId = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;

try {
    if ($categoryId > 0) {
        $stmt = mysqli_prepare($con, "SELECT id, name, description, price, status, TO_BASE64(image) AS image FROM items WHERE deleted = 0 AND category_id = ? ORDER BY name ASC");
        if (!$stmt) { throw new Exception('Prepare failed'); }
        mysqli_stmt_bind_param($stmt, 'i', $categoryId);
    } else {
        $stmt = mysqli_prepare($con, "SELECT id, name, description, price, status, TO_BASE64(image) AS image FROM items WHERE deleted = 0 ORDER BY name ASC");
        if (!$stmt) { throw new Exception('Prepare failed'); }
    }
    if (!mysqli_stmt_execute($stmt)) { throw new Exception('Execute failed'); }
    if (!mysqli_stmt_bind_result($stmt, $id, $name, $description, $price, $status, $imageBase64)) { throw new Exception('Bind result failed'); }
    $items = [];
    while (mysqli_stmt_fetch($stmt)) {
        $items[] = [
            'id' => (int)$id,
            'name' => $name,
            'description' => $description,
            'price' => (int)$price,
            'status' => $status,
            'image' => $imageBase64 ?: null
        ];
    }
    mysqli_stmt_close($stmt);
    echo json_encode(['success' => true, 'items' => $items]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}

?>

