<?php
// Enable error reporting for development
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include security functions
require_once 'includes/security_functions.php';
setSecureSession();

// Include database configuration
require_once 'config/database.php';
require_once 'includes/certificate_password_manager.php';

// Generate CSRF token
$csrf_token = generateCSRFToken();

// Handle form submission
$message = '';
$error = '';
$generatedPassword = '';

$pdo = getDBConnection();
$passwordManager = new CertificatePasswordManager($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!validateCSRFToken()) {
        $error = 'Erreur de sécurité. Veuillez réessayer.';
    } else {
        $request_type = $_POST['request_type'] ?? '';
        $cert_type = $_POST['cert_type'] ?? '';
        $subject_dn = $_POST['subject_dn'] ?? '';
        $organization = $_POST['organization'] ?? '';
        $email = $_POST['email'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $address = $_POST['address'] ?? '';
        $country = $_POST['country'] ?? '';
        $requested_password = $_POST['requested_password'] ?? '';
        $generate_password = isset($_POST['generate_password']);
        
        if (empty($request_type) || empty($cert_type) || empty($subject_dn)) {
            $error = 'Please fill in all required fields.';
        } else {
            try {
                // Generate password if requested
                if ($generate_password) {
                    $generatedPassword = $passwordManager->generatePassword();
                    $requested_password = $generatedPassword;
                } else {
                    // Validate user-provided password
                    $passwordErrors = $passwordManager->validatePasswordStrength($requested_password);
                    if (!empty($passwordErrors)) {
                        $error = 'Password validation failed: ' . implode(', ', $passwordErrors);
                    }
                }
                
                if (empty($error)) {
                    // Process the request directly without cURL
                    try {
                        $pdo->beginTransaction();
                        
                        // Create or get user
                        $stmt = $pdo->prepare("SELECT user_id FROM users WHERE email = ?");
                        $stmt->execute([$email]);
                        $user = $stmt->fetch();
                        
                        if (!$user) {
                            $stmt = $pdo->prepare("
                                INSERT INTO users (username, email, phone, first_name, last_name, user_type, created_at) 
                                VALUES (?, ?, ?, ?, ?, 'client', NOW())
                            ");
                            $stmt->execute([$email, $email, $phone, $subject_dn, $organization]);
                            $userId = $pdo->lastInsertId();
                        } else {
                            $userId = $user['user_id'];
                        }
                        
                        // Generate unique codes
                        $auth_code = 'AUTH-' . strtoupper(substr(md5(uniqid()), 0, 8));
                        $ref_code = 'REF-' . date('Ymd') . '-' . strtoupper(substr(md5(uniqid()), 0, 6));
                        
                        // Create certificate request
                        $stmt = $pdo->prepare("
                            INSERT INTO certificate_requests 
                            (user_id, request_type, cert_type, subject_dn, organization, email, phone, address, country, 
                             requested_password, password_generated, password_generated_at, auth_code, ref_code, status, submitted_at) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, ?, 'pending', NOW())
                        ");
                        
                        $stmt->execute([
                            $userId, $request_type, $cert_type, $subject_dn, $organization, 
                            $email, $phone, $address, $country,
                            $requested_password, $generate_password ? 1 : 0, $auth_code, $ref_code
                        ]);
                        
                        $request_id = $pdo->lastInsertId();
                        $pdo->commit();
                        
                        $message = 'Certificate request submitted successfully! Your reference code is: ' . $ref_code;
                        if ($generatedPassword) {
                            $message .= '<br><strong>Generated Password: ' . $generatedPassword . '</strong><br>Please save this password securely!';
                        }
                        
                        // Log the successful request
                        logSecurityEvent('certificate_requested', "New certificate request submitted: " . $ref_code);
                        
                        // Clear form
                        $_POST = [];
                        
                    } catch (Exception $e) {
                        $pdo->rollback();
                        $error = 'Error submitting request: ' . $e->getMessage();
                        logSecurityEvent('certificate_request_error', $e->getMessage());
                    }
                }
            } catch (Exception $e) {
                $error = 'Error submitting request: ' . $e->getMessage();
                logSecurityEvent('certificate_request_error', $e->getMessage());
            }
        }
    }
}

$passwordRequirements = $passwordManager->getPasswordRequirements();

// Certificate types data
$certificate_types = [
    'individual' => [
        'name' => 'Certificat Individuel',
        'description' => 'Certificat pour usage personnel et authentification',
        'price' => '50,000 FCFA',
        'validity' => '1-3 ans',
        'features' => ['Signature électronique', 'Authentification', 'Support technique']
    ],
    'organization' => [
        'name' => 'Certificat Organisation',
        'description' => 'Certificat pour entreprises et organisations',
        'price' => '150,000 FCFA',
        'validity' => '1-3 ans',
        'features' => ['Signature organisationnelle', 'Gestion multi-utilisateurs', 'Support prioritaire']
    ],
    'ssl' => [
        'name' => 'Certificat SSL/TLS',
        'description' => 'Sécurisation des communications web et protection des données',
        'price' => '150,000 FCFA',
        'validity' => '1-3 ans',
        'features' => ['Chiffrement 256-bit', 'Validation étendue', 'Support technique']
    ],
    'code_signing' => [
        'name' => 'Certificat de Signature de Code',
        'description' => 'Signature numérique des applications et logiciels',
        'price' => '200,000 FCFA',
        'validity' => '1-3 ans',
        'features' => ['Signature de code', 'Authentification développeur', 'Compatibilité multi-plateforme']
    ],
    'email' => [
        'name' => 'Certificat Email',
        'description' => 'Chiffrement et signature des emails',
        'price' => '100,000 FCFA',
        'validity' => '1-3 ans',
        'features' => ['Chiffrement email', 'Signature email', 'Conformité légale']
    ]
];

// Applicant types
$applicant_types = [
    'individual' => 'Particulier',
    'company' => 'Entreprise',
    'government' => 'Institution Gouvernementale',
    'ngo' => 'Organisation Non-Gouvernementale'
];

// Validity periods
$validity_periods = [
    '1_year' => '1 an',
    '2_years' => '2 ans',
    '3_years' => '3 ans'
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demande de Certificats - CamGovCA (Version Corrigée)</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f8f9fa;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Header */
        .header {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            padding: 20px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo-section {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .logo-left, .logo-right {
            width: 60px;
            height: 60px;
            background: rgba(255,255,255,0.1);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            text-align: center;
            border: 2px solid rgba(255,255,255,0.2);
        }

        .agency-title {
            text-align: center;
            flex-grow: 1;
        }

        .agency-title h1 {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .agency-title p {
            font-size: 14px;
            opacity: 0.9;
        }

        /* Navigation */
        .nav {
            background: white;
            padding: 15px 0;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .nav ul {
            list-style: none;
            display: flex;
            justify-content: center;
            gap: 30px;
        }

        .nav a {
            text-decoration: none;
            color: #333;
            font-weight: 500;
            padding: 10px 15px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .nav a:hover {
            background-color: #f8f9fa;
        }

        /* Main Content */
        .main-content {
            padding: 40px 0;
        }

        .form-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            padding: 40px;
            margin-bottom: 40px;
        }

        .form-title {
            text-align: center;
            margin-bottom: 30px;
        }

        .form-title h2 {
            color: #1e3c72;
            font-size: 28px;
            margin-bottom: 10px;
        }

        .form-title p {
            color: #666;
            font-size: 16px;
        }

        /* Form Styles */
        .form-group {
            margin-bottom: 25px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }

        .required {
            color: #e74c3c;
        }

        input[type="text"],
        input[type="email"],
        input[type="tel"],
        input[type="password"],
        select,
        textarea {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s;
            background: white;
        }

        input:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: #1e3c72;
        }

        textarea {
            resize: vertical;
            min-height: 100px;
        }

        /* Certificate Type Cards */
        .cert-type-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .cert-type-card {
            border: 2px solid #e1e5e9;
            border-radius: 10px;
            padding: 20px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .cert-type-card:hover {
            border-color: #1e3c72;
            transform: translateY(-2px);
        }

        .cert-type-card.selected {
            border-color: #1e3c72;
            background-color: #f8f9ff;
        }

        .cert-type-card h4 {
            color: #1e3c72;
            margin-bottom: 10px;
        }

        .cert-type-card .price {
            font-size: 18px;
            font-weight: bold;
            color: #27ae60;
            margin-bottom: 10px;
        }

        .cert-type-card .features {
            list-style: none;
            margin-top: 15px;
        }

        .cert-type-card .features li {
            padding: 5px 0;
            color: #666;
        }

        .cert-type-card .features li:before {
            content: "✓ ";
            color: #27ae60;
            font-weight: bold;
        }

        /* Password Section */
        .password-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
        }

        .password-requirements {
            background: #e8f4fd;
            padding: 15px;
            border-radius: 5px;
            margin-top: 15px;
        }

        .password-requirements h4 {
            color: #1e3c72;
            margin-bottom: 10px;
        }

        .password-requirements ul {
            list-style: none;
            margin: 0;
        }

        .password-requirements li {
            padding: 3px 0;
            color: #666;
        }

        .password-requirements li:before {
            content: "• ";
            color: #1e3c72;
        }

        /* Submit Button */
        .submit-section {
            text-align: center;
            margin-top: 40px;
        }

        .btn-submit {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            padding: 15px 40px;
            border: none;
            border-radius: 8px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.3s;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
        }

        /* Messages */
        .message {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 500;
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

        /* Footer */
        .footer {
            background: #333;
            color: white;
            text-align: center;
            padding: 20px 0;
            margin-top: 40px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .cert-type-cards {
                grid-template-columns: 1fr;
            }
            
            .header-content {
                flex-direction: column;
                gap: 15px;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="header-content">
                <div class="logo-section">
                    <div class="logo-left">ANTIC</div>
                </div>
                <div class="agency-title">
                    <h1>Agence Nationale des Technologies de l'Information et de la Communication</h1>
                    <p>Centre de Certification CamGovCA</p>
                </div>
                <div class="logo-section">
                    <div class="logo-right">CamGovCA</div>
                </div>
            </div>
        </div>
    </header>

    <!-- Navigation -->
    <nav class="nav">
        <div class="container">
            <ul>
                <li><a href="index.php">Accueil</a></li>
                <li><a href="services-et-produits.php">Services</a></li>
                <li><a href="demande-de-certificats-fr-fixed.php">Demande de Certificat</a></li>
                <li><a href="verifier-certificat.php">Vérifier Certificat</a></li>
                <li><a href="contact-info.php">Contact</a></li>
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container">
            <div class="form-container">
                <div class="form-title">
                    <h2>Demande de Certificat Numérique</h2>
                    <p>Formulaire corrigé pour la demande de certificats CamGovCA</p>
                </div>

                <?php if ($message): ?>
                    <div class="message success"><?php echo $message; ?></div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="message error"><?php echo $error; ?></div>
                <?php endif; ?>

                <form method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                    
                    <!-- Certificate Type Selection -->
                    <div class="form-group">
                        <label>Type de Certificat <span class="required">*</span></label>
                        <div class="cert-type-cards">
                            <?php foreach ($certificate_types as $type => $info): ?>
                                <div class="cert-type-card" onclick="selectCertType('<?php echo $type; ?>')">
                                    <h4><?php echo $info['name']; ?></h4>
                                    <p><?php echo $info['description']; ?></p>
                                    <div class="price"><?php echo $info['price']; ?></div>
                                    <ul class="features">
                                        <?php foreach ($info['features'] as $feature): ?>
                                            <li><?php echo $feature; ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <input type="hidden" name="cert_type" id="cert_type" value="<?php echo $_POST['cert_type'] ?? ''; ?>" required>
                    </div>

                    <!-- Request Type -->
                    <div class="form-group">
                        <label for="request_type">Type de Demande <span class="required">*</span></label>
                        <select id="request_type" name="request_type" required>
                            <option value="">Sélectionner le type de demande</option>
                            <option value="new" <?php echo ($_POST['request_type'] ?? '') == 'new' ? 'selected' : ''; ?>>Nouvelle demande</option>
                            <option value="renewal" <?php echo ($_POST['request_type'] ?? '') == 'renewal' ? 'selected' : ''; ?>>Renouvellement</option>
                            <option value="reissue" <?php echo ($_POST['request_type'] ?? '') == 'reissue' ? 'selected' : ''; ?>>Réémission</option>
                        </select>
                    </div>

                    <!-- Personal Information -->
                    <div class="form-row">
                        <div class="form-group">
                            <label for="subject_dn">Nom Complet <span class="required">*</span></label>
                            <input type="text" id="subject_dn" name="subject_dn" value="<?php echo htmlspecialchars($_POST['subject_dn'] ?? ''); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Adresse Email <span class="required">*</span></label>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="phone">Numéro de Téléphone</label>
                            <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label for="organization">Organisation</label>
                            <input type="text" id="organization" name="organization" value="<?php echo htmlspecialchars($_POST['organization'] ?? ''); ?>">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="address">Adresse</label>
                            <textarea id="address" name="address"><?php echo htmlspecialchars($_POST['address'] ?? ''); ?></textarea>
                        </div>
                        <div class="form-group">
                            <label for="country">Pays</label>
                            <select id="country" name="country">
                                <option value="">Sélectionner un pays</option>
                                <option value="CM" <?php echo ($_POST['country'] ?? '') == 'CM' ? 'selected' : ''; ?>>Cameroun</option>
                                <option value="FR" <?php echo ($_POST['country'] ?? '') == 'FR' ? 'selected' : ''; ?>>France</option>
                                <option value="US" <?php echo ($_POST['country'] ?? '') == 'US' ? 'selected' : ''; ?>>États-Unis</option>
                                <option value="CA" <?php echo ($_POST['country'] ?? '') == 'CA' ? 'selected' : ''; ?>>Canada</option>
                            </select>
                        </div>
                    </div>

                    <!-- Password Section -->
                    <div class="password-section">
                        <h3>Mot de Passe du Certificat</h3>
                        <div class="form-row">
                            <div class="form-group">
                                <label>
                                    <input type="checkbox" name="generate_password" id="generate_password" <?php echo isset($_POST['generate_password']) ? 'checked' : ''; ?>>
                                    Générer automatiquement un mot de passe sécurisé
                                </label>
                            </div>
                        </div>
                        
                        <div id="password_input_section" style="<?php echo isset($_POST['generate_password']) ? 'display: none;' : ''; ?>">
                            <div class="form-group">
                                <label for="requested_password">Mot de Passe Personnalisé</label>
                                <input type="password" id="requested_password" name="requested_password" value="<?php echo htmlspecialchars($_POST['requested_password'] ?? ''); ?>">
                            </div>
                        </div>

                        <div class="password-requirements">
                            <h4>Exigences du Mot de Passe:</h4>
                            <ul>
                                <?php foreach ($passwordRequirements as $requirement): ?>
                                    <li><?php echo htmlspecialchars($requirement); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="submit-section">
                        <button type="submit" class="btn-submit">Soumettre la Demande</button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 CamGovCA - Agence Nationale des Technologies de l'Information et de la Communication</p>
        </div>
    </footer>

    <script>
        function selectCertType(type) {
            // Remove selected class from all cards
            document.querySelectorAll('.cert-type-card').forEach(card => {
                card.classList.remove('selected');
            });
            
            // Add selected class to clicked card
            event.currentTarget.classList.add('selected');
            
            // Set the hidden input value
            document.getElementById('cert_type').value = type;
        }

        // Handle password generation checkbox
        document.getElementById('generate_password').addEventListener('change', function() {
            const passwordSection = document.getElementById('password_input_section');
            if (this.checked) {
                passwordSection.style.display = 'none';
                document.getElementById('requested_password').value = '';
            } else {
                passwordSection.style.display = 'block';
            }
        });

        // Pre-select certificate type if already set
        <?php if (!empty($_POST['cert_type'])): ?>
        document.addEventListener('DOMContentLoaded', function() {
            const certType = '<?php echo $_POST['cert_type']; ?>';
            const card = document.querySelector(`[onclick*="${certType}"]`);
            if (card) {
                card.classList.add('selected');
            }
        });
        <?php endif; ?>
    </script>
</body>
</html> 