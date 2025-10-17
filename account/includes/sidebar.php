<?php
// Sidebar Navigation for Admin Panel
// Usage: Set $current_page variable before including this file
// Example: $current_page = 'overview'; include 'includes/sidebar.php';

// Get admin info from session
$name = isset($_SESSION['name']) ? $_SESSION['name'] : 'Admin';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'Administrator';
// Profile image path from session (set in profile-setting), fallback to default
$profile_image = isset($_SESSION['profile_image']) && file_exists($_SESSION['profile_image']) ? $_SESSION['profile_image'] : 'images/avatar.jpg';
?>

<aside id="left-sidebar-nav">
    <ul id="slide-out" class="side-nav fixed leftside-navigation">
        <li class="user-details" style="background: linear-gradient(45deg, #CD853F, #D2B48C);">
            <div class="row">
                <div class="col s4">
                    <img src="<?php echo htmlspecialchars($profile_image); ?>" alt="avatar" class="circle responsive-img valign profile-image" style="object-fit: cover; width: 64px; height: 64px;">
                </div>
                <div class="col s8">
                    <p style="color: white; font-weight: bold; margin: 0;">Welcome Back!</p>
                    <p style="color: white; margin: 0;"><?php echo htmlspecialchars($name); ?></p>
                    <p style="color: #fff; opacity:.8; margin:0; font-size:12px;"><?php echo htmlspecialchars($role); ?></p>
                </div>
            </div>
        </li>

        <li class="<?php echo ($current_page == 'overview') ? 'bold active' : 'bold'; ?>">
            <a href="dashboard.php" class="waves-effect">
                <i class="fa fa-tachometer"></i><span>Overview</span>
            </a>
        </li>
        
        <li class="<?php echo ($current_page == 'menu-management') ? 'bold active' : 'bold'; ?>">
            <a href="menu-management.php" class="waves-effect">
                <i class="fa fa-cutlery"></i><span>Menu Management</span>
            </a>
        </li>
        
        <li class="<?php echo ($current_page == 'orders') ? 'bold active' : 'bold'; ?>">
            <a href="orders.php" class="waves-effect">
                <i class="fa fa-shopping-cart"></i><span>Orders</span>
            </a>
        </li>
        
        <li class="<?php echo ($current_page == 'users') ? 'bold active' : 'bold'; ?>">
            <a href="users-admin.php" class="waves-effect">
                <i class="fa fa-users"></i><span>Users</span>
            </a>
        </li>
        
        <li class="<?php echo ($current_page == 'reviews') ? 'bold active' : 'bold'; ?>">
            <a href="reviews-admin.php" class="waves-effect">
                <i class="fa fa-star"></i><span>Reviews</span>
            </a>
        </li>
        
        <li class="<?php echo ($current_page == 'staffs') ? 'bold active' : 'bold'; ?>">
            <a href="staffs-admin.php" class="waves-effect">
                <i class="fa fa-user-circle"></i><span>Staffs</span>
            </a>
        </li>
        
        <li class="<?php echo ($current_page == 'profile-setting') ? 'bold active' : 'bold'; ?>">
            <a href="profile-setting.php" class="waves-effect">
                <i class="fa fa-user"></i><span>Profile Setting</span>
            </a>
        </li>
        
        <li class="<?php echo ($current_page == 'utilities') ? 'bold active' : 'bold'; ?>">
            <a href="utilities-admin.php" class="waves-effect">
                <i class="fa fa-cogs"></i><span>Utilities</span>
            </a>
        </li>
        
        <li class="bold">
            <a href="routers/logout.php" class="waves-effect">
                <i class="fa fa-sign-out"></i><span>Logout</span>
            </a>
        </li>
    </ul>
</aside>
