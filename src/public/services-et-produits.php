<?php
// Enable error reporting for development
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include database configuration
require_once 'config/database.php';

// Services data
$services = [
    'certificates' => [
        'title' => 'Certificats Électroniques',
        'description' => 'Gamme complète de certificats numériques pour sécuriser vos transactions électroniques',
        'icon' => '🔐',
        'color' => '#1e3c72',
        'products' => [
            [
                'name' => 'Certificat SSL/TLS',
                'description' => 'Sécurisation des communications web avec chiffrement 256-bit',
                'price' => '150,000 FCFA',
                'duration' => '1-3 ans',
                'features' => ['Chiffrement 256-bit', 'Validation étendue', 'Support technique 24/7', 'Compatibilité multi-navigateurs']
            ],
            [
                'name' => 'Certificat de Signature de Code',
                'description' => 'Signature numérique des applications et logiciels',
                'price' => '200,000 FCFA',
                'duration' => '1-3 ans',
                'features' => ['Signature de code', 'Authentification développeur', 'Compatibilité multi-plateforme', 'Support prioritaire']
            ],
            [
                'name' => 'Certificat de Signature de Documents',
                'description' => 'Signature électronique de documents PDF et autres formats',
                'price' => '100,000 FCFA',
                'duration' => '1-3 ans',
                'features' => ['Signature PDF', 'Horodatage', 'Conformité légale', 'API d\'intégration']
            ],
            [
                'name' => 'Certificat Qualifié',
                'description' => 'Certificat de niveau le plus élevé pour usage légal',
                'price' => '300,000 FCFA',
                'duration' => '1-3 ans',
                'features' => ['Usage légal', 'Validation renforcée', 'Support prioritaire', 'Conformité eIDAS']
            ]
        ]
    ],
    'pki' => [
        'title' => 'Infrastructure PKI',
        'description' => 'Infrastructure à clés publiques complète pour la sécurité numérique',
        'icon' => '🏗️',
        'color' => '#667eea',
        'products' => [
            [
                'name' => 'Autorité de Certification (CA)',
                'description' => 'Infrastructure complète d\'autorité de certification',
                'price' => 'Sur devis',
                'duration' => 'Personnalisé',
                'features' => ['Infrastructure complète', 'Gestion des clés', 'Sécurité renforcée', 'Support dédié']
            ],
            [
                'name' => 'Autorité d\'Enregistrement (RA)',
                'description' => 'Services d\'enregistrement et de validation',
                'price' => 'Sur devis',
                'duration' => 'Personnalisé',
                'features' => ['Validation d\'identité', 'Gestion des demandes', 'Interface web', 'API d\'intégration']
            ],
            [
                'name' => 'Service OCSP',
                'description' => 'Vérification en temps réel du statut des certificats',
                'price' => '50,000 FCFA/mois',
                'duration' => 'Mensuel',
                'features' => ['Vérification temps réel', 'Haute disponibilité', 'API REST', 'Monitoring 24/7']
            ]
        ]
    ],
    'timestamping' => [
        'title' => 'Horodatage Électronique',
        'description' => 'Services d\'horodatage pour garantir l\'intégrité temporelle',
        'icon' => '⏰',
        'color' => '#764ba2',
        'products' => [
            [
                'name' => 'Service d\'Horodatage Qualifié',
                'description' => 'Horodatage conforme aux standards internationaux',
                'price' => '75,000 FCFA/mois',
                'duration' => 'Mensuel',
                'features' => ['Conformité eIDAS', 'Horodatage qualifié', 'API d\'intégration', 'Support technique']
            ],
            [
                'name' => 'Horodatage de Documents',
                'description' => 'Horodatage de documents PDF et autres formats',
                'price' => '25,000 FCFA/mois',
                'duration' => 'Mensuel',
                'features' => ['Multi-format', 'Horodatage automatique', 'Interface web', 'Historique complet']
            ]
        ]
    ],
    'training' => [
        'title' => 'Formation et Sensibilisation',
        'description' => 'Programmes de formation sur la cybersécurité et les bonnes pratiques',
        'icon' => '📚',
        'color' => '#f093fb',
        'products' => [
            [
                'name' => 'Formation PKI',
                'description' => 'Formation complète sur l\'infrastructure à clés publiques',
                'price' => '500,000 FCFA',
                'duration' => '5 jours',
                'features' => ['Formation théorique', 'Pratiques en laboratoire', 'Certification', 'Support post-formation']
            ],
            [
                'name' => 'Sensibilisation Cybersécurité',
                'description' => 'Programme de sensibilisation aux risques cyber',
                'price' => '200,000 FCFA',
                'duration' => '2 jours',
                'features' => ['Bonnes pratiques', 'Simulations d\'attaques', 'Évaluation', 'Certificat de participation']
            ],
            [
                'name' => 'Formation Technique',
                'description' => 'Formation technique sur nos produits et services',
                'price' => '300,000 FCFA',
                'duration' => '3 jours',
                'features' => ['Formation produit', 'Intégration technique', 'Support technique', 'Documentation']
            ]
        ]
    ]
];

// Testimonials
$testimonials = [
    [
        'name' => 'M. Jean Dupont',
        'position' => 'Directeur IT, Banque Centrale',
        'content' => 'L\'ANTIC nous a fourni une infrastructure PKI robuste qui répond parfaitement à nos besoins de sécurité.',
        'rating' => 5
    ],
    [
        'name' => 'Mme. Marie Mbarga',
        'position' => 'Responsable Sécurité, Ministère',
        'content' => 'Les certificats qualifiés de l\'ANTIC nous permettent de respecter les exigences légales.',
        'rating' => 5
    ],
    [
        'name' => 'M. Paul Ekambi',
        'position' => 'CTO, Startup Tech',
        'content' => 'Support technique excellent et produits de qualité. L\'ANTIC est un partenaire de confiance.',
        'rating' => 5
    ]
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services et Produits - CamGovCA</title>
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

        /* Service Categories */
        .service-categories {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-bottom: 40px;
            flex-wrap: wrap;
        }

        .category-tab {
            background: white;
            border: 2px solid #e9ecef;
            color: #666;
            padding: 15px 25px;
            border-radius: 25px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .category-tab.active {
            background: #1e3c72;
            color: white;
            border-color: #1e3c72;
        }

        .category-tab:hover {
            border-color: #1e3c72;
            color: #1e3c72;
        }

        .category-tab.active:hover {
            color: white;
        }

        .category-icon {
            font-size: 20px;
        }

        /* Service Section */
        .service-section {
            display: none;
        }

        .service-section.active {
            display: block;
        }

        .service-header {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            padding: 40px;
            margin-bottom: 40px;
            text-align: center;
        }

        .service-icon {
            font-size: 64px;
            margin-bottom: 20px;
        }

        .service-title {
            color: #1e3c72;
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .service-description {
            color: #666;
            font-size: 18px;
            line-height: 1.6;
            max-width: 800px;
            margin: 0 auto;
        }

        /* Products Grid */
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 30px;
            margin-bottom: 40px;
        }

        .product-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            padding: 30px;
            transition: all 0.3s ease;
            border-top: 4px solid #1e3c72;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }

        .product-name {
            color: #1e3c72;
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .product-description {
            color: #666;
            margin-bottom: 20px;
            line-height: 1.5;
        }

        .product-price {
            color: #1e3c72;
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .product-duration {
            color: #666;
            font-size: 14px;
            margin-bottom: 20px;
        }

        .product-features {
            list-style: none;
            padding: 0;
            margin-bottom: 25px;
        }

        .product-features li {
            color: #666;
            font-size: 14px;
            margin-bottom: 8px;
            padding-left: 20px;
            position: relative;
        }

        .product-features li::before {
            content: '✓';
            color: #1e3c72;
            position: absolute;
            left: 0;
            font-weight: bold;
        }

        .product-button {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
        }

        .product-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(30,60,114,0.3);
        }

        /* Testimonials */
        .testimonials-section {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            padding: 40px;
            margin-bottom: 40px;
        }

        .testimonials-title {
            color: #1e3c72;
            font-size: 28px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 40px;
        }

        .testimonials-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }

        .testimonial-card {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 15px;
            border-left: 4px solid #1e3c72;
        }

        .testimonial-content {
            color: #666;
            font-style: italic;
            margin-bottom: 20px;
            line-height: 1.6;
        }

        .testimonial-author {
            color: #1e3c72;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .testimonial-position {
            color: #666;
            font-size: 14px;
        }

        .testimonial-rating {
            margin-top: 10px;
        }

        .star {
            color: #fcd116;
            font-size: 16px;
        }

        /* CTA Section */
        .cta-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 60px 40px;
            border-radius: 15px;
            text-align: center;
            margin-bottom: 40px;
        }

        .cta-title {
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .cta-description {
            font-size: 18px;
            margin-bottom: 30px;
            opacity: 0.9;
        }

        .cta-buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .cta-button {
            display: inline-block;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 50px;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .cta-button.primary {
            background: white;
            color: #1e3c72;
        }

        .cta-button.secondary {
            background: transparent;
            color: white;
            border: 2px solid white;
        }

        .cta-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
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

            .service-categories {
                flex-direction: column;
                align-items: center;
            }

            .products-grid {
                grid-template-columns: 1fr;
            }

            .testimonials-grid {
                grid-template-columns: 1fr;
            }

            .cta-buttons {
                flex-direction: column;
                align-items: center;
            }

            .page-title {
                font-size: 28px;
            }

            .service-header {
                padding: 30px 20px;
            }

            .cta-section {
                padding: 40px 20px;
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
                <h1 class="page-title">Services et Produits</h1>
                <p class="page-subtitle">Découvrez notre gamme complète de services de certification électronique et de sécurité</p>
            </div>

            <!-- Service Categories -->
            <div class="service-categories">
                <?php foreach ($services as $key => $service): ?>
                    <div class="category-tab <?php echo $key === 'certificates' ? 'active' : ''; ?>" onclick="showService('<?php echo $key; ?>')">
                        <span class="category-icon"><?php echo $service['icon']; ?></span>
                        <span><?php echo htmlspecialchars($service['title']); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Service Sections -->
            <?php foreach ($services as $key => $service): ?>
                <div id="service-<?php echo $key; ?>" class="service-section <?php echo $key === 'certificates' ? 'active' : ''; ?>">
                    <div class="service-header">
                        <div class="service-icon"><?php echo $service['icon']; ?></div>
                        <h2 class="service-title"><?php echo htmlspecialchars($service['title']); ?></h2>
                        <p class="service-description"><?php echo htmlspecialchars($service['description']); ?></p>
                    </div>

                    <div class="products-grid">
                        <?php foreach ($service['products'] as $product): ?>
                            <div class="product-card">
                                <h3 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h3>
                                <p class="product-description"><?php echo htmlspecialchars($product['description']); ?></p>
                                <div class="product-price"><?php echo htmlspecialchars($product['price']); ?></div>
                                <div class="product-duration">Durée: <?php echo htmlspecialchars($product['duration']); ?></div>
                                <ul class="product-features">
                                    <?php foreach ($product['features'] as $feature): ?>
                                        <li><?php echo htmlspecialchars($feature); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                                <button class="product-button" onclick="requestProduct('<?php echo htmlspecialchars($product['name']); ?>')">
                                    Demander ce Produit
                                </button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>

            <!-- Testimonials -->
            <div class="testimonials-section">
                <h2 class="testimonials-title">Ce que disent nos clients</h2>
                <div class="testimonials-grid">
                    <?php foreach ($testimonials as $testimonial): ?>
                        <div class="testimonial-card">
                            <p class="testimonial-content">"<?php echo htmlspecialchars($testimonial['content']); ?>"</p>
                            <div class="testimonial-author"><?php echo htmlspecialchars($testimonial['name']); ?></div>
                            <div class="testimonial-position"><?php echo htmlspecialchars($testimonial['position']); ?></div>
                            <div class="testimonial-rating">
                                <?php for ($i = 0; $i < $testimonial['rating']; $i++): ?>
                                    <span class="star">★</span>
                                <?php endfor; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- CTA Section -->
            <div class="cta-section">
                <h2 class="cta-title">Prêt à sécuriser votre infrastructure numérique ?</h2>
                <p class="cta-description">Contactez-nous pour discuter de vos besoins et obtenir un devis personnalisé</p>
                <div class="cta-buttons">
                    <a href="demande-de-certificats-fr.php" class="cta-button primary">Demander un Devis</a>
                    <a href="contact-info.php" class="cta-button secondary">Nous Contacter</a>
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
        // Service category switching
        function showService(serviceKey) {
            // Hide all service sections
            document.querySelectorAll('.service-section').forEach(section => {
                section.classList.remove('active');
            });
            
            // Remove active class from all tabs
            document.querySelectorAll('.category-tab').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Show selected service section
            document.getElementById('service-' + serviceKey).classList.add('active');
            
            // Add active class to clicked tab
            event.currentTarget.classList.add('active');
        }

        // Product request function
        function requestProduct(productName) {
            // Redirect to certificate request page with pre-filled product
            window.location.href = 'demande-de-certificats-fr.php?product=' + encodeURIComponent(productName);
        }

        // Animate elements on scroll
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        });

        // Observe elements for animation
        document.addEventListener('DOMContentLoaded', function() {
            const animateElements = document.querySelectorAll('.product-card, .testimonial-card');
            animateElements.forEach(el => {
                el.style.opacity = '0';
                el.style.transform = 'translateY(20px)';
                el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
                observer.observe(el);
            });
        });

        // Pre-fill product in form if specified in URL
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const product = urlParams.get('product');
            if (product) {
                // You can add logic here to pre-fill the certificate request form
                console.log('Product requested:', product);
            }
        });
    </script>
</body>
</html> 