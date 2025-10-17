<?php
session_start();
include 'includes/connect.php';

if(isset($_SESSION['admin_sid']) && $_SESSION['admin_sid']==session_id()) {

    $name = isset($_SESSION['name']) ? $_SESSION['name'] : 'Admin';
    $role = isset($_SESSION['role']) ? $_SESSION['role'] : 'Administrator';


    // Filters
    $search = isset($_GET['search']) ? mysqli_real_escape_string($con, $_GET['search']) : '';
    $status_filter = isset($_GET['status']) ? mysqli_real_escape_string($con, $_GET['status']) : '';

    // Build where clause
    $where = [];
    if ($search !== '') {
        $where[] = "o.id LIKE '%$search%'";
    }
    if ($status_filter !== '') {
        $where[] = "o.status = '$status_filter'";
    }
    $where_clause = empty($where) ? '' : 'WHERE '.implode(' AND ', $where);

    // One row per order with user info
    // Always hide soft-deleted orders
    $deleted_clause = empty($where) ? "WHERE o.deleted = 0" : "AND o.deleted = 0";
    $orders_result = mysqli_query($con, "
        SELECT o.id, o.date, o.payment_type, o.status, o.total, o.deleted,
               u.name AS customer_name, u.contact
        FROM orders o
        LEFT JOIN users u ON o.customer_id = u.id
        $where_clause
        $deleted_clause
        ORDER BY o.id DESC
    ");

    // Distinct statuses for dropdown
    $status_result = mysqli_query($con, "SELECT DISTINCT status FROM orders");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>All Orders</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <link href="css/materialize.min.css" rel="stylesheet">
  <link href="css/style.min.css" rel="stylesheet">
  <link href="css/custom/custom.min.css" rel="stylesheet">
  <link href="css/admin-custom.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>


<div id="main">
<div class="wrapper">

<!-- SIDEBAR -->
<div class="left-sidebar">
  <div class="scroll-sidebar">
    <?php 
    $current_page = 'orders';
    include 'includes/sidebar.php'; 
    ?>
  </div>
</div>

<!-- CONTENT -->
<section id="content">
<div class="container">

  <div class="page-header">
    <div style="display:flex;align-items:center;justify-content:space-between;">
      <h1 class="page-title"><i class="fa fa-shopping-cart"></i> Orders</h1>
      <button class="btn btn-primary-custom" onclick="location.reload()"><i class="fa fa-refresh"></i></button>
    </div>
  </div>

  <div class="content-card">
    <div class="card-header">
      <div class="row" style="display:flex;align-items:center;">
        <div class="col s12 m6"><h2 class="card-title">Order Management</h2></div>
        <div class="col s12 m6">
          <form method="GET" class="right" style="display:flex;gap:15px;align-items:center;height:48px;">
            <div class="input-field" style="margin:0;">
              <select name="status" class="form-control-custom" style="min-width:140px;">
                <option value="">All Orders</option>
                <?php while ($status = mysqli_fetch_assoc($status_result)): ?>
                <option value="<?php echo $status['status']; ?>" <?php echo ($status_filter == $status['status']) ? 'selected' : ''; ?>>
                  <?php echo ucfirst($status['status']); ?>
                </option>
                <?php endwhile; ?>
              </select>
            </div>
            <div class="input-field" style="margin:15px 0 0;min-width:220px;">
              <input type="text" name="search" class="form-control-custom" placeholder="Search by Order ID" value="<?php echo htmlspecialchars($search); ?>">
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
                <span class="status-badge status-<?php echo strtolower(str_replace(' ', '-', $order['status'])); ?>">
                  <?php echo htmlspecialchars($order['status']); ?>
                </span>
              </td>
              <td><?php echo htmlspecialchars($order['payment_type'] ?? 'Takeaway'); ?></td>
              <td class="actions-cell">
                <div class="button-group">
                  <form method="post" action="routers/edit-orders.php" class="inline-form">
                    <input type="hidden" name="id" value="<?php echo $order['id']; ?>">
                    <input type="hidden" name="status" value="Completed">
                    <button type="submit" class="btn btn-primary-custom btn-sm" style="background:#D2B48C !important; width:100% !important;">Completed</button>
                  </form>
                  <a class="btn btn-primary-custom btn-sm" href="orders.php?id=<?php echo $order['id']; ?>">View Details</a>
                  <form id="delete-form-<?php echo $order['id']; ?>" method="post" action="routers/cancel-order.php" class="inline-form">
                    <input type="hidden" name="id" value="<?php echo $order['id']; ?>">
                    <input type="hidden" name="status" value="Cancelled by Admin">
                    <input type="hidden" name="payment_type" value="<?php echo htmlspecialchars($order['payment_type']); ?>">
                    <button type="button" class="btn btn-danger-custom btn-sm open-delete-modal" data-form-id="delete-form-<?php echo $order['id']; ?>" data-order-id="#<?php echo $order['id']; ?>">Delete</button>
                  </form>
                </div>
              </td>
            </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

</div>
</section>

</div>
</div>

<!-- FOOTER -->
<?php include 'includes/footer.php'; ?>

<script src="js/plugins/jquery-1.11.2.min.js"></script>
<script src="js/materialize.min.js"></script>
<script>
$(document).ready(function(){
    $('.collapsible').collapsible();
    $('.dropdown-button').dropdown();
    // Delete modal logic
    $(document).on('click', '.open-delete-modal', function(){
        var formId = $(this).data('form-id');
        var orderId = $(this).data('order-id');
        $('#confirm-delete-order-id').text(orderId);
        $('#confirm-delete').data('form-id', formId);
        $('#deleteModal').fadeIn(120);
    });
    $('#cancel-delete, #close-delete').on('click', function(){
        $('#deleteModal').fadeOut(100);
    });
    $('#confirm-delete').on('click', function(){
        var formId = $(this).data('form-id');
        if (formId) { document.getElementById(formId).submit(); }
    });
});
</script>
<!-- Custom Delete Confirmation Modal -->
<div id="deleteModal" style="display:none; position: fixed; inset: 0; z-index: 9999; background: rgba(0,0,0,0.45);">
  <div style="max-width: 420px; width: 92%; margin: 10% auto; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
    <div style="padding: 18px 20px; background: #fdf2e9; border-bottom: 1px solid #f1e3d6; display:flex; align-items:center; justify-content:space-between;">
      <div style="display:flex; align-items:center; gap:10px;">
        <span style="display:inline-flex; width:34px; height:34px; border-radius:50%; align-items:center; justify-content:center; background:#ffe3e3; color:#dc3545;">
          <i class="fa fa-trash"></i>
        </span>
        <h3 style="margin:0; font-size: 18px; font-weight: 600; color:#333;">Confirm Deletion</h3>
      </div>
      <button id="close-delete" style="border:none; background:transparent; font-size:18px; cursor:pointer; color:#666;">×</button>
    </div>
    <div style="padding: 20px;">
      <p style="margin:0 0 10px; color:#555;">You're about to delete order <strong id="confirm-delete-order-id">#</strong>.</p>
      <p style="margin:0; color:#777; font-size: 13px;">This will mark the order as cancelled and hide it from the list. This action can be reversed only by database changes.</p>
    </div>
    <div style="padding: 14px 20px; display:flex; gap:10px; justify-content:flex-end; background:#fafafa; border-top:1px solid #eee;">
      <button id="cancel-delete" class="btn btn-primary-custom" style="background:#6c757d; padding:10px 16px; line-height:1.2; display:inline-flex; align-items:center; justify-content:center; height:40px;">Cancel</button>
      <button id="confirm-delete" class="btn btn-danger-custom" style="background:#dc3545; padding:10px 16px; line-height:1.2; display:inline-flex; align-items:center; justify-content:center; height:40px;">Yes, Delete</button>
    </div>
  </div>
</div>
</body>
</html>

<?php
} else {
    if(isset($_SESSION['customer_sid']) && $_SESSION['customer_sid']==session_id()){
        header("Location: orders.php");
        exit();
    } else {
        header("Location: login.php");
        exit();
    }
}
?>
