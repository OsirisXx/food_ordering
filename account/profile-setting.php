<?php
session_start();
include 'includes/connect.php';
include 'includes/logger.php';

if(isset($_SESSION['admin_sid']) && $_SESSION['admin_sid']==session_id()) {

    $name = isset($_SESSION['name']) ? $_SESSION['name'] : 'Admin';
    $role = isset($_SESSION['role']) ? $_SESSION['role'] : 'Administrator';

    $success_message = '';
    $error_message = '';

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $first_name = mysqli_real_escape_string($con, $_POST['first_name']);
        $last_name = mysqli_real_escape_string($con, $_POST['last_name']);
        $email = mysqli_real_escape_string($con, $_POST['email']);
        $contact = mysqli_real_escape_string($con, $_POST['contact']);
        $password = $_POST['password'];

        // Resolve current admin id from session or database
        $admin_id = isset($_SESSION['admin_id']) ? (int)$_SESSION['admin_id'] : 0;
        if ($admin_id === 0 && isset($_SESSION['name'])) {
            $res = mysqli_query($con, "SELECT id FROM users WHERE name = '".mysqli_real_escape_string($con, $_SESSION['name'])."' LIMIT 1");
            if ($row = mysqli_fetch_assoc($res)) { $admin_id = (int)$row['id']; $_SESSION['admin_id'] = $admin_id; }
        }

        // Handle profile image upload
        if (!empty($_FILES['profile_image']['name'])) {
            $upload_dir = 'images/';
            $file_extension = pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
            $filename = 'admin_' . $admin_id . '_' . time() . '.' . $file_extension;
            $image_path = $upload_dir . $filename;
            
            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $image_path)) {
                // Update admin profile with new image
                $update_query = "UPDATE users SET name = '$first_name $last_name', email = '$email', contact = '$contact'";
                if (!empty($password)) {
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $update_query .= ", password = '$hashed_password'";
                }
                $update_query .= " WHERE id = $admin_id";
                
                if (mysqli_query($con, $update_query)) {
                    $_SESSION['name'] = "$first_name $last_name";
                    $_SESSION['profile_image'] = $image_path; // remember uploaded image path since DB has no column
                logActivity($con, 'Updated profile with new image');
                    $success_message = "Profile updated successfully!";
                } else {
                    $error_message = "Error updating profile: " . mysqli_error($con);
                }
            } else {
                $error_message = "Error uploading image";
            }
        } else {
            // Update without image
            $update_query = "UPDATE users SET name = '$first_name $last_name', email = '$email', contact = '$contact'";
            if (!empty($password)) {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $update_query .= ", password = '$hashed_password'";
            }
            $update_query .= " WHERE id = $admin_id";
            
            if (mysqli_query($con, $update_query)) {
                $_SESSION['name'] = "$first_name $last_name";
                logActivity($con, 'Updated profile information');
                $success_message = "Profile updated successfully!";
            } else {
                $error_message = "Error updating profile: " . mysqli_error($con);
            }
        }
    }

    // Get current admin data from users table
    $admin_id = $_SESSION['admin_id'] ?? 1;
    $admin_query = "SELECT * FROM users WHERE id = $admin_id";
    $admin_result = mysqli_query($con, $admin_query);
    $admin_data = mysqli_fetch_assoc($admin_result);
    // Compute current profile image path (fallback to default)
    $profile_image = isset($_SESSION['profile_image']) && file_exists($_SESSION['profile_image']) ? $_SESSION['profile_image'] : 'images/avatar.jpg';
    
    // Split name into first and last name
    $name_parts = explode(' ', $admin_data['name'] ?? $name);
    $first_name = $name_parts[0] ?? '';
    $last_name = isset($name_parts[1]) ? implode(' ', array_slice($name_parts, 1)) : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profile Settings - Admin Panel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link href="css/materialize.min.css" rel="stylesheet">
    <link href="css/style.min.css" rel="stylesheet">
    <link href="css/custom/custom.min.css" rel="stylesheet">
    <link href="css/admin-custom.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>

  <!-- MAIN -->
  <div id="main-wrapper">
    <div class="left-sidebar">
      <div class="scroll-sidebar">
        <?php 
        $current_page = 'profile-setting';
        include 'includes/sidebar.php'; 
        ?>
      </div>
    </div>

    <div class="page-wrapper">
      <div class="container-fluid">
        <div class="page-header">
          <h1 class="page-title">
            <i class="fa fa-user"></i>
            Profile Settings
          </h1>
        </div>

        <div class="content-card">
          
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

          <div class="row">
            <div class="col s12 m8">
              <div class="card">
                <div class="card-content">
                  <span class="card-title">Update Profile Information</span>
                  
                  <form method="POST" enctype="multipart/form-data">
                    <div class="row">
                      <div class="input-field col s6">
                        <input id="first_name" name="first_name" type="text" class="validate" value="<?php echo htmlspecialchars($first_name); ?>" required>
                        <label for="first_name">First Name</label>
                      </div>
                      <div class="input-field col s6">
                        <input id="last_name" name="last_name" type="text" class="validate" value="<?php echo htmlspecialchars($last_name); ?>" required>
                        <label for="last_name">Last Name</label>
                      </div>
                    </div>

                    <div class="row">
                      <div class="input-field col s12">
                        <input id="email" name="email" type="email" class="validate" value="<?php echo htmlspecialchars($admin_data['email'] ?? ''); ?>" required>
                        <label for="email">Email Address</label>
                      </div>
                    </div>

                    <div class="row">
                      <div class="input-field col s12">
                        <input id="contact" name="contact" type="text" class="validate" value="<?php echo htmlspecialchars($admin_data['contact'] ?? ''); ?>">
                        <label for="contact">Contact Number</label>
                      </div>
                    </div>

                    <div class="row">
                      <div class="input-field col s12">
                        <input id="password" name="password" type="password" class="validate">
                        <label for="password">New Password (leave blank to keep current)</label>
                      </div>
                    </div>

                    <div class="row">
                      <div class="col s12">
                        <div class="file-field input-field">
                          <div class="btn orange">
                            <span>Profile Image</span>
                            <input type="file" name="profile_image" accept="image/*">
                          </div>
                          <div class="file-path-wrapper">
                            <input class="file-path validate" type="text" placeholder="Choose a profile image">
                          </div>
                        </div>
                      </div>
                    </div>

                    <div class="row">
                      <div class="col s12">
                        <button class="btn orange waves-effect" type="submit" style="opacity:1; cursor:pointer;">
                          <i class="fa fa-save"></i> Update Profile
                        </button>
                      </div>
                    </div>
                  </form>
                </div>
              </div>
            </div>

            <div class="col s12 m4">
              <div class="card">
                <div class="card-content">
                  <span class="card-title">Current Profile</span>
                  <div class="center-align">
                    <img src="<?php echo htmlspecialchars($profile_image); ?>" alt="Profile" class="circle responsive-img" style="width: 150px; height: 150px; object-fit: cover;">
                    <h5><?php echo htmlspecialchars($name); ?></h5>
                    <p class="grey-text"><?php echo htmlspecialchars($role); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($admin_data['email'] ?? ''); ?></p>
                    <p><strong>Contact:</strong> <?php echo htmlspecialchars($admin_data['contact'] ?? 'Not set'); ?></p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="js/plugins/jquery-1.11.2.min.js"></script>
  <script src="js/materialize.min.js"></script>
  <script>
    $(document).ready(function(){
        $('.collapsible').collapsible();
        $('.dropdown-button').dropdown();
    });
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