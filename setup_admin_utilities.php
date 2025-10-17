<?php
/**
 * Setup script for Admin Utilities functionality
 * Run this script once to create the necessary database tables
 */

include 'account/includes/connect.php';

// Check if tables already exist
$check_activity = mysqli_query($con, "SHOW TABLES LIKE 'activity_logs'");
$check_order_archive = mysqli_query($con, "SHOW TABLES LIKE 'order_archive'");
$check_menu_archive = mysqli_query($con, "SHOW TABLES LIKE 'menu_archive'");

$tables_exist = mysqli_num_rows($check_activity) > 0 && 
                mysqli_num_rows($check_order_archive) > 0 && 
                mysqli_num_rows($check_menu_archive) > 0;

if ($tables_exist) {
    echo "<h2>Admin Utilities tables already exist!</h2>";
    echo "<p>The required tables are already set up in your database.</p>";
} else {
    echo "<h2>Setting up Admin Utilities tables...</h2>";
    
    // Create activity_logs table
    $activity_sql = "CREATE TABLE IF NOT EXISTS `activity_logs` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `user_role` varchar(50) NOT NULL,
        `action` text NOT NULL,
        `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1";
    
    if (mysqli_query($con, $activity_sql)) {
        echo "<p>✓ Activity logs table created successfully</p>";
    } else {
        echo "<p>✗ Error creating activity logs table: " . mysqli_error($con) . "</p>";
    }
    
    // Create order_archive table
    $order_archive_sql = "CREATE TABLE IF NOT EXISTS `order_archive` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `order_id` int(11) NOT NULL,
        `customer_name` varchar(100) DEFAULT NULL,
        `email` varchar(100) DEFAULT NULL,
        `total` decimal(10,2) NOT NULL,
        `archived_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1";
    
    if (mysqli_query($con, $order_archive_sql)) {
        echo "<p>✓ Order archive table created successfully</p>";
    } else {
        echo "<p>✗ Error creating order archive table: " . mysqli_error($con) . "</p>";
    }
    
    // Create menu_archive table
    $menu_archive_sql = "CREATE TABLE IF NOT EXISTS `menu_archive` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `menu_id` int(11) NOT NULL,
        `item_name` varchar(100) NOT NULL,
        `image` longblob DEFAULT NULL,
        `description` text DEFAULT NULL,
        `price` decimal(10,2) NOT NULL,
        `category` varchar(50) DEFAULT NULL,
        `status` varchar(20) DEFAULT 'Available',
        `archived_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1";
    
    if (mysqli_query($con, $menu_archive_sql)) {
        echo "<p>✓ Menu archive table created successfully</p>";
    } else {
        echo "<p>✗ Error creating menu archive table: " . mysqli_error($con) . "</p>";
    }
    
    // Insert sample data
    $sample_data_sql = "INSERT IGNORE INTO `activity_logs` (`user_role`, `action`, `date`) VALUES
        ('Admin', 'Deleted order ID: 36 (moved to archive)', '2025-10-04 13:05:21'),
        ('Admin', 'Deleted order ID: 28 (moved to archive)', '2025-10-04 13:05:07'),
        ('Admin', 'Deleted order ID: 34 (moved to archive)', '2025-10-04 13:03:18'),
        ('Admin', 'Deleted order ID: 31 (moved to archive)', '2025-10-04 13:03:15'),
        ('Admin', 'Deleted order ID: 33 (moved to archive)', '2025-10-04 13:03:13'),
        ('Admin', 'Deleted order ID: 35 (moved to archive)', '2025-10-04 13:03:04'),
        ('Admin', 'Restored menu item: Chocolate Donut', '2025-10-03 20:46:00'),
        ('Unknown', 'Deleted menu item \'Chocolate Donut\'', '2025-10-03 20:45:50'),
        ('Admin', 'Deleted order ID: 26 (moved to archive)', '2025-10-03 20:41:59'),
        ('Admin', 'Deleted order ID: 35 (moved to archive)', '2025-10-03 20:39:44')";
    
    if (mysqli_query($con, $sample_data_sql)) {
        echo "<p>✓ Sample activity log data inserted</p>";
    }
    
    $sample_orders_sql = "INSERT IGNORE INTO `order_archive` (`order_id`, `customer_name`, `email`, `total`, `archived_at`) VALUES
        (28, '', 'jimmuel@gmail.com', 58.00, '2025-10-04 13:05:07'),
        (34, '', '', 0.00, '2025-10-04 13:03:18'),
        (31, '', '', 65.00, '2025-10-04 13:03:15'),
        (33, '', '', 0.00, '2025-10-04 13:03:13'),
        (35, '', 'jimmuel@gmail.com', 0.00, '2025-10-04 13:03:04')";
    
    if (mysqli_query($con, $sample_orders_sql)) {
        echo "<p>✓ Sample order archive data inserted</p>";
    }
    
    $sample_menu_sql = "INSERT IGNORE INTO `menu_archive` (`menu_id`, `item_name`, `description`, `price`, `category`, `status`, `archived_at`) VALUES
        (0, 'Ensaymada', 'Light, fluffy, and perfectly sweet with a timeless flavor.', 58.00, 'Pastries', 'Available', '2025-10-11 10:44:09')";
    
    if (mysqli_query($con, $sample_menu_sql)) {
        echo "<p>✓ Sample menu archive data inserted</p>";
    }
    
    echo "<h3>Setup Complete!</h3>";
    echo "<p>All Admin Utilities tables have been created with sample data.</p>";
}

echo "<p><a href='account/utilities-admin.php'>Go to Admin Utilities</a></p>";
?>

