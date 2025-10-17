<?php
include '../includes/connect.php';
include '../includes/logger.php';
header('Content-Type: application/json');

if (!isset($_SESSION['admin_sid']) || $_SESSION['admin_sid'] !== session_id()) {
    echo json_encode(['success' => false, 'message' => 'Not authorized']);
    exit();
}

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
if ($id <= 0) { echo json_encode(['success'=>false,'message'=>'Invalid id']); exit(); }

$ok = $con->query("UPDATE reviews SET deleted = 1 WHERE id = $id");
if ($ok) { logActivity($con, "Deleted review #$id (soft delete)"); }
echo json_encode(['success' => $ok ? true : false]);
?>


