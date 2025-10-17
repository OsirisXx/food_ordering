<?php
session_start();
include 'includes/connect.php';

// Check if admin is logged in
if (isset($_SESSION['admin_sid']) && $_SESSION['admin_sid'] == session_id()) {
    $name = isset($_SESSION['name']) ? $_SESSION['name'] : 'Admin';
    $role = isset($_SESSION['role']) ? $_SESSION['role'] : 'Administrator';

    // Fetch users from database (hide soft-deleted)
    $users_query = "SELECT * FROM users WHERE deleted = 0 ORDER BY id DESC";
    $users_result = mysqli_query($con, $users_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users - Admin Panel</title>
    <link href="css/materialize.min.css" rel="stylesheet">
    <link href="css/style.min.css" rel="stylesheet">
    <link href="css/custom/custom.min.css" rel="stylesheet">
    <link href="css/admin-custom.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="fix-header">
    <div id="main-wrapper">
        <?php 
        $current_page = 'users';
        include 'includes/sidebar.php'; 
        ?>

        <div class="page-wrapper">
            <div class="container-fluid">
                <div class="page-header">
                    <h1 class="page-title">
                        <i class="fa fa-users"></i>
                        Users
                    </h1>
                </div>

                <div class="content-card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col s12 m6">
                                <h2 class="card-title">User Management</h2>
                            </div>
                            <div class="col s12 m6 right-align">
                                <a class="btn btn-primary-custom modal-trigger waves-effect waves-light" href="#addUserModal">
                                    <i class="fa fa-plus"></i> Add User
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <div class="responsive-table">
                            <table class="table table-custom striped">
                                <thead>
                                    <tr>
                                        <th>User ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Contact Number</th>
                                        <th>Role</th>
                                        <th>Registration Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($user = mysqli_fetch_assoc($users_result)): ?>
                                    <tr>
                                        <td><strong>#<?php echo $user['id']; ?></strong></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-circle">
                                                    <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                                                </div>
                                                <div class="ml-2">
                                                    <?php echo htmlspecialchars($user['name']); ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?php echo htmlspecialchars($user['email'] ?? ''); ?></td>
                                        <td><?php echo htmlspecialchars($user['contact'] ?? '-'); ?></td>
                                        <td>
                                            <span class="status-badge <?php echo ($user['role'] == 'admin') ? 'status-approved' : 'status-pending'; ?>">
                                                <?php echo ucfirst($user['role'] ?? 'customer'); ?>
                                            </span>
                                        </td>
                                        <td><?php echo !empty($user['date']) ? date('M d, Y', strtotime($user['date'])) : 'N/A'; ?></td>
                                        <td>
                                            <a class="btn btn-sm btn-primary-custom waves-effect waves-light" onclick="editUser(<?php echo $user['id']; ?>)">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <a class="btn btn-sm btn-danger-custom waves-effect waves-light" onclick="deleteUser(<?php echo $user['id']; ?>)">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add User Modal (mirrors menu-management Add Item design) -->
    <div id="addUserModal" class="modal">
        <div class="modal-content">
            <div class="modal-header-custom">
                <h4 id="addUserModalTitle">Add New User</h4>
                <span class="close" onclick="closeAddUserModal();">&times;</span>
            </div>
            <form action="/food_ordering/account/routers/add-user.php" method="POST" id="addUserForm">
                <div class="row">
                    <div class="col s12 m6">
                        <div class="input-field">
                            <input id="add_name" type="text" name="name" class="form-control-custom" placeholder="Full Name" required>
                        </div>
                    </div>
                    <div class="col s12 m6">
                        <div class="input-field">
                            <input id="add_email" type="email" name="email" class="form-control-custom" placeholder="Email" required>
                        </div>
                    </div>
                    <div class="col s12 m6">
                        <div class="input-field">
                            <input id="add_contact" type="text" name="contact" class="form-control-custom" placeholder="Contact Number">
                        </div>
                    </div>
                    <div class="col s12 m6">
                        <div class="input-field">
                            <input id="add_password" type="password" name="password" class="form-control-custom" placeholder="Password" required>
                        </div>
                    </div>
                    <div class="col s12">
                        <div class="input-field">
                            <label style="display:block; margin-bottom:6px; color:#666; font-size:13px;">Role</label>
                            <select name="role" class="form-control-custom" required style="display:block; width:100%; padding:10px 12px; border:1px solid #ddd; border-radius:6px; background:#fff;">
                                <option value="customer" selected>Customer</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer modal-actions-row">
                    <button type="button" class="btn btn-ghost" onclick="closeAddUserModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div id="editUserModal" class="modal">
        <div class="modal-content">
            <h4>Edit User</h4>
            <form id="editUserForm" method="POST">
                <div class="row">
                    <div class="input-field col s12 m6">
                        <input id="editUserName" type="text" name="name" class="validate" required>
                        <label for="editUserName">Full Name</label>
                    </div>
                    <div class="input-field col s12 m6">
                        <input id="editUserEmail" type="email" name="email" class="validate" required>
                        <label for="editUserEmail">Email</label>
                    </div>
                </div>
                <div class="row">
                    <div class="input-field col s12 m6">
                        <input id="editUserContact" type="text" name="contact" class="validate">
                        <label for="editUserContact">Contact Number</label>
                    </div>
                    <div class="input-field col s12 m6">
                        <select name="role" id="editUserRole" required>
                            <option value="" disabled>Choose Role</option>
                            <option value="customer">Customer</option>
                            <option value="admin">Admin</option>
                        </select>
                        <label>Role</label>
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <a href="#!" class="modal-close waves-effect waves-green btn-flat">Cancel</a>
            <a href="#!" class="waves-effect waves-green btn btn-primary-custom" onclick="submitEditUser()">Update User</a>
        </div>
    </div>

    <script src="js/plugins/jquery-1.11.2.min.js"></script>
    <script src="js/materialize.min.js"></script>
    <script>
        $(document).ready(function(){
            // Modal handlers without Materialize JS
            $(document).on('click', 'a.modal-trigger[href=\"#addUserModal\"]', function(e){
                e.preventDefault();
                $('body').addClass('modal-open');
                $('#addUserModal').fadeIn(120);
            });
            $(document).on('click', '.modal-close', function(e){
                e.preventDefault();
                $(this).closest('.modal').fadeOut(100);
                $('body').removeClass('modal-open');
            });
            $(document).on('click', '.modal', function(e){
                if ($(e.target).is('.modal')) { $(this).fadeOut(100); $('body').removeClass('modal-open'); }
            });

            window.closeAddUserModal = function() {
                $('#addUserModal').fadeOut(100);
                $('body').removeClass('modal-open');
            };
        });

        function editUser(userId) {
            // Fetch user data and populate edit modal
            $.ajax({
                url: 'routers/get-user.php',
                method: 'GET',
                data: { user_id: userId },
                dataType: 'json',
                success: function(user) {
                    $('#editUserName').val(user.name);
                    $('#editUserEmail').val(user.email);
                    $('#editUserContact').val(user.contact);
                    $('#editUserRole').val(user.role);
                    $('#editUserForm').attr('action', 'routers/update-user.php?id=' + userId);
                    
                    // Open modal (no Materialize dependency)
                    $('#editUserModal').fadeIn(120);
                },
                error: function() {
                    alert('Error loading user data');
                }
            });
        }

        function deleteUser(userId) {
            // Custom delete confirmation similar to orders modal
            if (!window.userDeleteModal) {
                $('body').append(`
                <div id="userDeleteModal" style="display:none; position: fixed; inset: 0; z-index: 9999; background: rgba(0,0,0,0.45);">
                  <div style="max-width: 420px; width: 92%; margin: 10% auto; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
                    <div style="padding: 18px 20px; background: #fdf2e9; border-bottom: 1px solid #f1e3d6; display:flex; align-items:center; justify-content:space-between;">
                      <div style="display:flex; align-items:center; gap:10px;">
                        <span style="display:inline-flex; width:34px; height:34px; border-radius:50%; align-items:center; justify-content:center; background:#ffe3e3; color:#dc3545;">
                          <i class=\"fa fa-trash\"></i>
                        </span>
                        <h3 style="margin:0; font-size: 18px; font-weight: 600; color:#333;">Delete User</h3>
                      </div>
                      <button id="close-user-delete" style="border:none; background:transparent; font-size:18px; cursor:pointer; color:#666;">×</button>
                    </div>
                    <div style="padding: 20px;">
                      <p style="margin:0 0 10px; color:#555;">You're about to delete user <strong id="confirm-delete-user-id">#</strong>.</p>
                      <p style="margin:0; color:#777; font-size: 13px;">This will hide the user from lists. Related records remain intact.</p>
                    </div>
                    <div style="padding: 14px 20px; display:flex; gap:10px; justify-content:flex-end; background:#fafafa; border-top:1px solid #eee;">
                      <button id="cancel-user-delete" class="btn btn-primary-custom" style="background:#6c757d; padding:10px 16px; line-height:1.2; display:inline-flex; align-items:center; justify-content:center; height:40px;">Cancel</button>
                      <button id="confirm-user-delete" class="btn btn-danger-custom" style="background:#dc3545; padding:10px 16px; line-height:1.2; display:inline-flex; align-items:center; justify-content:center; height:40px;">Yes, Delete</button>
                    </div>
                  </div>
                </div>`);
                window.userDeleteModal = true;
                $(document).on('click', '#close-user-delete, #cancel-user-delete', function(){ $('#userDeleteModal').fadeOut(100); });
            }
            $('#confirm-delete-user-id').text('#'+userId);
            $('#userDeleteModal').fadeIn(120);
            $(document).off('click', '#confirm-user-delete').on('click', '#confirm-user-delete', function(){
                $.ajax({
                    url: '/food_ordering/account/routers/delete-user.php',
                    method: 'POST',
                    dataType: 'json',
                    data: { user_id: userId },
                    success: function(response) {
                        if (response && response.success) {
                            location.reload();
                        } else {
                            alert('Error deleting user');
                        }
                    },
                    error: function() { alert('Error deleting user'); }
                });
            });
        }

        function submitAddUser() {
            // Get form data and submit
            var form = $('#addUserModal form');
            form.submit();
        }

        function submitEditUser() {
            // Get form data and submit
            var form = $('#editUserForm');
            form.submit();
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
        
        .responsive-table {
            overflow-x: auto;
        }
        
        .table-custom.striped tbody tr:nth-child(odd) {
            background-color: #f8f9fa;
        }
        
        .table-custom.striped tbody tr:hover {
            background-color: #e3f2fd !important;
        }
        
        .btn-sm {
            padding: 0 8px !important;
            height: 32px !important;
            line-height: 32px !important;
            margin: 0 2px;
        }
        
        .btn-sm i {
            font-size: 14px;
        }
        
        /* Match menu-management modal overlay and container */
        body.modal-open { overflow: hidden; padding-right: 0 !important; }
        .modal { position: fixed; z-index: 9999; inset: 0; width: 100%; min-height: 100vh; background-color: rgba(0,0,0,0.45); display: none; overflow: auto; }
        .modal .modal-content { background: #fff; margin: 5% auto; width: 90%; max-width: 600px; max-height: calc(100vh - 10% - 40px); border-radius: 8px; box-shadow: 0 4px 16px rgba(0,0,0,0.2); position: relative; overflow: auto; }
        .modal-header-custom { background: linear-gradient(90deg, #D2B48C 0%, #F5DEB3 100%); padding: 20px; border-bottom: 1px solid #eee; position: relative; border-radius: 8px 8px 0 0; }
        .modal-header-custom h4 { margin: 0; color: #333; font-weight: 600; font-size: 18px; }
        .modal-header-custom .close { position: absolute; right: 20px; top: 50%; transform: translateY(-50%); font-size: 24px; cursor: pointer; color: #333; font-weight: bold; line-height: 1; }
        .modal-header-custom .close:hover { color: #000; }
        .modal-content form { padding: 25px; }
        .modal-actions-row { text-align: right; margin-top: 20px; display:flex; justify-content:flex-end; gap:12px; }
        .btn.btn-primary { background:#ff6b6b; color:#fff; border:none; padding:12px 24px; border-radius:8px; line-height:1.2; display:inline-flex; align-items:center; justify-content:center; height:44px; box-shadow:0 2px 6px rgba(255,107,107,0.4); transition:transform .08s ease, box-shadow .2s ease; }
        .btn.btn-primary:hover { box-shadow:0 4px 10px rgba(255,107,107,0.5); transform: translateY(-1px); }
        .btn.btn-ghost { background:#fff; color:#ff6b6b; border:1px solid #ff6b6b; padding:12px 24px; border-radius:8px; line-height:1.2; display:inline-flex; align-items:center; justify-content:center; height:44px; }
        .btn.btn-ghost:hover { background:#ffecec; }
        
        .input-field label {
            color: #666;
        }
        
        .input-field input:focus + label,
        .input-field input:valid + label {
            color: #CD853F;
        }
        
        .input-field input:focus {
            border-bottom: 1px solid #CD853F !important;
            box-shadow: 0 1px 0 0 #CD853F !important;
        }
        
        .dropdown-content li > a,
        .dropdown-content li > span {
            color: #333;
        }
        
        .dropdown-content li > a:hover,
        .dropdown-content li > span:hover {
            background-color: #f5f5f5;
        }
</style>
<?php include 'includes/footer.php'; ?>
</body>
</html>

<?php
} else {
    header("Location: login.php");
    exit();
}
?>

