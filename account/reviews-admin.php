<?php
session_start();
include 'includes/connect.php';

if(isset($_SESSION['admin_sid']) && $_SESSION['admin_sid']==session_id()) {

    $name = isset($_SESSION['name']) ? $_SESSION['name'] : 'Admin';
    $role = isset($_SESSION['role']) ? $_SESSION['role'] : 'Administrator';

    // Ensure reviews table exists
    $con->query("CREATE TABLE IF NOT EXISTS reviews (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT NOT NULL,
        customer_id INT NULL,
        email VARCHAR(100) NULL,
        review_text TEXT NOT NULL,
        rating TINYINT NOT NULL DEFAULT 0,
        status ENUM('Pending','Approved','Rejected') NOT NULL DEFAULT 'Pending',
        response TEXT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        deleted TINYINT(1) NOT NULL DEFAULT 0
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    // Filters
    $status_filter = isset($_GET['status']) ? mysqli_real_escape_string($con, $_GET['status']) : '';
    $where = "WHERE deleted = 0";
    if ($status_filter && $status_filter !== 'All') {
        $where .= " AND status = '".$status_filter."'";
    }
    $reviews_result = mysqli_query($con, "SELECT id, order_id, email, review_text, rating, status, response FROM reviews $where ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reviews - Admin Panel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link href="css/materialize.min.css" rel="stylesheet">
    <link href="css/style.min.css" rel="stylesheet">
    <link href="css/custom/custom.min.css" rel="stylesheet">
    <link href="css/admin-custom.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>

  <!-- MAIN -->
  <div id="main">
    <div class="wrapper">

      <?php 
      $current_page = 'reviews';
      include 'includes/sidebar.php'; 
      ?>

      <!-- CONTENT -->
      <section id="content">
        <div class="container">
          <div class="page-header">
            <i class="fa fa-star" style="color: #000; margin-right: 10px;"></i>
            <h4 class="header" style="display: inline; color: #000;">Reviews</h4>
            <div class="filter-dropdown" style="float: right; margin-top: -10px;">
              <select id="statusFilter" onchange="filterByStatus(this.value)" style="padding: 8px 12px; border: 1px solid #ccc; border-radius: 4px; background: white;">
                <option value="All" <?php echo ($status_filter === '' || $status_filter === 'All') ? 'selected' : ''; ?>>All</option>
                <option value="Pending" <?php echo $status_filter === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                <option value="Approved" <?php echo $status_filter === 'Approved' ? 'selected' : ''; ?>>Approved</option>
                <option value="Rejected" <?php echo $status_filter === 'Rejected' ? 'selected' : ''; ?>>Rejected</option>
              </select>
            </div>
          </div>
          
          <div class="row">
            <div class="col s12">
              <div class="card" style="background-color: #fdf2f2;">
                <div class="card-content" style="padding: 0;">
                  <table class="reviews-table">
                    <thead>
                      <tr style="background-color: #f5d0d0;">
                        <th style="padding: 15px; font-weight: bold; color: #000;">Order ID</th>
                        <th style="padding: 15px; font-weight: bold; color: #000;">Email</th>
                        <th style="padding: 15px; font-weight: bold; color: #000;">Review Text</th>
                        <th style="padding: 15px; font-weight: bold; color: #000;">Rating</th>
                        <th style="padding: 15px; font-weight: bold; color: #000;">Status</th>
                        <th style="padding: 15px; font-weight: bold; color: #000;">Response</th>
                        <th style="padding: 15px; font-weight: bold; color: #000;">Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php while ($review = mysqli_fetch_assoc($reviews_result)) { ?>
                        <tr style="background-color: #fff;">
                          <td style="padding: 15px; color: #000;">#<?php echo (int)$review['order_id']; ?></td>
                          <td style="padding: 15px; color: #000;"><?php echo htmlspecialchars($review['email'] ?? ''); ?></td>
                          <td style="padding: 15px; color: #000;"><?php echo htmlspecialchars($review['review_text']); ?></td>
                          <td style="padding: 15px; color: #000;">
                            <div class="rating">
                              <?php for ($i = 1; $i <= 5; $i++) { ?>
                                <i class="fa fa-star <?php echo ($i <= (int)$review['rating']) ? 'text-warning' : 'text-muted'; ?>" style="color: <?php echo ($i <= (int)$review['rating']) ? '#ffc107' : '#ccc'; ?>;"></i>
                              <?php } ?>
                            </div>
                          </td>
                          <td style="padding: 15px; color: #000;">
                            <select onchange="updateReviewStatus(<?php echo (int)$review['id']; ?>, this.value)" style="border: none; background: transparent; color: #000;">
                              <option value="Approved" <?php echo $review['status'] == 'Approved' ? 'selected' : ''; ?>>Approved</option>
                              <option value="Pending" <?php echo $review['status'] == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                              <option value="Rejected" <?php echo $review['status'] == 'Rejected' ? 'selected' : ''; ?>>Rejected</option>
                            </select>
                          </td>
                          <td style="padding: 15px; color: #000;"><?php echo htmlspecialchars($review['response'] ?? ''); ?></td>
                          <td style="padding: 15px; color: #000;">
                            <button class="btn-edit" onclick="editReview(<?php echo (int)$review['id']; ?>)" style="background: #28a745; color: white; border: none; padding: 5px 8px; margin-right: 5px; border-radius: 3px;">
                              <i class="fa fa-edit"></i>
                            </button>
                            <button class="btn-delete" onclick="deleteReview(<?php echo (int)$review['id']; ?>)" style="background: #dc3545; color: white; border: none; padding: 5px 8px; border-radius: 3px;">
                              <i class="fa fa-trash"></i>
                            </button>
                          </td>
                        </tr>
                      <?php } ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
  </div>

  <script src="js/plugins/jquery-1.11.2.min.js"></script>
  <script src="js/materialize.min.js"></script>
  <script>
    $(document).ready(function(){
        $('.collapsible').collapsible();
        $('.dropdown-button').dropdown();
    });

    function filterByStatus(status) {
        if (status === 'All') {
            window.location.href = 'reviews-admin.php';
        } else {
            window.location.href = 'reviews-admin.php?status=' + status;
        }
    }

    function updateReviewStatus(id, status) {
        $.post('routers/reviews-update.php', { id: id, status: status }, function(res){
            if (res && res.success) { location.reload(); }
            else { alert(res && res.message ? res.message : 'Failed to update'); }
        }, 'json').fail(function(){ alert('Failed to update'); });
    }

    function editReview(id) {
        const response = prompt('Enter admin response (optional):');
        if (response !== null) {
            $.post('routers/reviews-update.php', { id: id, response: response }, function(res){
                if (res && res.success) { location.reload(); }
                else { alert(res && res.message ? res.message : 'Failed to save'); }
            }, 'json').fail(function(){ alert('Failed to save'); });
        }
    }

    function deleteReview(id) {
        if (confirm('Are you sure you want to delete this review?')) {
            $.post('routers/reviews-delete.php', { id: id }, function(res){
                if (res && res.success) { location.reload(); }
                else { alert(res && res.message ? res.message : 'Failed to delete'); }
            }, 'json').fail(function(){ alert('Failed to delete'); });
        }
    }
  </script>

  <style>
    body {
      background-color: #fdf2f2;
    }
    
    .page-header {
      margin-bottom: 20px;
      padding: 15px 0;
    }
    
    .page-header h4 {
      font-size: 24px;
      font-weight: bold;
      margin: 0;
    }
    
    .filter-dropdown select {
      font-size: 14px;
      cursor: pointer;
    }
    
    .reviews-table {
      width: 100%;
      border-collapse: collapse;
      background-color: #fff;
    }
    
    .reviews-table th {
      text-align: left;
      font-weight: bold;
      border-bottom: 2px solid #f5d0d0;
    }
    
    .reviews-table td {
      border-bottom: 1px solid #f0f0f0;
    }
    
    .reviews-table tr:hover {
      background-color: #f9f9f9;
    }
    
    .rating {
      display: inline-block;
    }
    
    .rating i {
      font-size: 16px;
      margin-right: 2px;
    }
    
    .btn-edit, .btn-delete {
      cursor: pointer;
      transition: opacity 0.2s;
    }
    
    .btn-edit:hover, .btn-delete:hover {
      opacity: 0.8;
    }
    
    .card {
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
      border-radius: 8px;
    }
  </style>
<?php include 'includes/footer.php'; ?>
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
?>