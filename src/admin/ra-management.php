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
            case 'add_ra':
                $ra_name = $_POST['ra_name'] ?? '';
                $ra_code = $_POST['ra_code'] ?? '';
                $ra_type = $_POST['ra_type'] ?? 'regional';
                $address = $_POST['address'] ?? '';
                $city = $_POST['city'] ?? '';
                $region = $_POST['region'] ?? '';
                $phone = $_POST['phone'] ?? '';
                $email = $_POST['email'] ?? '';
                $contact_person = $_POST['contact_person'] ?? '';
                
                if (empty($ra_name) || empty($ra_code)) {
                    $error_message = 'RA name and code are required.';
                } else {
                    // Check if RA code already exists
                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM registration_authorities WHERE ra_code = ?");
                    $stmt->execute([$ra_code]);
                    if ($stmt->fetchColumn() > 0) {
                        $error_message = 'RA code already exists.';
                    } else {
                        $stmt = $pdo->prepare("INSERT INTO registration_authorities (ra_name, ra_code, ra_type, address, city, region, phone, email, contact_person) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                        $stmt->execute([$ra_name, $ra_code, $ra_type, $address, $city, $region, $phone, $email, $contact_person]);
                        $success_message = 'Registration Authority added successfully!';
                    }
                }
                break;
                
            case 'update_ra':
                $ra_id = $_POST['ra_id'] ?? '';
                $ra_name = $_POST['ra_name'] ?? '';
                $ra_type = $_POST['ra_type'] ?? 'regional';
                $address = $_POST['address'] ?? '';
                $city = $_POST['city'] ?? '';
                $region = $_POST['region'] ?? '';
                $phone = $_POST['phone'] ?? '';
                $email = $_POST['email'] ?? '';
                $contact_person = $_POST['contact_person'] ?? '';
                $status = $_POST['status'] ?? 'active';
                
                if (empty($ra_name)) {
                    $error_message = 'RA name is required.';
                } else {
                    $stmt = $pdo->prepare("UPDATE registration_authorities SET ra_name = ?, ra_type = ?, address = ?, city = ?, region = ?, phone = ?, email = ?, contact_person = ?, status = ? WHERE ra_id = ?");
                    $stmt->execute([$ra_name, $ra_type, $address, $city, $region, $phone, $email, $contact_person, $status, $ra_id]);
                    $success_message = 'Registration Authority updated successfully!';
                }
                break;
                
            case 'add_operator':
                $ra_id = $_POST['ra_id'] ?? '';
                $user_id = $_POST['user_id'] ?? '';
                $role = $_POST['role'] ?? 'operator';
                
                if (empty($ra_id) || empty($user_id)) {
                    $error_message = 'RA and user are required.';
                } else {
                    // Check if user is already an operator for this RA
                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM ra_operators WHERE ra_id = ? AND user_id = ?");
                    $stmt->execute([$ra_id, $user_id]);
                    if ($stmt->fetchColumn() > 0) {
                        $error_message = 'User is already an operator for this RA.';
                    } else {
                        $stmt = $pdo->prepare("INSERT INTO ra_operators (ra_id, user_id, role) VALUES (?, ?, ?)");
                        $stmt->execute([$ra_id, $user_id, $role]);
                        $success_message = 'RA operator added successfully!';
                    }
                }
                break;
        }
    } catch (Exception $e) {
        $error_message = 'Error: ' . $e->getMessage();
    }
}

// Get all RAs
$stmt = $pdo->query("SELECT * FROM registration_authorities ORDER BY ra_name");
$ras = $stmt->fetchAll();

// Get all users for operator assignment
$stmt = $pdo->query("SELECT user_id, username, first_name, last_name, email FROM users WHERE user_type IN ('client', 'ra_operator') ORDER BY first_name, last_name");
$users = $stmt->fetchAll();

// Get RA operators
$stmt = $pdo->query("
    SELECT ro.*, u.username, u.first_name, u.last_name, u.email, ra.ra_name 
    FROM ra_operators ro 
    JOIN users u ON ro.user_id = u.user_id 
    JOIN registration_authorities ra ON ro.ra_id = ra.ra_id 
    ORDER BY ra.ra_name, u.first_name
");
$ra_operators = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CamGovCA Admin - RA Management</title>
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
        .card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            overflow: hidden;
        }
        .card-header {
            background: #f8f9fa;
            padding: 15px 20px;
            border-bottom: 1px solid #dee2e6;
            font-weight: bold;
            color: #333;
        }
        .card-body {
            padding: 20px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        .table th,
        .table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }
        .table th {
            background: #f8f9fa;
            font-weight: bold;
            color: #333;
        }
        .status-active {
            color: #28a745;
            font-weight: bold;
        }
        .status-inactive {
            color: #6c757d;
            font-weight: bold;
        }
        .status-suspended {
            color: #dc3545;
            font-weight: bold;
        }
        .ra-type {
            background: #e9ecef;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
        }
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
    </style>
</head>
<body>
    <div class="header">
        <h1>Registration Authority Management</h1>
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
                <div class="stat-number"><?php echo count($ras); ?></div>
                <div class="stat-label">Total RAs</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo count(array_filter($ras, function($ra) { return $ra['status'] === 'active'; })); ?></div>
                <div class="stat-label">Active RAs</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo count($ra_operators); ?></div>
                <div class="stat-label">RA Operators</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo count(array_filter($ras, function($ra) { return $ra['ra_type'] === 'regional'; })); ?></div>
                <div class="stat-label">Regional RAs</div>
            </div>
        </div>
        
        <div class="page-header">
            <h2>Registration Authorities</h2>
            <button onclick="openAddRAModal()" class="btn btn-success">Add New RA</button>
        </div>
        
        <!-- RAs List -->
        <div class="card">
            <div class="card-header">All Registration Authorities</div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>RA Code</th>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Location</th>
                            <th>Contact</th>
                            <th>Status</th>
                            <th>Operators</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ras as $ra): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($ra['ra_code']); ?></strong></td>
                            <td><?php echo htmlspecialchars($ra['ra_name']); ?></td>
                            <td><span class="ra-type"><?php echo ucfirst(htmlspecialchars($ra['ra_type'])); ?></span></td>
                            <td>
                                <?php echo htmlspecialchars($ra['city']); ?>, <?php echo htmlspecialchars($ra['region']); ?>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($ra['contact_person']); ?><br>
                                <small><?php echo htmlspecialchars($ra['email']); ?></small>
                            </td>
                            <td>
                                <span class="status-<?php echo $ra['status']; ?>">
                                    <?php echo ucfirst(htmlspecialchars($ra['status'])); ?>
                                </span>
                            </td>
                            <td>
                                <?php 
                                $ra_operator_count = count(array_filter($ra_operators, function($op) use ($ra) { 
                                    return $op['ra_id'] == $ra['ra_id']; 
                                }));
                                echo $ra_operator_count . ' operator' . ($ra_operator_count != 1 ? 's' : '');
                                ?>
                            </td>
                            <td>
                                <button onclick="editRA(<?php echo $ra['ra_id']; ?>)" class="btn btn-warning btn-small">Edit</button>
                                <button onclick="manageOperators(<?php echo $ra['ra_id']; ?>)" class="btn btn-small">Operators</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- RA Operators List -->
        <div class="card">
            <div class="card-header">RA Operators</div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>RA</th>
                            <th>Operator</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ra_operators as $operator): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($operator['ra_name']); ?></strong></td>
                            <td><?php echo htmlspecialchars($operator['first_name'] . ' ' . $operator['last_name']); ?></td>
                            <td><?php echo htmlspecialchars($operator['email']); ?></td>
                            <td><span class="ra-type"><?php echo ucfirst(htmlspecialchars($operator['role'])); ?></span></td>
                            <td>
                                <button onclick="removeOperator(<?php echo $operator['ra_operator_id']; ?>)" class="btn btn-danger btn-small">Remove</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add RA Modal -->
    <div id="addRAModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('addRAModal')">&times;</span>
            <h3>Add New Registration Authority</h3>
            <form method="POST">
                <input type="hidden" name="action" value="add_ra">
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="ra_name">RA Name <span style="color: red;">*</span></label>
                        <input type="text" id="ra_name" name="ra_name" required>
                    </div>
                    <div class="form-group">
                        <label for="ra_code">RA Code <span style="color: red;">*</span></label>
                        <input type="text" id="ra_code" name="ra_code" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="ra_type">RA Type</label>
                        <select id="ra_type" name="ra_type">
                            <option value="central">Central</option>
                            <option value="regional" selected>Regional</option>
                            <option value="sectoral">Sectoral</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="city">City</label>
                        <input type="text" id="city" name="city">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="region">Region</label>
                    <input type="text" id="region" name="region">
                </div>
                
                <div class="form-group">
                    <label for="address">Address</label>
                    <textarea id="address" name="address" rows="3"></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="phone">Phone</label>
                        <input type="text" id="phone" name="phone">
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="contact_person">Contact Person</label>
                    <input type="text" id="contact_person" name="contact_person">
                </div>
                
                <button type="submit" class="btn btn-success">Add RA</button>
                <button type="button" onclick="closeModal('addRAModal')" class="btn">Cancel</button>
            </form>
        </div>
    </div>

    <!-- Add Operator Modal -->
    <div id="addOperatorModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('addOperatorModal')">&times;</span>
            <h3>Add RA Operator</h3>
            <form method="POST">
                <input type="hidden" name="action" value="add_operator">
                <input type="hidden" name="ra_id" id="operator_ra_id">
                
                <div class="form-group">
                    <label for="user_id">Select User <span style="color: red;">*</span></label>
                    <select id="user_id" name="user_id" required>
                        <option value="">Select a user...</option>
                        <?php foreach ($users as $user): ?>
                        <option value="<?php echo $user['user_id']; ?>">
                            <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name'] . ' (' . $user['email'] . ')'); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="role">Role</label>
                    <select id="role" name="role">
                        <option value="operator">Operator</option>
                        <option value="manager">Manager</option>
                        <option value="supervisor">Supervisor</option>
                    </select>
                </div>
                
                <button type="submit" class="btn btn-success">Add Operator</button>
                <button type="button" onclick="closeModal('addOperatorModal')" class="btn">Cancel</button>
            </form>
        </div>
    </div>

    <script>
        function openAddRAModal() {
            document.getElementById('addRAModal').style.display = 'block';
        }
        
        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }
        
        function manageOperators(raId) {
            document.getElementById('operator_ra_id').value = raId;
            document.getElementById('addOperatorModal').style.display = 'block';
        }
        
        function editRA(raId) {
            // Implement edit functionality
            alert('Edit RA functionality will be implemented in the next update.');
        }
        
        function removeOperator(operatorId) {
            if (confirm('Are you sure you want to remove this operator?')) {
                // Implement remove functionality
                alert('Remove operator functionality will be implemented in the next update.');
            }
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
    </script>
</body>
</html> 