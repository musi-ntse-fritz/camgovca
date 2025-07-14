<?php
$page_title = "Communiqués de Presse - ANTIC";
$page_description = "Actualités et communiqués de presse de l'Agence Nationale des Technologies de l'Information et de la Communication";
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <meta name="description" content="<?php echo $page_description; ?>">
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

        .header {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            padding: 40px 0;
            text-align: center;
        }

        .header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
            font-weight: 300;
        }

        .header p {
            font-size: 1.2em;
            opacity: 0.9;
        }

        .breadcrumb {
            background: white;
            padding: 15px 0;
            border-bottom: 1px solid #e9ecef;
        }

        .breadcrumb a {
            color: #1e3c72;
            text-decoration: none;
        }

        .breadcrumb a:hover {
            text-decoration: underline;
        }

        .main-content {
            padding: 40px 0;
        }

        .news-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 30px;
            margin-bottom: 40px;
        }

        .news-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: transform 0.3s ease;
        }

        .news-card:hover {
            transform: translateY(-5px);
        }

        .news-image {
            height: 200px;
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 3em;
        }

        .news-content {
            padding: 25px;
        }

        .news-date {
            color: #666;
            font-size: 0.9em;
            margin-bottom: 10px;
        }

        .news-title {
            color: #1e3c72;
            font-size: 1.3em;
            margin-bottom: 15px;
            font-weight: bold;
        }

        .news-excerpt {
            color: #666;
            margin-bottom: 20px;
            line-height: 1.6;
        }

        .news-category {
            display: inline-block;
            background: #fcd116;
            color: #1e3c72;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8em;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .read-more {
            color: #1e3c72;
            text-decoration: none;
            font-weight: bold;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .read-more:hover {
            text-decoration: underline;
        }

        .filters {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .filter-title {
            color: #1e3c72;
            margin-bottom: 20px;
            font-size: 1.2em;
            font-weight: bold;
        }

        .filter-buttons {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .filter-btn {
            background: #f8f9fa;
            border: 2px solid #1e3c72;
            color: #1e3c72;
            padding: 8px 16px;
            border-radius: 25px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .filter-btn.active,
        .filter-btn:hover {
            background: #1e3c72;
            color: white;
        }

        .back-button {
            display: inline-block;
            background: #1e3c72;
            color: white;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
            transition: background-color 0.3s ease;
        }

        .back-button:hover {
            background: #2a5298;
        }

        .footer {
            background: #2c3e50;
            color: white;
            text-align: center;
            padding: 20px 0;
            margin-top: 40px;
        }

        @media (max-width: 768px) {
            .news-grid {
                grid-template-columns: 1fr;
            }
            
            .filter-buttons {
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="container">
            <h1>Communiqués de Presse</h1>
            <p>Actualités et Annonces Officielles de l'ANTIC</p>
        </div>
    </div>

    <div class="breadcrumb">
        <div class="container">
            <a href="dynamic_home.php">Accueil</a> > 
            <a href="news-announcements.php">Actualités</a> > 
            Communiqués de Presse
        </div>
    </div>

    <div class="main-content">
        <div class="container">
            <div class="filters">
                <div class="filter-title">Filtrer par Catégorie</div>
                <div class="filter-buttons">
                    <button class="filter-btn active">Tous</button>
                    <button class="filter-btn">Certification</button>
                    <button class="filter-btn">Sécurité</button>
                    <button class="filter-btn">Innovation</button>
                    <button class="filter-btn">Partenariats</button>
                    <button class="filter-btn">Événements</button>
                </div>
            </div>

            <div class="news-grid">
                <div class="news-card">
                    <div class="news-image">🔐</div>
                    <div class="news-content">
                        <div class="news-category">Certification</div>
                        <div class="news-date">15 Décembre 2024</div>
                        <h3 class="news-title">Lancement de la Nouvelle Plateforme Doc@authANTIC 2.0</h3>
                        <p class="news-excerpt">
                            L'ANTIC annonce le lancement de sa nouvelle plateforme de certification numérique 
                            Doc@authANTIC 2.0, offrant des services améliorés et une interface utilisateur modernisée.
                        </p>
                        <a href="#" class="read-more">Lire la suite →</a>
                    </div>
                </div>

                <div class="news-card">
                    <div class="news-image">🛡️</div>
                    <div class="news-content">
                        <div class="news-category">Sécurité</div>
                        <div class="news-date">10 Décembre 2024</div>
                        <h3 class="news-title">Renforcement de la Sécurité de l'Infrastructure PKI</h3>
                        <p class="news-excerpt">
                            L'ANTIC a mis en place de nouvelles mesures de sécurité pour protéger son infrastructure 
                            PKI contre les menaces cybernétiques émergentes.
                        </p>
                        <a href="#" class="read-more">Lire la suite →</a>
                    </div>
                </div>

                <div class="news-card">
                    <div class="news-image">🤝</div>
                    <div class="news-content">
                        <div class="news-category">Partenariats</div>
                        <div class="news-date">5 Décembre 2024</div>
                        <h3 class="news-title">Partenariat avec l'Union Africaine pour l'Interopérabilité</h3>
                        <p class="news-excerpt">
                            Signature d'un accord de partenariat avec l'Union Africaine pour promouvoir 
                            l'interopérabilité des certificats électroniques en Afrique.
                        </p>
                        <a href="#" class="read-more">Lire la suite →</a>
                    </div>
                </div>

                <div class="news-card">
                    <div class="news-image">🎓</div>
                    <div class="news-content">
                        <div class="news-category">Formation</div>
                        <div class="news-date">1 Décembre 2024</div>
                        <h3 class="news-title">Programme de Formation sur la Cybersécurité</h3>
                        <p class="news-excerpt">
                            L'ANTIC lance un programme de formation destiné aux professionnels de la cybersécurité 
                            pour renforcer les compétences nationales.
                        </p>
                        <a href="#" class="read-more">Lire la suite →</a>
                    </div>
                </div>

                <div class="news-card">
                    <div class="news-image">📱</div>
                    <div class="news-content">
                        <div class="news-category">Innovation</div>
                        <div class="news-date">25 Novembre 2024</div>
                        <h3 class="news-title">Application Mobile de Certification</h3>
                        <p class="news-excerpt">
                            Développement d'une application mobile permettant aux utilisateurs de gérer 
                            leurs certificats électroniques depuis leur smartphone.
                        </p>
                        <a href="#" class="read-more">Lire la suite →</a>
                    </div>
                </div>

                <div class="news-card">
                    <div class="news-image">🏛️</div>
                    <div class="news-content">
                        <div class="news-category">Administration</div>
                        <div class="news-date">20 Novembre 2024</div>
                        <h3 class="news-title">Extension des Services aux Collectivités Locales</h3>
                        <p class="news-excerpt">
                            L'ANTIC étend ses services de certification électronique aux collectivités 
                            locales pour faciliter l'administration décentralisée.
                        </p>
                        <a href="#" class="read-more">Lire la suite →</a>
                    </div>
                </div>

                <div class="news-card">
                    <div class="news-image">🌍</div>
                    <div class="news-content">
                        <div class="news-category">International</div>
                        <div class="news-date">15 Novembre 2024</div>
                        <h3 class="news-title">Participation au Forum Mondial de la Cybersécurité</h3>
                        <p class="news-excerpt">
                            L'ANTIC participe au Forum Mondial de la Cybersécurité à Paris pour présenter 
                            ses innovations en matière de certification électronique.
                        </p>
                        <a href="#" class="read-more">Lire la suite →</a>
                    </div>
                </div>

                <div class="news-card">
                    <div class="news-image">📊</div>
                    <div class="news-content">
                        <div class="news-category">Statistiques</div>
                        <div class="news-date">10 Novembre 2024</div>
                        <h3 class="news-title">Rapport Trimestriel sur l'Utilisation des Certificats</h3>
                        <p class="news-excerpt">
                            Publication du rapport trimestriel montrant une augmentation de 25% 
                            de l'utilisation des certificats électroniques au Cameroun.
                        </p>
                        <a href="#" class="read-more">Lire la suite →</a>
                    </div>
                </div>
            </div>

            <a href="dynamic_home.php" class="back-button">← Retour à l'accueil</a>
        </div>
    </div>

    <div class="footer">
        <div class="container">
            <p>&copy; 2024 ANTIC - Agence Nationale des Technologies de l'Information et de la Communication. Tous droits réservés.</p>
        </div>
    </div>

    <script>
        // Filter functionality
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                // Remove active class from all buttons
                document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                // Add active class to clicked button
                this.classList.add('active');
                
                // Here you would implement the actual filtering logic
                // For now, we'll just show all cards
                const category = this.textContent;
                console.log('Filtering by:', category);
            });
        });
    </script>
</body>
</html>