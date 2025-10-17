<?php
session_start();
include 'includes/connect.php';

if(isset($_SESSION['admin_sid']) && $_SESSION['admin_sid']==session_id()) {

    $name = isset($_SESSION['name']) ? $_SESSION['name'] : 'Admin';
    $role = isset($_SESSION['role']) ? $_SESSION['role'] : 'Administrator';

    $success_message = '';
    $error_message = '';
    
    // Get current tab
    $current_tab = isset($_GET['tab']) ? $_GET['tab'] : 'activity';
    $archive_tab = isset($_GET['archive']) ? $_GET['archive'] : 'orders';
    
    // Debug: Uncomment the line below to see what tab is being detected
    // echo "<!-- Current tab: " . $current_tab . " -->";

    // Handle system settings update
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_settings'])) {
        // Here you would update system settings in database
        $success_message = "System settings updated successfully!";
    }

    // Handle database backup
    if (isset($_POST['backup_database'])) {
        // Here you would create a database backup
        $success_message = "Database backup created successfully!";
    }

    // Handle cache clear
    if (isset($_POST['clear_cache'])) {
        // Here you would clear system cache
        $success_message = "System cache cleared successfully!";
    }

    // Ensure archived_pages rows exist for known pages
    mysqli_query($con, "INSERT INTO archived_pages (page_key, archived, archived_at) VALUES ('all-orders', 0, NULL) ON DUPLICATE KEY UPDATE page_key=VALUES(page_key)");
    mysqli_query($con, "INSERT INTO archived_pages (page_key, archived, archived_at) VALUES ('menu-management', 0, NULL) ON DUPLICATE KEY UPDATE page_key=VALUES(page_key)");
    mysqli_query($con, "INSERT INTO archived_pages (page_key, archived, archived_at) VALUES ('staffs-admin', 0, NULL) ON DUPLICATE KEY UPDATE page_key=VALUES(page_key)");

    // Handle page archive toggle
    if (isset($_POST['toggle_page_archive']) && isset($_POST['page_key'])) {
        $page_key = mysqli_real_escape_string($con, $_POST['page_key']);
        $desired = isset($_POST['desired']) ? (int)$_POST['desired'] : 0;
        $now = $desired ? 'NOW()' : 'NULL';
        $q = "UPDATE archived_pages SET archived = $desired, archived_at = $now WHERE page_key = '$page_key'";
        if (mysqli_query($con, $q)) {
            $action = $desired ? 'archived' : 'unarchived';
            $success_message = ucfirst(str_replace('-', ' ', $page_key)) . " has been $action.";
            // Log activity
            mysqli_query($con, "INSERT INTO activity_logs (user_role, action, date) VALUES ('Admin', '" . ucfirst($action) . " page: $page_key', NOW())");
        } else {
            $error_message = "Failed to update page archive: " . mysqli_error($con);
        }
    }

    // Bulk archive current data per page
    if (isset($_POST['bulk_archive_page']) && isset($_POST['bulk_page_key'])) {
        $bulk_page_key = mysqli_real_escape_string($con, $_POST['bulk_page_key']);
        if ($bulk_page_key === 'all-orders') {
            // Move all non-deleted orders to order_archive and mark deleted
            $ins = "INSERT INTO order_archive (order_id, customer_name, email, total, archived_at)
                    SELECT o.id, u.name, u.email, o.total, NOW()
                    FROM orders o LEFT JOIN users u ON o.customer_id = u.id
                    WHERE o.deleted = 0";
            $ok1 = mysqli_query($con, $ins);
            $ok2 = mysqli_query($con, "UPDATE orders SET deleted = 1 WHERE deleted = 0");
            if ($ok1 && $ok2) {
                $success_message = "All current orders archived.";
                mysqli_query($con, "INSERT INTO activity_logs (user_role, action, date) VALUES ('Admin', 'Bulk archived all orders', NOW())");
            } else {
                $error_message = "Failed to archive orders: " . mysqli_error($con);
            }
        } elseif ($bulk_page_key === 'menu-management') {
            // Move all non-deleted items to menu_archive and mark deleted
            $ins = "INSERT INTO menu_archive (menu_id, item_name, image, description, price, category, status, archived_at)
                    SELECT i.id, i.name, i.image, i.description, i.price,
                           (SELECT name FROM categories WHERE id=i.category_id LIMIT 1) as category,
                           i.status, NOW()
                    FROM items i WHERE i.deleted = 0";
            $ok1 = mysqli_query($con, $ins);
            $ok2 = mysqli_query($con, "UPDATE items SET deleted = 1 WHERE deleted = 0");
            if ($ok1 && $ok2) {
                $success_message = "All current menu items archived.";
                mysqli_query($con, "INSERT INTO activity_logs (user_role, action, date) VALUES ('Admin', 'Bulk archived all menu items', NOW())");
            } else {
                $error_message = "Failed to archive menu items: " . mysqli_error($con);
            }
        } elseif ($bulk_page_key === 'staffs-admin') {
            // Ensure staff table exists (align with staffs-admin.php)
            mysqli_query($con, "CREATE TABLE IF NOT EXISTS staff (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                email VARCHAR(120) NOT NULL,
                contact VARCHAR(50) NOT NULL,
                role VARCHAR(40) NOT NULL,
                status ENUM('active','inactive') NOT NULL DEFAULT 'active',
                hire_date DATE DEFAULT NULL,
                deleted TINYINT(1) NOT NULL DEFAULT 0
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
            // Move all non-deleted staff to staff_archive and mark deleted
            $ins = "INSERT INTO staff_archive (staff_id, name, email, contact, role, status, archived_at)
                    SELECT s.id, s.name, s.email, s.contact, s.role, s.status, NOW()
                    FROM staff s WHERE s.deleted = 0";
            $ok1 = mysqli_query($con, $ins);
            $ok2 = mysqli_query($con, "UPDATE staff SET deleted = 1 WHERE deleted = 0");
            if ($ok1 && $ok2) {
                $success_message = "All current staff archived.";
                mysqli_query($con, "INSERT INTO activity_logs (user_role, action, date) VALUES ('Admin', 'Bulk archived all staff', NOW())");
            } else {
                $error_message = "Failed to archive staff: " . mysqli_error($con);
            }
        } else {
            $error_message = "Unknown page for bulk archive.";
        }
    }
    
    // Handle restore order
    if (isset($_POST['restore_order'])) {
        $order_id = $_POST['order_id'];
        
        // Get archived order data
        $get_archived_order = "SELECT * FROM order_archive WHERE order_id = '$order_id'";
        $archived_result = mysqli_query($con, $get_archived_order);
        
        if (mysqli_num_rows($archived_result) > 0) {
            $archived_order = mysqli_fetch_assoc($archived_result);
            
            // Get customer_id from users table using email
            $customer_query = "SELECT id FROM users WHERE email = '{$archived_order['email']}'";
            $customer_result = mysqli_query($con, $customer_query);
            
            if (mysqli_num_rows($customer_result) > 0) {
                $customer = mysqli_fetch_assoc($customer_result);
                $customer_id = $customer['id'];
                
                // Insert order back into orders table (let auto-increment handle the ID)
                $restore_query = "INSERT INTO orders (customer_id, address, description, date, payment_type, total, status, deleted) 
                                 VALUES ('$customer_id', 'Restored from Archive', 'Restored from Archive', NOW(), 'Wallet', '{$archived_order['total']}', 'Yet to be delivered', 0)";
                
                if (mysqli_query($con, $restore_query)) {
                    // Remove from archive
                    $delete_archive = "DELETE FROM order_archive WHERE order_id = '$order_id'";
                    mysqli_query($con, $delete_archive);
                    
                    // Log activity
                    $log_query = "INSERT INTO activity_logs (user_role, action, date) VALUES ('Admin', 'Restored order ID: $order_id from archive', NOW())";
                    mysqli_query($con, $log_query);
                    
                    $success_message = "Order restored successfully!";
                } else {
                    $error_message = "Error restoring order: " . mysqli_error($con);
                }
            } else {
                $error_message = "Customer not found for this archived order.";
            }
        } else {
            $error_message = "Archived order not found.";
        }
    }
    
    // Handle restore menu item
    if (isset($_POST['restore_menu'])) {
        $menu_id = $_POST['menu_id'];
        
        // Get archived menu data
        $get_archived_menu = "SELECT * FROM menu_archive WHERE menu_id = '$menu_id'";
        $archived_result = mysqli_query($con, $get_archived_menu);
        
        if (mysqli_num_rows($archived_result) > 0) {
            $archived_menu = mysqli_fetch_assoc($archived_result);
            
            // Get category_id from categories table using category name
            $category_query = "SELECT id FROM categories WHERE name = '{$archived_menu['category']}' LIMIT 1";
            $category_result = mysqli_query($con, $category_query);
            $category_id = mysqli_num_rows($category_result) > 0 ? mysqli_fetch_assoc($category_result)['id'] : NULL;
            
            // Insert menu item back into items table using prepared statement for safe BLOB handling
            $stmt = mysqli_prepare($con, "INSERT INTO items (name, description, price, status, category_id, image, deleted) VALUES (?, ?, ?, ?, ?, ?, 0)");
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "ssdsib", 
                    $archived_menu['item_name'], 
                    $archived_menu['description'], 
                    $archived_menu['price'], 
                    $archived_menu['status'], 
                    $category_id, 
                    $archived_menu['image']
                );
                
                if (mysqli_stmt_execute($stmt)) {
                    // Remove from archive
                    $delete_archive = "DELETE FROM menu_archive WHERE menu_id = '$menu_id'";
                    mysqli_query($con, $delete_archive);
                    
                    // Log activity
                    $log_query = "INSERT INTO activity_logs (user_role, action, date) VALUES ('Admin', 'Restored menu item: {$archived_menu['item_name']} from archive', NOW())";
                    mysqli_query($con, $log_query);
                    
                    $success_message = "Menu item restored successfully!";
                } else {
                    $error_message = "Error restoring menu item: " . mysqli_stmt_error($stmt);
                }
                mysqli_stmt_close($stmt);
            } else {
                $error_message = "Error preparing statement: " . mysqli_error($con);
            }
        } else {
            $error_message = "Archived menu item not found.";
        }
    }
    
    // Handle delete from archive
    if (isset($_POST['delete_archive'])) {
        $type = $_POST['type'];
        $id = $_POST['id'];
        
        if ($type == 'order') {
            // Get order info for logging
            $get_order_info = "SELECT order_id FROM order_archive WHERE id = '$id'";
            $order_info_result = mysqli_query($con, $get_order_info);
            
            if (mysqli_num_rows($order_info_result) > 0) {
                $order_info = mysqli_fetch_assoc($order_info_result);
                $order_id = $order_info['order_id'];
                
                // Delete from archive
                $delete_query = "DELETE FROM order_archive WHERE id = '$id'";
                
                if (mysqli_query($con, $delete_query)) {
                    // Log activity
                    $log_query = "INSERT INTO activity_logs (user_role, action, date) VALUES ('Admin', 'Permanently deleted order ID: $order_id from archive', NOW())";
                    mysqli_query($con, $log_query);
                    
                    $success_message = "Order permanently deleted from archive!";
                } else {
                    $error_message = "Error deleting order: " . mysqli_error($con);
                }
            } else {
                $error_message = "Order not found in archive.";
            }
        } elseif ($type == 'menu') {
            // Get menu info for logging
            $get_menu_info = "SELECT item_name FROM menu_archive WHERE id = '$id'";
            $menu_info_result = mysqli_query($con, $get_menu_info);
            
            if (mysqli_num_rows($menu_info_result) > 0) {
                $menu_info = mysqli_fetch_assoc($menu_info_result);
                $item_name = $menu_info['item_name'];
                
                // Delete from archive
                $delete_query = "DELETE FROM menu_archive WHERE id = '$id'";
                
                if (mysqli_query($con, $delete_query)) {
                    // Log activity
                    $log_query = "INSERT INTO activity_logs (user_role, action, date) VALUES ('Admin', 'Permanently deleted menu item: $item_name from archive', NOW())";
                    mysqli_query($con, $log_query);
                    
                    $success_message = "Menu item permanently deleted from archive!";
                } else {
                    $error_message = "Error deleting menu item: " . mysqli_error($con);
                }
            } else {
                $error_message = "Menu item not found in archive.";
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Utilities - Admin Panel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link href="css/materialize.min.css" rel="stylesheet">
    <link href="css/style.min.css" rel="stylesheet">
    <link href="css/custom/custom.min.css" rel="stylesheet">
    <link href="css/admin-custom.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
      .tabs {
        background-color: #f5f5f5;
        border-radius: 5px;
        margin-bottom: 20px;
      }
      .tabs .tab a {
        color: #d84315;
        font-weight: 500;
      }
      .tabs .tab a.active {
        background-color: #d84315;
        color: white;
        border-radius: 5px;
      }
      .tabs .tab a:hover {
        background-color: rgba(216, 67, 21, 0.1);
      }
      .tabs .indicator {
        background-color: #d84315;
      }
      /* Inner archive tabs: underline active only */
      #archive-tabs {
        background: transparent;
        border-radius: 0;
        margin-bottom: 12px;
      }
      #archive-tabs .tab a {
        background: transparent !important;
        color: #d84315;
        border-bottom: 3px solid transparent;
      }
      #archive-tabs .tab a.active {
        background: transparent !important;
        color: #d84315 !important;
        border-bottom-color: #d84315;
      }
      .btn-small {
        padding: 0 8px;
        margin: 2px;
        font-size: 11px;
      }
      .card-title {
        font-size: 1.2rem;
        font-weight: 600;
      }
      .responsive-table th {
        background-color: #ffebee;
        font-weight: 600;
        color: #d84315;
      }
      .responsive-table tbody tr:nth-child(even) {
        background-color: #fafafa;
      }
      .responsive-table tbody tr:hover {
        background-color: #ffebee;
      }
      .header {
        color: #d84315;
        font-weight: 700;
      }
      .btn.red {
        background-color: #d84315;
      }
      .btn.red:hover {
        background-color: #bf360c;
      }
      
      /* Modal fallback styling */
      .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.5);
      }
      .modal-content {
        background-color: #fefefe;
        margin: 15% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 80%;
        max-width: 500px;
        border-radius: 5px;
      }
      .modal-footer {
        text-align: right;
        margin-top: 20px;
      }
      .btn.blue {
        background-color: #CD853F;
      }
      .btn.orange {
        background-color: #f57c00;
      }
      .action-buttons {
        display: flex;
        flex-direction: column;
        gap: 2px;
      }
      .action-buttons form {
        margin: 0;
      }
      .center {
        text-align: center;
      }
    </style>
</head>
<body>

  <!-- MAIN -->
  <div id="main-wrapper">
    <div class="left-sidebar">
      <div class="scroll-sidebar">
        <?php 
        $current_page = 'utilities';
        include 'includes/sidebar.php'; 
        ?>
      </div>
    </div>

    <div class="page-wrapper">
      <div class="container-fluid">
        <div class="page-header">
          <h1 class="page-title">
            <i class="fa fa-cogs"></i>
            Admin Utilities
          </h1>
        </div>

        <div class="content-card">
          <div class="row">
            <div class="col s12">
              
              <?php if ($success_message): ?>
                <div class="card-panel green lighten-2">
                  <span class="white-text"><?php echo $success_message; ?></span>
                </div>
              <?php endif; ?>

              <?php if ($error_message): ?>
                <div class="card-panel red lighten-2">
                  <span class="white-text"><?php echo $error_message; ?></span>
                </div>
              <?php endif; ?>
              
              <!-- Utility Tabs -->
              <div class="row">
                <div class="col s12">
                  <ul class="tabs">
                    <li class="tab col s4">
                      <a href="utilities-admin.php?tab=activity">
                        Activity Log
                      </a>
                    </li>
                    <li class="tab col s4">
                      <a href="utilities-admin.php?tab=archive">
                        Archive
                      </a>
                    </li>
                    <li class="tab col s4">
                      <a href="utilities-admin.php?tab=backup">
                        Backup
                      </a>
                    </li>
                  </ul>
                </div>
              </div>

              <!-- Tab Content -->
              <!-- Activity Log Tab -->
              <div id="activity-log" class="col s12" style="display: none;">
                  <h5>Activity Log</h5>
                  
                  <?php
                  // Fetch activity logs
                  $activity_query = "SELECT * FROM activity_logs ORDER BY date DESC LIMIT 20";
                  $activity_result = mysqli_query($con, $activity_query);
                  ?>
                  
                  <div class="card">
                    <div class="card-content">
                      <table class="striped responsive-table">
                        <thead>
                          <tr>
                            <th>ID</th>
                            <th>User Role</th>
                            <th>Action</th>
                            <th>Date</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php 
                          if (mysqli_num_rows($activity_result) > 0) {
                            while ($log = mysqli_fetch_assoc($activity_result)): 
                          ?>
                            <tr>
                              <td><?php echo $log['id']; ?></td>
                              <td><?php echo $log['user_role']; ?></td>
                              <td><?php echo $log['action']; ?></td>
                              <td><?php echo $log['date']; ?></td>
                            </tr>
                          <?php 
                            endwhile;
                          } else {
                            echo "<tr><td colspan='4' class='center'>No activity logs found</td></tr>";
                          }
                          ?>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
                
              <!-- Archive Tab -->
              <div id="archive" class="col s12" style="display: none;">
                  <h5>Archive</h5>

                  <!-- Top control: bulk archive based on current tab -->
                  <div class="card">
                    <div class="card-content">
                      <form method="POST" style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
                        <input type="hidden" name="bulk_page_key" value="<?php echo $archive_tab == 'menu' ? 'menu-management' : 'all-orders'; ?>">
                        <button type="submit" name="bulk_archive_page" class="btn red btn-small" onclick="return confirmBulkArchive('<?php echo $archive_tab == 'menu' ? 'Menu Management' : 'Orders'; ?>');">Archive Page</button>
                        <span style="color:#777;font-size:12px;">Moves current page data into its archive and keeps them listed below.</span>
                      </form>
                    </div>
                  </div>

                  <!-- Internal Archive buttons -->
                  <div class="row">
                    <div class="col s12">
                      <ul class="tabs" id="archive-tabs">
                        <li class="tab col s6">
                          <a href="utilities-admin.php?tab=archive&archive=orders" class="<?php echo $archive_tab == 'orders' ? 'active' : ''; ?>">Order Archives</a>
                        </li>
                        <li class="tab col s6">
                          <a href="utilities-admin.php?tab=archive&archive=menu" class="<?php echo $archive_tab == 'menu' ? 'active' : ''; ?>">Menu Archives</a>
                        </li>
                      </ul>
                    </div>
                  </div>

                  <?php if ($archive_tab == 'orders'): ?>
                    <!-- Order Archive Table -->
                    <div id="order-archive" class="col s12">
                      <?php
                      $order_archive_query = "SELECT * FROM order_archive ORDER BY archived_at DESC";
                      $order_archive_result = mysqli_query($con, $order_archive_query);
                      ?>
                      <div class="card">
                        <div class="card-content">
                          <h6>Order Archive</h6>
                          <table class="striped responsive-table">
                            <thead>
                              <tr>
                                <th>Order ID</th>
                                <th>Customer Name</th>
                                <th>Email</th>
                                <th>Total</th>
                                <th>View Details</th>
                                <th>Archived At</th>
                                <th>Actions</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php 
                              if ($order_archive_result && mysqli_num_rows($order_archive_result) > 0) {
                                while ($order = mysqli_fetch_assoc($order_archive_result)): 
                              ?>
                                <tr>
                                  <td><?php echo $order['order_id']; ?></td>
                                  <td><?php echo !empty($order['customer_name']) ? $order['customer_name'] : ''; ?></td>
                                  <td><?php echo $order['email']; ?></td>
                                  <td>P<?php echo number_format($order['total'], 2); ?></td>
                                  <td>
                                    <button class="btn red btn-small modal-trigger" data-target="order-details-modal" onclick="showOrderDetails(<?php echo $order['order_id']; ?>, '<?php echo htmlspecialchars($order['customer_name']); ?>', '<?php echo htmlspecialchars($order['email']); ?>', <?php echo $order['total']; ?>, '<?php echo $order['archived_at']; ?>')">View Details</button>
                                  </td>
                                  <td><?php echo $order['archived_at']; ?></td>
                                  <td>
                                    <div class="action-buttons">
                                      <form method="POST" style="display: inline-block;">
                                        <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                        <button type="submit" name="restore_order" class="btn red btn-small">Restore</button>
                                      </form>
                                      <form method="POST" style="display: inline-block;" onsubmit="return confirmDelete('order', <?php echo $order['order_id']; ?>)">
                                        <input type="hidden" name="type" value="order">
                                        <input type="hidden" name="id" value="<?php echo $order['id']; ?>">
                                        <button type="submit" name="delete_archive" class="btn red btn-small">Delete</button>
                                      </form>
                                    </div>
                                  </td>
                                </tr>
                              <?php 
                                endwhile;
                              } else {
                                echo "<tr><td colspan='7' class='center'>No archived orders found</td></tr>";
                              }
                              ?>
                            </tbody>
                          </table>
                        </div>
                      </div>
                    </div>
                  <?php else: ?>
                    <!-- Menu Archive Table -->
                    <div id="menu-archive" class="col s12">
                      <?php
                      $menu_archive_query = "SELECT * FROM menu_archive ORDER BY archived_at DESC";
                      $menu_archive_result = mysqli_query($con, $menu_archive_query);
                      ?>
                      <div class="card">
                        <div class="card-content">
                          <h6>Menu Archive</h6>
                          <table class="striped responsive-table">
                            <thead>
                              <tr>
                                <th>Menu ID</th>
                                <th>Item Name</th>
                                <th>Image</th>
                                <th>Price</th>
                                <th>Category</th>
                                <th>Status</th>
                                <th>Archived At</th>
                                <th>Actions</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php 
                              if ($menu_archive_result && mysqli_num_rows($menu_archive_result) > 0) {
                                while ($menu = mysqli_fetch_assoc($menu_archive_result)): 
                              ?>
                                <tr>
                                  <td><?php echo $menu['menu_id']; ?></td>
                                  <td><?php echo $menu['item_name']; ?></td>
                                  <td><i class="fa fa-image"></i> Image</td>
                                  <td>P<?php echo number_format($menu['price'], 2); ?></td>
                                  <td><?php echo $menu['category']; ?></td>
                                  <td><?php echo $menu['status']; ?></td>
                                  <td><?php echo $menu['archived_at']; ?></td>
                                  <td>
                                    <div class="action-buttons">
                                      <form method="POST" style="display: inline-block;">
                                        <input type="hidden" name="menu_id" value="<?php echo $menu['menu_id']; ?>">
                                        <button type="submit" name="restore_menu" class="btn red btn-small">Restore</button>
                                      </form>
                                      <form method="POST" style="display: inline-block;" onsubmit="return confirmDelete('menu', '<?php echo htmlspecialchars($menu['item_name']); ?>')">
                                        <input type="hidden" name="type" value="menu">
                                        <input type="hidden" name="id" value="<?php echo $menu['id']; ?>">
                                        <button type="submit" name="delete_archive" class="btn red btn-small">Delete</button>
                                      </form>
                                    </div>
                                  </td>
                                </tr>
                              <?php 
                                endwhile;
                              } else {
                                echo "<tr><td colspan='8' class='center'>No archived menu items found</td></tr>";
                              }
                              ?>
                            </tbody>
                          </table>
                        </div>
                      </div>
                    </div>
                  <?php endif; ?>
                </div>
                
              <!-- Backup Tab -->
              <div id="backup" class="col s12" style="display: none;">
                  <h5>Backup</h5>
                  
                  <div class="row">
                    <div class="col s12 m6">
                      <div class="card">
                        <div class="card-content">
                          <span class="card-title">
                            <i class="fa fa-database"></i> Database Backup
                          </span>
                          <p>Create a backup of your database to ensure data safety.</p>
                          
                          <button id="btnBackupNow" class="btn waves-effect waves-light" style="background-color: #CD853F;">
                            <i class="fa fa-download"></i> Backup Database
                          </button>
                        </div>
                      </div>
                    </div>
                    
                    <div class="col s12 m6">
                      <div class="card">
                        <div class="card-content">
                          <span class="card-title">
                            <i class="fa fa-broom"></i> System Maintenance
                          </span>
                          <p>Clear system cache and optimize performance.</p>
                          
                          <form method="POST" style="display: inline;">
                            <button class="btn orange waves-effect waves-light" type="submit" name="clear_cache">
                              <i class="fa fa-trash"></i> Clear Cache
                            </button>
                          </form>
                        </div>
                      </div>
                    </div>
                  </div>
                  
                  <!-- Backups List -->
                  <div class="row">
                    <div class="col s12">
                      <div class="card">
                        <div class="card-content">
                          <span class="card-title"><i class="fa fa-list"></i> Existing Backups</span>
                          <div id="backupsTableWrapper">
                            <?php
                              $backups_dir = __DIR__ . '/backups';
                              if (!is_dir($backups_dir)) { @mkdir($backups_dir, 0755, true); }
                              $files = [];
                              if (is_dir($backups_dir)) {
                                foreach (scandir($backups_dir) as $f) {
                                  if ($f === '.' || $f === '..') continue;
                                  if (preg_match('/^backup_\d{8}_\d{6}\.sql$/', $f)) {
                                    $files[] = $f;
                                  }
                                }
                              }
                              sort($files);
                              $files = array_reverse($files);
                            ?>
                            <table class="striped responsive-table">
                              <thead>
                                <tr>
                                  <th>File</th>
                                  <th>Size</th>
                                  <th>Created</th>
                                  <th>Actions</th>
                                </tr>
                              </thead>
                              <tbody id="backupsTbody">
                                <?php if (!empty($files)): foreach ($files as $f): 
                                  $fp = $backups_dir . DIRECTORY_SEPARATOR . $f;
                                  $size = @filesize($fp);
                                  $mtime = @filemtime($fp);
                                ?>
                                  <tr>
                                    <td><?php echo htmlspecialchars($f); ?></td>
                                    <td><?php echo $size !== false ? number_format($size/1024, 2) . ' KB' : '-'; ?></td>
                                    <td><?php echo $mtime ? date('Y-m-d H:i:s', $mtime) : '-'; ?></td>
                                    <td>
                                      <a class="btn btn-small" href="backups/<?php echo rawurlencode($f); ?>" download>Download</a>
                                      <button class="btn btn-small" data-delete-backup="<?php echo htmlspecialchars($f); ?>" style="background:#dc3545;">Delete</button>
                                    </td>
                                  </tr>
                                <?php endforeach; else: ?>
                                  <tr><td colspan="4" class="center">No backups yet</td></tr>
                                <?php endif; ?>
                              </tbody>
                            </table>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- System Information -->
                  <div class="row">
                    <div class="col s12">
                      <div class="card">
                        <div class="card-content">
                          <span class="card-title">
                            <i class="fa fa-info-circle"></i> System Information
                          </span>
                          
                          <div class="row">
                            <div class="col s12 m4">
                              <h6>PHP Version</h6>
                              <p><?php echo phpversion(); ?></p>
                            </div>
                            <div class="col s12 m4">
                              <h6>Database</h6>
                              <p>MySQL <?php echo mysqli_get_server_info($con); ?></p>
                            </div>
                            <div class="col s12 m4">
                              <h6>Server</h6>
                              <p><?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'; ?></p>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Order Details Modal -->
  <div id="order-details-modal" class="modal">
    <div class="modal-content">
      <h4>Archived Order Details</h4>
      <div class="row">
        <div class="col s12">
          <table class="striped">
            <tbody>
              <tr>
                <td><strong>Order ID:</strong></td>
                <td id="modal-order-id"></td>
              </tr>
              <tr>
                <td><strong>Customer Name:</strong></td>
                <td id="modal-customer-name"></td>
              </tr>
              <tr>
                <td><strong>Email:</strong></td>
                <td id="modal-email"></td>
              </tr>
              <tr>
                <td><strong>Total:</strong></td>
                <td id="modal-total"></td>
              </tr>
              <tr>
                <td><strong>Archived At:</strong></td>
                <td id="modal-archived-at"></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <a href="#!" class="modal-close waves-effect waves-green btn-flat">Close</a>
    </div>
  </div>

  <script src="js/plugins/jquery-1.11.2.min.js"></script>
  <script src="js/materialize.min.js"></script>
  <script>
    $(document).ready(function(){
        // Initialize Materialize components safely
        try {
            if (typeof $.fn.collapsible === 'function') {
                $('.collapsible').collapsible();
            }
            if (typeof $.fn.dropdown === 'function') {
                $('.dropdown-button').dropdown();
            }
            if (typeof $.fn.modal === 'function') {
                $('.modal').modal();
            }
        } catch(e) {
            console.log('Materialize components not available:', e);
        }
        
        // Handle tab switching
        function switchTab(tabName) {
            console.log('Switching to tab:', tabName);
            
            // Hide all tab content
            $('#activity-log, #archive, #backup').hide();
            
            // Remove active class from all tabs
            $('.tabs a').removeClass('active');
            
            // Show the selected tab content and add active class
            if (tabName === 'activity') {
                $('#activity-log').show();
                $('a[href="?tab=activity"]').addClass('active');
                console.log('Showing activity log');
            } else if (tabName === 'archive') {
                $('#archive').show();
                $('a[href="?tab=archive"]').addClass('active');
                console.log('Showing archive');
            } else if (tabName === 'backup') {
                $('#backup').show();
                $('a[href="?tab=backup"]').addClass('active');
                console.log('Showing backup');
            }
        }
        
        // Handle initial tab display based on URL parameter
        var urlParams = new URLSearchParams(window.location.search);
        var tab = urlParams.get('tab');
        
        console.log('Initial tab parameter:', tab);
        
        if (tab) {
            switchTab(tab);
        } else {
            // Default to activity tab
            switchTab('activity');
        }
        
        // Handle top-level tab clicks only (exclude inner archive tabs)
        $('.tabs:not(#archive-tabs) a').click(function(e) {
            var href = $(this).attr('href');
            if (!href) return;            
            // Allow full navigation for inner archive sub-tabs so PHP can render correct table
            if (href.indexOf('&archive=') !== -1 && href.indexOf('?tab=archive') !== -1) {
                return; // do not preventDefault; let the page reload to update server-rendered content
            }
            e.preventDefault();
            // Single-page switching for top-level tabs
            try {
                var url = new URL(href, window.location.href);
                var tabName = url.searchParams.get('tab') || 'activity';
                switchTab(tabName);
                // Keep current path; only swap query string
                var newUrl = window.location.pathname + url.search;
                history.pushState(null, '', newUrl);
            } catch(err) {
                var match = href.match(/\?tab=([^&]+)/);
                var tabName2 = match ? match[1] : 'activity';
                switchTab(tabName2.indexOf('archive') === 0 ? 'archive' : tabName2);
                var q = href.charAt(0) === '?' ? href : ('?' + href);
                history.pushState(null, '', window.location.pathname + q);
            }
        });

        // Force full navigation for inner archive buttons so PHP swaps the table
        $('#archive-tabs a').on('click', function(e){
            var href = $(this).attr('href');
            if (!href) { return; }
            e.preventDefault();
            e.stopPropagation();
            window.location.href = href;
        });
        
        // Handle modal close button
        $('.modal-close').click(function(e) {
            e.preventDefault();
            $('.modal').hide();
        });

        // Backup actions
        $('#btnBackupNow').on('click', function(){
            var $btn = $(this);
            $btn.prop('disabled', true).text('Backing up...');
            $.post('routers/backup-database.php', {}, function(res){
                if (res && res.success) {
                    alert(res.message || 'Backup created');
                    // Prepend the new backup row
                    var now = new Date();
                    var row = '<tr>'+
                        '<td>' + res.file + '</td>'+
                        '<td>-</td>'+
                        '<td>' + now.getFullYear() + '-' + String(now.getMonth()+1).padStart(2,'0') + '-' + String(now.getDate()).padStart(2,'0') + ' ' + String(now.getHours()).padStart(2,'0') + ':' + String(now.getMinutes()).padStart(2,'0') + ':' + String(now.getSeconds()).padStart(2,'0') + '</td>'+
                        '<td><a class="btn btn-small" href="backups/' + encodeURIComponent(res.file) + '" download>Download</a> '+
                        '<button class="btn btn-small" data-delete-backup="' + res.file + '" style="background:#dc3545;">Delete</button></td>'+
                        '</tr>';
                    var $tb = $('#backupsTbody');
                    if ($tb.find('tr td').first().text() === 'No backups yet') { $tb.empty(); }
                    $tb.prepend(row);
                } else {
                    alert((res && res.message) ? res.message : 'Backup failed');
                }
            }, 'json').fail(function(xhr){
                alert('Backup failed');
            }).always(function(){
                $btn.prop('disabled', false).html('<i class="fa fa-download"></i> Backup Database');
            });
        });

        $(document).on('click', '[data-delete-backup]', function(){
            var name = $(this).data('delete-backup');
            if (!confirm('Delete backup ' + name + '? This cannot be undone.')) return;
            $.post('routers/delete-backup.php', { name: name }, function(res){
                if (res && res.success) {
                    // Remove row
                    var $row = $('[data-delete-backup="' + name.replace(/([.*+?^${}()|[\]\\])/g,'\\$1') + '"]').closest('tr');
                    $row.remove();
                    if ($('#backupsTbody tr').length === 0) {
                        $('#backupsTbody').html('<tr><td colspan="4" class="center">No backups yet</td></tr>');
                    }
                } else {
                    alert((res && res.message) ? res.message : 'Delete failed');
                }
            }, 'json').fail(function(){
                alert('Delete failed');
            });
        });
    });
    
    function showOrderDetails(orderId, customerName, email, total, archivedAt) {
        document.getElementById('modal-order-id').textContent = orderId;
        document.getElementById('modal-customer-name').textContent = customerName || 'N/A';
        document.getElementById('modal-email').textContent = email || 'N/A';
        document.getElementById('modal-total').textContent = 'P' + parseFloat(total).toFixed(2);
        document.getElementById('modal-archived-at').textContent = archivedAt;
        
        // Open the modal
        try {
            if (typeof $.fn.modal === 'function') {
                $('#order-details-modal').modal('open');
            } else {
                // Fallback: show modal manually
                $('#order-details-modal').show();
            }
        } catch(e) {
            // Fallback: show modal manually
            $('#order-details-modal').show();
        }
    }
    
    function confirmDelete(type, id) {
        var message = type === 'order' ? 
            'Are you sure you want to permanently delete order ID ' + id + ' from the archive? This action cannot be undone.' :
            'Are you sure you want to permanently delete this menu item from the archive? This action cannot be undone.';
        
        return confirm(message);
    }

    // Confirm irreversible bulk archive
    function confirmBulkArchive(pageLabel) {
        var msg = 'Archive current data for ' + pageLabel + '?\n\nThis will move all current records into the archive and hide them from the live page. This is a one-way process and cannot be undone.';
        return confirm(msg);
    }
  </script>
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