<?php
include '../includes/connect.php';
include '../includes/wallet.php';
include '../includes/logger.php';
$total = 0;

// Safely read POST inputs
$address = isset($_POST['address']) ? htmlspecialchars($_POST['address']) : '';
$description = isset($_POST['description']) ? htmlspecialchars($_POST['description']) : '';
$payment_type = isset($_POST['payment_type']) ? $_POST['payment_type'] : '';
$total = isset($_POST['total']) ? (int)$_POST['total'] : 0;

// Persist order header
    $sql = "INSERT INTO orders (customer_id, payment_type, address, total, description) VALUES ($user_id, '" . mysqli_real_escape_string($con, $payment_type) . "', '" . mysqli_real_escape_string($con, $address) . "', $total, '" . mysqli_real_escape_string($con, $description) . "')";
	if ($con->query($sql) === TRUE){
		$order_id =  $con->insert_id;
		foreach ($_POST as $key => $value)
		{
			if(is_numeric($key)){
			$result = mysqli_query($con, "SELECT * FROM items WHERE id = $key");
            while($row = mysqli_fetch_array($result))
            {
                $price = $row['price']; // unit price
            }
            // Store unit price; total for the line will be derived as quantity * price when displaying
            $sql = "INSERT INTO order_details (order_id, item_id, quantity, price) VALUES ($order_id, $key, $value, $price)";
			$con->query($sql) === TRUE;		
			}
		}
        if(isset($_POST['payment_type']) && $_POST['payment_type'] == 'Wallet'){
        // Resolve wallet_id and current balance for logged-in user
        $wallet_id = null;
        $current_balance = null;
        if (isset($_SESSION['user_id'])) {
            $current_user_id = (int) $_SESSION['user_id'];
            $resWallet = mysqli_query($con, "SELECT id FROM wallet WHERE customer_id = $current_user_id");
            if ($rowW = mysqli_fetch_assoc($resWallet)) {
                $wallet_id = (int)$rowW['id'];
                $resDetails = mysqli_query($con, "SELECT balance FROM wallet_details WHERE wallet_id = $wallet_id");
                if ($rowD = mysqli_fetch_assoc($resDetails)) {
                    $current_balance = (int)$rowD['balance'];
                }
            }
        }
        if ($wallet_id !== null && $current_balance !== null) {
            $new_balance = $current_balance - $total;
            $sql = "UPDATE wallet_details SET balance = $new_balance WHERE wallet_id = $wallet_id;";
            $con->query($sql);
        }
		}
			logActivity($con, "Placed order ID: $order_id");
			header("location: ../orders.php");
	}

?>