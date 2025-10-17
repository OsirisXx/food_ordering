<?php
include '../includes/connect.php';
header('Content-Type: application/json');

if (!isset($_SESSION['admin_sid']) || $_SESSION['admin_sid'] !== session_id()) {
    echo json_encode(['success' => false, 'message' => 'Not authorized']);
    exit();
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { echo json_encode(['success'=>false, 'message'=>'Invalid id']); exit(); }

$res = mysqli_query($con, "SELECT id, name, email, contact, role, status, hire_date FROM staff WHERE id = $id AND deleted = 0");
$staff = mysqli_fetch_assoc($res);
if (!$staff) { echo json_encode(['success'=>false, 'message'=>'Not found']); exit(); }

echo json_encode(['success'=>true, 'staff'=>$staff]);
?>


