<?php
session_start(); // Make sure session is started
include 'includes/connect.php';

// Check if admin is logged in
if (isset($_SESSION['admin_sid']) && $_SESSION['admin_sid'] == session_id()) {

    // Get admin info (optional: set defaults if not available)
    $name = isset($_SESSION['name']) ? $_SESSION['name'] : 'Admin';
    $role = isset($_SESSION['role']) ? $_SESSION['role'] : 'Administrator';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Menu Management - Admin Panel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link href="css/materialize.min.css" rel="stylesheet">
    <link href="css/style.min.css" rel="stylesheet">
    <link href="css/custom/custom.min.css" rel="stylesheet">
    <link href="css/admin-custom.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="js/plugins/data-tables/css/jquery.dataTables.min.css" type="text/css" rel="stylesheet">
    
    <!-- Define functions in HEAD to ensure they're available early -->
    <script>
      // Test if elements exist when page loads
      window.addEventListener('DOMContentLoaded', function() {
        console.log('DOM loaded');
        console.log('addItemModal exists:', document.getElementById('addItemModal') !== null);
        console.log('addCategoryModal exists:', document.getElementById('addCategoryModal') !== null);
        
        // Debug: Check all elements with "Modal" in the id
        var allElements = document.querySelectorAll('[id*="Modal"]');
        console.log('All modal elements found:', allElements.length);
        for (var i = 0; i < allElements.length; i++) {
          console.log('Modal element:', allElements[i].id, allElements[i]);
        }
      });
      
      function openAddItemModal() {
        var modal = document.getElementById('addItemModal');
        modal.classList.add('active');
      }
      
      function closeAddItemModal() {
        var modal = document.getElementById('addItemModal');
        modal.classList.remove('active');
      }
      
      function openAddCategoryModal() {
        console.log('openAddCategoryModal called');
        var modal = document.getElementById('addCategoryModal');
        console.log('Modal element:', modal);
        if (modal) {
          modal.classList.add('active');
          console.log('Modal opened successfully');
        } else {
          console.error('addCategoryModal element not found!');
          alert('Error: Add Category Modal not found in DOM');
        }
      }
      
      function closeAddCategoryModal() {
        var modal = document.getElementById('addCategoryModal');
        modal.classList.remove('active');
      }
      
      function submitAddItemForm() {
        document.getElementById('addItemForm').submit();
      }
      
      function submitAddCategoryForm() {
        document.getElementById('addCategoryForm').submit();
      }
      
      // Close modal when clicking outside
      window.onclick = function(event) {
        if (event.target.classList.contains('custom-modal')) {
          event.target.classList.remove('active');
        }
      }
    </script>
    
    <style>
        /* Modern Admin Page Styling */
        
        /* Custom Modal Styling */
        .custom-modal {
            display: none;
            position: fixed;
            z-index: 9999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0);
            backdrop-filter: blur(5px);
            transition: all 0.3s ease;
            opacity: 0;
        }
        
        .custom-modal.active {
            display: block;
            background-color: rgba(0,0,0,0.4);
            opacity: 1;
            animation: modalFadeIn 0.3s ease-out;
        }
        
        .custom-modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 0;
            border: 1px solid #888;
            width: 90%;
            max-width: 600px;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
            transform: scale(0.8) translateY(-50px);
            transition: all 0.3s ease;
        }
        
        .custom-modal.active .custom-modal-content {
            transform: scale(1) translateY(0);
            animation: modalSlideIn 0.3s ease-out;
        }
        
        @keyframes modalFadeIn {
            from {
                opacity: 0;
                background-color: rgba(0,0,0,0);
            }
            to {
                opacity: 1;
                background-color: rgba(0,0,0,0.4);
            }
        }
        
        @keyframes modalSlideIn {
            from {
                transform: scale(0.8) translateY(-50px);
                opacity: 0;
            }
            to {
                transform: scale(1) translateY(0);
                opacity: 1;
            }
        }
        
        .custom-modal-header {
            padding: 20px;
            background: linear-gradient(135deg, #CD853F 0%, #D2B48C 100%);
            color: white;
            border-radius: 8px 8px 0 0;
        }
        
        .custom-modal-header h4 {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 600;
        }
        
        .custom-modal-body {
            padding: 20px;
        }
        
        .custom-modal-footer {
            padding: 15px 20px;
            background: #f8f9fa;
            border-top: 1px solid #e9ecef;
            text-align: right;
            border-radius: 0 0 8px 8px;
        }
        
        /* Form Field Grouping Styles */
        .custom-modal-body .row {
            margin-bottom: 15px;
        }
        
        .custom-modal-body .input-field {
            margin-bottom: 0;
        }
        
        .custom-modal-body .input-field label {
            color: #666;
            font-weight: 500;
        }
        
        .custom-modal-body .input-field label i {
            margin-right: 8px;
            color: #CD853F;
        }
        
        .custom-modal-body .input-field input:focus + label,
        .custom-modal-body .input-field textarea:focus + label,
        .custom-modal-body .input-field select:focus + label {
            color: #CD853F;
        }
        
        .custom-modal-body .input-field input:focus,
        .custom-modal-body .input-field textarea:focus,
        .custom-modal-body .input-field select:focus {
            border-bottom: 2px solid #CD853F;
            box-shadow: 0 1px 0 0 #CD853F;
        }
        
        .custom-modal-body .file-field .btn-modern {
            background-color: #CD853F;
            border-radius: 4px;
            padding: 0 20px;
            height: 36px;
            line-height: 36px;
            font-size: 0.9rem;
        }
        
        .custom-modal-body .file-field .btn-modern:hover {
            background-color: #B8860B;
        }
        
        .custom-modal-body .file-field .btn-modern span {
            color: white;
            font-weight: 500;
        }
        
        .close-modal {
            color: white;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            line-height: 1;
        }
        
        .close-modal:hover,
        .close-modal:focus {
            opacity: 0.7;
        }
        
        .content-card {
            background: white;
            border-radius: 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            margin-bottom: 20px;
            overflow: hidden;
            border: 1px solid #e9ecef;
        }
        
        .card-header {
            background: #f8f9fa;
            padding: 20px 25px;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .card-title {
            color: #333;
            font-size: 1.2rem;
            font-weight: 600;
            margin: 0;
            display: flex;
            align-items: center;
        }
        
        .card-title i {
            margin-right: 10px;
            font-size: 1.1rem;
            color: #CD853F;
        }
        
        .card-body {
            padding: 25px;
        }
        
        .action-buttons {
            display: flex;
            gap: 10px;
        }
        
        .btn-action {
            background: #CD853F;
            border: none;
            color: white;
            padding: 8px 16px;
            border-radius: 4px;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.3s ease;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        
        .btn-action:hover {
            background: #B8860B;
            color: white;
            text-decoration: none;
        }
        
        .btn-action:focus {
            outline: none;
            box-shadow: 0 0 0 2px rgba(205, 133, 63, 0.3);
        }
        
        .btn-action.secondary {
            background: #6c757d;
        }
        
        .btn-action.secondary:hover {
            background: #5a6268;
        }
        
        .modern-table {
            background: white;
            border-radius: 0;
            overflow: hidden;
            box-shadow: none;
            border: 1px solid #e9ecef;
        }
        
        .modern-table thead {
            background: #f8f9fa;
        }
        
        .modern-table thead th {
            color: #333;
            font-weight: 600;
            border: none;
            padding: 15px;
            font-size: 0.9rem;
            border-bottom: 1px solid #e9ecef;
        }
        
        .modern-table tbody tr {
            transition: all 0.3s ease;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .modern-table tbody tr:hover {
            background: #f8f9fa;
        }
        
        .modern-table tbody td {
            padding: 15px;
            border: none;
            vertical-align: middle;
        }
        
        .form-input {
            border: 1px solid #e1e5e9;
            border-radius: 4px;
            padding: 8px 12px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            width: 100%;
            background: white;
        }
        
        .form-input:focus {
            border-color: #CD853F;
            box-shadow: 0 0 0 2px rgba(205, 133, 63, 0.1);
            background: white;
            outline: none;
        }
        
        .btn-modern {
            background: #CD853F;
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 4px;
            font-weight: 500;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .btn-modern:hover {
            background: #B8860B;
        }
        
        .btn-success {
            background: #8B4513;
        }
        
        .btn-success:hover {
            background: #A0522D;
        }
        
        .image-preview {
            width: 60px;
            height: 60px;
            border-radius: 4px;
            object-fit: cover;
            border: 1px solid #e1e5e9;
            transition: all 0.3s ease;
        }
        
        .image-preview:hover {
            border-color: #CD853F;
        }
        
        .no-image {
            width: 60px;
            height: 60px;
            border-radius: 4px;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #999;
            font-size: 0.8rem;
            border: 1px dashed #ccc;
        }
        
        .alert {
            padding: 12px 16px;
            border-radius: 4px;
            margin-bottom: 15px;
            border-left: 3px solid;
        }
        
        .alert-info {
            background: #f5f5dc;
            border-left-color: #CD853F;
            color: #8B4513;
        }
        
        @media (max-width: 768px) {
            .admin-container {
                padding: 10px;
            }
            
            .page-title {
                font-size: 2rem;
            }
            
            .modern-table {
                font-size: 0.9rem;
            }
            
            .modern-table thead th,
            .modern-table tbody td {
                padding: 15px 10px;
            }
        }
    </style>
</head>
<body>

  <!-- MAIN -->
  <div id="main-wrapper">
    <div class="left-sidebar">
      <div class="scroll-sidebar">
        <?php 
        $current_page = 'menu-management';
        include 'includes/sidebar.php'; 
        ?>
      </div>
    </div>

    <div class="page-wrapper">
      <div class="container-fluid">
        <div class="page-header">
          <h1 class="page-title">
            <i class="fa fa-utensils"></i>
            Menu Management
          </h1>
          <div style="background: #CD853F; color: white; padding: 10px; margin: 10px 0; border-radius: 4px;">
            <strong>Admin Status:</strong> Logged in as <?php echo htmlspecialchars($name); ?> (<?php echo htmlspecialchars($role); ?>)
            <br><small>Session ID: <?php echo session_id(); ?> | Admin SID: <?php echo $_SESSION['admin_sid'] ?? 'Not set'; ?></small>
          </div>
        </div>

        <div class="content-card">
              <div class="card-header">
                <h2 class="card-title">
                  <i class="fas fa-edit"></i>
                  Edit Menu Items
                </h2>
                <div class="action-buttons">
                  <button type="button" class="btn-action" onclick="openAddItemModal()">
                    <i class="fas fa-plus"></i> Add New Item
                  </button>
                  <button type="button" class="btn-action secondary" onclick="openAddCategoryModal()">
                    <i class="fas fa-tags"></i> Add Category
                  </button>
                </div>
              </div>
              <div class="card-body">
                <div class="alert alert-info">
                  <i class="fas fa-info-circle"></i>
                  <strong>Tip:</strong> Click on any field to edit the menu item details. Changes will be saved when you click "Save Changes".
                </div>
                
                <form method="post" action="routers/menu-router.php">
                  <div class="modern-table">
                    <table class="responsive-table">
                      <thead>
                        <tr>
                          <th><i class="fas fa-tag"></i> Item Name</th>
                          <th><i class="fas fa-dollar-sign"></i> Price</th>
                          <th><i class="fas fa-image"></i> Image Preview</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        $result = mysqli_query($con, "SELECT * FROM items");
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>";
                            echo "<td><input type='text' class='form-input' name='" . $row['id'] . "_name' value='" . htmlspecialchars($row['name'] ?? '', ENT_QUOTES) . "' placeholder='Enter item name'></td>";
                            echo "<td><input type='number' class='form-input' name='" . $row['id'] . "_price' value='" . htmlspecialchars($row['price'] ?? '', ENT_QUOTES) . "' placeholder='Enter price' step='0.01' min='0'></td>";
                            // Check if image column exists and has data
                            if (isset($row['image']) && !empty($row['image'])) {
                                echo "<td><img src='data:image/jpeg;base64," . base64_encode($row['image']) . "' class='image-preview' alt='Item image'></td>";
                            } else {
                                echo "<td><div class='no-image'><i class='fas fa-image'></i><br>No Image</div></td>";
                            }
                            echo "</tr>";
                        }
                        ?>
                      </tbody>
                    </table>
                  </div>
                  <div style="text-align: right; margin-top: 20px;">
                    <button class="btn-modern" type="submit" name="action">
                      <i class="fas fa-save"></i> Save Changes
                    </button>
                  </div>
                </form>
              </div>
            </div>


        </div>
      </div>
    </div>
  </div>

  <!-- Add New Item Modal -->
  <div id="addItemModal" class="custom-modal">
    <div class="custom-modal-content">
      <div class="custom-modal-header">
        <span class="close-modal" onclick="closeAddItemModal()">&times;</span>
        <h4><i class="fas fa-plus-circle"></i> Add New Menu Item</h4>
      </div>
      <div class="custom-modal-body">
        <form method="post" action="routers/add-item.php" enctype="multipart/form-data" id="addItemForm">
        <!-- Item Name - Full Width -->
        <div class="row">
          <div class="col s12">
            <div class="input-field">
              <input type="text" id="item_name" name="name" required>
              <label for="item_name"><i class="fas fa-tag"></i> Item Name</label>
            </div>
          </div>
        </div>
        
        <!-- Description - Full Width -->
        <div class="row">
          <div class="col s12">
            <div class="input-field">
              <textarea id="item_description" name="description" class="materialize-textarea"></textarea>
              <label for="item_description"><i class="fas fa-align-left"></i> Description</label>
            </div>
          </div>
        </div>
        
        <!-- Status and Price - Side by Side -->
        <div class="row">
          <div class="col s12 m6">
            <div class="input-field">
              <label><i class="fas fa-toggle-on"></i> Status</label>
              <select id="item_status" name="status" required class="browser-default">
                <option value="available" selected>Available</option>
                <option value="unavailable">Unavailable</option>
              </select>
            </div>
          </div>
          <div class="col s12 m6">
            <div class="input-field">
              <input type="number" id="item_price" name="price" step="0.01" min="0" required>
              <label for="item_price"><i class="fas fa-dollar-sign"></i> Price</label>
            </div>
          </div>
        </div>
        
        <!-- Category and File Upload - Side by Side -->
        <div class="row">
          <div class="col s12 m6">
            <div class="input-field">
              <label><i class="fas fa-folder"></i> Category</label>
              <select id="item_category" name="category" required class="browser-default">
                <option value="" disabled selected>Choose Category</option>
                <?php
                // Fetch categories from database
                $category_result = mysqli_query($con, "SELECT * FROM categories ORDER BY name");
                while ($category_row = mysqli_fetch_assoc($category_result)) {
                    echo "<option value='" . $category_row['id'] . "'>" . htmlspecialchars($category_row['name']) . "</option>";
                }
                ?>
              </select>
            </div>
          </div>
          <div class="col s12 m6">
            <div class="input-field">
              <label><i class="fas fa-upload"></i> Choose File</label>
              <input type="file" name="image" accept="image/*" required style="padding: 8px 0;">
            </div>
          </div>
        </div>
        </form>
      </div>
      <div class="custom-modal-footer">
        <button type="button" class="btn-action secondary" onclick="closeAddItemModal()">Cancel</button>
        <button type="button" class="btn-action" onclick="submitAddItemForm()">Add Item</button>
      </div>
    </div>
  </div>

  <!-- Add Category Modal -->
  <div id="addCategoryModal" class="custom-modal">
    <div class="custom-modal-content">
      <div class="custom-modal-header">
        <span class="close-modal" onclick="closeAddCategoryModal()">&times;</span>
        <h4><i class="fas fa-tags"></i> Add New Category</h4>
      </div>
      <div class="custom-modal-body">
        <form method="post" action="routers/add-category.php" id="addCategoryForm">
        <div class="row">
          <div class="col s12">
            <div class="input-field">
              <input type="text" id="category_name" name="category_name" required>
              <label for="category_name">Category Name</label>
            </div>
          </div>
        </div>
        </form>
      </div>
      <div class="custom-modal-footer">
        <button type="button" class="btn-action secondary" onclick="closeAddCategoryModal()">Cancel</button>
        <button type="button" class="btn-action" onclick="submitAddCategoryForm()">Add Category</button>
      </div>
    </div>
  </div>

  <!-- SCRIPTS -->
  <script src="js/plugins/jquery-1.11.2.min.js"></script>
  <script src="js/materialize.min.js"></script>
  <script src="js/plugins/data-tables/js/jquery.dataTables.min.js"></script>
  <script src="js/plugins/data-tables/data-tables-script.js"></script>
  
  <script>
    // Initialize Materialize components when DOM is ready
    $(document).ready(function() {
      // Initialize all select dropdowns
      $('select').formSelect();
      
      // Initialize textareas
      $('.materialize-textarea').characterCounter();
      
      console.log('Materialize initialized');
    });
  </script>

  <!-- FOOTER -->
  <?php include 'includes/footer.php'; ?>

</body>
</html>

<?php
} else {
    // Redirect customer or not logged in
    if (isset($_SESSION['customer_sid']) && $_SESSION['customer_sid'] == session_id()) {
        header("Location: index.php");
        exit();
    } else {
        header("Location: login.php");
        exit();
    }
}
?>
