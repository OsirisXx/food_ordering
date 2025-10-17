<?php
include '../includes/connect.php';

if(isset($_POST['status']) && isset($_POST['id'])) {
    $status = mysqli_real_escape_string($con, $_POST['status']);
    $id = intval($_POST['id']);
    
    $sql = "UPDATE orders SET status='$status' WHERE id=$id";
    if(mysqli_query($con, $sql)) {
        // Log activity
        $log_query = "INSERT INTO activity_logs (user_role, action, date) VALUES ('Admin', 'Updated order #$id status to $status', NOW())";
        mysqli_query($con, $log_query);
    }
}

header("location: ../all-orders.php");
?>