<?php
// Enable error reporting for development
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include database configuration
require_once 'config/database.php';

// Get FAQ data from database or use default data
$faqs = [];
try {
    if (checkDatabaseConnection()) {
        $pdo = getDBConnection();
        $stmt = $pdo->query("SELECT question, answer, category FROM faq_entries ORDER BY category, faq_id");
        $faqs = $stmt->fetchAll();
    }
} catch (Exception $e) {
    // Use default FAQ data if database is not available
    $faqs = [
        [
            'question' => 'Qu\'est-ce que CamGovCA ?',
            'answer' => 'CamGovCA est l\'Autorité de Certification Électronique officielle du Cameroun, fournissant des certificats numériques sécurisés et des services PKI pour les entités gouvernementales et privées.',
            'category' => 'Général'
        ],
        [
            'question' => 'Comment demander un certificat numérique ?',
            'answer' => 'Vous pouvez demander un certificat numérique via notre portail en ligne ou en contactant notre équipe de support. Le processus implique la vérification d\'identité et la soumission de documents.',
            'category' => 'Certificats'
        ],
        [
            'question' => 'Quels types de certificats proposez-vous ?',
            'answer' => 'Nous proposons divers types de certificats : certificats SSL, certificats de signature de code, certificats de signature de documents, et certificats qualifiés pour usage légal.',
            'category' => 'Certificats'
        ],
        [
            'question' => 'Combien coûte un certificat électronique ?',
            'answer' => 'Les tarifs varient selon le type de certificat et la durée de validité. Contactez-nous pour obtenir un devis personnalisé selon vos besoins.',
            'category' => 'Tarifs'
        ],
        [
            'question' => 'Quelle est la durée de validité d\'un certificat ?',
            'answer' => 'La durée de validité varie de 1 à 3 ans selon le type de certificat et les besoins du client.',
            'category' => 'Certificats'
        ],
        [
            'question' => 'Comment révoquer un certificat ?',
            'answer' => 'La révocation peut être demandée via notre portail d\'administration ou en contactant directement notre équipe de support.',
            'category' => 'Certificats'
        ],
        [
            'question' => 'Quels sont les documents requis pour une demande ?',
            'answer' => 'Les documents requis incluent : pièce d\'identité, justificatif d\'adresse, et documents d\'entreprise si applicable.',
            'category' => 'Procédures'
        ],
        [
            'question' => 'Comment vérifier l\'authenticité d\'un certificat ?',
            'answer' => 'Vous pouvez vérifier l\'authenticité d\'un certificat via notre service OCSP en ligne ou en utilisant notre outil de vérification.',
            'category' => 'Vérification'
        ]
    ];
}

// Group FAQs by category
$faq_categories = [];
foreach ($faqs as $faq) {
    $category = $faq['category'];
    if (!isset($faq_categories[$category])) {
        $faq_categories[$category] = [];
    }
    $faq_categories[$category][] = $faq;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAQ - CamGovCA</title>
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

        /* Search Section */
        .search-section {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .search-container {
            display: flex;
            gap: 15px;
            max-width: 600px;
            margin: 0 auto;
        }

        .search-input {
            flex: 1;
            padding: 15px;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            font-size: 16px;
            outline: none;
            transition: border-color 0.3s ease;
        }

        .search-input:focus {
            border-color: #1e3c72;
        }

        .search-button {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            border: none;
            padding: 15px 25px;
            border-radius: 10px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .search-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(30,60,114,0.3);
        }

        /* Category Tabs */
        .category-tabs {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }

        .category-tab {
            background: white;
            border: 2px solid #e9ecef;
            color: #666;
            padding: 12px 20px;
            border-radius: 25px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 500;
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

        /* FAQ Items */
        .faq-section {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .faq-category {
            display: none;
        }

        .faq-category.active {
            display: block;
        }

        .faq-item {
            border-bottom: 1px solid #e9ecef;
            transition: all 0.3s ease;
        }

        .faq-item:last-child {
            border-bottom: none;
        }

        .faq-question {
            background: #f8f9fa;
            padding: 20px;
            cursor: pointer;
            font-weight: 600;
            font-size: 16px;
            color: #1e3c72;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.3s ease;
        }

        .faq-question:hover {
            background: #e9ecef;
        }

        .faq-question.active {
            background: #1e3c72;
            color: white;
        }

        .faq-arrow {
            width: 20px;
            height: 20px;
            transition: transform 0.3s ease;
        }

        .faq-question.active .faq-arrow {
            transform: rotate(180deg);
        }

        .faq-answer {
            padding: 0 20px;
            max-height: 0;
            overflow: hidden;
            transition: all 0.3s ease;
            background: white;
        }

        .faq-answer.active {
            padding: 20px;
            max-height: 500px;
        }

        .faq-answer p {
            color: #666;
            line-height: 1.6;
            margin: 0;
        }

        /* Contact Section */
        .contact-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px;
            border-radius: 15px;
            text-align: center;
            margin-top: 40px;
        }

        .contact-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .contact-text {
            font-size: 16px;
            margin-bottom: 25px;
            opacity: 0.9;
        }

        .contact-button {
            display: inline-block;
            background: white;
            color: #1e3c72;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 50px;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .contact-button:hover {
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

            .search-container {
                flex-direction: column;
            }

            .category-tabs {
                justify-content: flex-start;
                overflow-x: auto;
                padding-bottom: 10px;
            }

            .page-title {
                font-size: 28px;
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
                <h1 class="page-title">Questions Fréquemment Posées</h1>
                <p class="page-subtitle">Trouvez rapidement des réponses à vos questions sur nos services de certification électronique</p>
            </div>

            <div class="search-section">
                <div class="search-container">
                    <input type="text" id="searchInput" class="search-input" placeholder="Rechercher dans les FAQ..." />
                    <button onclick="searchFAQ()" class="search-button">Rechercher</button>
                </div>
            </div>

            <div class="category-tabs">
                <div class="category-tab active" onclick="showCategory('all')">Toutes</div>
                <?php foreach (array_keys($faq_categories) as $category): ?>
                    <div class="category-tab" onclick="showCategory('<?php echo htmlspecialchars($category); ?>')">
                        <?php echo htmlspecialchars($category); ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="faq-section">
                <?php foreach ($faq_categories as $category => $category_faqs): ?>
                    <div id="category-<?php echo htmlspecialchars($category); ?>" class="faq-category <?php echo $category === array_keys($faq_categories)[0] ? 'active' : ''; ?>">
                        <?php foreach ($category_faqs as $index => $faq): ?>
                            <div class="faq-item" data-category="<?php echo htmlspecialchars($category); ?>">
                                <div class="faq-question" onclick="toggleFAQ(this)">
                                    <?php echo htmlspecialchars($faq['question']); ?>
                                    <svg class="faq-arrow" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M7 10l5 5 5-5z"/>
                                    </svg>
                                </div>
                                <div class="faq-answer">
                                    <p><?php echo htmlspecialchars($faq['answer']); ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="contact-section">
                <h2 class="contact-title">Vous ne trouvez pas votre réponse ?</h2>
                <p class="contact-text">Notre équipe de support est là pour vous aider. Contactez-nous pour obtenir une assistance personnalisée.</p>
                <a href="contact-info.php" class="contact-button">Nous Contacter</a>
            </div>
        </div>
    </main>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 National Agency for Information and Communication Technologies (ANTIC) - Tous droits réservés</p>
        </div>
    </footer>

    <script>
        // FAQ Toggle Function
        function toggleFAQ(element) {
            const answer = element.nextElementSibling;
            const isActive = answer.classList.contains('active');
            
            // Close all other FAQ answers
            document.querySelectorAll('.faq-answer').forEach(item => {
                item.classList.remove('active');
            });
            document.querySelectorAll('.faq-question').forEach(item => {
                item.classList.remove('active');
            });
            
            // Toggle current FAQ answer
            if (!isActive) {
                answer.classList.add('active');
                element.classList.add('active');
            }
        }

        // Category Filter Function
        function showCategory(category) {
            // Update active tab
            document.querySelectorAll('.category-tab').forEach(tab => {
                tab.classList.remove('active');
            });
            event.target.classList.add('active');

            // Show/hide categories
            document.querySelectorAll('.faq-category').forEach(cat => {
                cat.classList.remove('active');
            });

            if (category === 'all') {
                document.querySelectorAll('.faq-category').forEach(cat => {
                    cat.classList.add('active');
                });
            } else {
                document.getElementById('category-' + category).classList.add('active');
            }
        }

        // Search Function
        function searchFAQ() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const faqItems = document.querySelectorAll('.faq-item');

            faqItems.forEach(item => {
                const question = item.querySelector('.faq-question').textContent.toLowerCase();
                const answer = item.querySelector('.faq-answer p').textContent.toLowerCase();
                
                if (question.includes(searchTerm) || answer.includes(searchTerm)) {
                    item.style.display = 'block';
                    // Auto-expand matching items
                    if (searchTerm.length > 2) {
                        item.querySelector('.faq-answer').classList.add('active');
                        item.querySelector('.faq-question').classList.add('active');
                    }
                } else {
                    item.style.display = 'none';
                }
            });

            // Show all categories when searching
            if (searchTerm.length > 0) {
                document.querySelectorAll('.faq-category').forEach(cat => {
                    cat.classList.add('active');
                });
            }
        }

        // Enter key support for search
        document.getElementById('searchInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                searchFAQ();
            }
        });

        // Clear search when input is cleared
        document.getElementById('searchInput').addEventListener('input', function(e) {
            if (e.target.value === '') {
                document.querySelectorAll('.faq-item').forEach(item => {
                    item.style.display = 'block';
                });
                document.querySelectorAll('.faq-answer').forEach(item => {
                    item.classList.remove('active');
                });
                document.querySelectorAll('.faq-question').forEach(item => {
                    item.classList.remove('active');
                });
            }
        });
    </script>
</body>
</html> 