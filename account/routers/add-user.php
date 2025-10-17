<?php
include '../includes/connect.php';
include '../includes/logger.php';

if (!isset($_SESSION['admin_sid']) || $_SESSION['admin_sid'] !== session_id()) {
    header('HTTP/1.1 403 Forbidden');
    exit('Forbidden');
}

function random_digits($length) {
    $out = '';
    for ($i = 0; $i < $length; $i++) { $out .= mt_rand(0, 9); }
    return $out;
}

$name = mysqli_real_escape_string($con, $_POST['name'] ?? '');
$email = mysqli_real_escape_string($con, $_POST['email'] ?? '');
$contact = mysqli_real_escape_string($con, $_POST['contact'] ?? '');
$password = mysqli_real_escape_string($con, $_POST['password'] ?? '');
$role = $_POST['role'] ?? 'customer';

// Minimal username generation from email/name
$username = !empty($email) ? $email : preg_replace('/\s+/', '', strtolower($name));

// Map UI role to DB values
$dbRole = ($role === 'admin') ? 'Administrator' : 'Customer';

// Insert user
$stmt = mysqli_prepare($con, "INSERT INTO users (role, name, username, password, email, address, contact, verified, deleted) VALUES (?, ?, ?, ?, ?, '', ?, 1, 0)");
mysqli_stmt_bind_param($stmt, 'ssssss', $dbRole, $name, $username, $password, $email, $contact);
if (!mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);
    header('Location: ../users-admin.php?error=add');
    exit();
}
$user_id = $con->insert_id;
mysqli_stmt_close($stmt);

// Create wallet + details
$stmtW = mysqli_prepare($con, "INSERT INTO wallet (customer_id) VALUES (?)");
mysqli_stmt_bind_param($stmtW, 'i', $user_id);
if (mysqli_stmt_execute($stmtW)) {
    $wallet_id = $con->insert_id;
    mysqli_stmt_close($stmtW);
    $cc = random_digits(16);
    $cvv = random_digits(3);
    $stmtD = mysqli_prepare($con, "INSERT INTO wallet_details (wallet_id, number, cvv) VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($stmtD, 'iss', $wallet_id, $cc, $cvv);
    mysqli_stmt_execute($stmtD);
    mysqli_stmt_close($stmtD);
} else {
    mysqli_stmt_close($stmtW);
}

header('Location: ../users-admin.php?added=1');
logActivity($con, "Added user: $name");
exit();
?>


