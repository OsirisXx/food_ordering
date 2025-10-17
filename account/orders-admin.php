<?php
session_start();
include 'includes/connect.php';

// Check if admin is logged in
if (isset($_SESSION['admin_sid']) && $_SESSION['admin_sid'] == session_id()) {
    $name = isset($_SESSION['name']) ? $_SESSION['name'] : 'Admin';
    $role = isset($_SESSION['role']) ? $_SESSION['role'] : 'Administrator';

    // Get search and filter parameters
    $search = isset($_GET['search']) ? mysqli_real_escape_string($con, $_GET['search']) : '';
    $status_filter = isset($_GET['status']) ? mysqli_real_escape_string($con, $_GET['status']) : '';

    // Build query
    $where_conditions = [];
    if (!empty($search)) {
        $where_conditions[] = "o.id LIKE '%$search%'";
    }
    if (!empty($status_filter)) {
        $where_conditions[] = "o.status = '$status_filter'";
    }

    $where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

    // Fetch orders
    $orders_query = "SELECT o.*, u.name as customer_name, u.contact, u.email 
                     FROM orders o 
                     LEFT JOIN users u ON o.customer_id = u.id 
                     $where_clause 
                     ORDER BY o.id DESC";
    $orders_result = mysqli_query($con, $orders_query);

    // Fetch distinct statuses for filter dropdown
    $status_query = "SELECT DISTINCT status FROM orders";
    $status_result = mysqli_query($con, $status_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders - Admin Panel</title>
    <link href="css/lib/bootstrap/bootstrap.min.css" rel="stylesheet">
    <link href="css/helper.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="css/admin-custom.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="fix-header">
    <div id="main-wrapper">
        <?php 
        $current_page = 'orders';
        include 'includes/sidebar.php'; 
        ?>

        <div class="page-wrapper">
            <div class="container-fluid">
                <div class="page-header">
                    <h1 class="page-title">
                        <i class="fa fa-shopping-cart"></i>
                        Orders
                    </h1>
                    <div class="header-actions">
                        <button class="btn btn-primary-custom" onclick="location.reload()">
                            <i class="fa fa-refresh"></i>
                        </button>
                    </div>
                </div>

                <div class="content-card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h2 class="card-title">Order Management</h2>
                            </div>
                            <div class="col-md-6">
                                <form method="GET" class="form-inline float-right">
                                    <div class="form-group mr-2">
                                        <select name="status" class="form-control form-control-custom">
                                            <option value="">All Orders</option>
                                            <?php while ($status = mysqli_fetch_assoc($status_result)): ?>
                                            <option value="<?php echo $status['status']; ?>" 
                                                    <?php echo ($status_filter == $status['status']) ? 'selected' : ''; ?>>
                                                <?php echo ucfirst($status['status']); ?>
                                            </option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>
                                    <div class="form-group mr-2">
                                        <input type="text" name="search" class="form-control form-control-custom" 
                                               placeholder="Search by Order ID" value="<?php echo htmlspecialchars($search); ?>">
                                    </div>
                                    <button type="submit" class="btn btn-primary-custom">Filter</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-custom">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Customer Name</th>
                                        <th>Contact</th>
                                        <th>Total</th>
                                        <th>Order Status</th>
                                        <th>Payment Mode</th>
                                        <th>Cancel Reason</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($order = mysqli_fetch_assoc($orders_result)): ?>
                                    <tr>
                                        <td><strong>#<?php echo $order['id']; ?></strong></td>
                                        <td><?php echo htmlspecialchars($order['customer_name'] ?? 'Unknown'); ?></td>
                                        <td><?php echo htmlspecialchars($order['contact'] ?? '-'); ?></td>
                                        <td>₱<?php echo number_format($order['total'], 2); ?></td>
                                        <td>
                                            <span class="status-badge status-<?php echo strtolower($order['status']); ?>">
                                                <?php echo ucfirst($order['status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo htmlspecialchars($order['payment_type'] ?? 'Takeaway'); ?></td>
                                        <td><?php echo htmlspecialchars($order['cancel_reason'] ?? '-'); ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-primary-custom" onclick="viewOrderDetails(<?php echo $order['id']; ?>)">
                                                View Details
                                            </button>
                                            <button class="btn btn-sm btn-danger-custom" onclick="deleteOrder(<?php echo $order['id']; ?>)">
                                                Delete
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Details Modal -->
    <div class="modal fade" id="orderDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header modal-header-custom">
                    <h5 class="modal-title" id="orderDetailsTitle">Order Details</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="orderDetailsContent">
                    <!-- Order details will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <script src="js/lib/jquery/jquery.min.js"></script>
    <script src="js/lib/bootstrap/js/popper.min.js"></script>
    <script src="js/lib/bootstrap/js/bootstrap.min.js"></script>
    <script>
        function viewOrderDetails(orderId) {
            // Load order details via AJAX
            $.ajax({
                url: 'routers/order-details.php',
                method: 'GET',
                data: { order_id: orderId },
                success: function(response) {
                    $('#orderDetailsTitle').text('Order #' + orderId + ' Details');
                    $('#orderDetailsContent').html(response);
                    $('#orderDetailsModal').modal('show');
                },
                error: function() {
                    alert('Error loading order details');
                }
            });
        }

        function deleteOrder(orderId) {
            if (confirm('Are you sure you want to delete this order?')) {
                $.ajax({
                    url: 'routers/delete-order.php',
                    method: 'POST',
                    data: { order_id: orderId },
                    success: function(response) {
                        if (response.success) {
                            alert('Order deleted successfully');
                            location.reload();
                        } else {
                            alert('Error deleting order');
                        }
                    },
                    error: function() {
                        alert('Error deleting order');
                    }
                });
            }
        }

        function updateOrderStatus(orderId) {
            const newStatus = $('#statusSelect' + orderId).val();
            const paymentStatus = $('#paymentSelect' + orderId).val();
            
            $.ajax({
                url: 'routers/update-order-status.php',
                method: 'POST',
                data: {
                    order_id: orderId,
                    status: newStatus,
                    payment_status: paymentStatus
                },
                success: function(response) {
                    if (response.success) {
                        alert('Order status updated successfully');
                        location.reload();
                    } else {
                        alert('Error updating order status');
                    }
                },
                error: function() {
                    alert('Error updating order status');
                }
            });
        }
    </script>
</body>
</html>

<?php
} else {
    header("Location: login.php");
    exit();
}
?>

