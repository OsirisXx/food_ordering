<?php
session_start();
include '../includes/connect.php';
include '../includes/logger.php';

header('Content-Type: application/json');

// Check if user is admin
if (!isset($_SESSION['admin_sid']) || $_SESSION['admin_sid'] != session_id()) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $status = trim($_POST['status'] ?? 'Available');
    $price = (int)($_POST['price'] ?? 0);
    $category_id = (int)($_POST['category_id'] ?? 0);
    
    // Validate required fields
    if (empty($name) || $price <= 0 || $category_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Please fill all required fields']);
        exit();
    }
    
    // Validate status
    if (!in_array($status, ['Available', 'Unavailable'])) {
        $status = 'Available';
    }
    
    // Handle image upload
    $image_data = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $image_data = file_get_contents($_FILES['image']['tmp_name']);
    }
    
    try {
        // Check if item name already exists
        $check_stmt = mysqli_prepare($con, "SELECT id FROM items WHERE name = ? AND deleted = 0");
        mysqli_stmt_bind_param($check_stmt, "s", $name);
        mysqli_stmt_execute($check_stmt);
        $result = mysqli_stmt_get_result($check_stmt);
        
        if (mysqli_num_rows($result) > 0) {
            echo json_encode(['success' => false, 'message' => 'Item name already exists']);
            exit();
        }
        
        // Insert new item
        $stmt = mysqli_prepare($con, "INSERT INTO items (name, description, price, status, category_id, image) VALUES (?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "ssisss", $name, $description, $price, $status, $category_id, $image_data);
        
        if (mysqli_stmt_execute($stmt)) {
            logActivity($con, "Added menu item: $name");
            echo json_encode(['success' => true, 'message' => 'Item added successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to add item']);
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