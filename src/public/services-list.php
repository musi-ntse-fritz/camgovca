<?php
$page_title = "Services - ANTIC";
$page_description = "Découvrez tous les services de certification électronique offerts par l'ANTIC";
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

        .services-intro {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            text-align: center;
        }

        .services-intro h2 {
            color: #1e3c72;
            margin-bottom: 20px;
            font-size: 1.8em;
        }

        .services-intro p {
            font-size: 1.1em;
            color: #666;
            max-width: 800px;
            margin: 0 auto;
        }

        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 30px;
            margin-bottom: 40px;
        }

        .service-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: transform 0.3s ease;
        }

        .service-card:hover {
            transform: translateY(-5px);
        }

        .service-icon {
            height: 120px;
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 3em;
        }

        .service-content {
            padding: 25px;
        }

        .service-title {
            color: #1e3c72;
            font-size: 1.4em;
            margin-bottom: 15px;
            font-weight: bold;
        }

        .service-description {
            color: #666;
            margin-bottom: 20px;
            line-height: 1.6;
        }

        .service-features {
            list-style: none;
            margin-bottom: 20px;
        }

        .service-features li {
            margin-bottom: 8px;
            padding-left: 20px;
            position: relative;
        }

        .service-features li:before {
            content: "✓";
            color: #1e3c72;
            font-weight: bold;
            position: absolute;
            left: 0;
        }

        .service-button {
            display: inline-block;
            background: #1e3c72;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .service-button:hover {
            background: #2a5298;
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
            .services-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="container">
            <h1>Nos Services</h1>
            <p>Solutions de Certification Électronique Complètes</p>
        </div>
    </div>

    <div class="breadcrumb">
        <div class="container">
            <a href="dynamic_home.php">Accueil</a> > 
            <a href="services-et-produits.php">Services et Produits</a> > 
            Services
        </div>
    </div>

    <div class="main-content">
        <div class="container">
            <div class="services-intro">
                <h2>Services de Certification Électronique</h2>
                <p>
                    L'ANTIC propose une gamme complète de services de certification électronique conçus pour répondre 
                    aux besoins de sécurité numérique des citoyens, entreprises et institutions gouvernementales du Cameroun.
                </p>
            </div>

            <div class="services-grid">
                <div class="service-card">
                    <div class="service-icon">🔐</div>
                    <div class="service-content">
                        <h3 class="service-title">Certificats de Signature Électronique</h3>
                        <p class="service-description">
                            Certificats qualifiés permettant de signer électroniquement des documents 
                            avec la même valeur juridique qu'une signature manuscrite.
                        </p>
                        <ul class="service-features">
                            <li>Signature électronique avancée</li>
                            <li>Conformité eIDAS</li>
                            <li>Valeur juridique reconnue</li>
                            <li>Intégrité des documents</li>
                        </ul>
                        <a href="#" class="service-button">En savoir plus</a>
                    </div>
                </div>

                <div class="service-card">
                    <div class="service-icon">🛡️</div>
                    <div class="service-content">
                        <h3 class="service-title">Certificats d'Authentification</h3>
                        <p class="service-description">
                            Certificats permettant l'authentification forte des utilisateurs 
                            pour accéder aux systèmes et services sécurisés.
                        </p>
                        <ul class="service-features">
                            <li>Authentification multi-facteurs</li>
                            <li>Accès sécurisé aux systèmes</li>
                            <li>Protection contre l'usurpation</li>
                            <li>Gestion centralisée des identités</li>
                        </ul>
                        <a href="#" class="service-button">En savoir plus</a>
                    </div>
                </div>

                <div class="service-card">
                    <div class="service-icon">🔒</div>
                    <div class="service-content">
                        <h3 class="service-title">Certificats de Chiffrement</h3>
                        <p class="service-description">
                            Certificats pour le chiffrement des communications et la protection 
                            de la confidentialité des données sensibles.
                        </p>
                        <ul class="service-features">
                            <li>Chiffrement des communications</li>
                            <li>Protection des données sensibles</li>
                            <li>Chiffrement des emails</li>
                            <li>VPN et connexions sécurisées</li>
                        </ul>
                        <a href="#" class="service-button">En savoir plus</a>
                    </div>
                </div>

                <div class="service-card">
                    <div class="service-icon">⏰</div>
                    <div class="service-content">
                        <h3 class="service-title">Services d'Horodatage</h3>
                        <p class="service-description">
                            Services d'horodatage électronique pour prouver l'existence 
                            d'un document à un moment précis.
                        </p>
                        <ul class="service-features">
                            <li>Preuve temporelle</li>
                            <li>Horodatage qualifié</li>
                            <li>Conformité légale</li>
                            <li>Traçabilité complète</li>
                        </ul>
                        <a href="#" class="service-button">En savoir plus</a>
                    </div>
                </div>

                <div class="service-card">
                    <div class="service-icon">🌐</div>
                    <div class="service-content">
                        <h3 class="service-title">Certificats SSL/TLS</h3>
                        <p class="service-description">
                            Certificats pour sécuriser les sites web et les communications 
                            en ligne avec le protocole HTTPS.
                        </p>
                        <ul class="service-features">
                            <li>Sécurisation des sites web</li>
                            <li>Protocole HTTPS</li>
                            <li>Confiance des utilisateurs</li>
                            <li>Conformité aux standards</li>
                        </ul>
                        <a href="#" class="service-button">En savoir plus</a>
                    </div>
                </div>

                <div class="service-card">
                    <div class="service-icon">📱</div>
                    <div class="service-content">
                        <h3 class="service-title">Services Mobiles</h3>
                        <p class="service-description">
                            Solutions de certification adaptées aux appareils mobiles 
                            pour une authentification sécurisée en déplacement.
                        </p>
                        <ul class="service-features">
                            <li>Certificats mobiles</li>
                            <li>Authentification biométrique</li>
                            <li>Applications sécurisées</li>
                            <li>Accès nomade</li>
                        </ul>
                        <a href="#" class="service-button">En savoir plus</a>
                    </div>
                </div>

                <div class="service-card">
                    <div class="service-icon">🏢</div>
                    <div class="service-content">
                        <h3 class="service-title">Services Entreprise</h3>
                        <p class="service-description">
                            Solutions de certification spécialement conçues pour les entreprises 
                            et les organisations de toutes tailles.
                        </p>
                        <ul class="service-features">
                            <li>Certificats d'entreprise</li>
                            <li>Gestion des accès</li>
                            <li>Audit et conformité</li>
                            <li>Support dédié</li>
                        </ul>
                        <a href="#" class="service-button">En savoir plus</a>
                    </div>
                </div>

                <div class="service-card">
                    <div class="service-icon">🏛️</div>
                    <div class="service-content">
                        <h3 class="service-title">Services Gouvernementaux</h3>
                        <p class="service-description">
                            Solutions de certification pour l'administration publique 
                            et les services gouvernementaux.
                        </p>
                        <ul class="service-features">
                            <li>Certificats fonctionnaires</li>
                            <li>Administration électronique</li>
                            <li>Services publics sécurisés</li>
                            <li>Interopérabilité nationale</li>
                        </ul>
                        <a href="#" class="service-button">En savoir plus</a>
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
</body>
</html>