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
    $name = trim($_POST['name'] ?? '');
    
    if (empty($name)) {
        echo json_encode(['success' => false, 'message' => 'Category name is required']);
        exit();
    }
    
    try {
        // Check if category name already exists
        $check_stmt = mysqli_prepare($con, "SELECT id FROM categories WHERE name = ? AND deleted = 0");
        mysqli_stmt_bind_param($check_stmt, "s", $name);
        mysqli_stmt_execute($check_stmt);
        $result = mysqli_stmt_get_result($check_stmt);
        
        if (mysqli_num_rows($result) > 0) {
            echo json_encode(['success' => false, 'message' => 'Category name already exists']);
            exit();
        }
        
        // Insert new category
        $stmt = mysqli_prepare($con, "INSERT INTO categories (name) VALUES (?)");
        mysqli_stmt_bind_param($stmt, "s", $name);
        
        if (mysqli_stmt_execute($stmt)) {
            $category_id = mysqli_insert_id($con);
            echo json_encode(['success' => true, 'message' => 'Category added successfully', 'category_id' => $category_id]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to add category']);
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
