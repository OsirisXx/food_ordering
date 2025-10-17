<?php
session_start();
include '../includes/connect.php';
header('Content-Type: application/json');

// Check if user is logged in as customer
if (!isset($_SESSION['customer_sid']) || $_SESSION['customer_sid'] !== session_id()) {
    echo json_encode(['success' => false, 'message' => 'Not authorized']);
    exit();
}

// Get form data
$order_id = isset($_POST['order_id']) ? (int)$_POST['order_id'] : 0;
$rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;
$review_text = isset($_POST['review_text']) ? trim($_POST['review_text']) : '';

// Validation
if ($order_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid order ID']);
    exit();
}

if ($rating < 1 || $rating > 5) {
    echo json_encode(['success' => false, 'message' => 'Please select a valid rating']);
    exit();
}

if (empty($review_text)) {
    echo json_encode(['success' => false, 'message' => 'Please write a review']);
    exit();
}

// Check if order exists and belongs to the customer
$user_id = $_SESSION['user_id'];
$order_check = mysqli_query($con, "SELECT id, status FROM orders WHERE id = $order_id AND customer_id = $user_id AND deleted = 0");
if (!$order_check || mysqli_num_rows($order_check) == 0) {
    echo json_encode(['success' => false, 'message' => 'Order not found or not accessible']);
    exit();
}

$order = mysqli_fetch_assoc($order_check);
if ($order['status'] !== 'Completed') {
    echo json_encode(['success' => false, 'message' => 'You can only review completed orders']);
    exit();
}

// Check if review already exists for this order
$existing_review = mysqli_query($con, "SELECT id FROM reviews WHERE order_id = $order_id AND deleted = 0");
if ($existing_review && mysqli_num_rows($existing_review) > 0) {
    echo json_encode(['success' => false, 'message' => 'You have already reviewed this order']);
    exit();
}

// Get customer email
$customer_query = mysqli_query($con, "SELECT email FROM users WHERE id = $user_id");
$customer_email = '';
if ($customer_query && mysqli_num_rows($customer_query) > 0) {
    $customer = mysqli_fetch_assoc($customer_query);
    $customer_email = $customer['email'];
}

// Insert review
$review_text_escaped = mysqli_real_escape_string($con, $review_text);
$customer_email_escaped = mysqli_real_escape_string($con, $customer_email);

$insert_query = "INSERT INTO reviews (order_id, customer_id, email, review_text, rating, status, created_at) 
                 VALUES ($order_id, $user_id, '$customer_email_escaped', '$review_text_escaped', $rating, 'Pending', NOW())";

if (mysqli_query($con, $insert_query)) {
    // Log activity
    $log_query = "INSERT INTO activity_logs (user_role, action, date) VALUES ('Customer', 'Submitted review for order #$order_id', NOW())";
    mysqli_query($con, $log_query);
    
    echo json_encode(['success' => true, 'message' => 'Review submitted successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to submit review: ' . mysqli_error($con)]);
}
?>
