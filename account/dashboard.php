<?php
// Bootstrap PHP before any output
include 'includes/connect.php';
// Temporarily show errors while we stabilize
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (isset($_SESSION['admin_sid']) && $_SESSION['admin_sid'] == session_id()) {
    $name = isset($_SESSION['name']) ? $_SESSION['name'] : 'Admin';
    $role = isset($_SESSION['role']) ? $_SESSION['role'] : 'Administrator';

    $metrics = [
        'total_users' => 0,
        'total_orders' => 0,
        'today_orders' => 0,
        'total_earning' => 0,
        'today_earning' => 0,
        'total_reservations' => 0
    ];

    if ($res = mysqli_query($con, "SELECT COUNT(*) AS c FROM users")) {
        $row = mysqli_fetch_assoc($res);
        $metrics['total_users'] = (int)$row['c'];
    }

    if ($res = mysqli_query($con, "SELECT COUNT(*) AS c, COALESCE(SUM(total),0) AS s FROM orders WHERE deleted = 0")) {
        $row = mysqli_fetch_assoc($res);
        $metrics['total_orders'] = (int)$row['c'];
        $metrics['total_earning'] = (int)$row['s'];
    }

    if ($res = mysqli_query($con, "SELECT COUNT(*) AS c, COALESCE(SUM(total),0) AS s FROM orders WHERE deleted = 0 AND DATE(`date`) = CURDATE()")) {
        $row = mysqli_fetch_assoc($res);
        $metrics['today_orders'] = (int)$row['c'];
        $metrics['today_earning'] = (int)$row['s'];
    }
} else {
    header('location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Admin Panel</title>
    <link href="css/materialize.min.css" rel="stylesheet">
    <link href="css/style.min.css" rel="stylesheet">
    <link href="css/custom/custom.min.css" rel="stylesheet">
    <link href="css/admin-custom.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body class="fix-header">


    <div id="main-wrapper">

        <div class="left-sidebar">
            <div class="scroll-sidebar">
                <?php 
                $current_page = 'overview';
                include 'includes/sidebar.php'; 
                ?>
            </div>
        </div>

        <div class="page-wrapper">
            <div class="container-fluid">
                <div class="page-header">
                    <h1 class="page-title">
                        <i class="fa fa-tachometer"></i>
                        Overview
                    </h1>
                </div>

                <div class="content-card">
                    <div class="row overview-metrics">
                        <div class="col s12 m6 l4">
                            <div class="metric-card primary" style="position:relative;">
                                <div class="metric-label">Total Earning</div>
                                <div class="metric-value">₱ <?php echo number_format($metrics['total_earning'], 0); ?></div>
                                <div style="color:#ff6b6b; font-size:12px; margin-top:6px;">▼ 0%</div>
                                <div style="position:absolute; right:16px; top:16px; width:36px; height:36px; border-radius:50%; background:#CD853F; display:flex; align-items:center; justify-content:center; color:#fff;">
                                    <i class="fa fa-dollar-sign"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col s12 m6 l4">
                            <div class="metric-card danger" style="position:relative;">
                                <div class="metric-label">Today's Earning</div>
                                <div class="metric-value">₱ <?php echo number_format($metrics['today_earning'], 0); ?></div>
                                <div style="color:#ff6b6b; font-size:12px; margin-top:6px;">▼ 0%</div>
                                <div style="position:absolute; right:16px; top:16px; width:36px; height:36px; border-radius:50%; background:#D2B48C; display:flex; align-items:center; justify-content:center; color:#fff;">
                                    <i class="fa fa-calendar"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col s12 m6 l4">
                            <div class="metric-card info" style="position:relative;">
                                <div class="metric-label">Total Orders</div>
                                <div class="metric-value"><?php echo $metrics['total_orders']; ?></div>
                                <div style="color:#ff6b6b; font-size:12px; margin-top:6px;">▼ 0%</div>
                                <div style="position:absolute; right:16px; top:16px; width:36px; height:36px; border-radius:50%; background:#F5DEB3; display:flex; align-items:center; justify-content:center; color:#333;">
                                    <i class="fa fa-shopping-cart"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col s12 m6 l4">
                            <div class="metric-card danger" style="position:relative;">
                                <div class="metric-label">Today's Orders</div>
                                <div class="metric-value"><?php echo $metrics['today_orders']; ?></div>
                                <div style="color:#ff6b6b; font-size:12px; margin-top:6px;">▼ 0%</div>
                                <div style="position:absolute; right:16px; top:16px; width:36px; height:36px; border-radius:50%; background:#D2B48C; display:flex; align-items:center; justify-content:center; color:#fff;">
                                    <i class="fa fa-calendar"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col s12 m6 l4">
                            <div class="metric-card primary" style="position:relative;">
                                <div class="metric-label">Total Users</div>
                                <div class="metric-value"><?php echo $metrics['total_users']; ?></div>
                                <div style="color:#ff6b6b; font-size:12px; margin-top:6px;">▼ 100%</div>
                                <div style="position:absolute; right:16px; top:16px; width:36px; height:36px; border-radius:50%; background:#CD853F; display:flex; align-items:center; justify-content:center; color:#fff;">
                                    <i class="fa fa-users"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col s12 m6 l4">
                            <div class="metric-card success" style="position:relative;">
                                <div class="metric-label">Total Reservations</div>
                                <div class="metric-value"><?php echo $metrics['total_reservations']; ?></div>
                                <div style="color:#ff6b6b; font-size:12px; margin-top:6px;">▼ 100%</div>
                                <div style="position:absolute; right:16px; top:16px; width:36px; height:36px; border-radius:50%; background:#DEB887; display:flex; align-items:center; justify-content:center; color:#fff;">
                                    <i class="fa fa-calendar"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Orders Table -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">Latest Orders</h4>
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Order ID</th>
                                                    <th>Customer Name</th>
                                                    <th>Status</th>
                                                    <th>Total Amount</th>
                                                    <th>Date</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $sql = mysqli_query($con, "SELECT o.id, o.total, o.status, o.date, u.name AS customer_name FROM orders o LEFT JOIN users u ON u.id = o.customer_id WHERE o.deleted = 0 ORDER BY o.date DESC LIMIT 10");
                                                while($row = mysqli_fetch_assoc($sql)) {
                                                    echo '<tr>';
                                                    echo '<td>#' . (int)$row['id'] . '</td>';
                                                    echo '<td>' . htmlspecialchars($row['customer_name'] ?? 'Unknown') . '</td>';
                                                    echo '<td>' . htmlspecialchars($row['status']) . '</td>';
                                                    echo '<td>₱ ' . number_format((int)$row['total'], 0) . '</td>';
                                                    echo '<td>' . date('M d, Y', strtotime($row['date'])) . '</td>';
                                                    echo '</tr>';
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php include 'includes/footer.php'; ?>
        </div>
    </div>

    <script src="js/plugins/jquery-1.11.2.min.js"></script>
    <script src="js/materialize.min.js"></script>
    <script src="js/jquery.slimscroll.js"></script>
    <script src="js/sidebarmenu.js"></script>
    <script src="js/lib/sticky-kit-master/dist/sticky-kit.min.js"></script>
    <script src="js/custom.min.js"></script>

</body>
</html>