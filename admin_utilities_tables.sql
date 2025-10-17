-- Additional tables for Admin Utilities functionality
-- Activity Log table
CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_role` varchar(50) NOT NULL,
  `action` text NOT NULL,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Page-level Archive control
CREATE TABLE IF NOT EXISTS `archived_pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page_key` varchar(64) NOT NULL, -- e.g., 'all-orders', 'menu-management', 'staffs-admin'
  `archived` tinyint(1) NOT NULL DEFAULT 0,
  `archived_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_archived_pages_page_key` (`page_key`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Staff Archive table (for bulk page archive of staff)
CREATE TABLE IF NOT EXISTS `staff_archive` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `staff_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(120) NOT NULL,
  `contact` varchar(50) NOT NULL,
  `role` varchar(40) NOT NULL,
  `status` varchar(20) NOT NULL,
  `archived_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Order Archive table
CREATE TABLE `order_archive` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `customer_name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `total` decimal(10,2) NOT NULL,
  `archived_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Menu Archive table  
CREATE TABLE `menu_archive` (
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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Insert some sample activity log data
INSERT INTO `activity_logs` (`user_role`, `action`, `date`) VALUES
('Admin', 'Deleted order ID: 36 (moved to archive)', '2025-10-04 13:05:21'),
('Admin', 'Deleted order ID: 28 (moved to archive)', '2025-10-04 13:05:07'),
('Admin', 'Deleted order ID: 34 (moved to archive)', '2025-10-04 13:03:18'),
('Admin', 'Deleted order ID: 31 (moved to archive)', '2025-10-04 13:03:15'),
('Admin', 'Deleted order ID: 33 (moved to archive)', '2025-10-04 13:03:13'),
('Admin', 'Deleted order ID: 35 (moved to archive)', '2025-10-04 13:03:04'),
('Admin', 'Restored menu item: Chocolate Donut', '2025-10-03 20:46:00'),
('Unknown', 'Deleted menu item \'Chocolate Donut\'', '2025-10-03 20:45:50'),
('Admin', 'Deleted order ID: 26 (moved to archive)', '2025-10-03 20:41:59'),
('Admin', 'Deleted order ID: 35 (moved to archive)', '2025-10-03 20:39:44');

-- Insert some sample order archive data
INSERT INTO `order_archive` (`order_id`, `customer_name`, `email`, `total`, `archived_at`) VALUES
(28, '', 'jimmuel@gmail.com', 58.00, '2025-10-04 13:05:07'),
(34, '', '', 0.00, '2025-10-04 13:03:18'),
(31, '', '', 65.00, '2025-10-04 13:03:15'),
(33, '', '', 0.00, '2025-10-04 13:03:13'),
(35, '', 'jimmuel@gmail.com', 0.00, '2025-10-04 13:03:04');

-- Insert some sample menu archive data
INSERT INTO `menu_archive` (`menu_id`, `item_name`, `description`, `price`, `category`, `status`, `archived_at`) VALUES
(0, 'Ensaymada', 'Light, fluffy, and perfectly sweet with a timeless flavor.', 58.00, 'Pastries', 'Available', '2025-10-11 10:44:09');

-- Seed archived_pages defaults (all unarchived)
INSERT INTO `archived_pages` (`page_key`, `archived`, `archived_at`) VALUES
('all-orders', 0, NULL)
ON DUPLICATE KEY UPDATE page_key=VALUES(page_key);

INSERT INTO `archived_pages` (`page_key`, `archived`, `archived_at`) VALUES
('menu-management', 0, NULL)
ON DUPLICATE KEY UPDATE page_key=VALUES(page_key);

INSERT INTO `archived_pages` (`page_key`, `archived`, `archived_at`) VALUES
('staffs-admin', 0, NULL)
ON DUPLICATE KEY UPDATE page_key=VALUES(page_key);

