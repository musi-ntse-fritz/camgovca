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

$org_id = $_GET['id'] ?? 0;
$message = '';
$error = '';

// Get organization data
$stmt = $pdo->prepare("SELECT * FROM organizations WHERE org_id = ?");
$stmt->execute([$org_id]);
$org = $stmt->fetch();

if (!$org) {
    header('Location: organizations.php');
    exit();
}

if ($_POST) {
    $org_name = $_POST['org_name'] ?? '';
    $org_type = $_POST['org_type'] ?? 'private';
    $registration_number = $_POST['registration_number'] ?? '';
    $tax_id = $_POST['tax_id'] ?? '';
    $address = $_POST['address'] ?? '';
    $city = $_POST['city'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $email = $_POST['email'] ?? '';
    $website = $_POST['website'] ?? '';
    $contact_person = $_POST['contact_person'] ?? '';
    $contact_phone = $_POST['contact_phone'] ?? '';
    $contact_email = $_POST['contact_email'] ?? '';
    $status = $_POST['status'] ?? 'active';
    
    if (empty($org_name) || empty($org_type)) {
        $error = 'Organization name and type are required.';
    } else {
        try {
            // Check if organization name already exists for other organizations
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM organizations WHERE org_name = ? AND org_id != ?");
            $stmt->execute([$org_name, $org_id]);
            if ($stmt->fetchColumn() > 0) {
                $error = 'Organization name already exists.';
            } else {
                // Update organization
                $stmt = $pdo->prepare("UPDATE organizations SET org_name = ?, org_type = ?, registration_number = ?, tax_id = ?, address = ?, city = ?, phone = ?, email = ?, website = ?, contact_person = ?, contact_phone = ?, contact_email = ?, status = ? WHERE org_id = ?");
                $stmt->execute([$org_name, $org_type, $registration_number, $tax_id, $address, $city, $phone, $email, $website, $contact_person, $contact_phone, $contact_email, $status, $org_id]);
                
                $message = 'Organization updated successfully!';
                
                // Refresh organization data
                $stmt = $pdo->prepare("SELECT * FROM organizations WHERE org_id = ?");
                $stmt->execute([$org_id]);
                $org = $stmt->fetch();
            }
        } catch (Exception $e) {
            $error = 'Error updating organization: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CamGovCA Admin - Edit Organization</title>
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
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .form-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 30px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            box-sizing: border-box;
        }
        .form-group textarea {
            height: 80px;
            resize: vertical;
        }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        .btn {
            background: #667eea;
            color: white;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            font-size: 16px;
            margin-right: 10px;
        }
        .btn-secondary {
            background: #6c757d;
        }
        .btn:hover {
            opacity: 0.9;
        }
        .message {
            padding: 10px;
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
        .required {
            color: #dc3545;
        }
        .org-display {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Edit Organization</h1>
        <div class="nav-links">
            <a href="organizations.php">Back to Organizations</a>
            <a href="dashboard.php">Dashboard</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>
    
    <div class="container">
        <div class="form-card">
            <div class="org-display">
                Editing Organization: <?php echo htmlspecialchars($org['org_name']); ?>
            </div>
            
            <?php if ($message): ?>
                <div class="message success"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="message error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label for="org_name">Organization Name <span class="required">*</span></label>
                    <input type="text" id="org_name" name="org_name" value="<?php echo htmlspecialchars($org['org_name']); ?>" required>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="org_type">Organization Type <span class="required">*</span></label>
                        <select id="org_type" name="org_type" required>
                            <option value="government" <?php echo $org['org_type'] == 'government' ? 'selected' : ''; ?>>Government</option>
                            <option value="private" <?php echo $org['org_type'] == 'private' ? 'selected' : ''; ?>>Private</option>
                            <option value="ngo" <?php echo $org['org_type'] == 'ngo' ? 'selected' : ''; ?>>NGO</option>
                            <option value="foreign" <?php echo $org['org_type'] == 'foreign' ? 'selected' : ''; ?>>Foreign</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="registration_number">Registration Number</label>
                        <input type="text" id="registration_number" name="registration_number" value="<?php echo htmlspecialchars($org['registration_number']); ?>">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="tax_id">Tax ID</label>
                        <input type="text" id="tax_id" name="tax_id" value="<?php echo htmlspecialchars($org['tax_id']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="city">City</label>
                        <input type="text" id="city" name="city" value="<?php echo htmlspecialchars($org['city']); ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="address">Address</label>
                    <textarea id="address" name="address"><?php echo htmlspecialchars($org['address']); ?></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="phone">Phone</label>
                        <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($org['phone']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($org['email']); ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="website">Website</label>
                    <input type="url" id="website" name="website" value="<?php echo htmlspecialchars($org['website']); ?>">
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="contact_person">Contact Person</label>
                        <input type="text" id="contact_person" name="contact_person" value="<?php echo htmlspecialchars($org['contact_person']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="contact_phone">Contact Phone</label>
                        <input type="text" id="contact_phone" name="contact_phone" value="<?php echo htmlspecialchars($org['contact_phone']); ?>">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="contact_email">Contact Email</label>
                        <input type="email" id="contact_email" name="contact_email" value="<?php echo htmlspecialchars($org['contact_email']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="status">Status <span class="required">*</span></label>
                        <select id="status" name="status" required>
                            <option value="active" <?php echo $org['status'] == 'active' ? 'selected' : ''; ?>>Active</option>
                            <option value="inactive" <?php echo $org['status'] == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                            <option value="suspended" <?php echo $org['status'] == 'suspended' ? 'selected' : ''; ?>>Suspended</option>
                            <option value="pending" <?php echo $org['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                        </select>
                    </div>
                </div>
                
                <div style="text-align: center; margin-top: 30px;">
                    <button type="submit" class="btn">Update Organization</button>
                    <a href="organizations.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html> 