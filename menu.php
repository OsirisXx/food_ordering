<?php
session_start();
if(isset($_SESSION['message'])){
    echo "<p style='color:green;'>".$_SESSION['message']."</p>";
    unset($_SESSION['message']);
}
?>




<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>DEPRESSO COFFEE | Menu</title>
<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="css/font-awesome.min.css">
<link rel="stylesheet" type="text/css" href="css/bistro-icons.css">
<link rel="stylesheet" type="text/css" href="css/animate.min.css">
<link rel="stylesheet" type="text/css" href="css/settings.css">
<link rel="stylesheet" type="text/css" href="css/owl.carousel.css">
<link rel="stylesheet" type="text/css" href="css/owl.transitions.css">
<link rel="stylesheet" type="text/css" href="css/style.css">
<link rel="stylesheet" type="text/css" href="css/loader.css">
<link rel="shortcut icon" href="images/favicon.png">
<!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]-->
</head>

<body>

<!--Loader-->
<div class="loader"> 
   <div class="cssload-container">
     <div class="cssload-circle"></div>
     <div class="cssload-circle"></div>
   </div>
</div>

<!--Top bar-->
<div class="topbar">
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <p class="pull-left hidden-xs">DEPRESSO COFFEE</p>
        <p class="pull-right"><i class="fa fa-coffee"></i>Life Begins After Coffee</p>
      </div>
    </div>
  </div>
</div>


<!-- Navigation -->
<header id="main-navigation">
  <div id="navigation" data-spy="affix" data-offset-top="20">
    <div class="container">
      <div class="row">
        <div class="col-md-12">
          <nav class="navbar navbar-default">
            <div class="navbar-header">
              <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#fixed-collapse-navbar" aria-expanded="false"> 
                <strong class="icon-bar top-bar"></strong> 
                <strong class="icon-bar middle-bar"></strong> 
                <strong class="icon-bar bottom-bar"></strong> 
              </button>
              <a class="navbar-brand" href="index.html">
                <a class="navbar-brand" href="index.php">
  <img src="images/dep2.png" alt="Depresso Coffee Logo" class="img-responsive" style="height:80px; width:auto;">
</a>

            </div>
            
            <div id="fixed-collapse-navbar" class="navbar-collapse collapse navbar-right">
              <ul class="nav navbar-nav">
                <li><a href="index.html">Home</a></li>
                <li><a href="menu.₱">Menu</a></li>
                <li><a href="food.html">Our Food</a></li>
                <li><a href="about.html">About Us</a></li>
                <li><a href="faq.html">FAQ</a></li>
                <li><a href="./account/register.php">Order Now</a></li>
              </ul>
              
            </div>
          </nav>
        </div>
      </div>
    </div>
  </div>
</header>

<!--Page header & Title-->
<section id="page_header">
<div class="page_title">
  <div class="container">
    <div class="row">
      <div class="col-md-12">
         <h2 class="title">Menu</h2>
         <p>Duis autem vel eum iriure dolor in hendrerit in vulputate velit</p>
      </div>
    </div>
  </div>
</div>  
</section>

<section id="pricing" class="padding-top" >
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <h2 class="heading">What We Serve</h2>
        <hr class="heading_space">
      </div>
    </div>

    <div class="row">
      <!-- Left Column: Food -->
      <div class="col-sm-6">
        <h3 class="heading">Food</h3>
        <ul class="pricing_feature" style="list-style:none; padding:0;">
          <!-- Food Items -->
          <li style="margin-bottom:12px; display:flex; align-items:center; justify-content:space-between;">
            <span>Chicken Depresso <strong style="color:#ff7f00;">PHP 150.00</strong></span>
            <form method="post" action="cart.php" style="display:flex; gap:5px; margin:0;">
              <input type="number" name="quantity" value="1" min="1" style="width:45px; padding:3px; border:1px solid #ccc; border-radius:4px;">
              <input type="hidden" name="item_name" value="Chicken Depresso">
              <input type="hidden" name="item_price" value="150">
              <button type="submit" name="add_to_cart" style="padding:4px 8px; background:#ff7f00; color:#fff; border:none; border-radius:4px; cursor:pointer;">Add</button>
            </form>
          </li>

          <li style="margin-bottom:12px; display:flex; align-items:center; justify-content:space-between;">
            <span>Tacos Overload <strong style="color:#ff7f00;">₱100.00</strong></span>
            <form method="post" action="cart.php" style="display:flex; gap:5px; margin:0;">
              <input type="number" name="quantity" value="1" min="1" style="width:45px; padding:3px; border:1px solid #ccc; border-radius:4px;">
              <input type="hidden" name="item_name" value="Tacos Overload">
              <input type="hidden" name="item_price" value="100">
              <button type="submit" name="add_to_cart" style="padding:4px 8px; background:#ff7f00; color:#fff; border:none; border-radius:4px; cursor:pointer;">Add</button>
            </form>
          </li>

          <li style="margin-bottom:12px; display:flex; align-items:center; justify-content:space-between;">
            <span>Depresso Fries <strong style="color:#ff7f00;">₱100.00</strong></span>
            <form method="post" action="cart.php" style="display:flex; gap:5px; margin:0;">
              <input type="number" name="quantity" value="1" min="1" style="width:45px; padding:3px; border:1px solid #ccc; border-radius:4px;">
              <input type="hidden" name="item_name" value="Depresso Fries">
              <input type="hidden" name="item_price" value="100">
              <button type="submit" name="add_to_cart" style="padding:4px 8px; background:#ff7f00; color:#fff; border:none; border-radius:4px; cursor:pointer;">Add</button>
            </form>
          </li>

          <li style="margin-bottom:12px; display:flex; align-items:center; justify-content:space-between;">
            <span>Depresso Waffle <strong style="color:#ff7f00;">₱90.00</strong></span>
            <form method="post" action="cart.php" style="display:flex; gap:5px; margin:0;">
              <input type="number" name="quantity" value="1" min="1" style="width:45px; padding:3px; border:1px solid #ccc; border-radius:4px;">
              <input type="hidden" name="item_name" value="Depresso Waffle">
              <input type="hidden" name="item_price" value="90">
              <button type="submit" name="add_to_cart" style="padding:4px 8px; background:#ff7f00; color:#fff; border:none; border-radius:4px; cursor:pointer;">Add</button>
            </form>
          </li>

          <li style="margin-bottom:12px; display:flex; align-items:center; justify-content:space-between;">
            <span>Depresso PRC <strong style="color:#ff7f00;">₱50.00</strong></span>
            <form method="post" action="cart.php" style="display:flex; gap:5px; margin:0;">
              <input type="number" name="quantity" value="1" min="1" style="width:45px; padding:3px; border:1px solid #ccc; border-radius:4px;">
              <input type="hidden" name="item_name" value="Depresso PRC">
              <input type="hidden" name="item_price" value="50">
              <button type="submit" name="add_to_cart" style="padding:4px 8px; background:#ff7f00; color:#fff; border:none; border-radius:4px; cursor:pointer;">Add</button>
            </form>
          </li>

          <li style="margin-bottom:12px; display:flex; align-items:center; justify-content:space-between;">
            <span>Depresso Cinnamon <strong style="color:#ff7f00;">₱190.00</strong></span>
            <form method="post" action="cart.php" style="display:flex; gap:5px; margin:0;">
              <input type="number" name="quantity" value="1" min="1" style="width:45px; padding:3px; border:1px solid #ccc; border-radius:4px;">
              <input type="hidden" name="item_name" value="Depresso Cinnamon">
              <input type="hidden" name="item_price" value="190">
              <button type="submit" name="add_to_cart" style="padding:4px 8px; background:#ff7f00; color:#fff; border:none; border-radius:4px; cursor:pointer;">Add</button>
            </form>
          </li>

          <li style="margin-bottom:12px; display:flex; align-items:center; justify-content:space-between;">
            <span>Mini Cake <strong style="color:#ff7f00;">₱75.00</strong></span>
            <form method="post" action="cart.php" style="display:flex; gap:5px; margin:0;">
              <input type="number" name="quantity" value="1" min="1" style="width:45px; padding:3px; border:1px solid #ccc; border-radius:4px;">
              <input type="hidden" name="item_name" value="Mini Cake">
              <input type="hidden" name="item_price" value="75">
              <button type="submit" name="add_to_cart" style="padding:4px 8px; background:#ff7f00; color:#fff; border:none; border-radius:4px; cursor:pointer;">Add</button>
            </form>
          </li>

          <li style="margin-bottom:12px; display:flex; align-items:center; justify-content:space-between;">
            <span>Caramel Tea <strong style="color:#ff7f00;">₱89.00</strong></span>
            <form method="post" action="cart.php" style="display:flex; gap:5px; margin:0;">
              <input type="number" name="quantity" value="1" min="1" style="width:45px; padding:3px; border:1px solid #ccc; border-radius:4px;">
              <input type="hidden" name="item_name" value="Caramel Tea">
              <input type="hidden" name="item_price" value="89">
              <button type="submit" name="add_to_cart" style="padding:4px 8px; background:#ff7f00; color:#fff; border:none; border-radius:4px; cursor:pointer;">Add</button>
            </form>
          </li>

          <li style="margin-bottom:12px; display:flex; align-items:center; justify-content:space-between;">
            <span>Beef Quesadillas <strong style="color:#ff7f00;">₱160.00</strong></span>
            <form method="post" action="cart.php" style="display:flex; gap:5px; margin:0;">
              <input type="number" name="quantity" value="1" min="1" style="width:45px; padding:3px; border:1px solid #ccc; border-radius:4px;">
              <input type="hidden" name="item_name" value="Beef Quesadillas">
              <input type="hidden" name="item_price" value="160">
              <button type="submit" name="add_to_cart" style="padding:4px 8px; background:#ff7f00; color:#fff; border:none; border-radius:4px; cursor:pointer;">Add</button>
            </form>
          </li>

          <li style="margin-bottom:12px; display:flex; align-items:center; justify-content:space-between;">
            <span>Bischokollate <strong style="color:#ff7f00;">₱139.00</strong></span>
            <form method="post" action="cart.php" style="display:flex; gap:5px; margin:0;">
              <input type="number" name="quantity" value="1" min="1" style="width:45px; padding:3px; border:1px solid #ccc; border-radius:4px;">
              <input type="hidden" name="item_name" value="Bischokollate">
              <input type="hidden" name="item_price" value="139">
              <button type="submit" name="add_to_cart" style="padding:4px 8px; background:#ff7f00; color:#fff; border:none; border-radius:4px; cursor:pointer;">Add</button>
            </form>
          </li>

          <li style="margin-bottom:12px; display:flex; align-items:center; justify-content:space-between;">
            <span>Beef Burger <strong style="color:#ff7f00;">₱230.00</strong></span>
            <form method="post" action="cart.php" style="display:flex; gap:5px; margin:0;">
              <input type="number" name="quantity" value="1" min="1" style="width:45px; padding:3px; border:1px solid #ccc; border-radius:4px;">
              <input type="hidden" name="item_name" value="Beef Burger">
              <input type="hidden" name="item_price" value="230">
              <button type="submit" name="add_to_cart" style="padding:4px 8px; background:#ff7f00; color:#fff; border:none; border-radius:4px; cursor:pointer;">Add</button>
            </form>
          </li>
        </ul>
      </div>

      <!-- Right Column: Drinks -->
      <div class="col-sm-6">
        <h3 class="heading">Beverages HOT</h3>
        <ul class="pricing_feature" style="list-style:none; padding:0;">
          <!-- HOT Drinks -->
          <li style="margin-bottom:12px; display:flex; align-items:center; justify-content:space-between;">
            <span>Americano <strong style="color:#ff7f00;">₱90.00</strong></span>
            <form method="post" action="cart.php" style="display:flex; gap:5px; margin:0;">
              <input type="number" name="quantity" value="1" min="1" style="width:45px; padding:3px; border:1px solid #ccc; border-radius:4px;">
              <input type="hidden" name="item_name" value="Americano">
              <input type="hidden" name="item_price" value="90">
              <button type="submit" name="add_to_cart" style="padding:4px 8px; background:#ff7f00; color:#fff; border:none; border-radius:4px; cursor:pointer;">Add</button>
            </form>
          </li>


    <li style="margin-bottom:15px; display:flex; align-items:center; justify-content:space-between;">
      <span>Caramel Cream Frappe <strong style="color:#ff7f00;">₱160.00</strong></span>
      <form method="post" action="cart.php" style="display:flex; align-items:center; gap:5px; margin:0;">
        <input type="number" name="quantity" value="1" min="1" style="width:50px; padding:3px; border:1px solid #ccc; border-radius:4px;">
        <input type="hidden" name="item_name" value="Caramel Cream Frappe">
        <input type="hidden" name="item_price" value="160">
        <button type="submit" name="add_to_cart" style="padding:5px 10px; background:#ff7f00; color:#fff; border:none; border-radius:4px; cursor:pointer;">Add</button>
      </form>
    </li>

    <li style="margin-bottom:15px; display:flex; align-items:center; justify-content:space-between;">
      <span>Mocha Frappe <strong style="color:#ff7f00;">₱170.00</strong></span>
      <form method="post" action="cart.php" style="display:flex; align-items:center; gap:5px; margin:0;">
        <input type="number" name="quantity" value="1" min="1" style="width:50px; padding:3px; border:1px solid #ccc; border-radius:4px;">
        <input type="hidden" name="item_name" value="Mocha Frappe">
        <input type="hidden" name="item_price" value="170">
        <button type="submit" name="add_to_cart" style="padding:5px 10px; background:#ff7f00; color:#fff; border:none; border-radius:4px; cursor:pointer;">Add</button>
      </form>
    </li>

    <li style="margin-bottom:15px; display:flex; align-items:center; justify-content:space-between;">
      <span>Matcha Frappe <strong style="color:#ff7f00;">₱165.00</strong></span>
      <form method="post" action="cart.php" style="display:flex; align-items:center; gap:5px; margin:0;">
        <input type="number" name="quantity" value="1" min="1" style="width:50px; padding:3px; border:1px solid #ccc; border-radius:4px;">
        <input type="hidden" name="item_name" value="Matcha Frappe">
        <input type="hidden" name="item_price" value="165">
        <button type="submit" name="add_to_cart" style="padding:5px 10px; background:#ff7f00; color:#fff; border:none; border-radius:4px; cursor:pointer;">Add</button>
      </form>
    </li>

    <li style="margin-bottom:15px; display:flex; align-items:center; justify-content:space-between;">
      <span>Strawberry Milk <strong style="color:#ff7f00;">₱135.00</strong></span>
      <form method="post" action="cart.php" style="display:flex; align-items:center; gap:5px; margin:0;">
        <input type="number" name="quantity" value="1" min="1" style="width:50px; padding:3px; border:1px solid #ccc; border-radius:4px;">
        <input type="hidden" name="item_name" value="Strawberry Milk">
        <input type="hidden" name="item_price" value="135">
        <button type="submit" name="add_to_cart" style="padding:5px 10px; background:#ff7f00; color:#fff; border:none; border-radius:4px; cursor:pointer;">Add</button>
      </form>
    </li>

    <li style="margin-bottom:15px; display:flex; align-items:center; justify-content:space-between;">
      <span>Hot Chocolate <strong style="color:#ff7f00;">₱130.00</strong></span>
      <form method="post" action="cart.php" style="display:flex; align-items:center; gap:5px; margin:0;">
        <input type="number" name="quantity" value="1" min="1" style="width:50px; padding:3px; border:1px solid #ccc; border-radius:4px;">
        <input type="hidden" name="item_name" value="Hot Chocolate">
        <input type="hidden" name="item_price" value="130">
        <button type="submit" name="add_to_cart" style="padding:5px 10px; background:#ff7f00; color:#fff; border:none; border-radius:4px; cursor:pointer;">Add</button>
      </form>
    </li>

    <li style="margin-bottom:15px; display:flex; align-items:center; justify-content:space-between;">
      <span>Chai Latte <strong style="color:#ff7f00;">₱140.00</strong></span>
      <form method="post" action="cart.php" style="display:flex; align-items:center; gap:5px; margin:0;">
        <input type="number" name="quantity" value="1" min="1" style="width:50px; padding:3px; border:1px solid #ccc; border-radius:4px;">
        <input type="hidden" name="item_name" value="Chai Latte">
        <input type="hidden" name="item_price" value="140">
        <button type="submit" name="add_to_cart" style="padding:5px 10px; background:#ff7f00; color:#fff; border:none; border-radius:4px; cursor:pointer;">Add</button>
      </form>
    </li>

  </ul>
</div>

         

<a href="#." id="back-top"><i class="fa fa-angle-up fa-2x"></i></a>

<script src="js/jquery-2.2.3.js" type="text/javascript"></script>
<script src="js/bootstrap.min.js" type="text/javascript"></script>
<script src="js/jquery.parallax-1.1.3.js"></script>
<script src="js/jquery.appear.js"></script>  
<script src="js/jquery-countTo.js"></script>
<script src="js/owl.carousel.min.js" type="text/javascript"></script>
<script src="js/jquery.fancybox.js"></script>
<script src="js/jquery.mixitup.min.js"></script>
<script src="js/functions.js" type="text/javascript"></script> 

</body>
</html>
