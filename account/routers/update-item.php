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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_id = (int)($_POST['item_id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $status = trim($_POST['status'] ?? 'Available');
    $price = (int)($_POST['price'] ?? 0);
    $category_id = (int)($_POST['category_id'] ?? 0);
    
    // Validate required fields
    if ($item_id <= 0 || empty($name) || $price <= 0 || $category_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Please fill all required fields']);
        exit();
    }
    
    // Validate status
    if (!in_array($status, ['Available', 'Unavailable'])) {
        $status = 'Available';
    }
    
    try {
        // Check if item exists
        $check_stmt = mysqli_prepare($con, "SELECT id FROM items WHERE id = ? AND deleted = 0");
        mysqli_stmt_bind_param($check_stmt, "i", $item_id);
        mysqli_stmt_execute($check_stmt);
        $result = mysqli_stmt_get_result($check_stmt);
        
        if (mysqli_num_rows($result) === 0) {
            echo json_encode(['success' => false, 'message' => 'Item not found']);
            exit();
        }
        
        // Check if item name already exists (excluding current item)
        $name_check_stmt = mysqli_prepare($con, "SELECT id FROM items WHERE name = ? AND id != ? AND deleted = 0");
        mysqli_stmt_bind_param($name_check_stmt, "si", $name, $item_id);
        mysqli_stmt_execute($name_check_stmt);
        $name_result = mysqli_stmt_get_result($name_check_stmt);
        
        if (mysqli_num_rows($name_result) > 0) {
            echo json_encode(['success' => false, 'message' => 'Item name already exists']);
            exit();
        }
        
        // Handle image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            // Update with new image
            $image_data = file_get_contents($_FILES['image']['tmp_name']);
            $stmt = mysqli_prepare($con, "UPDATE items SET name = ?, description = ?, price = ?, status = ?, category_id = ?, image = ? WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "ssisssi", $name, $description, $price, $status, $category_id, $image_data, $item_id);
        } else {
            // Update without changing image
            $stmt = mysqli_prepare($con, "UPDATE items SET name = ?, description = ?, price = ?, status = ?, category_id = ? WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "ssissi", $name, $description, $price, $status, $category_id, $item_id);
        }
        
        if (mysqli_stmt_execute($stmt)) {
            echo json_encode(['success' => true, 'message' => 'Item updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update item']);
        }
        
        mysqli_stmt_close($stmt);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}
?>
