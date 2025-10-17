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
    $category_id = intval($_POST['category_id'] ?? 0);
    
    if ($category_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid category ID']);
        exit();
    }
    
    try {
        // Check if category exists and get its name
        $check_stmt = mysqli_prepare($con, "SELECT name FROM categories WHERE id = ? AND deleted = 0");
        mysqli_stmt_bind_param($check_stmt, "i", $category_id);
        mysqli_stmt_execute($check_stmt);
        $result = mysqli_stmt_get_result($check_stmt);
        
        if (mysqli_num_rows($result) === 0) {
            echo json_encode(['success' => false, 'message' => 'Category not found']);
            exit();
        }
        
        $category = mysqli_fetch_assoc($result);
        $category_name = $category['name'];
        
        // Check if there are items in this category
        $items_check = mysqli_prepare($con, "SELECT COUNT(*) as item_count FROM items WHERE category_id = ? AND deleted = 0");
        mysqli_stmt_bind_param($items_check, "i", $category_id);
        mysqli_stmt_execute($items_check);
        $items_result = mysqli_stmt_get_result($items_check);
        $items_count = mysqli_fetch_assoc($items_result)['item_count'];
        
        if ($items_count > 0) {
            echo json_encode([
                'success' => false, 
                'message' => "Cannot delete category '{$category_name}' because it contains {$items_count} item(s). Please move or delete the items first."
            ]);
            exit();
        }
        
        // Soft delete the category (set deleted = 1)
        $delete_stmt = mysqli_prepare($con, "UPDATE categories SET deleted = 1 WHERE id = ?");
        mysqli_stmt_bind_param($delete_stmt, "i", $category_id);
        
        if (mysqli_stmt_execute($delete_stmt)) {
            echo json_encode([
                'success' => true, 
                'message' => "Category '{$category_name}' deleted successfully"
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete category']);
        }
        
        mysqli_stmt_close($delete_stmt);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}
?>
