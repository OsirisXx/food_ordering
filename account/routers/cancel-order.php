<?php
include '../includes/connect.php';
include '../includes/wallet.php';
include '../includes/logger.php';
$status = isset($_POST['status']) ? mysqli_real_escape_string($con, $_POST['status']) : '';
$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$sql = "UPDATE orders SET status='$status', deleted=1 WHERE id=$id;";
$con->query($sql);
$sql = mysqli_query($con, "SELECT * FROM orders where id=$id");
while($row1 = mysqli_fetch_array($sql)){
$total = $row1['total'];
}
// Resolve wallet context for logged-in user
$wallet_id = null;
$balance = null;
if (isset($_SESSION['user_id'])) {
	$user_id = (int) $_SESSION['user_id'];
	$resWallet = mysqli_query($con, "SELECT id FROM wallet WHERE customer_id = $user_id");
	if ($rowW = mysqli_fetch_assoc($resWallet)) {
		$wallet_id = (int)$rowW['id'];
		$resDetails = mysqli_query($con, "SELECT balance FROM wallet_details WHERE wallet_id = $wallet_id");
		if ($rowD = mysqli_fetch_assoc($resDetails)) {
			$balance = (int)$rowD['balance'];
		}
	}
}
if($_POST['payment_type'] == 'Wallet'){
	if ($wallet_id !== null && $balance !== null) {
		$balance = $balance + $total;
		$sql = "UPDATE wallet_details SET balance = $balance WHERE wallet_id = $wallet_id;";
		$con->query($sql);
	}
}
logActivity($con, "Cancelled order ID: $id");
// Redirect admins back to all orders, customers to their orders
if (isset($_SESSION['admin_sid']) && $_SESSION['admin_sid'] === session_id()) {
	header("location: ../all-orders.php");
} else {
	header("location: ../orders.php");
}
?>