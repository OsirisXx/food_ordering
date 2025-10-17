<?php
session_start();
include 'includes/connect.php';

if(isset($_SESSION['admin_sid']) && $_SESSION['admin_sid']==session_id()) {

    $name = isset($_SESSION['name']) ? $_SESSION['name'] : 'Admin';
    $role = isset($_SESSION['role']) ? $_SESSION['role'] : 'Administrator';

    // Fetch all users
    $sql = mysqli_query($con, "SELECT * FROM users");
    if(!$sql){
        die("Query failed: " . mysqli_error($con));
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Users - Admin Panel</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  
  <link href="css/materialize.min.css" rel="stylesheet">
  <link href="css/style.min.css" rel="stylesheet">
  <link href="css/custom/custom.min.css" rel="stylesheet">
  <link href="css/admin-custom.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="fix-header">

  <div class="preloader">
    <svg class="circular" viewBox="25 25 50 50">
      <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10" />
    </svg>
  </div>

  <!-- MAIN -->
  <div id="main">
    <div class="wrapper">

      <?php 
      $current_page = 'users';
      include 'includes/sidebar.php'; 
      ?>

      <!-- CONTENT -->
      <section id="content">
        <div class="container">
          <h4 class="header">User Management</h4>
          <table class="striped responsive-table">
            <thead>
              <tr>
                <th>User ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Contact</th>
                <th>Role</th>
                <th>Registration Date</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php while($row = mysqli_fetch_assoc($sql)) { ?>
                <tr>
                  <td><strong>#<?php echo $row['id']; ?></strong></td>
                  <td><?php echo htmlspecialchars($row['name'] ?? ''); ?></td>
                  <td><?php echo htmlspecialchars($row['email'] ?? ''); ?></td>
                  <td><?php echo htmlspecialchars($row['contact'] ?? '-'); ?></td>
                  <td><?php echo htmlspecialchars($row['role'] ?? 'customer'); ?></td>
                  <td><?php echo date('M d, Y', strtotime($row['date'])); ?></td>
                  <td>
                    <a class="btn btn-small waves-effect waves-light" onclick="editUser(<?php echo $row['id']; ?>)">
                      <i class="fa fa-edit"></i>
                    </a>
                    <a class="btn btn-small red waves-effect waves-light" onclick="deleteUser(<?php echo $row['id']; ?>)">
                      <i class="fa fa-trash"></i>
                    </a>
                  </td>
                </tr>
              <?php } ?>
            </tbody>
          </table>
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

    function editUser(userId) {
      alert('Edit user ID: ' + userId);
    }

    function deleteUser(userId) {
      if (confirm('Are you sure you want to delete this user?')) {
        alert('Delete user ID: ' + userId);
      }
    }
  </script>

  <style>
    .avatar-circle {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background: linear-gradient(45deg, #FF6B4A, #FF8A65);
      color: white;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: bold;
      font-size: 16px;
    }
  </style>
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

