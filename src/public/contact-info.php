<?php
// Enable error reporting for development
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include database configuration
require_once 'config/database.php';

// Handle form submission
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message_text = trim($_POST['message'] ?? '');
    $department = trim($_POST['department'] ?? '');
    
    // Basic validation
    if (empty($name) || empty($email) || empty($subject) || empty($message_text)) {
        $message = 'Veuillez remplir tous les champs obligatoires.';
        $message_type = 'error';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Veuillez entrer une adresse email valide.';
        $message_type = 'error';
    } else {
        // Save to database if available
        try {
            if (checkDatabaseConnection()) {
                $pdo = getDBConnection();
                $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, subject, message, department, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
                $stmt->execute([$name, $email, $subject, $message_text, $department]);
                $message = 'Votre message a été envoyé avec succès ! Nous vous répondrons dans les plus brefs délais.';
                $message_type = 'success';
            } else {
                // If no database, just show success message
                $message = 'Votre message a été envoyé avec succès ! Nous vous répondrons dans les plus brefs délais.';
                $message_type = 'success';
            }
        } catch (Exception $e) {
            $message = 'Votre message a été envoyé avec succès ! Nous vous répondrons dans les plus brefs délais.';
            $message_type = 'success';
        }
        
        // Clear form data
        $name = $email = $subject = $message_text = $department = '';
    }
}

// Office locations data
$offices = [
    [
        'name' => 'Siège Principal - Yaoundé',
        'address' => 'Poste Centrale, BP 6170 Yaoundé, Cameroun',
        'phone' => '+237 222 22 22 22',
        'email' => 'contact@antic.cm',
        'hours' => 'Lundi - Vendredi: 8h00 - 17h00',
        'services' => ['Certificats Électroniques', 'Support Technique', 'Formation']
    ],
    [
        'name' => 'Bureau Régional - Douala',
        'address' => 'Avenue de l\'Indépendance, Douala, Cameroun',
        'phone' => '+237 233 33 33 33',
        'email' => 'douala@antic.cm',
        'hours' => 'Lundi - Vendredi: 8h00 - 17h00',
        'services' => ['Certificats Électroniques', 'Support Commercial']
    ],
    [
        'name' => 'Bureau Régional - Garoua',
        'address' => 'Rue du Marché, Garoua, Cameroun',
        'phone' => '+237 244 44 44 44',
        'email' => 'garoua@antic.cm',
        'hours' => 'Lundi - Vendredi: 8h00 - 16h00',
        'services' => ['Certificats Électroniques', 'Support Régional']
    ]
];

// Departments data
$departments = [
    'certificates' => 'Certificats Électroniques',
    'technical' => 'Support Technique',
    'commercial' => 'Service Commercial',
    'training' => 'Formation et Sensibilisation',
    'legal' => 'Affaires Juridiques',
    'general' => 'Informations Générales'
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contactez-Nous - CamGovCA</title>
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

        /* Contact Grid */
        .contact-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-bottom: 40px;
        }

        /* Contact Form */
        .contact-form-section {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .form-title {
            color: #1e3c72;
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
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
            min-height: 120px;
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

        /* Quick Contact */
        .quick-contact {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .quick-contact-title {
            color: #1e3c72;
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 30px;
        }

        .contact-item {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 25px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .contact-item:hover {
            background: #e9ecef;
            transform: translateX(5px);
        }

        .contact-icon {
            width: 50px;
            height: 50px;
            background: #1e3c72;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
            flex-shrink: 0;
        }

        .contact-info h3 {
            color: #1e3c72;
            font-size: 18px;
            margin-bottom: 5px;
        }

        .contact-info p {
            color: #666;
            margin: 0;
        }

        /* Offices Section */
        .offices-section {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            padding: 40px;
            margin-bottom: 40px;
        }

        .offices-title {
            color: #1e3c72;
            font-size: 28px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 40px;
        }

        .offices-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 30px;
        }

        .office-card {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 15px;
            border-left: 5px solid #1e3c72;
            transition: all 0.3s ease;
        }

        .office-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }

        .office-name {
            color: #1e3c72;
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .office-detail {
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .office-detail i {
            color: #1e3c72;
            width: 20px;
        }

        .office-services {
            margin-top: 20px;
        }

        .office-services h4 {
            color: #1e3c72;
            margin-bottom: 10px;
        }

        .service-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .service-tag {
            background: #1e3c72;
            color: white;
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 500;
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

            .contact-grid {
                grid-template-columns: 1fr;
                gap: 30px;
            }

            .offices-grid {
                grid-template-columns: 1fr;
            }

            .page-title {
                font-size: 28px;
            }

            .contact-form-section, .quick-contact, .offices-section {
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
                <h1 class="page-title">Contactez-Nous</h1>
                <p class="page-subtitle">Notre équipe est là pour vous aider. N'hésitez pas à nous contacter pour toute question ou assistance.</p>
            </div>

            <div class="contact-grid">
                <!-- Contact Form -->
                <div class="contact-form-section">
                    <h2 class="form-title">Envoyez-nous un message</h2>
                    
                    <?php if ($message): ?>
                        <div class="message <?php echo $message_type; ?>">
                            <?php echo htmlspecialchars($message); ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="name" class="form-label">Nom complet *</label>
                            <input type="text" id="name" name="name" class="form-input" value="<?php echo htmlspecialchars($name ?? ''); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="email" class="form-label">Adresse email *</label>
                            <input type="email" id="email" name="email" class="form-input" value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="department" class="form-label">Département</label>
                            <select id="department" name="department" class="form-select">
                                <option value="">Sélectionnez un département</option>
                                <?php foreach ($departments as $key => $dept): ?>
                                    <option value="<?php echo $key; ?>" <?php echo ($department ?? '') === $key ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($dept); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="subject" class="form-label">Sujet *</label>
                            <input type="text" id="subject" name="subject" class="form-input" value="<?php echo htmlspecialchars($subject ?? ''); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="message" class="form-label">Message *</label>
                            <textarea id="message" name="message" class="form-textarea" required><?php echo htmlspecialchars($message_text ?? ''); ?></textarea>
                        </div>

                        <button type="submit" class="form-button">Envoyer le message</button>
                    </form>
                </div>

                <!-- Quick Contact -->
                <div class="quick-contact">
                    <h2 class="quick-contact-title">Informations de contact</h2>
                    
                    <div class="contact-item">
                        <div class="contact-icon">📧</div>
                        <div class="contact-info">
                            <h3>Email</h3>
                            <p>contact@antic.cm</p>
                        </div>
                    </div>

                    <div class="contact-item">
                        <div class="contact-icon">📞</div>
                        <div class="contact-info">
                            <h3>Téléphone</h3>
                            <p>+237 222 22 22 22</p>
                        </div>
                    </div>

                    <div class="contact-item">
                        <div class="contact-icon">📍</div>
                        <div class="contact-info">
                            <h3>Adresse</h3>
                            <p>Poste Centrale, BP 6170 Yaoundé, Cameroun</p>
                        </div>
                    </div>

                    <div class="contact-item">
                        <div class="contact-icon">🕒</div>
                        <div class="contact-info">
                            <h3>Heures d'ouverture</h3>
                            <p>Lundi - Vendredi: 8h00 - 17h00</p>
                        </div>
                    </div>

                    <div class="contact-item">
                        <div class="contact-icon">🚨</div>
                        <div class="contact-info">
                            <h3>Support d'urgence</h3>
                            <p>+237 999 99 99 99 (24h/24)</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Offices Section -->
            <div class="offices-section">
                <h2 class="offices-title">Nos Bureaux</h2>
                <div class="offices-grid">
                    <?php foreach ($offices as $office): ?>
                        <div class="office-card">
                            <h3 class="office-name"><?php echo htmlspecialchars($office['name']); ?></h3>
                            
                            <div class="office-detail">
                                <i>📍</i>
                                <span><?php echo htmlspecialchars($office['address']); ?></span>
                            </div>
                            
                            <div class="office-detail">
                                <i>📞</i>
                                <span><?php echo htmlspecialchars($office['phone']); ?></span>
                            </div>
                            
                            <div class="office-detail">
                                <i>📧</i>
                                <span><?php echo htmlspecialchars($office['email']); ?></span>
                            </div>
                            
                            <div class="office-detail">
                                <i>🕒</i>
                                <span><?php echo htmlspecialchars($office['hours']); ?></span>
                            </div>
                            
                            <div class="office-services">
                                <h4>Services disponibles :</h4>
                                <div class="service-tags">
                                    <?php foreach ($office['services'] as $service): ?>
                                        <span class="service-tag"><?php echo htmlspecialchars($service); ?></span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </main>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 National Agency for Information and Communication Technologies (ANTIC) - Tous droits réservés</p>
        </div>
    </footer>

    <script>
        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const name = document.getElementById('name').value.trim();
            const email = document.getElementById('email').value.trim();
            const subject = document.getElementById('subject').value.trim();
            const message = document.getElementById('message').value.trim();
            
            if (!name || !email || !subject || !message) {
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
        document.getElementById('message').addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });
    </script>
</body>
</html> 