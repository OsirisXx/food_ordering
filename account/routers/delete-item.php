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
    $item_id = (int)($_POST['item_id'] ?? 0);
    
    if ($item_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid item ID']);
        exit();
    }
    
    try {
        // Soft delete the item
        $stmt = mysqli_prepare($con, "UPDATE items SET deleted = 1 WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $item_id);
        
        if (mysqli_stmt_execute($stmt)) {
            logActivity($con, "Deleted menu item ID: $item_id (soft delete)");
            if (mysqli_stmt_affected_rows($stmt) > 0) {
                echo json_encode(['success' => true, 'message' => 'Item deleted successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Item not found']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete item']);
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
