<?php
session_start();
include 'includes/connect.php';
include 'includes/wallet.php';

// Check if admin is viewing specific order details
if(isset($_SESSION['admin_sid']) && $_SESSION['admin_sid']==session_id() && isset($_GET['id'])) {
    $order_id = mysqli_real_escape_string($con, $_GET['id']);
    
    // Get order details
    $order_result = mysqli_query($con, "
        SELECT o.*, u.name AS customer_name, u.contact, u.email, u.address AS customer_address
        FROM orders o
        LEFT JOIN users u ON o.customer_id = u.id
        WHERE o.id = '$order_id'
    ");
    
    if($order_result && mysqli_num_rows($order_result) > 0) {
        $order = mysqli_fetch_assoc($order_result);
        ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Order Details #<?php echo $order['id']; ?></title>
  <link href="css/materialize.min.css" rel="stylesheet">
  <link href="css/style.min.css" rel="stylesheet">
  <link href="css/custom/custom.min.css" rel="stylesheet">
  <link href="css/admin-custom.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #fdf2e9 !important; /* Light peach background */
    }
    .content-card {
      background-color: #fdf2e9 !important;
      box-shadow: none !important;
    }
    .order-items-section, .order-fee-section {
      background: #fff !important;
      border: 1px solid #e9ecef;
    }
    .order-item {
      background: #f8f9fa !important;
      border: 1px solid #e9ecef;
    }
    .order-item:hover {
      background: #e9ecef !important;
    }
    select {
      background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e") !important;
      background-position: right 8px center !important;
      background-repeat: no-repeat !important;
      background-size: 16px !important;
      padding-right: 32px !important;
    }
  </style>
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
      <h1 class="page-title"><i class="fa fa-shopping-cart"></i> Order Details #<?php echo $order['id']; ?></h1>
      <a href="all-orders.php" class="btn btn-primary-custom"><i class="fa fa-arrow-left"></i> Back to Orders</a>
    </div>
  </div>

  <div class="content-card">
    <div class="card-header">
      <h2 class="card-title">Order Information</h2>
    </div>
    <div class="card-body">
      <!-- Order Items and Fee Layout -->
      <div class="row" style="margin-top: 20px;">
        <!-- Left Column: Order Items -->
        <div class="col s12 m8">
          <div class="order-items-section" style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h4 style="margin-bottom: 20px; color: #333; font-weight: 600;">Order Items</h4>
            
            <?php
            $order_details_result = mysqli_query($con, "
                SELECT od.*, i.name as item_name, i.image, i.price AS unit_price
                FROM order_details od
                LEFT JOIN items i ON od.item_id = i.id
                WHERE od.order_id = '$order_id'
            ");
            
            while($item = mysqli_fetch_assoc($order_details_result)):
                // Prefer canonical unit price from items; fallback to stored od.price
                $unit_price = isset($item['unit_price']) && $item['unit_price'] !== null
                    ? (float)$item['unit_price']
                    : (float)$item['price'];
                $item_total = ((int)$item['quantity']) * $unit_price;
            ?>
            <div class="order-item" style="background: #f8f9fa; padding: 15px; margin-bottom: 10px; border-radius: 6px; display: flex; align-items: center;">
              <div style="width: 60px; height: 60px; margin-right: 15px; border-radius: 6px; overflow: hidden;">
                <?php 
                $item_image = '';
                if (!empty($item['image'])) {
                    $item_image = 'data:image/jpeg;base64,' . base64_encode($item['image']);
                } else {
                    $item_image = '../images/logo1.png';
                }
                ?>
                <img src="<?php echo $item_image; ?>" 
                     alt="<?php echo htmlspecialchars($item['item_name'] ?? 'Item'); ?>" 
                     style="width: 100%; height: 100%; object-fit: cover;">
              </div>
              <div style="flex: 1;">
                <h5 style="margin: 0; color: #333; font-weight: 500;"><?php echo htmlspecialchars($item['item_name'] ?? 'Unknown Item'); ?></h5>
                <p style="margin: 5px 0; color: #666;">PHP <?php echo number_format($unit_price, 2); ?> x <?php echo (int)$item['quantity']; ?></p>
              </div>
              <div style="background: #dc3545; color: white; padding: 8px 15px; border-radius: 20px; font-weight: 600;">
                PHP <?php echo number_format($item_total, 2); ?>
              </div>
            </div>
            <?php endwhile; ?>
            
            <!-- Customer Details Section -->
            <div style="margin-top: 30px;">
              <h4 style="margin-bottom: 15px; color: #333; font-weight: 600;">Customer Details</h4>
              <div style="background: #f8f9fa; padding: 15px; border-radius: 6px;">
                <p style="margin: 5px 0;"><strong>Name:</strong> <?php echo htmlspecialchars($order['customer_name'] ?? 'Unknown'); ?></p>
                <p style="margin: 5px 0;"><strong>Contact:</strong> <?php echo htmlspecialchars($order['contact'] ?? '-'); ?></p>
                <p style="margin: 5px 0;"><strong>Email:</strong> <?php echo htmlspecialchars($order['email'] ?? '-'); ?></p>
                <p style="margin: 5px 0;"><strong>Address:</strong> <?php echo htmlspecialchars($order['customer_address'] ?? $order['address'] ?? '-'); ?></p>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Right Column: Order Fee -->
        <div class="col s12 m4">
          <div class="order-fee-section" style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h4 style="margin-bottom: 20px; color: #333; font-weight: 600;">Order Fee</h4>
            
            <!-- Fee Details -->
            <div style="margin-bottom: 20px;">
              <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                <span>Subtotal:</span>
                <span>₱<?php echo number_format($order['total'], 2); ?></span>
              </div>
              <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                <span>Fee:</span>
                <span>₱0.00</span>
              </div>
              <div style="border-top: 1px solid #ddd; padding-top: 10px; margin-top: 10px;">
                <div style="display: flex; justify-content: space-between; font-weight: 600; font-size: 16px;">
                  <span>Total:</span>
                  <span>₱<?php echo number_format($order['total'], 2); ?></span>
                </div>
              </div>
            </div>
            
            <!-- Order Information -->
            <div style="margin-bottom: 20px;">
              <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 500;">Payment Mode:</label>
                <input type="text" value="<?php echo htmlspecialchars($order['payment_type'] ?? 'Takeaway'); ?>" readonly 
                       style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; background: #f8f9fa;">
              </div>
              
              <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 500;">Payment Status:</label>
                <select style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; background: white;">
                  <option value="Pending" <?php echo ($order['status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                  <option value="Paid" <?php echo ($order['status'] == 'Paid') ? 'selected' : ''; ?>>Paid</option>
                  <option value="Cancelled" <?php echo (strpos($order['status'], 'Cancelled') !== false) ? 'selected' : ''; ?>>Cancelled</option>
                </select>
              </div>
              
              <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 500;">Cancel Reason:</label>
                <input type="text" placeholder="Enter reason if cancelled" 
                       style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
              </div>
              
              <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 500;">Order Status:</label>
                <select style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; background: white;">
                  <option value="Pending" <?php echo ($order['status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                  <option value="Preparing" <?php echo ($order['status'] == 'Preparing') ? 'selected' : ''; ?>>Preparing</option>
                  <option value="Ready" <?php echo ($order['status'] == 'Ready') ? 'selected' : ''; ?>>Ready</option>
                  <option value="Delivered" <?php echo ($order['status'] == 'Delivered') ? 'selected' : ''; ?>>Delivered</option>
                  <option value="Cancelled" <?php echo (strpos($order['status'], 'Cancelled') !== false) ? 'selected' : ''; ?>>Cancelled</option>
                </select>
              </div>
              
              <button type="button" style="width: 100%; background: #dc3545; color: white; border: none; padding: 12px; border-radius: 4px; font-weight: 600; cursor: pointer;">
                Update Status
              </button>
            </div>
          </div>
        </div>
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
});
</script>
</body>
</html>
        <?php
    } else {
        // Order not found
        header("Location: all-orders.php?error=Order not found");
        exit();
    }
} else if($_SESSION['customer_sid']==session_id()) {
		?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="msapplication-tap-highlight" content="no">
  <title>Past Orders</title>

  <!-- Favicons-->
  <link rel="icon" href="images/favicon/favicon-32x32.png" sizes="32x32">
  <!-- Favicons-->
  <link rel="apple-touch-icon-precomposed" href="images/favicon/apple-touch-icon-152x152.png">
  <!-- For iPhone -->
  <meta name="msapplication-TileColor" content="#00bcd4">
  <meta name="msapplication-TileImage" content="images/favicon/mstile-144x144.png">
  <!-- For Windows Phone -->


  <!-- CORE CSS-->
  <link href="css/materialize.min.css" type="text/css" rel="stylesheet" media="screen,projection">
  <link href="css/style.min.css" type="text/css" rel="stylesheet" media="screen,projection">
  <!-- Custome CSS-->    
  <link href="css/custom/custom.min.css" type="text/css" rel="stylesheet" media="screen,projection">

  <!-- INCLUDED PLUGIN CSS ON THIS PAGE -->
  <link href="js/plugins/perfect-scrollbar/perfect-scrollbar.css" type="text/css" rel="stylesheet" media="screen,projection">
  
</head>

<body>
  <!-- Start Page Loading -->
  <div id="loader-wrapper">
      <div id="loader"></div>        
      <div class="loader-section section-left"></div>
      <div class="loader-section section-right"></div>
  </div>
  <!-- End Page Loading -->

  <!-- //////////////////////////////////////////////////////////////////////////// -->

  <!-- START HEADER -->
  <header id="header" class="page-topbar">
        <!-- start header nav-->
        <div class="navbar-fixed">
            <nav class="navbar-color">
                <div class="nav-wrapper">
                    <ul class="left">                      
                      <li><h1 class="logo-wrapper"><a href="index.php" class="brand-logo darken-1"><img src="images/logo.png" alt="logo"></a> <span class="logo-text">Logo</span></h1></li>
                    </ul> -->
                    <ul class="right hide-on-med-and-down">                        
                        <!-- <li><a href="#"  class="waves-effect waves-block waves-light"><i class="mdi-editor-attach-money"><?php echo $balance;?></i></a>
                        </li>
                    </ul>						
                </div>
            </nav>
        </div>
        <!-- end header nav-->
  </header>
  <!-- END HEADER -->

  <!-- //////////////////////////////////////////////////////////////////////////// -->

  <!-- START MAIN -->
  <div id="main">
    <!-- START WRAPPER -->
    <div class="wrapper">

      <!-- START LEFT SIDEBAR NAV-->
      <aside id="left-sidebar-nav">
        <ul id="slide-out" class="side-nav fixed leftside-navigation">
            <li class="user-details cyan darken-2">
            <div class="row">
                <div class="col col s4 m4 l4">
                    <img src="images/avatar.jpg" alt="" class="circle responsive-img valign profile-image">
                </div>
				<div class="col col s8 m8 l8">
                    <ul id="profile-dropdown" class="dropdown-content">
                        <li><a href="routers/logout.php"><i class="mdi-hardware-keyboard-tab"></i> Logout</a>
                        </li>
                    </ul>
                </div>
                <div class="col col s8 m8 l8">
                    <a class="btn-flat dropdown-button waves-effect waves-light white-text profile-btn" href="#" data-activates="profile-dropdown"><?php echo $name;?> <i class="mdi-navigation-arrow-drop-down right"></i></a>
                    <p class="user-roal"><?php echo $role;?></p>
                </div>
            </div>
            </li>
            <li class="bold"><a href="index.php" class="waves-effect waves-cyan"><i class="mdi-editor-border-color"></i> Order Food</a>
            </li>
                <li class="no-padding">
                    <ul class="collapsible collapsible-accordion">
                        <li class="bold"><a class="collapsible-header waves-effect waves-cyan active"><i class="mdi-editor-insert-invitation"></i> Orders</a>
                            <div class="collapsible-body">
                                <ul>
								<li class="<?php
								if(!isset($_GET['status'])){
										echo 'active';
									}?>
									"><a href="orders.php">All Orders</a>
                                </li>
								<?php
									$sql = mysqli_query($con, "SELECT DISTINCT status FROM orders  WHERE customer_id = $user_id;;");
									while($row = mysqli_fetch_array($sql)){
									if(isset($_GET['status'])){
										$status = $row['status'];
									}
                                    echo '<li class='.(isset($_GET['status'])?($status == $_GET['status'] ? 'active' : ''): '').'><a href="orders.php?status='.$row['status'].'">'.$row['status'].'</a>
                                    </li>';
									}
									?>
                                </ul>
                            </div>
                        </li>
                    </ul>
                </li>
                <!-- <li class="no-padding">
                    <ul class="collapsible collapsible-accordion">
                        <li class="bold"><a class="collapsible-header waves-effect waves-cyan"><i class="mdi-action-question-answer"></i> Tickets</a>
                            <div class="collapsible-body">
                                <ul>
								<li><a href="tickets.php">All Tickets</a>
                                </li>
								<?php
									$sql = mysqli_query($con, "SELECT DISTINCT status FROM tickets WHERE poster_id = $user_id AND not deleted;");
									while($row = mysqli_fetch_array($sql)){
                                    echo '<li><a href="tickets.php?status='.$row['status'].'">'.$row['status'].'</a>
                                    </li>';
									}
									?>
                                </ul>
                            </div>
                        </li>
                    </ul>
                </li>				 -->
            <li class="bold"><a href="details.php" class="waves-effect waves-cyan"><i class="mdi-social-person"></i> Edit Details</a>
            </li>				
        </ul>
        <a href="#" data-activates="slide-out" class="sidebar-collapse btn-floating btn-medium waves-effect waves-light hide-on-large-only cyan"><i class="mdi-navigation-menu"></i></a>
        </aside>
      <!-- END LEFT SIDEBAR NAV-->

      <!-- //////////////////////////////////////////////////////////////////////////// -->

      <!-- START CONTENT -->
      <section id="content">

        <!--breadcrumbs start-->
        <div id="breadcrumbs-wrapper">
          <div class="container">
            <div class="row">
              <div class="col s12 m12 l12">
                <h5 class="breadcrumbs-title">Past Orders</h5>
              </div>
            </div>
          </div>
        </div>
        <!--breadcrumbs end-->


        <!--start container-->
        <div class="container">
          <p class="caption">List of your past orders with details</p>
          <div class="divider"></div>
          <!--editableTable-->
<div id="work-collections" class="seaction">
             
					<?php
					if(isset($_GET['status'])){
						$status = $_GET['status'];
					}
					else{
						$status = '%';
					}
					$sql = mysqli_query($con, "SELECT * FROM orders WHERE customer_id = $user_id AND status LIKE '$status';;");
					echo '              <div class="row">
                <div>
                    <h4 class="header">List</h4>
                    <ul id="issues-collection" class="collection">';
					while($row = mysqli_fetch_array($sql))
					{
						$status = $row['status'];
						echo '<li class="collection-item avatar">
                              <i class="mdi-content-content-paste red circle"></i>
                              <span class="collection-header">Order No. '.$row['id'].'</span>
                              <p><strong>Date:</strong> '.$row['date'].'</p>
                              <p><strong>Payment Type:</strong> '.$row['payment_type'].'</p>
							  <p><strong>Address: </strong>'.$row['address'].'</p>							  
                              <p><strong>Status:</strong> '.($status=='Paused' ? 'Paused <a  data-position="bottom" data-delay="50" data-tooltip="Please contact administrator for further details." class="btn-floating waves-effect waves-light tooltipped cyan">    ?</a>' : $status).'</p>							  
							  '.(!empty($row['description']) ? '<p><strong>Note: </strong>'.$row['description'].'</p>' : '').'						                               
							  <a href="#" class="secondary-content"><i class="mdi-action-grade"></i></a>
                              </li>';
						$order_id = $row['id'];
						$sql1 = mysqli_query($con, "SELECT * FROM order_details WHERE order_id = $order_id;");
						while($row1 = mysqli_fetch_array($sql1))
						{
							$item_id = $row1['item_id'];
							$sql2 = mysqli_query($con, "SELECT * FROM items WHERE id = $item_id;");
							while($row2 = mysqli_fetch_array($sql2)){
								$item_name = $row2['name'];
							}
							echo '<li class="collection-item">
                            <div class="row">
                            <div class="col s7">
                            <p class="collections-title"><strong>#'.$row1['item_id'].'</strong> '.$item_name.'</p>
                            </div>
                            <div class="col s2">
                            <span>'.$row1['quantity'].' Pieces</span>
                            </div>
                            <div class="col s3">
                            <span>PHP. '.$row1['price'].'</span>
                            </div>
                            </div>
                            </li>';
							$id = $row1['order_id'];
						}
								echo'<li class="collection-item">
                                        <div class="row">
                                            <div class="col s7">
                                                <p class="collections-title"> Total</p>
                                            </div>
                                            <div class="col s2">
											<span> </span>
                                            </div>
                                            <div class="col s3">
                                                <span><strong>PHP. '.$row['total'].'</strong></span>
                                            </div>';
								if(!preg_match('/^Cancelled/', $status)){
									if($status == 'Completed'){
										// Check if review already exists for this order
										$review_check = mysqli_query($con, "SELECT id FROM reviews WHERE order_id = $id AND deleted = 0");
										if(mysqli_num_rows($review_check) > 0) {
											echo '<button class="btn waves-effect waves-light right submit" type="button" onclick="alert(\'You have already reviewed this order!\')" style="background: #6c757d;">Review Submitted
	                                              <i class="mdi-action-rate-review right"></i> 
											</button>';
										} else {
											echo '<button class="btn waves-effect waves-light right submit" type="button" onclick="openReviewModal('.$id.')">Leave Review
	                                              <i class="mdi-action-rate-review right"></i> 
											</button>';
										}
									} else if($status != 'Delivered'){
										echo '<form action="routers/cancel-order.php" method="post">
												<input type="hidden" value="'.$id.'" name="id">
												<input type="hidden" value="Cancelled by Customer" name="status">	
												<input type="hidden" value="'.$row['payment_type'].'" name="payment_type">											
												<button class="btn waves-effect waves-light right submit" type="submit" name="action">Cancel Order
	                                              <i class="mdi-content-clear right"></i> 
												</button>
												</form>';
									}
								}
								echo'</div></li>';

					}
					?>
					 </ul>
                </div>
              </div>
            </div>
        </div>
        <!--end container-->

      </section>
      <!-- END CONTENT -->
    </div>
    <!-- END WRAPPER -->

  </div>
  <!-- END MAIN -->



  <!-- //////////////////////////////////////////////////////////////////////////// -->

  <!-- START FOOTER -->
  <?php include 'includes/footer.php'; ?>
    <!-- END FOOTER -->

  <!-- Review Modal -->
  <div id="reviewModal" style="display:none; position: fixed; inset: 0; z-index: 9999; background: rgba(0,0,0,0.45);">
    <div style="max-width: 500px; width: 92%; margin: 5% auto; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
      <div style="padding: 18px 20px; background: #fdf2e9; border-bottom: 1px solid #f1e3d6; display:flex; align-items:center; justify-content:space-between;">
        <div style="display:flex; align-items:center; gap:10px;">
          <span style="display:inline-flex; width:34px; height:34px; border-radius:50%; align-items:center; justify-content:center; background:#fff3cd; color:#856404;">
            <i class="fa fa-star"></i>
          </span>
          <h3 style="margin:0; font-size: 18px; font-weight: 600; color:#333;">Leave a Review</h3>
        </div>
        <button id="closeReviewModal" style="border:none; background:transparent; font-size:18px; cursor:pointer; color:#666;">×</button>
      </div>
      <form id="reviewForm" style="padding: 20px;">
        <input type="hidden" id="reviewOrderId" name="order_id">
        <div style="margin-bottom: 20px;">
          <label style="display: block; margin-bottom: 10px; font-weight: 500; color: #333;">Rating:</label>
          <div class="star-rating" style="display: flex; gap: 8px; margin-bottom: 10px; align-items: center;">
            <span class="star" data-rating="1" style="font-size: 32px; color: #ccc; cursor: pointer; transition: color 0.2s; user-select: none; display: inline-block; width: 32px; height: 32px; text-align: center; line-height: 32px;">★</span>
            <span class="star" data-rating="2" style="font-size: 32px; color: #ccc; cursor: pointer; transition: color 0.2s; user-select: none; display: inline-block; width: 32px; height: 32px; text-align: center; line-height: 32px;">★</span>
            <span class="star" data-rating="3" style="font-size: 32px; color: #ccc; cursor: pointer; transition: color 0.2s; user-select: none; display: inline-block; width: 32px; height: 32px; text-align: center; line-height: 32px;">★</span>
            <span class="star" data-rating="4" style="font-size: 32px; color: #ccc; cursor: pointer; transition: color 0.2s; user-select: none; display: inline-block; width: 32px; height: 32px; text-align: center; line-height: 32px;">★</span>
            <span class="star" data-rating="5" style="font-size: 32px; color: #ccc; cursor: pointer; transition: color 0.2s; user-select: none; display: inline-block; width: 32px; height: 32px; text-align: center; line-height: 32px;">★</span>
            <span id="ratingText" style="margin-left: 15px; color: #666; font-size: 14px;">Click to rate</span>
          </div>
          <input type="hidden" id="ratingValue" name="rating" value="0">
        </div>
        <div style="margin-bottom: 20px;">
          <label style="display: block; margin-bottom: 10px; font-weight: 500; color: #333;">Review:</label>
          <textarea name="review_text" placeholder="Share your experience with this order..." 
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 6px; resize: vertical; min-height: 100px; font-family: inherit;" required></textarea>
        </div>
        <div style="display:flex; gap:10px; justify-content:flex-end; background:#fafafa; padding: 14px 20px; margin: 0 -20px -20px -20px; border-top:1px solid #eee;">
          <button type="button" id="cancelReview" class="btn" style="background:#6c757d; color: white; padding:10px 16px; border: none; border-radius: 4px; cursor: pointer; display: flex; align-items: center; justify-content: center; min-width: 80px;">Cancel</button>
          <button type="submit" class="btn" style="background:#28a745; color: white; padding:10px 16px; border: none; border-radius: 4px; cursor: pointer; display: flex; align-items: center; justify-content: center; min-width: 120px;">Submit Review</button>
        </div>
      </form>
    </div>
  </div>



    <!-- ================================================
    Scripts
    ================================================ -->
    
    <!-- jQuery Library -->
    <script type="text/javascript" src="js/plugins/jquery-1.11.2.min.js"></script>    
    <!--angularjs-->
    <script type="text/javascript" src="js/plugins/angular.min.js"></script>
    <!--materialize js-->
    <script type="text/javascript" src="js/materialize.min.js"></script>
    <!--scrollbar-->
    <script type="text/javascript" src="js/plugins/perfect-scrollbar/perfect-scrollbar.min.js"></script>       
    <!--plugins.js - Some Specific JS codes for Plugin Settings-->
    <script type="text/javascript" src="js/plugins.min.js"></script>
    <!--custom-script.js - Add your own theme custom JS-->
    <script type="text/javascript" src="js/custom-script.js"></script>
    
    <script>
    // Review Modal Functionality
    let currentRating = 0;
    
    function openReviewModal(orderId) {
        document.getElementById('reviewOrderId').value = orderId;
        document.getElementById('reviewModal').style.display = 'block';
        resetRating();
    }
    
    function closeReviewModal() {
        document.getElementById('reviewModal').style.display = 'none';
        document.getElementById('reviewForm').reset();
        resetRating();
    }
    
    function resetRating() {
        currentRating = 0;
        document.getElementById('ratingValue').value = 0;
        document.getElementById('ratingText').textContent = 'Click to rate';
        document.querySelectorAll('.star-rating i').forEach(star => {
            star.style.color = '#ddd';
        });
    }
    
    // Star rating functionality
    document.addEventListener('DOMContentLoaded', function() {
        const stars = document.querySelectorAll('.star-rating i');
        stars.forEach(star => {
            star.addEventListener('click', function() {
                const rating = parseInt(this.getAttribute('data-rating'));
                currentRating = rating;
                document.getElementById('ratingValue').value = rating;
                
                // Update rating text
                const ratingTexts = ['', 'Poor', 'Fair', 'Good', 'Very Good', 'Excellent'];
                document.getElementById('ratingText').textContent = ratingTexts[rating];
                
                stars.forEach((s, index) => {
                    if (index < rating) {
                        s.style.color = '#ffc107';
                    } else {
                        s.style.color = '#ddd';
                    }
                });
            });
            
            star.addEventListener('mouseenter', function() {
                const rating = parseInt(this.getAttribute('data-rating'));
                stars.forEach((s, index) => {
                    if (index < rating) {
                        s.style.color = '#ffc107';
                    } else {
                        s.style.color = '#ddd';
                    }
                });
            });
        });
        
        document.querySelector('.star-rating').addEventListener('mouseleave', function() {
            stars.forEach((s, index) => {
                if (index < currentRating) {
                    s.style.color = '#ffc107';
                } else {
                    s.style.color = '#ddd';
                }
            });
        });
        
        // Modal close events
        document.getElementById('closeReviewModal').addEventListener('click', closeReviewModal);
        document.getElementById('cancelReview').addEventListener('click', closeReviewModal);
        
        // Close modal when clicking outside
        document.getElementById('reviewModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeReviewModal();
            }
        });
        
        // Form submission
        document.getElementById('reviewForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const rating = document.getElementById('ratingValue').value;
            const reviewText = document.querySelector('textarea[name="review_text"]').value;
            
            if (rating == 0) {
                alert('Please select a rating');
                return;
            }
            
            if (!reviewText.trim()) {
                alert('Please write a review');
                return;
            }
            
            const formData = new FormData(this);
            
            fetch('routers/submit-review.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Review submitted successfully!');
                    closeReviewModal();
                    location.reload();
                } else {
                    alert(data.message || 'Failed to submit review');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to submit review');
            });
        });
    });
    </script>
</body>

</html>
<?php
	}
	else
	{
		if($_SESSION['admin_sid']==session_id())
		{
			header("location:all-orders.php");		
		}
		else{
			header("location:login.php");
		}
	}
?>