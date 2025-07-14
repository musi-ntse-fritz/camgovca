<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit();
}

$pdo = getDBConnection();
if (!$pdo) {
    die('Database connection failed');
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    
    try {
        switch ($action) {
            case 'update_user_role':
                $user_id = $_POST['user_id'] ?? '';
                $new_role = $_POST['new_role'] ?? '';
                $new_status = $_POST['new_status'] ?? 'active';
                
                if (empty($user_id) || empty($new_role)) {
                    $error_message = 'User ID and role are required.';
                } else {
                    $stmt = $pdo->prepare("UPDATE users SET user_type = ?, status = ? WHERE user_id = ?");
                    $stmt->execute([$new_role, $new_status, $user_id]);
                    $success_message = 'User role updated successfully!';
                }
                break;
                
            case 'add_custom_role':
                $role_name = $_POST['role_name'] ?? '';
                $role_key = $_POST['role_key'] ?? '';
                $role_permissions = $_POST['permissions'] ?? [];
                $role_description = $_POST['role_description'] ?? '';
                
                if (empty($role_name) || empty($role_key)) {
                    $error_message = 'Role name and key are required.';
                } else {
                    // Store custom role in system_settings (for demo purposes)
                    $permissions_json = json_encode($role_permissions);
                    $stmt = $pdo->prepare("INSERT INTO system_settings (setting_key, setting_value, setting_type, description) VALUES (?, ?, 'json', ?)");
                    $stmt->execute(['custom_role_' . $role_key, $permissions_json, $role_description]);
                    $success_message = 'Custom role added successfully!';
                }
                break;
                
            case 'delete_user':
                $user_id = $_POST['user_id'] ?? '';
                
                if (empty($user_id)) {
                    $error_message = 'User ID is required.';
                } else {
                    // Soft delete by setting status to inactive
                    $stmt = $pdo->prepare("UPDATE users SET status = 'inactive', updated_at = NOW() WHERE user_id = ?");
                    $stmt->execute([$user_id]);
                    $success_message = 'User deactivated successfully!';
                }
                break;
                
            case 'add_user':
                $first_name = $_POST['first_name'] ?? '';
                $last_name = $_POST['last_name'] ?? '';
                $email = $_POST['email'] ?? '';
                $username = $_POST['username'] ?? '';
                $password = $_POST['password'] ?? '';
                $confirm_password = $_POST['confirm_password'] ?? '';
                $user_type = $_POST['user_type'] ?? 'client';
                $status = $_POST['status'] ?? 'active';
                
                if (empty($first_name) || empty($last_name) || empty($email) || empty($username) || empty($password)) {
                    $error_message = 'All fields are required.';
                } elseif ($password !== $confirm_password) {
                    $error_message = 'Passwords do not match.';
                } else {
                    // Check if email already exists
                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
                    $stmt->execute([$email]);
                    if ($stmt->fetchColumn() > 0) {
                        $error_message = 'Email already exists.';
                    } else {
                        // Check if username already exists
                        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
                        $stmt->execute([$username]);
                        if ($stmt->fetchColumn() > 0) {
                            $error_message = 'Username already exists.';
                        } else {
                            // Hash password and create user
                            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                            $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, username, password, user_type, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
                            $stmt->execute([$first_name, $last_name, $email, $username, $hashed_password, $user_type, $status]);
                            $success_message = 'User added successfully!';
                        }
                    }
                }
                break;
        }
    } catch (Exception $e) {
        $error_message = 'Error: ' . $e->getMessage();
    }
}

// Get users with their roles
$stmt = $pdo->query("SELECT user_id, username, email, user_type, status, created_at, first_name, last_name FROM users ORDER BY created_at DESC");
$users = $stmt->fetchAll();

// Get custom roles from system_settings
$stmt = $pdo->prepare("SELECT setting_key, setting_value, description FROM system_settings WHERE setting_key LIKE 'custom_role_%'");
$stmt->execute();
$custom_roles = $stmt->fetchAll();

// Define available roles and permissions
$roles = [
    'admin' => [
        'name' => 'Administrator',
        'permissions' => ['all'],
        'description' => 'Full system access with all administrative privileges'
    ],
    'operator' => [
        'name' => 'Certificate Operator',
        'permissions' => ['certificates', 'users', 'organizations'],
        'description' => 'Can manage certificates and basic user operations'
    ],
    'client' => [
        'name' => 'Client',
        'permissions' => ['own_certificates', 'own_profile'],
        'description' => 'Can manage own certificates and profile only'
    ],
    'ra_operator' => [
        'name' => 'RA Operator',
        'permissions' => ['certificates', 'organizations', 'audit_logs'],
        'description' => 'Registration Authority operator with limited access'
    ]
];

// Add custom roles
foreach ($custom_roles as $custom_role) {
    $role_key = str_replace('custom_role_', '', $custom_role['setting_key']);
    $permissions = json_decode($custom_role['setting_value'], true);
    $roles[$role_key] = [
        'name' => ucfirst(str_replace('_', ' ', $role_key)),
        'permissions' => $permissions,
        'description' => $custom_role['description'],
        'custom' => true
    ];
}

$permissions = [
    'all' => 'All Permissions',
    'certificates' => 'Manage Certificates',
    'users' => 'Manage Users',
    'organizations' => 'Manage Organizations',
    'audit_logs' => 'View Audit Logs',
    'system_settings' => 'System Settings',
    'own_certificates' => 'Own Certificates',
    'own_profile' => 'Own Profile',
    'ra_management' => 'RA Management',
    'certificate_requests' => 'Certificate Requests',
    'reports' => 'View Reports'
];

// Get role statistics
$role_stats = [];
foreach ($roles as $role_key => $role) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE user_type = ? AND status = 'active'");
    $stmt->execute([$role_key]);
    $role_stats[$role_key] = $stmt->fetchColumn();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CamGovCA Admin - User Roles & Permissions</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: #f4f6f9;
        }
        .header {
            background: #667eea;
            color: white;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .nav-links {
            display: flex;
            gap: 20px;
        }
        .nav-links a {
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            background: rgba(255,255,255,0.2);
            border-radius: 5px;
        }
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        .btn {
            background: #667eea;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }
        .btn-success {
            background: #28a745;
        }
        .btn-warning {
            background: #ffc107;
            color: #212529;
        }
        .btn-danger {
            background: #dc3545;
        }
        .btn-small {
            padding: 5px 10px;
            font-size: 12px;
        }
        
        /* Tab Navigation */
        .tab-navigation {
            display: flex;
            background: white;
            border-radius: 10px 10px 0 0;
            overflow: hidden;
            margin-bottom: 0;
        }
        .tab-button {
            flex: 1;
            padding: 15px 20px;
            background: #f8f9fa;
            border: none;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
            color: #666;
            transition: all 0.3s ease;
        }
        .tab-button.active {
            background: #667eea;
            color: white;
        }
        .tab-button:hover {
            background: #5a6fd8;
            color: white;
        }
        
        /* Tab Content */
        .tab-content {
            display: none;
            background: white;
            border-radius: 0 0 10px 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 20px;
        }
        .tab-content.active {
            display: block;
        }
        
        /* Statistics Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        .stat-number {
            font-size: 36px;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 10px;
        }
        .stat-label {
            color: #666;
            font-size: 14px;
        }
        
        /* Roles Grid */
        .roles-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }
        .roles-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 25px;
        }
        .roles-card h3 {
            margin-top: 0;
            color: #333;
            border-bottom: 2px solid #667eea;
            padding-bottom: 10px;
        }
        .role-item {
            background: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 15px;
        }
        .role-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        .role-name {
            font-weight: bold;
            color: #333;
            font-size: 16px;
        }
        .role-type {
            background: #667eea;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
        }
        .role-custom {
            background: #28a745;
        }
        .role-description {
            color: #666;
            margin-bottom: 10px;
        }
        .permissions-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .permission-item {
            background: #e9ecef;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            margin: 2px;
            display: inline-block;
        }
        
        /* Tables */
        .table-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        .table th,
        .table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        .table th {
            background: #f8f9fa;
            font-weight: bold;
            color: #333;
        }
        .user-type {
            background: #e9ecef;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
        }
        .status-active {
            color: #28a745;
            font-weight: bold;
        }
        .status-inactive {
            color: #dc3545;
            font-weight: bold;
        }
        .status-pending {
            color: #ffc107;
            font-weight: bold;
        }
        .actions {
            display: flex;
            gap: 10px;
        }
        
        /* Forms */
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .form-row {
            display: flex;
            gap: 15px;
        }
        .form-row .form-group {
            flex: 1;
        }
        .checkbox-group {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 10px;
            margin-top: 10px;
        }
        .checkbox-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        /* Messages */
        .message {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        /* Modals */
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
            background-color: white;
            margin: 5% auto;
            padding: 20px;
            border-radius: 10px;
            width: 80%;
            max-width: 600px;
            max-height: 80vh;
            overflow-y: auto;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        .close:hover {
            color: #000;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>User Roles & Permissions</h1>
        <div class="nav-links">
            <a href="dashboard.php">Dashboard</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>
    
    <div class="container">
        <?php if (isset($success_message)): ?>
            <div class="message success">✅ <?php echo htmlspecialchars($success_message); ?></div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <div class="message error">❌ <?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>
        
        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?php echo count($users); ?></div>
                <div class="stat-label">Total Users</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo count(array_filter($users, function($user) { return $user['status'] === 'active'; })); ?></div>
                <div class="stat-label">Active Users</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo count($roles); ?></div>
                <div class="stat-label">Available Roles</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo count($permissions); ?></div>
                <div class="stat-label">Total Permissions</div>
            </div>
        </div>
        
        <!-- Tab Navigation -->
        <div class="tab-navigation">
            <button class="tab-button active" onclick="showTab('overview')">Overview</button>
            <button class="tab-button" onclick="showTab('users')">User Management</button>
            <button class="tab-button" onclick="showTab('roles')">Role Management</button>
            <button class="tab-button" onclick="showTab('permissions')">Permissions</button>
        </div>
        
        <!-- Overview Tab -->
        <div id="overview" class="tab-content active">
            <h2>System Overview</h2>
            <div class="roles-grid">
                <div class="roles-card">
                    <h3>Role Distribution</h3>
                    <?php foreach ($role_stats as $role_key => $count): ?>
                    <div class="role-item">
                        <div class="role-header">
                            <span class="role-name"><?php echo htmlspecialchars($roles[$role_key]['name']); ?></span>
                            <span class="role-type <?php echo isset($roles[$role_key]['custom']) ? 'role-custom' : ''; ?>"><?php echo htmlspecialchars($role_key); ?></span>
                        </div>
                        <div class="role-description"><?php echo htmlspecialchars($roles[$role_key]['description']); ?></div>
                        <div><strong>Users: <?php echo $count; ?></strong></div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="roles-card">
                    <h3>Recent Users</h3>
                    <div class="table-container">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (array_slice($users, 0, 5) as $user): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></td>
                                    <td><span class="user-type"><?php echo htmlspecialchars($roles[$user['user_type']]['name'] ?? $user['user_type']); ?></span></td>
                                    <td><span class="status-<?php echo $user['status']; ?>"><?php echo ucfirst($user['status']); ?></span></td>
                                    <td><?php echo date('M j, Y', strtotime($user['created_at'])); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Users Tab -->
        <div id="users" class="tab-content">
            <div class="page-header">
                <h2>User Management</h2>
                <button onclick="openAddUserModal()" class="btn btn-success">Add New User</button>
            </div>
            
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td>
                                <span class="user-type"><?php echo htmlspecialchars($roles[$user['user_type']]['name'] ?? $user['user_type']); ?></span>
                            </td>
                            <td>
                                <span class="status-<?php echo $user['status']; ?>">
                                    <?php echo ucfirst(htmlspecialchars($user['status'])); ?>
                                </span>
                            </td>
                            <td><?php echo date('M j, Y', strtotime($user['created_at'])); ?></td>
                            <td>
                                <div class="actions">
                                    <button onclick="editUserRole(<?php echo $user['user_id']; ?>, '<?php echo $user['user_type']; ?>', '<?php echo $user['status']; ?>')" class="btn btn-small btn-warning">Edit Role</button>
                                    <button onclick="deleteUser(<?php echo $user['user_id']; ?>, '<?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>')" class="btn btn-small btn-danger">Delete</button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Roles Tab -->
        <div id="roles" class="tab-content">
            <div class="page-header">
                <h2>Role Management</h2>
                <button onclick="openAddRoleModal()" class="btn btn-success">Add Custom Role</button>
            </div>
            
            <div class="roles-grid">
                <div class="roles-card">
                    <h3>System Roles</h3>
                    <?php foreach ($roles as $role_key => $role): ?>
                    <?php if (!isset($role['custom'])): ?>
                    <div class="role-item">
                        <div class="role-header">
                            <span class="role-name"><?php echo htmlspecialchars($role['name']); ?></span>
                            <span class="role-type"><?php echo htmlspecialchars($role_key); ?></span>
                        </div>
                        <div class="role-description"><?php echo htmlspecialchars($role['description']); ?></div>
                        <div class="permissions-list">
                            <?php foreach ($role['permissions'] as $permission): ?>
                                <span class="permission-item"><?php echo htmlspecialchars($permissions[$permission] ?? $permission); ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php endforeach; ?>
                </div>
                
                <div class="roles-card">
                    <h3>Custom Roles</h3>
                    <?php 
                    $has_custom_roles = false;
                    foreach ($roles as $role_key => $role): 
                        if (isset($role['custom'])):
                            $has_custom_roles = true;
                    ?>
                    <div class="role-item">
                        <div class="role-header">
                            <span class="role-name"><?php echo htmlspecialchars($role['name']); ?></span>
                            <span class="role-type role-custom"><?php echo htmlspecialchars($role_key); ?></span>
                        </div>
                        <div class="role-description"><?php echo htmlspecialchars($role['description']); ?></div>
                        <div class="permissions-list">
                            <?php foreach ($role['permissions'] as $permission): ?>
                                <span class="permission-item"><?php echo htmlspecialchars($permissions[$permission] ?? $permission); ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php 
                        endif;
                    endforeach; 
                    if (!$has_custom_roles):
                    ?>
                    <p style="color: #666; text-align: center; padding: 20px;">No custom roles created yet.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Permissions Tab -->
        <div id="permissions" class="tab-content">
            <h2>Permission Definitions</h2>
            <div class="roles-grid">
                <div class="roles-card">
                    <h3>Available Permissions</h3>
                    <?php foreach ($permissions as $perm_key => $perm_name): ?>
                    <div class="role-item">
                        <div class="role-header">
                            <span class="role-name"><?php echo htmlspecialchars($perm_name); ?></span>
                        </div>
                        <div class="role-description">
                            <?php
                            switch ($perm_key) {
                                case 'all':
                                    echo 'Full system access including all administrative functions';
                                    break;
                                case 'certificates':
                                    echo 'Can create, view, edit, and revoke certificates';
                                    break;
                                case 'users':
                                    echo 'Can manage user accounts and profiles';
                                    break;
                                case 'organizations':
                                    echo 'Can manage organization information';
                                    break;
                                case 'audit_logs':
                                    echo 'Can view system audit logs';
                                    break;
                                case 'system_settings':
                                    echo 'Can modify system configuration';
                                    break;
                                case 'own_certificates':
                                    echo 'Can manage only their own certificates';
                                    break;
                                case 'own_profile':
                                    echo 'Can modify only their own profile';
                                    break;
                                case 'ra_management':
                                    echo 'Can manage Registration Authorities';
                                    break;
                                case 'certificate_requests':
                                    echo 'Can process certificate requests';
                                    break;
                                case 'reports':
                                    echo 'Can view system reports';
                                    break;
                                default:
                                    echo 'Permission description not available';
                            }
                            ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="roles-card">
                    <h3>Permission Matrix</h3>
                    <div class="table-container">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Role</th>
                                    <th>Permissions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($roles as $role_key => $role): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($role['name']); ?></strong></td>
                                    <td>
                                        <?php foreach ($role['permissions'] as $permission): ?>
                                            <span class="permission-item"><?php echo htmlspecialchars($permissions[$permission] ?? $permission); ?></span>
                                        <?php endforeach; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit User Role Modal -->
    <div id="editUserModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('editUserModal')">&times;</span>
            <h3>Edit User Role</h3>
            <form method="POST">
                <input type="hidden" name="action" value="update_user_role">
                <input type="hidden" name="user_id" id="edit_user_id">
                
                <div class="form-group">
                    <label for="new_role">Role</label>
                    <select id="new_role" name="new_role" required>
                        <?php foreach ($roles as $role_key => $role): ?>
                        <option value="<?php echo $role_key; ?>"><?php echo htmlspecialchars($role['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="new_status">Status</label>
                    <select id="new_status" name="new_status" required>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="suspended">Suspended</option>
                    </select>
                </div>
                
                <button type="submit" class="btn btn-success">Update User</button>
                <button type="button" onclick="closeModal('editUserModal')" class="btn">Cancel</button>
            </form>
        </div>
    </div>

    <!-- Add Custom Role Modal -->
    <div id="addRoleModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('addRoleModal')">&times;</span>
            <h3>Add Custom Role</h3>
            <form method="POST">
                <input type="hidden" name="action" value="add_custom_role">
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="role_name">Role Name <span style="color: red;">*</span></label>
                        <input type="text" id="role_name" name="role_name" required>
                    </div>
                    <div class="form-group">
                        <label for="role_key">Role Key <span style="color: red;">*</span></label>
                        <input type="text" id="role_key" name="role_key" required placeholder="e.g., custom_operator">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="role_description">Description</label>
                    <textarea id="role_description" name="role_description" rows="3"></textarea>
                </div>
                
                <div class="form-group">
                    <label>Permissions</label>
                    <div class="checkbox-group">
                        <?php foreach ($permissions as $perm_key => $perm_name): ?>
                        <div class="checkbox-item">
                            <input type="checkbox" id="perm_<?php echo $perm_key; ?>" name="permissions[]" value="<?php echo $perm_key; ?>">
                            <label for="perm_<?php echo $perm_key; ?>"><?php echo htmlspecialchars($perm_name); ?></label>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-success">Create Role</button>
                <button type="button" onclick="closeModal('addRoleModal')" class="btn">Cancel</button>
            </form>
        </div>
    </div>

    <!-- Add User Modal -->
    <div id="addUserModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('addUserModal')">&times;</span>
            <h3>Add New User</h3>
            <form method="POST">
                <input type="hidden" name="action" value="add_user">
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="first_name">First Name <span style="color: red;">*</span></label>
                        <input type="text" id="first_name" name="first_name" required>
                    </div>
                    <div class="form-group">
                        <label for="last_name">Last Name <span style="color: red;">*</span></label>
                        <input type="text" id="last_name" name="last_name" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="email">Email <span style="color: red;">*</span></label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="username">Username <span style="color: red;">*</span></label>
                        <input type="text" id="username" name="username" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="password">Password <span style="color: red;">*</span></label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirm Password <span style="color: red;">*</span></label>
                        <input type="password" id="confirm_password" name="confirm_password" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="user_type">Role</label>
                        <select id="user_type" name="user_type" required>
                            <?php foreach ($roles as $role_key => $role): ?>
                            <option value="<?php echo $role_key; ?>"><?php echo htmlspecialchars($role['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="user_status">Status</label>
                        <select id="user_status" name="status" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="suspended">Suspended</option>
                        </select>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-success">Add User</button>
                <button type="button" onclick="closeModal('addUserModal')" class="btn">Cancel</button>
            </form>
        </div>
    </div>

    <script>
        function showTab(tabName) {
            // Hide all tab contents
            const tabContents = document.getElementsByClassName('tab-content');
            for (let content of tabContents) {
                content.classList.remove('active');
            }
            
            // Remove active class from all tab buttons
            const tabButtons = document.getElementsByClassName('tab-button');
            for (let button of tabButtons) {
                button.classList.remove('active');
            }
            
            // Show selected tab content
            document.getElementById(tabName).classList.add('active');
            
            // Add active class to clicked button
            event.target.classList.add('active');
        }
        
        function editUserRole(userId, currentRole, currentStatus) {
            document.getElementById('edit_user_id').value = userId;
            document.getElementById('new_role').value = currentRole;
            document.getElementById('new_status').value = currentStatus;
            document.getElementById('editUserModal').style.display = 'block';
        }
        
        function deleteUser(userId, userName) {
            if (confirm('Are you sure you want to delete user "' + userName + '"? This action cannot be undone.')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="delete_user">
                    <input type="hidden" name="user_id" value="${userId}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }
        
        function openAddUserModal() {
            document.getElementById('addUserModal').style.display = 'block';
        }
        
        function openAddRoleModal() {
            document.getElementById('addRoleModal').style.display = 'block';
        }
        
        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }
        
        // Close modals when clicking outside
        window.onclick = function(event) {
            const modals = document.getElementsByClassName('modal');
            for (let modal of modals) {
                if (event.target === modal) {
                    modal.style.display = 'none';
                }
            }
        }
        
        // Auto-generate role key from role name
        document.getElementById('role_name').addEventListener('input', function() {
            const roleName = this.value;
            const roleKey = roleName.toLowerCase().replace(/[^a-z0-9]/g, '_');
            document.getElementById('role_key').value = 'custom_' + roleKey;
        });
        
        // Password confirmation validation
        document.getElementById('confirm_password').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;
            
            if (password !== confirmPassword) {
                this.setCustomValidity('Passwords do not match');
            } else {
                this.setCustomValidity('');
            }
        });
        
        document.getElementById('password').addEventListener('input', function() {
            const confirmPassword = document.getElementById('confirm_password');
            if (confirmPassword.value) {
                if (this.value !== confirmPassword.value) {
                    confirmPassword.setCustomValidity('Passwords do not match');
                } else {
                    confirmPassword.setCustomValidity('');
                }
            }
        });
    </script>
</body>
</html>