<?php
// Bootstrap PHP before any output
include 'includes/connect.php';
// Temporarily show errors while we stabilize
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (isset($_SESSION['admin_sid']) && $_SESSION['admin_sid'] == session_id()) {
    $name = isset($_SESSION['name']) ? $_SESSION['name'] : 'Admin';
    $role = isset($_SESSION['role']) ? $_SESSION['role'] : 'Administrator';

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
    <title>Menu Management - Admin Panel</title>
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
                $current_page = 'menu-management';
                include 'includes/sidebar.php'; 
                ?>
            </div>
        </div>

        <div class="page-wrapper">
            <div class="container-fluid">
                <div class="page-header">
                    <h1 class="page-title">
                        <i class="fa fa-cutlery"></i>
                        Menu Management
                    </h1>
                </div>

                <div class="content-card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col s12 m6">
                                <h4 class="card-title">Menu Items</h4>
                            </div>
                            <div class="col s12 m6" style="text-align: right;">
                                <button class="btn btn-primary-custom" onclick="openAddItemModal()">
                                    <i class="fa fa-plus"></i> Add New Item
                                </button>
                                <button class="btn btn-primary-custom" onclick="openAddCategoryModal()" style="margin-left: 10px;">
                                    <i class="fa fa-tags"></i> Add Category
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <!-- Category Tabs -->
                        <div id="categoryTabs" class="category-tabs"></div>

                        <!-- Items Grid for selected category -->
                        <div id="itemsGrid" class="items-grid"></div>
                    </div>
                </div>
            </div>

            <?php include 'includes/footer.php'; ?>
        </div>
    </div>

    <!-- Add/Edit Item Modal -->
    <div id="itemModal" class="modal">
        <div class="modal-content">
            <div class="modal-header-custom">
                <h4 id="itemModalTitle">Add New Item</h4>
                <span class="close" onclick="closeItemModal()">&times;</span>
            </div>
            <form id="itemForm" enctype="multipart/form-data">
                <input type="hidden" id="itemId" name="item_id">
                <div class="row">
                    <div class="col s12">
                        <div class="input-field">
                            <input type="text" id="itemName" name="name" class="form-control-custom" placeholder="Item Name" required>
                        </div>
                    </div>
                    
                    <div class="col s12 m6">
                        <div class="input-field">
                            <select id="itemStatus" name="status" class="form-control-custom" required>
                                <option value="Available">Available</option>
                                <option value="Unavailable">Unavailable</option>
                            </select>
                        </div>
                    </div>
                    <div class="col s12 m6">
                        <div class="input-field">
                            <input type="number" id="itemPrice" name="price" class="form-control-custom" placeholder="Price" min="0" step="0.01" required>
                        </div>
                    </div>
                    <div class="col s12">
                        <div class="inline-two">
                            <div class="field">
                                <div class="input-field">
                                    <select id="itemCategory" name="category_id" class="form-control-custom" required>
                                        <option value="">Select Category</option>
                                    </select>
                                </div>
                            </div>
                            <div class="field">
                                <div class="input-field">
                                    <input type="file" id="itemImage" name="image" accept="image/*" class="form-control-custom">
                                    <div id="imagePreview" style="margin-top: 10px;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="text-align: right; margin-top: 20px;">
                    <button type="button" class="btn btn-secondary" onclick="closeItemModal()" style="background: #ff6b6b; color: white; border: none; padding: 10px 20px; margin-right: 10px;">Cancel</button>
                    <button type="submit" class="btn btn-primary" style="background: #ff6b6b; color: white; border: none; padding: 10px 20px;">Save</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add Category Modal -->
    <div id="categoryModal" class="modal">
        <div class="modal-content">
            <div class="modal-header-custom">
                <h4>Manage Categories</h4>
                <span class="close" onclick="closeCategoryModal()">&times;</span>
            </div>
            
            <!-- Add New Category Section -->
            <div class="section">
                <h5 style="color: #ff6b6b; margin-bottom: 15px;">
                    <i class="fa fa-plus-circle"></i> Add New Category
                </h5>
                <form id="categoryForm">
                    <div class="row">
                        <div class="col s12">
                            <div class="input-field">
                                <input type="text" id="categoryName" name="name" class="form-control-custom" placeholder="Category Name" required>
                            </div>
                        </div>
                    </div>
                    <div style="text-align: right; margin-bottom: 20px;">
                        <button type="submit" class="btn btn-primary" style="background: #ff6b6b; color: white; border: none; padding: 12px 24px; border-radius: 6px; font-size: 14px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px; min-height: 40px;">
                            <i class="fa fa-plus"></i> Add Category
                        </button>
                    </div>
                </form>
            </div>

            <hr style="border: 1px solid #e0e0e0; margin: 20px 0;">

            <!-- Existing Categories Section -->
            <div class="section">
                <h5 style="color: #ff6b6b; margin-bottom: 15px;">
                    <i class="fa fa-list"></i> Existing Categories
                </h5>
                <div id="categoriesList" class="categories-list">
                    <!-- Categories will be loaded here -->
                </div>
            </div>

            <div class="modal-footer" style="text-align: right; margin-top: 20px;">
                <button type="button" class="btn btn-secondary" onclick="closeCategoryModal()" style="background: #666; color: white; border: none; padding: 10px 20px;">Close</button>
            </div>
        </div>
    </div>

    <script src="js/plugins/jquery-1.11.2.min.js"></script>
    <script src="js/materialize.min.js"></script>
    <script src="js/plugins.min.js"></script>
    <script src="js/plugins/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <script src="js/custom-script.js"></script>

    <script>
        let categories = [];
        let isEditMode = false;
        let activeCategoryId = null;

        // Initialize page
        $(document).ready(function() {
            loadCategories();
            initializeModals();
        });

        // Load categories for dropdown
        function loadCategories() {
            // Return the jqXHR promise so callers can wait for completion
            return $.ajax({
                url: 'routers/get-categories.php',
                dataType: 'json',
                cache: false
            }).done(function(response){
                if (window.console && console.log) console.log('[menu-management] categories response:', response);
                if (response && response.success) {
                    categories = response.categories || [];
                    renderCategoryTabs();
                    updateCategoryDropdown();
                    // Default to All items tab (id 0)
                    setActiveCategory(0);
                } else {
                    alert((response && response.message) ? response.message : 'Failed to load categories');
                }
            }).fail(function(xhr){
                alert('Error loading categories');
                if (window.console && console.error) console.error('[menu-management] categories error:', xhr && xhr.responseText);
            });
        }

        // Update category dropdown
        function updateCategoryDropdown() {
            const $select = $('#itemCategory');
            const el = $select.get(0);
            // Destroy existing Materialize instance (if any) before changing options
            if (window.M && M.FormSelect && el) {
                const inst = M.FormSelect.getInstance(el);
                if (inst) { inst.destroy(); }
            }
            $select.empty().append('<option value="">Select Category</option>');
            (categories || []).forEach(category => {
                $select.append(`<option value="${category.id}">${category.name}</option>`);
            });
            // Re-init Materialize select (v1.x)
            if (window.M && M.FormSelect && el) {
                M.FormSelect.init(el, { 
                    dropdownOptions: { 
                        container: document.body, 
                        coverTrigger: false, 
                        constrainWidth: false, 
                        alignment: 'left',
                        onOpenStart: function(){ document.body.classList.add('select-open'); },
                        onCloseEnd: function(){ document.body.classList.remove('select-open'); }
                    } 
                });
            }
            // Fallback for Materialize v0.x
            if (typeof $select.material_select === 'function') {
                $select.material_select();
            }
        }

        // Render category tabs
        function renderCategoryTabs() {
            const tabs = $('#categoryTabs');
            tabs.empty();
            // Add "All" tab first
            const allBtn = $('<button class="tab-btn" data-id="0">All</button>');
            allBtn.on('click', () => setActiveCategory(0));
            tabs.append(allBtn);
            // Then render real categories
            categories.forEach(cat => {
                const btn = $(`<button class="tab-btn" data-id="${cat.id}">${cat.name}</button>`);
                btn.on('click', () => setActiveCategory(cat.id));
                tabs.append(btn);
            });
            highlightActiveTab();
        }

        function highlightActiveTab() {
            $('#categoryTabs .tab-btn').each(function(){
                const id = parseInt($(this).data('id'));
                if (id === activeCategoryId) {
                    $(this).addClass('active');
                } else {
                    $(this).removeClass('active');
                }
            });
        }

        function setActiveCategory(categoryId) {
            activeCategoryId = parseInt(categoryId);
            highlightActiveTab();
            loadItemsForCategory(activeCategoryId);
        }

        // Load items for selected category
        function loadItemsForCategory(categoryId) {
            $.ajax({
                url: 'routers/get-items.php',
                data: { category_id: categoryId },
                dataType: 'json',
                cache: false
            }).done(function(response){
                if (response && response.success) {
                    renderItemsGrid(response.items);
                } else {
                    $('#itemsGrid').html('<div class="empty">No items found.</div>');
                }
            }).fail(function(xhr){
                $('#itemsGrid').html('<div class="empty">Failed to load items.</div>');
                if (window.console && console.error) console.error('[menu-management] items error:', xhr && xhr.responseText);
            });
        }

        // Render items grid
        function renderItemsGrid(items) {
            const grid = $('#itemsGrid');
            grid.empty();
            if (!items || !items.length) {
                grid.html('<div class="empty">No items in this category.</div>');
                return;
            }
            const fragment = $(document.createDocumentFragment());
            items.forEach(item => {
                const imgHtml = item.image ? `<img src="data:image/jpeg;base64,${item.image}" alt="${item.name}">` : '<div class="no-image">No Image</div>';
                const card = $(`
                    <div class="item-card" data-item-id="${item.id}">
                        <div class="item-media">${imgHtml}</div>
                        <div class="item-body">
                            <div class="item-name">${escapeHtml(item.name)}</div>
                            
                            <div class="item-meta">
                                <span class="price">₱ ${Number(item.price).toLocaleString()}</span>
                                <span class="status-badge ${item.status && item.status.toLowerCase() === 'available' ? 'status-available' : 'status-unavailable'}">${escapeHtml(item.status || '')}</span>
                            </div>
                        </div>
                        <div class="item-actions">
                            <button class="btn btn-primary-custom btn-sm" onclick="editItem(${item.id})">Edit</button>
                            <button class="btn btn-danger-custom btn-sm" onclick="deleteItem(${item.id})">Delete</button>
                        </div>
                    </div>
                `);
                fragment.append(card);
            });
            grid.append(fragment);
        }

        function escapeHtml(str){
            return String(str).replace(/[&<>"']/g, function(m) {
                return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;','\'':'&#39;'}[m]);
            });
        }

        // Initialize modals (fallback if Materialize modal plugin is unavailable)
        function initializeModals() {
            // No-op: we'll use simple show/hide to avoid dependency issues
        }

        // Open add item modal
        function openAddItemModal() {
            isEditMode = false;
            $('#itemModalTitle').text('Add New Item');
            $('#itemForm')[0].reset();
            $('#itemId').val('');
            $('#imagePreview').empty();
            // Ensure dropdown has latest categories before showing
            const ready = (categories && categories.length) ? $.Deferred().resolve().promise() : loadCategories();
            $.when(ready).always(function(){
                updateCategoryDropdown();
                $('body').addClass('modal-open');
                $('#itemModal').show();
            });
        }

        // Open edit item modal
        function editItem(itemId) {
            isEditMode = true;
            $('#itemModalTitle').text('Edit Item');
            $('#itemForm')[0].reset();
            $('#imagePreview').empty();

            $.get('routers/get-item.php', { item_id: itemId }, function(response) {
                if (!response || !response.success) {
                    alert(response && response.message ? response.message : 'Failed to load item');
                    return;
                }
                const item = response.item;
                $('#itemId').val(item.id);
                $('#itemName').val(item.name);
                
                $('#itemStatus').val(item.status || 'Available');
                $('#itemPrice').val(item.price);
                $('#itemCategory').val(item.category_id || '');

                if (item.image) {
                    $('#imagePreview').html(`<img src="data:image/jpeg;base64,${item.image}" width="100" height="100" style="object-fit: cover; border-radius: 5px;">`);
                }

                $('body').addClass('modal-open');
                $('#itemModal').show();
            }).fail(function(){
                alert('Error fetching item details');
            });
        }

        // Close item modal
        function closeItemModal() {
            $('#itemModal').hide();
            $('body').removeClass('modal-open');
        }

        // Open add category modal
        function openAddCategoryModal() {
            $('#categoryForm')[0].reset();
            loadExistingCategories();
            $('body').addClass('modal-open');
            $('#categoryModal').show();
        }

        // Close category modal
        function closeCategoryModal() {
            $('#categoryModal').hide();
            $('body').removeClass('modal-open');
        }

        // Handle item form submission
        $('#itemForm').on('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const url = isEditMode ? 'routers/update-item.php' : 'routers/add-item.php';
            
            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        showToast(response.message || 'Saved', 'success');
                        setTimeout(function(){ location.reload(); }, 600);
                    } else {
                        showToast(response.message || 'Failed to save', 'error');
                    }
                },
                error: function() {
                    showToast('An error occurred. Please try again.', 'error');
                }
            });
        });

        // Handle category form submission
        $('#categoryForm').on('submit', function(e) {
            e.preventDefault();
            
            $.post('routers/add-category.php', $(this).serialize(), function(response) {
                if (response.success) {
                    showToast(response.message || 'Category added', 'success');
                    loadCategories();
                    loadExistingCategories(); // Refresh the categories list in modal
                    $('#categoryForm')[0].reset(); // Clear the form
                } else {
                    showToast(response.message || 'Failed to add category', 'error');
                }
            });
        });

        // Load existing categories for display in modal
        function loadExistingCategories() {
            $.ajax({
                url: 'routers/get-categories.php',
                dataType: 'json',
                success: function(response) {
                    if (response && response.success) {
                        renderCategoriesList(response.categories || []);
                    } else {
                        $('#categoriesList').html('<p style="color: #999; text-align: center; padding: 20px;">No categories found</p>');
                    }
                },
                error: function() {
                    $('#categoriesList').html('<p style="color: #ff6b6b; text-align: center; padding: 20px;">Error loading categories</p>');
                }
            });
        }

        // Render categories list in modal
        function renderCategoriesList(categories) {
            if (categories.length === 0) {
                $('#categoriesList').html('<p style="color: #999; text-align: center; padding: 20px;">No categories found</p>');
                return;
            }

            let html = '<div class="categories-grid">';
            categories.forEach(function(category) {
                html += `
                    <div class="category-item" style="display: flex; justify-content: space-between; align-items: center; padding: 15px; margin: 10px 0; background: #f8f9fa; border: 1px solid #e9ecef; border-radius: 8px;">
                        <div>
                            <span style="font-weight: 500; color: #333;">${category.name}</span>
                        </div>
                        <div>
                            <button onclick="deleteCategory(${category.id}, '${category.name}')" 
                                    style="background: #dc3545; color: white; border: none; padding: 8px 12px; border-radius: 4px; cursor: pointer; font-size: 12px;">
                                <i class="fa fa-trash"></i> Delete
                            </button>
                        </div>
                    </div>
                `;
            });
            html += '</div>';
            $('#categoriesList').html(html);
        }

        // Delete category
        function deleteCategory(categoryId, categoryName) {
            showConfirm(`Delete category "${categoryName}"? This will also affect items in this category.`, 'Delete', 'Cancel', function(){
                $.post('routers/delete-category.php', {category_id: categoryId}, function(response) {
                    if (response.success) {
                        showToast(response.message || 'Category deleted', 'success');
                        loadCategories(); // Refresh main categories
                        loadExistingCategories(); // Refresh modal categories list
                    } else {
                        showToast(response.message || 'Failed to delete category', 'error');
                    }
                });
            });
        }

        // Delete item
        function deleteItem(itemId) {
            showConfirm('Delete this item?', 'Delete', 'Cancel', function(){
                $.post('routers/delete-item.php', {item_id: itemId}, function(response) {
                    if (response.success) {
                        showToast(response.message || 'Item deleted', 'success');
                        setTimeout(function(){ location.reload(); }, 600);
                    } else {
                        showToast(response.message || 'Failed to delete', 'error');
                    }
                });
            });
        }

        // Image preview
        $('#itemImage').on('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#imagePreview').html(`<img src="${e.target.result}" width="100" height="100" style="object-fit: cover; border-radius: 5px;">`);
                };
                reader.readAsDataURL(file);
            }
        });
    </script>

    <!-- Custom confirm/toast components -->
    <div id="uiConfirm" class="ui-confirm" style="display:none;">
        <div class="ui-confirm__card">
            <div class="ui-confirm__icon">&#9888;</div>
            <div class="ui-confirm__message" id="uiConfirmMsg">Are you sure?</div>
            <div class="ui-confirm__actions">
                <button type="button" id="uiConfirmCancel" class="btn btn-secondary">Cancel</button>
                <button type="button" id="uiConfirmOk" class="btn btn-primary" style="background:#ff6b6b;color:#fff;">Confirm</button>
            </div>
        </div>
    </div>
    <div id="uiToast" class="ui-toast" style="display:none;"><span id="uiToastMsg"></span></div>

    <script>
        function showConfirm(message, okText, cancelText, onConfirm){
            $('#uiConfirmMsg').text(message || 'Are you sure?');
            if (okText) $('#uiConfirmOk').text(okText);
            if (cancelText) $('#uiConfirmCancel').text(cancelText);
            const $dlg = $('#uiConfirm');
            $dlg.fadeIn(120);
            function cleanup(){
                $('#uiConfirmOk').off('click');
                $('#uiConfirmCancel').off('click');
                $(document).off('keydown.uiConfirm');
                $dlg.fadeOut(120);
            }
            $('#uiConfirmOk').on('click', function(){ cleanup(); if (typeof onConfirm==='function') onConfirm(); });
            $('#uiConfirmCancel').on('click', cleanup);
            $(document).on('keydown.uiConfirm', function(e){ if (e.key==='Escape'){ cleanup(); } });
        }

        function showToast(message, type){
            const $t = $('#uiToast');
            $('#uiToastMsg').text(message || '');
            $t.removeClass('is-error is-success').addClass(type==='error' ? 'is-error' : 'is-success');
            $t.stop(true,true).fadeIn(120);
            setTimeout(function(){ $t.fadeOut(200); }, 1500);
        }
    </script>

    <style>
        /* Prevent page shift/jump when modal opens */
        body.modal-open {
            overflow: hidden;
            padding-right: 0 !important; /* avoid scrollbar compensation shift */
        }
        /* Modal overlay and positioning */
        .modal {
            position: fixed;
            z-index: 9999;            /* sit above any footer/header */
            inset: 0;                 /* cover entire viewport */
            width: 100%;
            min-height: 100vh;        /* ensure no bottom gap */
            background-color: rgba(0,0,0,0.45);
            display: none;
            overflow: auto;           /* allow overlay to scroll if modal taller */
        }
        
        /* Modal content container */
        .modal .modal-content {
            background: #fff;
            margin: 5% auto;
            width: 90%;
            max-width: 600px;
            max-height: calc(100vh - 10% - 40px); /* keep within viewport, account for top/bottom margins */
            border-radius: 8px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.2);
            position: relative;
            animation: modalSlideIn 0.3s ease-out;
            overflow: auto;          /* scroll modal content instead of page */
        }
        
        /* Modal animation */
        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: translateY(-50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Header styling */
        .modal-header-custom {
            background: linear-gradient(90deg, #D2B48C 0%, #F5DEB3 100%);
            padding: 20px;
            border-bottom: 1px solid #eee;
            position: relative;
            border-radius: 8px 8px 0 0;
        }
        
        .modal-header-custom h4 {
            margin: 0;
            color: #333;
            font-weight: 600;
            font-size: 18px;
        }
        
        /* Close button */
        .close {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 24px;
            cursor: pointer;
            color: #333;
            font-weight: bold;
            line-height: 1;
        }
        
        .close:hover {
            color: #000;
        }
        
        /* Form styling */
        .modal-content form {
            padding: 25px;
        }
        
        /* Input field styling */
        .modal-content .input-field {
            margin-bottom: 20px;
        }
        
        .modal-content .form-control-custom {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            transition: border-color 0.3s ease;
        }
        
        .modal-content .form-control-custom:focus {
            outline: none;
            border-color: #CD853F;
            box-shadow: 0 0 0 3px rgba(205,133,63,0.1);
        }
        
        /* Button styling */
        .modal-footer {
            text-align: right;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            display: flex;
            justify-content: flex-end;
            gap: 12px;               /* spacing between Cancel and Save */
        }
        
        .modal-footer .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-left: 0;          /* gap handles spacing */
            display: inline-flex;     /* center label inside button */
            align-items: center;
            justify-content: center;
            text-align: center;
            min-height: 44px;
            min-width: 110px;
        }
        
        .modal-footer .btn-primary {
            background: linear-gradient(135deg, #ff6b6b 0%, #ff5252 100%);
            color: white;
        }
        
        .modal-footer .btn-primary:hover {
            background: linear-gradient(135deg, #ff5252 0%, #f44336 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255,107,107,0.3);
        }
        
        .modal-footer .btn-secondary {
            background: linear-gradient(135deg, #ff6b6b 0%, #ff5252 100%);
            color: white;
        }
        
        .modal-footer .btn-secondary:hover {
            background: linear-gradient(135deg, #ff5252 0%, #f44336 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255,107,107,0.3);
        }
        
        /* Status badges */
        .status-available {
            background-color: #D4EDDA;
            color: #155724;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
        }
        
        .status-unavailable {
            background-color: #F8D7DA;
            color: #721C24;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
        }
        
        /* Image preview styling */
        #imagePreview img {
            border-radius: 6px;
            border: 2px solid #ddd;
        }

        /* Category tabs */
        .category-tabs { display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 16px; }
        .category-tabs .tab-btn { 
            padding: 8px 16px; border: 1px solid #d6b38a; border-radius: 20px; background: #fff; color: #6b4f2a; cursor: pointer; 
        }
        .category-tabs .tab-btn.active { background: #D2B48C; color: #333; border-color: #D2B48C; }

        /* Items grid */
        .items-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 16px; }
        .item-card { background: #fff; border: 1px solid #eee; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.05); display:flex; flex-direction:column; }
        .item-media { height: 150px; background: #fafafa; display:flex; align-items:center; justify-content:center; }
        .item-media img { width: 100%; height: 100%; object-fit: cover; }
        .item-media .no-image { color: #999; }
        .item-body { padding: 12px 14px; flex:1; }
        .item-name { font-weight: 600; color:#333; margin-bottom:6px; }
        .item-desc { color:#666; font-size: 13px; min-height: 36px; }
        .item-meta { display:flex; align-items:center; justify-content:space-between; margin-top: 8px; }
        .item-actions { display:flex; gap: 10px; padding: 12px 14px; border-top: 1px solid #eee; }
        .empty { color:#777; padding:20px; text-align:center; }

        /* Inline two-column helper for aligning fields side-by-side */
        .inline-two { display:flex; gap:16px; align-items:flex-start; }
        .inline-two .field { flex:1; min-width:0; }

        /* Pretty confirm dialog */
        .ui-confirm { position: fixed; inset: 0; background: rgba(0,0,0,0.45); z-index: 2147483000; display:flex; align-items:center; justify-content:center; }
        .ui-confirm__card { background:#fff; width: 92%; max-width: 420px; border-radius:10px; box-shadow:0 8px 24px rgba(0,0,0,0.2); padding:20px; text-align:center; }
        .ui-confirm__icon { font-size: 28px; color:#ff6b6b; margin-bottom:8px; }
        .ui-confirm__message { color:#333; margin-bottom:16px; }
        .ui-confirm__actions { display:flex; gap:12px; justify-content:center; }
        .ui-confirm__actions .btn { min-width:120px; }

        /* Toast */
        .ui-toast { position: fixed; left:50%; transform: translateX(-50%); bottom: 24px; background:#333; color:#fff; padding:10px 16px; border-radius: 6px; z-index:2147483200; box-shadow:0 6px 18px rgba(0,0,0,0.25); }
        .ui-toast.is-success { background:#2e7d32; }
        .ui-toast.is-error { background:#c62828; }

        /* Ensure Materialize select dropdown renders above inputs inside modal */
        .select-wrapper + .dropdown-content,
        .select-dropdown,
        .dropdown-content.select-dropdown {
            position: absolute;
            z-index: 2147483647 !important; /* keep above any form controls (max-safe) */
            max-height: 220px;       /* ~5 items visible, then scroll */
            overflow-y: auto;
            overflow-x: hidden;
        }
        /* Avoid parent clipping for select wrapper */
        .select-wrapper {
            position: relative;
            overflow: visible;
        }

        /* Ensure native file input never floats above the dropdown on hover */
        .input-field input[type="file"] {
            position: relative;
            z-index: 0;
        }
        /* While select is open, push file input behind the dropdown */
        body.select-open .input-field input[type="file"],
        body.select-open .file-field input[type="file"] {
            z-index: -1 !important;
            pointer-events: none !important;
            visibility: hidden !important; /* prevent flicker on hover */
        }
        
        /* Responsive design */
        @media (max-width: 768px) {
            .modal .modal-content {
                margin: 10% auto;
                width: 95%;
                max-height: calc(100vh - 20% - 40px);
            }
            
            .modal-header-custom {
                padding: 15px;
            }
            
            .modal-content form {
                padding: 20px;
            }
        }
    </style>

</body>
</html>
