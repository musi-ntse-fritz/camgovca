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
                        
                        // Add audit logging
                        require_once 'includes/audit_logger.php';
                        $logger = getAuditLogger();
                        $logger->logCertificateOperation('request', $request_id, null, [
                            'cert_type' => $cert_type,
                            'subject_dn' => $subject_dn,
                            'ref_code' => $ref_code,
                            'organization' => $organization
                        ], "Certificate request submitted via web form");
                        
                        // Clear form
                        $_POST = [];
                        
                    } catch (Exception $e) {
                        $pdo->rollback();
                        $error = 'Error submitting request: ' . $e->getMessage();
                        logSecurityEvent('certificate_request_error', $e->getMessage());
                        
                        // Add audit logging for error
                        require_once 'includes/audit_logger.php';
                        $logger = getAuditLogger();
                        $logger->logSecurityEvent('certificate_request_failed', [
                            'error' => $e->getMessage(),
                            'subject_dn' => $subject_dn ?? 'unknown',
                            'email' => $email ?? 'unknown'
                        ]);
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
    <title>Demande de Certificats - CamGovCA</title>
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
            font-size: 12px;
            opacity: 0.9;
        }

        .nav-links {
            display: flex;
            gap: 15px;
        }

        .nav-links a {
            color: white;
            text-decoration: none;
            padding: 8px 15px;
            background: rgba(255,255,255,0.1);
            border-radius: 5px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .nav-links a:hover {
            background: rgba(255,255,255,0.2);
        }

        /* Main Content */
        .main-content {
            padding: 40px 0;
        }

        .page-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .page-title {
            color: #1e3c72;
            font-size: 36px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .page-subtitle {
            color: #666;
            font-size: 18px;
            margin-bottom: 30px;
        }

        /* Certificate Types Section */
        .certificate-types-section {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            padding: 40px;
            margin-bottom: 40px;
        }

        .section-title {
            color: #1e3c72;
            font-size: 28px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 40px;
        }

        .certificate-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
        }

        .certificate-card {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 30px;
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .certificate-card:hover {
            border-color: #1e3c72;
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }

        .certificate-card.selected {
            border-color: #1e3c72;
            background: #e3f2fd;
        }

        .certificate-name {
            color: #1e3c72;
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .certificate-description {
            color: #666;
            margin-bottom: 15px;
            font-size: 14px;
        }

        .certificate-price {
            color: #1e3c72;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .certificate-validity {
            color: #666;
            font-size: 14px;
            margin-bottom: 15px;
        }

        .certificate-features {
            list-style: none;
            padding: 0;
        }

        .certificate-features li {
            color: #666;
            font-size: 13px;
            margin-bottom: 5px;
            padding-left: 15px;
            position: relative;
        }

        .certificate-features li::before {
            content: '✓';
            color: #1e3c72;
            position: absolute;
            left: 0;
            font-weight: bold;
        }

        /* Application Form */
        .application-form-section {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            padding: 40px;
        }

        .form-title {
            color: #1e3c72;
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 30px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }

        .form-input, .form-select, .form-textarea {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }

        .form-input:focus, .form-select:focus, .form-textarea:focus {
            outline: none;
            border-color: #1e3c72;
        }

        .form-textarea {
            resize: vertical;
            min-height: 100px;
        }

        .form-help {
            display: block;
            margin-top: 5px;
            font-size: 12px;
            color: #666;
        }

        .password-requirements {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            margin-top: 10px;
        }

        .requirements-list {
            list-style: none;
            padding: 0;
            margin: 10px 0 0 0;
        }

        .requirements-list li {
            padding: 5px 0;
            font-size: 14px;
            color: #495057;
            position: relative;
            padding-left: 20px;
        }

        .requirements-list li:before {
            content: "✓";
            position: absolute;
            left: 0;
            color: #28a745;
            font-weight: bold;
        }

        .form-button {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
        }

        .form-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(30,60,114,0.3);
        }

        /* Message Display */
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

        /* Process Steps */
        .process-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px;
            border-radius: 15px;
            margin-bottom: 40px;
        }

        .process-title {
            font-size: 24px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 30px;
        }

        .process-steps {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
        }

        .process-step {
            text-align: center;
            padding: 20px;
        }

        .step-number {
            width: 50px;
            height: 50px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            font-weight: bold;
            margin: 0 auto 15px;
        }

        .step-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .step-description {
            font-size: 14px;
            opacity: 0.9;
        }

        /* Footer */
        .footer {
            background: #1e3c72;
            color: white;
            padding: 30px 0;
            text-align: center;
            margin-top: 40px;
        }

        .footer p {
            margin: 0;
            font-size: 14px;
            opacity: 0.8;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: 20px;
            }

            .nav-links {
                flex-wrap: wrap;
                justify-content: center;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .certificate-grid {
                grid-template-columns: 1fr;
            }

            .process-steps {
                grid-template-columns: 1fr;
            }

            .page-title {
                font-size: 28px;
            }

            .certificate-types-section, .application-form-section, .process-section {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="header-content">
                <div class="logo-section">
                    <div class="logo-left">LOGO</div>
                    <div class="agency-title">
                        <h1>ANTIC</h1>
                        <p>Agence Nationale des Technologies de l'Information et de la Communication</p>
                    </div>
                    <div class="logo-right">LOGO</div>
                </div>
                <div class="nav-links">
                    <a href="index.php">Accueil</a>
                    <a href="dynamic_home.php">Site Principal</a>
                </div>
            </div>
        </div>
    </header>

    <main class="main-content">
        <div class="container">
            <div class="page-header">
                <h1 class="page-title">Demande de Certificats Électroniques</h1>
                <p class="page-subtitle">Soumettez votre demande de certificat électronique en quelques étapes simples</p>
            </div>

            <!-- Process Steps -->
            <div class="process-section">
                <h2 class="process-title">Processus de Demande</h2>
                <div class="process-steps">
                    <div class="process-step">
                        <div class="step-number">1</div>
                        <h3 class="step-title">Choisir le Type</h3>
                        <p class="step-description">Sélectionnez le type de certificat qui correspond à vos besoins</p>
                    </div>
                    <div class="process-step">
                        <div class="step-number">2</div>
                        <h3 class="step-title">Remplir le Formulaire</h3>
                        <p class="step-description">Complétez le formulaire de demande avec vos informations</p>
                    </div>
                    <div class="process-step">
                        <div class="step-number">3</div>
                        <h3 class="step-title">Validation</h3>
                        <p class="step-description">Notre équipe valide votre demande et vos documents</p>
                    </div>
                    <div class="process-step">
                        <div class="step-number">4</div>
                        <h3 class="step-title">Émission</h3>
                        <p class="step-description">Le certificat est émis et vous est livré sécurisé</p>
                    </div>
                </div>
            </div>

            <!-- Certificate Types -->
            <div class="certificate-types-section">
                <h2 class="section-title">Types de Certificats Disponibles</h2>
                <div class="certificate-grid">
                    <?php foreach ($certificate_types as $key => $cert): ?>
                        <div class="certificate-card" onclick="selectCertificate('<?php echo $key; ?>')" data-type="<?php echo $key; ?>">
                            <h3 class="certificate-name"><?php echo htmlspecialchars($cert['name']); ?></h3>
                            <p class="certificate-description"><?php echo htmlspecialchars($cert['description']); ?></p>
                            <div class="certificate-price"><?php echo htmlspecialchars($cert['price']); ?></div>
                            <div class="certificate-validity">Validité: <?php echo htmlspecialchars($cert['validity']); ?></div>
                            <ul class="certificate-features">
                                <?php foreach ($cert['features'] as $feature): ?>
                                    <li><?php echo htmlspecialchars($feature); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Application Form -->
            <div class="application-form-section">
                <h2 class="form-title">Formulaire de Demande</h2>
                
                <?php if ($message): ?>
                    <div class="message success">
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                    <div class="message error">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="request_type" class="form-label">Type de Demande *</label>
                            <select id="request_type" name="request_type" class="form-select" required>
                                <option value="">Sélectionnez le type</option>
                                <?php foreach ($applicant_types as $key => $type): ?>
                                    <option value="<?php echo $key; ?>" <?php echo ($request_type ?? '') === $key ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($type); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="cert_type" class="form-label">Type de Certificat *</label>
                            <select id="cert_type" name="cert_type" class="form-select" required>
                                <option value="">Sélectionnez le certificat</option>
                                <?php foreach ($certificate_types as $key => $cert): ?>
                                    <option value="<?php echo $key; ?>" <?php echo ($cert_type ?? '') === $key ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($cert['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="subject_dn" class="form-label">Nom du Sujet *</label>
                            <input type="text" id="subject_dn" name="subject_dn" class="form-input" value="<?php echo htmlspecialchars($subject_dn ?? ''); ?>" required placeholder="Ex: John Doe">
                            <small class="form-help">Nom complet de la personne ou entité pour le certificat</small>
                        </div>

                        <div class="form-group" id="organization_group" style="display: none;">
                            <label for="organization" class="form-label">Nom de l'Organisation</label>
                            <input type="text" id="organization" name="organization" class="form-input" value="<?php echo htmlspecialchars($organization ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <label for="email" class="form-label">Email *</label>
                            <input type="email" id="email" name="email" class="form-input" value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="phone" class="form-label">Téléphone</label>
                            <input type="tel" id="phone" name="phone" class="form-input" value="<?php echo htmlspecialchars($phone ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <label for="address" class="form-label">Adresse</label>
                            <textarea id="address" name="address" class="form-textarea" placeholder="Entrez votre adresse..."><?php echo htmlspecialchars($address ?? ''); ?></textarea>
                        </div>

                        <div class="form-group">
                            <label for="country" class="form-label">Pays</label>
                            <input type="text" id="country" name="country" class="form-input" value="<?php echo htmlspecialchars($country ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <label for="requested_password" class="form-label">Mot de Passe du Certificat *</label>
                            <input type="password" id="requested_password" name="requested_password" class="form-input" value="<?php echo htmlspecialchars($requested_password ?? ''); ?>" required>
                            <small class="form-help">Ce mot de passe sera utilisé pour toutes les opérations sur le certificat</small>
                        </div>

                        <div class="form-group">
                            <label for="generate_password" class="form-label">
                                <input type="checkbox" id="generate_password" name="generate_password" style="margin-right: 8px;">
                                Générer un Mot de Passe Sécurisé
                            </label>
                            <small class="form-help">Cochez cette case pour générer automatiquement un mot de passe sécurisé</small>
                        </div>

                        <div class="form-group password-requirements" style="grid-column: 1 / -1;">
                            <label class="form-label">Exigences du Mot de Passe:</label>
                            <ul class="requirements-list">
                                <?php foreach ($passwordRequirements as $requirement): ?>
                                    <li><?php echo htmlspecialchars($requirement); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>

                    <button type="submit" class="form-button">Soumettre la Demande</button>
                </form>
            </div>
        </div>
    </main>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 National Agency for Information and Communication Technologies (ANTIC) - Tous droits réservés</p>
        </div>
    </footer>

    <script>
        // Certificate selection
        function selectCertificate(type) {
            // Remove previous selection
            document.querySelectorAll('.certificate-card').forEach(card => {
                card.classList.remove('selected');
            });
            
            // Add selection to clicked card
            event.currentTarget.classList.add('selected');
            
            // Update form select
            document.getElementById('cert_type').value = type;
        }

        // Show/hide organization field based on applicant type
        document.getElementById('request_type').addEventListener('change', function() {
            const organizationGroup = document.getElementById('organization_group');
            const selectedValue = this.value;
            
            if (selectedValue === 'individual') {
                organizationGroup.style.display = 'none';
                document.getElementById('organization').value = '';
            } else {
                organizationGroup.style.display = 'block';
            }
        });

        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const requestType = document.getElementById('request_type').value;
            const certType = document.getElementById('cert_type').value;
            const email = document.getElementById('email').value.trim();
            
            if (!requestType || !certType || !email) {
                e.preventDefault();
                alert('Veuillez remplir tous les champs obligatoires.');
                return false;
            }
            
            if (!isValidEmail(email)) {
                e.preventDefault();
                alert('Veuillez entrer une adresse email valide.');
                return false;
            }
        });

        function isValidEmail(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }

        // Auto-resize textarea
        document.getElementById('address').addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });

        // Initialize organization field visibility
        document.addEventListener('DOMContentLoaded', function() {
            const requestType = document.getElementById('request_type').value;
            const organizationGroup = document.getElementById('organization_group');
            
            if (requestType === 'individual') {
                organizationGroup.style.display = 'none';
            } else if (requestType) {
                organizationGroup.style.display = 'block';
            }
        });
    </script>
</body>
</html> 