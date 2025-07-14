<?php
// Enable error reporting for development
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include database configuration
require_once 'config/database.php';

// About ANTIC data
$about_data = [
    'mission' => [
        'title' => 'Notre Mission',
        'description' => 'L\'ANTIC a pour mission de promouvoir et de développer les technologies de l\'information et de la communication au Cameroun, en assurant la sécurité numérique et la confiance électronique.',
        'icon' => '🎯'
    ],
    'vision' => [
        'title' => 'Notre Vision',
        'description' => 'Devenir l\'autorité de référence en matière de certification électronique et de cybersécurité en Afrique Centrale, contribuant au développement numérique du Cameroun.',
        'icon' => '👁️'
    ],
    'values' => [
        'title' => 'Nos Valeurs',
        'description' => 'Excellence, Innovation, Sécurité, Transparence et Service Public.',
        'icon' => '💎'
    ]
];

// Key figures
$key_figures = [
    [
        'number' => '50,000+',
        'label' => 'Certificats Émis',
        'description' => 'Certificats électroniques délivrés depuis notre création'
    ],
    [
        'number' => '500+',
        'label' => 'Organisations',
        'description' => 'Entreprises et institutions partenaires'
    ],
    [
        'number' => '99.9%',
        'label' => 'Disponibilité',
        'description' => 'Taux de disponibilité de nos services'
    ],
    [
        'number' => '24/7',
        'label' => 'Support',
        'description' => 'Support technique disponible en permanence'
    ]
];

// Services offered
$services = [
    [
        'name' => 'Certification Électronique',
        'description' => 'Émission et gestion de certificats numériques pour sécuriser les transactions électroniques',
        'icon' => '🔐',
        'features' => ['Certificats SSL/TLS', 'Certificats de signature', 'Certificats qualifiés']
    ],
    [
        'name' => 'Infrastructure PKI',
        'description' => 'Infrastructure à clés publiques complète pour la sécurité numérique',
        'icon' => '🏗️',
        'features' => ['Autorité de certification', 'Autorité d\'enregistrement', 'Services OCSP']
    ],
    [
        'name' => 'Horodatage Électronique',
        'description' => 'Services d\'horodatage pour garantir l\'intégrité temporelle des documents',
        'icon' => '⏰',
        'features' => ['Horodatage qualifié', 'Conformité légale', 'API d\'intégration']
    ],
    [
        'name' => 'Formation et Sensibilisation',
        'description' => 'Programmes de formation sur la cybersécurité et les bonnes pratiques',
        'icon' => '📚',
        'features' => ['Formation technique', 'Sensibilisation', 'Certifications']
    ]
];

// Timeline
$timeline = [
    [
        'year' => '2003',
        'title' => 'Création de l\'ANTIC',
        'description' => 'Création de l\'Agence Nationale des Technologies de l\'Information et de la Communication'
    ],
    [
        'year' => '2007',
        'title' => 'Premier Certificat',
        'description' => 'Émission du premier certificat électronique au Cameroun'
    ],
    [
        'year' => '2010',
        'title' => 'Infrastructure PKI',
        'description' => 'Mise en place de l\'infrastructure à clés publiques complète'
    ],
    [
        'year' => '2015',
        'title' => 'Services Cloud',
        'description' => 'Lancement des services de certification en mode cloud'
    ],
    [
        'year' => '2020',
        'title' => 'Certificats Qualifiés',
        'description' => 'Introduction des certificats qualifiés pour usage légal'
    ],
    [
        'year' => '2024',
        'title' => 'Innovation Continue',
        'description' => 'Développement de nouveaux services et technologies'
    ]
];

// Team members
$team = [
    [
        'name' => 'Dr. Jean-Pierre Ngué',
        'position' => 'Directeur Général',
        'description' => 'Expert en cybersécurité avec plus de 20 ans d\'expérience',
        'photo' => '👨‍💼'
    ],
    [
        'name' => 'Mme. Marie-Claire Mbarga',
        'position' => 'Directrice Technique',
        'description' => 'Spécialiste en infrastructure PKI et certification électronique',
        'photo' => '👩‍💻'
    ],
    [
        'name' => 'M. Paul Ekambi',
        'position' => 'Directeur Commercial',
        'description' => 'Expert en développement commercial et partenariats stratégiques',
        'photo' => '👨‍💼'
    ],
    [
        'name' => 'Mme. Sarah Nkolo',
        'position' => 'Responsable Juridique',
        'description' => 'Spécialiste en droit numérique et conformité réglementaire',
        'photo' => '👩‍⚖️'
    ]
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>À Propos de l'ANTIC - CamGovCA</title>
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

        /* Hero Section */
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 60px 40px;
            border-radius: 15px;
            text-align: center;
            margin-bottom: 40px;
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="20" cy="20" r="2" fill="rgba(255,255,255,0.1)"/><circle cx="80" cy="40" r="1.5" fill="rgba(255,255,255,0.1)"/><circle cx="40" cy="80" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="70" cy="70" r="1.5" fill="rgba(255,255,255,0.1)"/></svg>') repeat;
            opacity: 0.3;
        }

        .hero-content {
            position: relative;
            z-index: 1;
        }

        .hero-title {
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .hero-description {
            font-size: 18px;
            line-height: 1.6;
            margin-bottom: 30px;
            opacity: 0.9;
        }

        .hero-button {
            display: inline-block;
            background: white;
            color: #1e3c72;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 50px;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .hero-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        /* Mission, Vision, Values */
        .mvp-section {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-bottom: 40px;
        }

        .mvp-card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            text-align: center;
            transition: all 0.3s ease;
        }

        .mvp-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }

        .mvp-icon {
            font-size: 48px;
            margin-bottom: 20px;
        }

        .mvp-title {
            color: #1e3c72;
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .mvp-description {
            color: #666;
            line-height: 1.6;
        }

        /* Key Figures */
        .figures-section {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            padding: 40px;
            margin-bottom: 40px;
        }

        .figures-title {
            color: #1e3c72;
            font-size: 28px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 40px;
        }

        .figures-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
        }

        .figure-card {
            text-align: center;
            padding: 30px 20px;
            background: #f8f9fa;
            border-radius: 15px;
            transition: all 0.3s ease;
        }

        .figure-card:hover {
            background: #e9ecef;
            transform: scale(1.05);
        }

        .figure-number {
            color: #1e3c72;
            font-size: 36px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .figure-label {
            color: #1e3c72;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .figure-description {
            color: #666;
            font-size: 14px;
        }

        /* Services */
        .services-section {
            margin-bottom: 40px;
        }

        .services-title {
            color: #1e3c72;
            font-size: 28px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 40px;
        }

        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }

        .service-card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }

        .service-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }

        .service-icon {
            font-size: 48px;
            margin-bottom: 20px;
            text-align: center;
        }

        .service-name {
            color: #1e3c72;
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 15px;
            text-align: center;
        }

        .service-description {
            color: #666;
            margin-bottom: 20px;
            line-height: 1.6;
        }

        .service-features {
            list-style: none;
            padding: 0;
        }

        .service-features li {
            color: #666;
            font-size: 14px;
            margin-bottom: 8px;
            padding-left: 20px;
            position: relative;
        }

        .service-features li::before {
            content: '✓';
            color: #1e3c72;
            position: absolute;
            left: 0;
            font-weight: bold;
        }

        /* Timeline */
        .timeline-section {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            padding: 40px;
            margin-bottom: 40px;
        }

        .timeline-title {
            color: #1e3c72;
            font-size: 28px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 40px;
        }

        .timeline {
            position: relative;
            max-width: 800px;
            margin: 0 auto;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 50%;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #1e3c72;
            transform: translateX(-50%);
        }

        .timeline-item {
            position: relative;
            margin-bottom: 40px;
            display: flex;
            align-items: center;
        }

        .timeline-item:nth-child(odd) {
            flex-direction: row;
        }

        .timeline-item:nth-child(even) {
            flex-direction: row-reverse;
        }

        .timeline-content {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            width: 45%;
            position: relative;
        }

        .timeline-year {
            color: #1e3c72;
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .timeline-title {
            color: #1e3c72;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .timeline-description {
            color: #666;
            font-size: 14px;
        }

        .timeline-dot {
            width: 20px;
            height: 20px;
            background: #1e3c72;
            border-radius: 50%;
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1;
        }

        /* Team */
        .team-section {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            padding: 40px;
            margin-bottom: 40px;
        }

        .team-title {
            color: #1e3c72;
            font-size: 28px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 40px;
        }

        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
        }

        .team-card {
            text-align: center;
            padding: 30px 20px;
            background: #f8f9fa;
            border-radius: 15px;
            transition: all 0.3s ease;
        }

        .team-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }

        .team-photo {
            font-size: 64px;
            margin-bottom: 20px;
        }

        .team-name {
            color: #1e3c72;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .team-position {
            color: #1e3c72;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 15px;
        }

        .team-description {
            color: #666;
            font-size: 14px;
            line-height: 1.5;
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

            .mvp-section, .figures-grid, .services-grid, .team-grid {
                grid-template-columns: 1fr;
            }

            .timeline::before {
                left: 20px;
            }

            .timeline-item {
                flex-direction: row !important;
            }

            .timeline-content {
                width: calc(100% - 60px);
                margin-left: 60px;
            }

            .timeline-dot {
                left: 20px;
            }

            .page-title {
                font-size: 28px;
            }

            .hero-section {
                padding: 40px 20px;
            }

            .hero-title {
                font-size: 24px;
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
                <h1 class="page-title">À Propos de l'ANTIC</h1>
                <p class="page-subtitle">L'autorité de certification électronique du Cameroun</p>
            </div>

            <!-- Hero Section -->
            <div class="hero-section">
                <div class="hero-content">
                    <h2 class="hero-title">L'ANTIC en Bref</h2>
                    <p class="hero-description">
                        L'Agence Nationale des Technologies de l'Information et de la Communication (ANTIC) 
                        est l'autorité de certification électronique officielle du Cameroun. Depuis sa création 
                        en 2003, nous œuvrons pour la sécurité numérique et la confiance électronique au service 
                        du développement du Cameroun.
                    </p>
                    <a href="demande-de-certificats-fr.php" class="hero-button">Demander un Certificat</a>
                </div>
            </div>

            <!-- Mission, Vision, Values -->
            <div class="mvp-section">
                <?php foreach ($about_data as $key => $data): ?>
                    <div class="mvp-card">
                        <div class="mvp-icon"><?php echo $data['icon']; ?></div>
                        <h3 class="mvp-title"><?php echo htmlspecialchars($data['title']); ?></h3>
                        <p class="mvp-description"><?php echo htmlspecialchars($data['description']); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Key Figures -->
            <div class="figures-section">
                <h2 class="figures-title">Chiffres Clés</h2>
                <div class="figures-grid">
                    <?php foreach ($key_figures as $figure): ?>
                        <div class="figure-card">
                            <div class="figure-number"><?php echo htmlspecialchars($figure['number']); ?></div>
                            <div class="figure-label"><?php echo htmlspecialchars($figure['label']); ?></div>
                            <div class="figure-description"><?php echo htmlspecialchars($figure['description']); ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Services -->
            <div class="services-section">
                <h2 class="services-title">Nos Services</h2>
                <div class="services-grid">
                    <?php foreach ($services as $service): ?>
                        <div class="service-card">
                            <div class="service-icon"><?php echo $service['icon']; ?></div>
                            <h3 class="service-name"><?php echo htmlspecialchars($service['name']); ?></h3>
                            <p class="service-description"><?php echo htmlspecialchars($service['description']); ?></p>
                            <ul class="service-features">
                                <?php foreach ($service['features'] as $feature): ?>
                                    <li><?php echo htmlspecialchars($feature); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Timeline -->
            <div class="timeline-section">
                <h2 class="timeline-title">Notre Histoire</h2>
                <div class="timeline">
                    <?php foreach ($timeline as $index => $event): ?>
                        <div class="timeline-item">
                            <div class="timeline-dot"></div>
                            <div class="timeline-content">
                                <div class="timeline-year"><?php echo htmlspecialchars($event['year']); ?></div>
                                <h3 class="timeline-title"><?php echo htmlspecialchars($event['title']); ?></h3>
                                <p class="timeline-description"><?php echo htmlspecialchars($event['description']); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Team -->
            <div class="team-section">
                <h2 class="team-title">Notre Équipe de Direction</h2>
                <div class="team-grid">
                    <?php foreach ($team as $member): ?>
                        <div class="team-card">
                            <div class="team-photo"><?php echo $member['photo']; ?></div>
                            <h3 class="team-name"><?php echo htmlspecialchars($member['name']); ?></h3>
                            <div class="team-position"><?php echo htmlspecialchars($member['position']); ?></div>
                            <p class="team-description"><?php echo htmlspecialchars($member['description']); ?></p>
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
        // Animate numbers on scroll
        function animateNumbers() {
            const figures = document.querySelectorAll('.figure-number');
            figures.forEach(figure => {
                const target = figure.textContent;
                const isPercentage = target.includes('%');
                const isTime = target.includes('/');
                
                if (!isPercentage && !isTime) {
                    const number = parseInt(target.replace(/,/g, ''));
                    let current = 0;
                    const increment = number / 50;
                    const timer = setInterval(() => {
                        current += increment;
                        if (current >= number) {
                            current = number;
                            clearInterval(timer);
                        }
                        figure.textContent = Math.floor(current).toLocaleString() + '+';
                    }, 50);
                }
            });
        }

        // Intersection Observer for animations
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    if (entry.target.classList.contains('figures-section')) {
                        animateNumbers();
                    }
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        });

        // Observe elements for animation
        document.addEventListener('DOMContentLoaded', function() {
            const animateElements = document.querySelectorAll('.mvp-card, .figure-card, .service-card, .team-card, .timeline-item');
            animateElements.forEach(el => {
                el.style.opacity = '0';
                el.style.transform = 'translateY(20px)';
                el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
                observer.observe(el);
            });
            
            observer.observe(document.querySelector('.figures-section'));
        });
    </script>
</body>
</html> 