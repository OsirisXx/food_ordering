<?php
include 'includes/connect.php';
include 'includes/wallet.php';

	if($_SESSION['customer_sid']==session_id())
	{
		?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="msapplication-tap-highlight" content="no">
  <title>Order Food</title>

  <!-- Favicons-->
  <!-- <link rel="icon" href="images/favicon/favicon1.png" sizes="32x32"> -->
  <!-- Favicons-->
  <!-- <link rel="apple-touch-icon-precomposed" href="images/favicon/apple-touch-icon-152x152.png"> -->
  <!-- For iPhone -->
  <!-- <meta name="msapplication-TileColor" content="#00bcd4"> -->
  <!-- <meta name="msapplication-TileImage" content="images/favicon/mstile-144x144.png"> -->
  <!-- For Windows Phone -->


  <!-- CORE CSS-->
  <link href="css/materialize.min.css" type="text/css" rel="stylesheet" media="screen,projection">
  <link href="css/style.min.css" type="text/css" rel="stylesheet" media="screen,projection">
  <!-- Custome CSS-->    
  <link href="css/custom/custom.min.css" type="text/css" rel="stylesheet" media="screen,projection">
  <!-- INCLUDED PLUGIN CSS ON THIS PAGE -->
  <link href="js/plugins/perfect-scrollbar/perfect-scrollbar.css" type="text/css" rel="stylesheet" media="screen,projection">
  <link href="js/plugins/data-tables/css/jquery.dataTables.min.css" type="text/css" rel="stylesheet" media="screen,projection">
  
   <style type="text/css">
  .input-field div.error{
    position: relative;
    top: -1rem;
    left: 0rem;
    font-size: 0.8rem;
    color:#FF4081;
    -webkit-transform: translateY(0%);
    -ms-transform: translateY(0%);
    -o-transform: translateY(0%);
    transform: translateY(0%);
  }
  .input-field label.active{
      width:100%;
  }
  .left-alert input[type=text] + label:after, 
  .left-alert input[type=password] + label:after, 
  .left-alert input[type=email] + label:after, 
  .left-alert input[type=url] + label:after, 
  .left-alert input[type=time] + label:after,
  .left-alert input[type=date] + label:after, 
  .left-alert input[type=datetime-local] + label:after, 
  .left-alert input[type=tel] + label:after, 
  .left-alert input[type=number] + label:after, 
  .left-alert input[type=search] + label:after, 
  .left-alert textarea.materialize-textarea + label:after{
      left:0px;
  }
  .right-alert input[type=text] + label:after, 
  .right-alert input[type=password] + label:after, 
  .right-alert input[type=email] + label:after, 
  .right-alert input[type=url] + label:after, 
  .right-alert input[type=time] + label:after,
  .right-alert input[type=date] + label:after, 
  .right-alert input[type=datetime-local] + label:after, 
  .right-alert input[type=tel] + label:after, 
  .right-alert input[type=number] + label:after, 
  .right-alert input[type=search] + label:after, 
  .right-alert textarea.materialize-textarea + label:after{
      right:70px;
  }

  /* Custom Food Ordering Styles */
  .page-header {
    background: linear-gradient(135deg, #CD853F 0%, #DEB887 100%);
    padding: 30px;
    border-radius: 10px;
    margin-bottom: 30px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    color: white;
  }

  .page-title {
    margin: 0 0 10px 0;
    font-size: 28px;
    font-weight: 600;
  }

  .page-subtitle {
    margin: 0;
    font-size: 16px;
    opacity: 0.9;
  }

  .food-item-card {
    height: 400px;
    border-radius: 15px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    overflow: hidden;
  }

  .food-item-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 30px rgba(0,0,0,0.15);
  }

  .food-item-image {
    height: 200px;
    object-fit: cover;
    width: 100%;
  }

  .food-item-name {
    font-size: 18px;
    font-weight: 600;
    margin: 15px 0 10px 0;
    color: #333;
  }

  .food-item-price {
    font-size: 20px;
    font-weight: 700;
    color: #CD853F;
    margin: 0;
  }

  .quantity-controls {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 15px;
    padding: 15px 0;
  }

  .quantity-btn {
    background-color: #CD853F !important;
    color: white !important;
    border-radius: 50% !important;
    width: 40px !important;
    height: 40px !important;
    line-height: 40px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    transition: all 0.3s ease !important;
  }

  .quantity-btn:hover {
    background-color: #B8860B !important;
    transform: scale(1.1);
  }

  .quantity-btn:disabled {
    background-color: #ccc !important;
    cursor: not-allowed !important;
    transform: none !important;
  }

  .quantity-display {
    font-size: 20px;
    font-weight: 600;
    color: #333;
    min-width: 30px;
    text-align: center;
    background: #f5f5f5;
    padding: 8px 12px;
    border-radius: 20px;
    border: 2px solid #CD853F;
  }

  .add-to-cart-btn {
    background-color: #4CAF50 !important;
    border-radius: 25px !important;
    padding: 0 20px !important;
    height: 40px !important;
    line-height: 40px !important;
    font-weight: 600 !important;
    transition: all 0.3s ease !important;
  }

  .add-to-cart-btn:hover {
    background-color: #45a049 !important;
    transform: translateY(-2px);
  }

  .add-to-cart-btn:disabled {
    background-color: #ccc !important;
    cursor: not-allowed !important;
    transform: none !important;
  }

  /* Cart Modal Styles */
  .modal-cart-total {
    text-align: right;
    font-size: 20px;
    padding: 20px 0;
    border-top: 2px solid #eee;
    margin-top: 20px;
    background-color: #f8f9fa;
    border-radius: 5px;
    padding: 15px;
  }

  #cart-modal .modal-content {
    padding: 30px;
  }

  #cart-modal .modal-footer {
    padding: 20px 30px;
    background-color: #f8f9fa;
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 15px;
  }

  #modal-place-order-btn {
    background-color: #26a69a !important;
    color: white !important;
    border-radius: 25px !important;
    padding: 0 30px !important;
    height: 45px !important;
    line-height: 45px !important;
    font-weight: 600 !important;
    font-size: 16px !important;
    box-shadow: 0 4px 8px rgba(0,0,0,0.2) !important;
    transition: all 0.3s ease !important;
  }

  #modal-place-order-btn:hover {
    background-color: #00897b !important;
    transform: translateY(-2px) !important;
    box-shadow: 0 6px 12px rgba(0,0,0,0.3) !important;
  }

  .cart-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 15px 0;
    border-bottom: 1px solid #eee;
  }

  .cart-item-info {
    flex-grow: 1;
  }

  .cart-item-name {
    font-weight: 600;
    color: #333;
  }

  .cart-item-price {
    color: #CD853F;
    font-weight: 500;
  }

  .cart-item-quantity {
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .cart-remove-btn {
    background-color: #f44336 !important;
    color: white !important;
    border: none;
    border-radius: 50%;
    width: 30px;
    height: 30px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  /* Cart Badge */
  .cart-count-badge {
    background-color: #f44336;
    color: white;
    border-radius: 50%;
    font-size: 11px;
    font-weight: bold;
    position: absolute;
    top: 2px;
    right: 2px;
    min-width: 18px;
    width: 18px;
    height: 18px;
    line-height: 18px;
    text-align: center;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    padding: 0;
    margin: 0;
  }

  .cart-trigger {
    position: relative;
    padding: 8px 25px !important;
    color: white !important;
    cursor: pointer !important;
    margin-top: 0px;
    margin-left: -10px;
    min-width: 60px;
    min-height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .cart-trigger:hover {
    background-color: transparent !important; /* prevent container glow */
  }
  .cart-trigger:hover i {
    color: #FFD28A !important; /* light warm highlight on icon only */
    text-shadow: 0 0 6px rgba(255,210,138,0.8);
  }

  .cart-trigger i {
    font-size: 24px;
    margin: 0;
  }

  /* Ensure proper spacing for cart in header */
  .navbar-fixed .nav-wrapper {
    padding: 0 35px;
    min-height: 70px;
  }

  /* Give more space to the right side of header */
  .navbar-fixed .nav-wrapper .right {
    padding-right: 15px;
  }

  /* Modal improvements */
  #cart-modal .modal-content h4 {
    color: #333;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid #CD853F;
  }

  /* Ensure modal is centered */
  #cart-modal.modal {
    top: 10% !important;
    max-height: 80% !important;
    overflow-y: auto !important;
  }

  /* Modal overlay positioning */
  .modal-overlay {
    position: fixed !important;
    top: 0 !important;
    left: 0 !important;
    width: 100% !important;
    height: 100% !important;
    background-color: rgba(0,0,0,0.5) !important;
    z-index: 1000 !important;
  }

  /* Responsive Design */
  @media (max-width: 768px) {
    .quantity-controls {
      flex-direction: column;
      gap: 10px;
    }
    
    .add-to-cart-btn {
      width: 100%;
    }
  }
  </style> 
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
                      <li><a href="#" class="brand-logo" style="margin-left: 20px;"><i class="mdi-restaurant-menu"></i></a></li>
                    </ul>
                    <ul class="right hide-on-med-and-down" style="margin-right: 35px; padding-top: 8px;">                        
                        <li>
                            <a href="#" class="cart-trigger" id="cart-trigger" style="padding: 8px 25px; margin-left: -10px;">
                                <i class="mdi-action-shopping-cart"></i>
                                <span class="cart-count-badge" id="cart-count">0</span>
                            </a>
                        </li>
                        <!-- <li><a href="#" class="waves-effect waves-block waves-light"><i class="mdi-editor-attach-money"><?php echo $balance;?></i></a>
                        </li> -->
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
            <li class="bold active"><a href="index.php" class="waves-effect waves-cyan"><i class="mdi-editor-border-color"></i> Order Food</a>
            </li>
                <li class="no-padding">
                    <ul class="collapsible collapsible-accordion">
                        <li class="bold"><a class="collapsible-header waves-effect waves-cyan"><i class="mdi-editor-insert-invitation"></i> Orders</a>
                            <div class="collapsible-body">
                                <ul>
								<li><a href="orders.php">All Orders</a>
                                </li>
								<?php
									$sql = mysqli_query($con, "SELECT DISTINCT status FROM orders WHERE customer_id = $user_id;");
									while($row = mysqli_fetch_array($sql)){
                                    echo '<li><a href="orders.php?status='.$row['status'].'">'.$row['status'].'</a>
                                    </li>';
									}
									?>
                                </ul>
                            </div>
                        </li>
                    </ul>
                <!-- </li>
                <li class="no-padding">
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
                    </ul> -->
                </li>					
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
        
        <!--breadcrumbs end-->


        <!--start container-->
        <div class="container">
          
          <!-- Page Header -->
          <div class="row">
            <div class="col s12">
              <div class="page-header">
                <h1 class="page-title">
                  <i class="mdi-restaurant-menu"></i>
                  Order Food
                </h1>
                <p class="page-subtitle">Choose your favorite items and add them to cart</p>
              </div>
            </div>
          </div>

          <!-- Food Items Grid -->
          <div class="row" id="food-items-grid">
            <?php
            $result = mysqli_query($con, "SELECT * FROM items where not deleted;");
            while($row = mysqli_fetch_array($result))
            {
              $item_id = $row["id"];
              $item_name = htmlspecialchars($row["name"]);
              $item_price = number_format($row["price"], 2);
              $item_image = '';
              
              // Check if image column exists and has data
              if (isset($row['image']) && !empty($row['image'])) {
                $item_image = 'data:image/jpeg;base64,'.base64_encode($row['image']);
              } else {
                $item_image = 'images/placeholder-food.jpg'; // You might want to add a placeholder image
              }
              
              echo '<div class="col s12 m6 l4">';
              echo '<div class="card food-item-card" data-item-id="'.$item_id.'">';
              echo '<div class="card-image">';
              echo '<img src="'.$item_image.'" alt="'.$item_name.'" class="food-item-image">';
              echo '</div>';
              echo '<div class="card-content">';
              echo '<h5 class="food-item-name">'.$item_name.'</h5>';
              echo '<p class="food-item-price">₱ '.$item_price.'</p>';
              echo '</div>';
              echo '<div class="card-action">';
              echo '<div class="quantity-controls">';
              echo '<button class="btn-floating btn-small waves-effect waves-light quantity-btn minus-btn" data-action="decrease" data-item-id="'.$item_id.'"><i class="mdi-content-remove"></i></button>';
              echo '<span class="quantity-display" id="quantity-'.$item_id.'">0</span>';
              echo '<button class="btn-floating btn-small waves-effect waves-light quantity-btn plus-btn" data-action="increase" data-item-id="'.$item_id.'"><i class="mdi-content-add"></i></button>';
              echo '<button class="btn waves-effect waves-light add-to-cart-btn" data-item-id="'.$item_id.'" disabled>Add to Cart</button>';
              echo '</div>';
              echo '</div>';
              echo '</div>';
              echo '</div>';
            }
            ?>
          </div>

            <div class="divider"></div>
				<div class="row">
              <div class="col s12 center-align" style="margin: 20px 0 40px;">
                <a href="#!" id="proceed-to-order-btn" class="btn btn-small btn-sm cyan waves-effect waves-light">Proceed to order</a>
				  </div>
				</div>
            
          </div>
        </div>
        <!--end container-->

      </section>
      <!-- END CONTENT -->


  </div>
  <!-- END MAIN -->

  <!-- Cart Modal -->
  <div id="cart-modal" class="modal" style="width: 80%; max-width: 600px;">
    <div class="modal-content">
      <h4><i class="mdi-action-shopping-cart"></i> Your Cart</h4>
      <div id="modal-cart-items">
        <!-- Cart items will be populated here -->
      </div>
      <div class="modal-cart-total">
        <strong>Total: ₱<span id="modal-cart-total">0.00</span></strong>
      </div>
      
      <!-- Order Notes -->
      <div class="row" style="margin-top: 20px;">
        <div class="col s12">
          <div class="input-field">
            <i class="mdi-editor-mode-edit prefix"></i>
            <textarea id="modal-description" name="description" class="materialize-textarea"></textarea>
            <label for="modal-description">Any note (optional)</label>
          </div>
        </div>
      </div>
    </div>
    <div class="modal-footer" style="text-align: center;">
      <a href="#!" class="modal-action modal-close waves-effect waves-green btn-flat" onclick="closeCartModal()">Continue Shopping</a>
      <form id="modal-checkout-form" method="post" action="place-order.php" style="display: inline;">
        <input type="hidden" name="cart_data" id="modal-cart-data">
        <input type="hidden" name="description" id="modal-description-hidden">
        <button class="btn cyan waves-effect waves-light" type="submit" name="action" id="modal-place-order-btn" style="margin: 0 10px;">
          Place Order <i class="mdi-content-send right"></i>
        </button>
      </form>
    </div>
  </div>



  <!-- //////////////////////////////////////////////////////////////////////////// -->

  <!-- START FOOTER -->
  <?php include 'includes/footer.php'; ?>
    <!-- END FOOTER -->



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
    <!-- data-tables -->
    <script type="text/javascript" src="js/plugins/data-tables/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="js/plugins/data-tables/data-tables-script.js"></script>
	
    <script type="text/javascript" src="js/plugins/jquery-validation/jquery.validate.min.js"></script>
    <script type="text/javascript" src="js/plugins/jquery-validation/additional-methods.min.js"></script>
    
    <!--plugins.js - Some Specific JS codes for Plugin Settings-->
    <script type="text/javascript" src="js/plugins.min.js"></script>
    <!--custom-script.js - Add your own theme custom JS-->
    <script type="text/javascript" src="js/custom-script.js"></script>
    <script type="text/javascript">
    // Cart functionality
    let cart = {};
    let itemPrices = {};

    // Get item prices from PHP
    <?php
    $result = mysqli_query($con, "SELECT * FROM items where not deleted;");
    while($row = mysqli_fetch_array($result))
    {
        echo "itemPrices[".$row['id']."] = ".$row['price'].";";
    }
    ?>

    // Initialize cart modal
    $(document).ready(function() {
        // Initialize modal with proper options
        $('#cart-modal').modal({
            dismissible: true,
            opacity: 0.5,
            inDuration: 300,
            outDuration: 200,
            startingTop: '10%',
            endingTop: '10%'
        });
        
        updateCartDisplay();
        
        // Debug: Log when modal is ready
        console.log('Cart modal initialized');
        
        // Test: Add a test item to cart to verify functionality
        // cart[1] = 1; // Uncomment this line to test with item ID 1
        // updateCartDisplay();
        
        // Handle window resize to keep modal centered
        $(window).on('resize', function() {
            if ($('#cart-modal').is(':visible')) {
                centerModal();
            }
        });
    });

    // Quantity control handlers
    $(document).on('click', '.quantity-btn', function() {
        const action = $(this).data('action');
        const itemId = $(this).data('item-id');
        
        console.log('Quantity button clicked:', action, itemId);
        
        if (!cart[itemId]) {
            cart[itemId] = 0;
        }
        
        if (action === 'increase') {
            cart[itemId]++;
        } else if (action === 'decrease' && cart[itemId] > 0) {
            cart[itemId]--;
        }
        
        console.log('Cart updated:', cart);
        
        updateQuantityDisplay(itemId);
        updateAddToCartButton(itemId);
        updateCartDisplay();
    });

    // Add to cart handler
    $(document).on('click', '.add-to-cart-btn', function() {
        const itemId = $(this).data('item-id');
        const quantity = cart[itemId] || 0;
        
        if (quantity > 0) {
            // Item is already in cart, just update display
            updateCartDisplay();
            Materialize.toast('Item added to cart!', 2000);
        }
    });

    // Cart modal trigger (header cart)
    $(document).on('click', '.cart-trigger', function(e) {
        e.preventDefault();
        console.log('Cart clicked');
        updateModalCart();
        
        // Try multiple methods to open modal
        try {
            $('#cart-modal').modal('open');
        } catch (error) {
            console.log('Modal open failed, trying alternative method');
            // Fallback: show modal manually with proper centering
            openCartModal();
        }
    });

    // Cart modal trigger (proceed button)
    $(document).on('click', '#proceed-to-order-btn', function(e) {
        e.preventDefault();
        updateModalCart();
        try {
            $('#cart-modal').modal('open');
        } catch (error) {
            openCartModal();
        }
    });

    // Custom modal opening function for better centering
    function openCartModal() {
        // Create overlay if it doesn't exist
        if ($('.modal-overlay').length === 0) {
            $('body').append('<div class="modal-overlay"></div>');
        }
        
        // Show modal and overlay
        $('#cart-modal').show();
        $('.modal-overlay').show();
        
        // Center the modal
        centerModal();
        
        // Add click handler to close modal when clicking overlay
        $('.modal-overlay').off('click').on('click', function() {
            closeCartModal();
        });
    }

    // Center modal function
    function centerModal() {
        const modal = $('#cart-modal');
        const windowHeight = $(window).height();
        const modalHeight = modal.outerHeight();
        
        // Center vertically
        const topPosition = Math.max(10, (windowHeight - modalHeight) / 2);
        modal.css({
            'position': 'fixed',
            'top': topPosition + 'px',
            'left': '50%',
            'transform': 'translateX(-50%)',
            'z-index': '1001'
        });
    }

    // Close modal function
    function closeCartModal() {
        $('#cart-modal').hide();
        $('.modal-overlay').hide();
    }


    // Update quantity display
    function updateQuantityDisplay(itemId) {
        const quantity = cart[itemId] || 0;
        $(`#quantity-${itemId}`).text(quantity);
        
        // Update minus button state
        const minusBtn = $(`.minus-btn[data-item-id="${itemId}"]`);
        if (quantity <= 0) {
            minusBtn.prop('disabled', true);
        } else {
            minusBtn.prop('disabled', false);
        }
    }

    // Update add to cart button
    function updateAddToCartButton(itemId) {
        const quantity = cart[itemId] || 0;
        const addBtn = $(`.add-to-cart-btn[data-item-id="${itemId}"]`);
        
        if (quantity > 0) {
            addBtn.prop('disabled', false);
            addBtn.text('Add to Cart');
        } else {
            addBtn.prop('disabled', true);
            addBtn.text('Add to Cart');
        }
    }

    // Update cart display
    function updateCartDisplay() {
        const totalItems = Object.values(cart).reduce((sum, qty) => sum + qty, 0);
        console.log('Updating cart display, total items:', totalItems);
        $('#cart-count').text(totalItems);
        
        // Show/hide cart badge
        if (totalItems > 0) {
            $('.cart-count-badge').show();
        } else {
            $('.cart-count-badge').hide();
        }
    }

    // Update modal cart
    function updateModalCart() {
        let total = 0;
        let cartHtml = '';
        
        Object.keys(cart).forEach(itemId => {
            const quantity = cart[itemId];
            if (quantity > 0) {
                const price = itemPrices[itemId] * quantity;
                total += price;
                
                const itemName = $(`.food-item-card[data-item-id="${itemId}"] .food-item-name`).text();
                
                cartHtml += `
                    <div class="cart-item">
                        <div class="cart-item-info">
                            <div class="cart-item-name">${itemName}</div>
                            <div class="cart-item-price">₱${price.toFixed(2)}</div>
                        </div>
                        <div class="cart-item-quantity">
                            <span>Qty: ${quantity}</span>
                            <button class="cart-remove-btn" onclick="removeFromCart(${itemId})">
                                <i class="mdi-content-clear"></i>
                            </button>
                        </div>
                    </div>
                `;
            }
        });
        
        if (cartHtml === '') {
            cartHtml = '<p>Your cart is empty.</p>';
        }
        
        $('#modal-cart-items').html(cartHtml);
        $('#modal-cart-total').text(total.toFixed(2));
    }

    // Remove from cart
    function removeFromCart(itemId) {
        cart[itemId] = 0;
        updateQuantityDisplay(itemId);
        updateAddToCartButton(itemId);
        updateCartDisplay();
        updateModalCart();
    }

    // Submit order from modal
    $(document).on('submit', '#modal-checkout-form', function(e) {
        e.preventDefault();
        
        // Prepare cart data for submission
        const cartData = {};
        Object.keys(cart).forEach(itemId => {
            if (cart[itemId] > 0) {
                cartData[itemId] = cart[itemId];
            }
        });
        
        if (Object.keys(cartData).length === 0) {
            Materialize.toast('Your cart is empty!', 2000);
            return;
        }
        
        // Set form data
        $('#modal-cart-data').val(JSON.stringify(cartData));
        $('#modal-description-hidden').val($('#modal-description').val());
        
        // Submit the form
        this.submit();
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
			header("location:admin-page.php");		
		}
		else{
			header("location:login.php");
		}
	}
?>