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

$fields = [];
if (isset($_POST['status'])) {
    $status = mysqli_real_escape_string($con, $_POST['status']);
    $fields[] = "status='".$status."'";
}
if (isset($_POST['response'])) {
    $response = mysqli_real_escape_string($con, $_POST['response']);
    $fields[] = "response='".$response."'";
}

if (!$fields) { echo json_encode(['success'=>false,'message'=>'No changes']); exit(); }

$sql = "UPDATE reviews SET ".implode(', ',$fields)." WHERE id=$id";
$ok = $con->query($sql);
if ($ok) { logActivity($con, "Updated review #$id"); }
echo json_encode(['success' => $ok ? true : false]);
?>


