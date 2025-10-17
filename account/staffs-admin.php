<?php
session_start();
include 'includes/connect.php';

if(isset($_SESSION['admin_sid']) && $_SESSION['admin_sid']==session_id()) {

    $name = isset($_SESSION['name']) ? $_SESSION['name'] : 'Admin';
    $role = isset($_SESSION['role']) ? $_SESSION['role'] : 'Administrator';


    // Ensure staff table exists
    $con->query("CREATE TABLE IF NOT EXISTS staff (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(120) NOT NULL,
        contact VARCHAR(50) NOT NULL,
        role VARCHAR(40) NOT NULL,
        status ENUM('active','inactive') NOT NULL DEFAULT 'active',
        hire_date DATE DEFAULT NULL,
        deleted TINYINT(1) NOT NULL DEFAULT 0
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    $staff_result = mysqli_query($con, "SELECT id, name, email, contact, role, status FROM staff WHERE deleted = 0 ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Staff Management - Admin Panel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link href="css/materialize.min.css" rel="stylesheet">
    <link href="css/style.min.css" rel="stylesheet">
    <link href="css/custom/custom.min.css" rel="stylesheet">
    <link href="css/admin-custom.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>

  <!-- MAIN -->
  <div id="main-wrapper" class="khaki-theme">
    <div class="left-sidebar">
      <div class="scroll-sidebar">
        <?php 
        $current_page = 'staffs';
        include 'includes/sidebar.php'; 
        ?>
      </div>
    </div>

    <div class="page-wrapper">
      <div class="container-fluid">
        <div class="page-header">
          <h1 class="page-title">
            <i class="fa fa-user-circle"></i>
            Staff Management
          </h1>
        </div>

        <div class="content-card">
          
          <div class="card-header">
            <div class="row">
              <div class="col s12 m6">
                <h4 class="card-title">Staff List</h4>
              </div>
              <div class="col s12 m6" style="text-align: right;">
                <button class="btn btn-primary-custom" onclick="openAddStaffModal()">
                  <i class="fa fa-plus"></i> Add Staff
                </button>
              </div>
            </div>
          </div>

          <div class="card-body">
            <div class="table-responsive staff-list-scroll">
              <table class="table table-custom" id="staffTable">
                      <thead>
                        <tr>
                          <th>Name</th>
                          <th>Contact</th>
                          <th>Email</th>
                          <th>Role</th>
                          <th>Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php while($staff = mysqli_fetch_assoc($staff_result)) { ?>
                          <tr>
                            <td><?php echo htmlspecialchars($staff['name']); ?></td>
                            <td><?php echo htmlspecialchars($staff['contact']); ?></td>
                            <td><?php echo htmlspecialchars($staff['email']); ?></td>
                            <td><span class="badge" style="background-color: #CD853F;">&nbsp;<?php echo htmlspecialchars($staff['role']); ?>&nbsp;</span></td>
                            <td>
                              <a class="btn btn-small waves-effect waves-light" onclick="editStaff(<?php echo (int)$staff['id']; ?>)">
                                <i class="fa fa-edit"></i>
                              </a>
                              <a class="btn btn-small waves-effect waves-light" onclick="deleteStaff(<?php echo (int)$staff['id']; ?>)" style="background:#dc3545; color:#fff;">
                                <i class="fa fa-trash"></i>
                              </a>
                            </td>
                          </tr>
                        <?php } ?>
                      </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Add/Edit Staff Modal -->
  <div id="staffModal" class="modal">
    <div class="modal-content">
      <div class="modal-header-custom">
        <h4 id="staffModalTitle">Add New Staff Member</h4>
        <span class="close" onclick="closeStaffModal()">&times;</span>
      </div>
      <form id="staffForm" method="POST" action="routers/staff-add.php">
        <div class="row">
          <div class="col s12">
            <div class="input-field">
              <input id="staff_name" name="name" type="text" class="form-control-custom" placeholder="Full Name" required>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col s12 m6">
            <div class="input-field">
              <input id="staff_contact" name="contact" type="text" class="form-control-custom" placeholder="Contact Number" required>
            </div>
          </div>
          <div class="col s12 m6">
            <div class="input-field">
              <input id="staff_email" name="email" type="email" class="form-control-custom" placeholder="Email" required>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col s12 m6">
            <div class="input-field">
              <input id="staff_role" name="role" type="text" class="form-control-custom" placeholder="Role" required>
            </div>
          </div>
          
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" onclick="closeStaffModal()">Cancel</button>
          <button type="submit" class="btn btn-primary">Save</button>
        </div>
      </form>
    </div>
  </div>

  <script src="js/plugins/jquery-1.11.2.min.js"></script>
  <script src="js/materialize.min.js"></script>
  <script>
    $(document).ready(function(){
        $('.collapsible').collapsible();
        $('.dropdown-button').dropdown();
        // initialize selects for Materialize styles if present (guarded)
        if (window.M && M.FormSelect) { M.FormSelect.init(document.querySelectorAll('select')); }

        // Close modal when clicking outside content
        $(document).on('click', '#staffModal', function(e){
            if ($(e.target).is('#staffModal')) { closeStaffModal(); }
        });
    });

    function openAddStaffModal() {
        $('#staffForm')[0].reset();
        $('#staffForm').attr('action', 'routers/staff-add.php');
        $('#staffModalTitle').text('Add New Staff Member');
        $('body').addClass('modal-open');
        $('#staffModal').show();
    }

    function closeStaffModal() {
        $('#staffModal').hide();
        $('body').removeClass('modal-open');
    }

    // handle submit
    $('#staffForm').on('submit', function(e){
        // allow normal POST submit for add/update
    });

    function addStaff() {
        alert('Adding new staff member...');
    }

    function editStaff(staffId) {
        // Load details via AJAX and open modal
        $.get('routers/get-staff.php', { id: staffId }, function(res){
            if (res && res.success) {
                $('#staff_name').val(res.staff.name);
                $('#staff_email').val(res.staff.email);
                $('#staff_contact').val(res.staff.contact);
                $('#staff_role').val(res.staff.role);
                if (res.staff.status) { $('#staff_status').val(res.staff.status); }
                if (res.staff.hire_date) { $('#staff_hire_date').val(res.staff.hire_date); }
                $('#staffForm').attr('action', 'routers/staff-update.php?id=' + staffId);
                $('#staffModalTitle').text('Edit Staff Member');
                $('body').addClass('modal-open');
                $('#staffModal').show();
            } else { alert(res && res.message ? res.message : 'Failed to load staff'); }
        }, 'json').fail(function(){ alert('Failed to load staff'); });
    }

    function deleteStaff(staffId) {
        if (confirm('Are you sure you want to delete this staff member?')) {
            $.post('routers/staff-delete.php', { id: staffId }, function(res){
                if (res && res.success) { location.reload(); }
                else { alert(res && res.message ? res.message : 'Failed to delete'); }
            }, 'json').fail(function(){ alert('Failed to delete'); });
        }
    }

    function toggleStaffStatus(staffId, currentStatus) {
        const newStatus = currentStatus == 'active' ? 'inactive' : 'active';
        if (confirm('Are you sure you want to ' + newStatus + ' this staff member?')) {
            $.post('routers/staff-update.php?id=' + staffId, { status: newStatus }, function(res){
                location.reload();
            }).fail(function(){ alert('Failed to update status'); });
        }
    }
  </script>

  <style>
    /* Modal overlay and container styling */
    body.modal-open { overflow: hidden; padding-right: 0 !important; }
    .modal { position: fixed; z-index: 9999; inset: 0; width: 100%; min-height: 100vh; background-color: rgba(0,0,0,0.45); display: none; overflow: auto; }
    .modal .modal-content { background: #fff; margin: 5% auto; width: 90%; max-width: 600px; max-height: calc(100vh - 10% - 40px); border-radius: 8px; box-shadow: 0 4px 16px rgba(0,0,0,0.2); position: relative; overflow: auto; }
    .modal-header-custom { background: linear-gradient(90deg, #D2B48C 0%, #F5DEB3 100%); padding: 20px; border-bottom: 1px solid #eee; position: relative; border-radius: 8px 8px 0 0; }
    .modal-header-custom h4 { margin: 0; color: #333; font-weight: 600; font-size: 18px; }
    .modal-header-custom .close { position: absolute; right: 20px; top: 50%; transform: translateY(-50%); font-size: 24px; cursor: pointer; color: #333; font-weight: bold; line-height: 1; }
    .modal-header-custom .close:hover { color: #000; }
    #staffForm { padding: 20px; }
    .modal-footer { text-align: right; margin-top: 10px; display:flex; justify-content:flex-end; gap:12px; padding: 0 20px 20px; }
    .btn.btn-primary { background:#ff6b6b; color:#fff; border:none; padding:12px 24px; border-radius:8px; line-height:1.2; display:inline-flex; align-items:center; justify-content:center; height:44px; box-shadow:0 2px 6px rgba(255,107,107,0.4); }
    .btn.btn-secondary { background:#fff; color:#ff6b6b; border:1px solid #ff6b6b; padding:12px 24px; border-radius:8px; line-height:1.2; display:inline-flex; align-items:center; justify-content:center; height:44px; }
    .badge {
      padding: 4px 8px;
      border-radius: 12px;
      font-size: 12px;
      font-weight: bold;
      color: white;
    }
    .khaki-table thead tr {
      background-color: #F0E1C2; /* light khaki */
    }
    .khaki-table thead th {
      color: #5A4632;
      font-weight: 600;
    }
    .khaki-table tbody tr:nth-child(even) {
      background-color: #FBF6EC;
    }
    .khaki-table tbody tr:hover {
      background-color: #F6EBD6;
    }
    .khaki-table td, .khaki-table th { border-bottom-color: #e6d7b9; }
    .staff-list-scroll { max-height: 60vh; overflow-y: auto; }
  </style>
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