<?php
$page_title = "Certificats Entreprise - ANTIC";
$page_description = "Certificats électroniques pour entreprises et organisations";
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

        .enterprise-intro {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            text-align: center;
        }

        .enterprise-intro h2 {
            color: #1e3c72;
            margin-bottom: 20px;
            font-size: 1.8em;
        }

        .enterprise-intro p {
            font-size: 1.1em;
            color: #666;
            max-width: 800px;
            margin: 0 auto;
        }

        .certificate-types {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 30px;
            margin-bottom: 40px;
        }

        .cert-type-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: transform 0.3s ease;
        }

        .cert-type-card:hover {
            transform: translateY(-5px);
        }

        .cert-icon {
            height: 120px;
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 3em;
        }

        .cert-content {
            padding: 25px;
        }

        .cert-title {
            color: #1e3c72;
            font-size: 1.4em;
            margin-bottom: 15px;
            font-weight: bold;
        }

        .cert-description {
            color: #666;
            margin-bottom: 20px;
            line-height: 1.6;
        }

        .cert-features {
            list-style: none;
            margin-bottom: 20px;
        }

        .cert-features li {
            margin-bottom: 8px;
            padding-left: 20px;
            position: relative;
        }

        .cert-features li:before {
            content: "✓";
            color: #1e3c72;
            font-weight: bold;
            position: absolute;
            left: 0;
        }

        .apply-button {
            display: inline-block;
            background: #1e3c72;
            color: white;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .apply-button:hover {
            background: #2a5298;
        }

        .benefits-section {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .benefits-section h3 {
            color: #1e3c72;
            margin-bottom: 20px;
            font-size: 1.5em;
        }

        .benefits-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }

        .benefit-item {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #1e3c72;
        }

        .benefit-item h4 {
            color: #1e3c72;
            margin-bottom: 10px;
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
            .certificate-types {
                grid-template-columns: 1fr;
            }
            
            .benefits-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="container">
            <h1>Certificats Entreprise</h1>
            <p>Solutions de Certification Électronique pour Entreprises</p>
        </div>
    </div>

    <div class="breadcrumb">
        <div class="container">
            <a href="dynamic_home.php">Accueil</a> > 
            <a href="demande-de-certificats-fr.php">Obtenir un Certificat</a> > 
            Entreprise
        </div>
    </div>

    <div class="main-content">
        <div class="container">
            <div class="enterprise-intro">
                <h2>Certificats pour Entreprises et Organisations</h2>
                <p>
                    L'ANTIC propose des solutions de certification électronique adaptées aux besoins 
                    des entreprises et organisations de toutes tailles, permettant de sécuriser 
                    les communications commerciales et les transactions électroniques.
                </p>
            </div>

            <div class="certificate-types">
                <div class="cert-type-card">
                    <div class="cert-icon">🏢</div>
                    <div class="cert-content">
                        <h3 class="cert-title">Certificat Entreprise Standard</h3>
                        <p class="cert-description">
                            Certificat électronique pour les entreprises permettant de signer 
                            des documents commerciaux et d'authentifier les communications.
                        </p>
                        <ul class="cert-features">
                            <li>Signature électronique avancée</li>
                            <li>Authentification des communications</li>
                            <li>Conformité réglementaire</li>
                            <li>Support technique dédié</li>
                        </ul>
                        <a href="#" class="apply-button">Demander un certificat</a>
                    </div>
                </div>

                <div class="cert-type-card">
                    <div class="cert-icon">🔐</div>
                    <div class="cert-content">
                        <h3 class="cert-title">Certificat Sécurisé</h3>
                        <p class="cert-description">
                            Certificat haute sécurité pour les entreprises nécessitant 
                            un niveau de protection renforcé pour leurs transactions.
                        </p>
                        <ul class="cert-features">
                            <li>Niveau de sécurité élevé</li>
                            <li>Support matériel sécurisé</li>
                            <li>Authentification multi-facteurs</li>
                            <li>Audit trail complet</li>
                        </ul>
                        <a href="#" class="apply-button">Demander un certificat</a>
                    </div>
                </div>

                <div class="cert-type-card">
                    <div class="cert-icon">🌐</div>
                    <div class="cert-content">
                        <h3 class="cert-title">Certificat SSL/TLS</h3>
                        <p class="cert-description">
                            Certificats pour sécuriser les sites web et applications 
                            en ligne de votre entreprise.
                        </p>
                        <ul class="cert-features">
                            <li>Sécurisation des sites web</li>
                            <li>Protocole HTTPS</li>
                            <li>Confiance des clients</li>
                            <li>Conformité aux standards</li>
                        </ul>
                        <a href="#" class="apply-button">Demander un certificat</a>
                    </div>
                </div>

                <div class="cert-type-card">
                    <div class="cert-icon">👥</div>
                    <div class="cert-content">
                        <h3 class="cert-title">Certificats Employés</h3>
                        <p class="cert-description">
                            Certificats pour les employés permettant l'accès sécurisé 
                            aux systèmes d'entreprise et la signature de documents.
                        </p>
                        <ul class="cert-features">
                            <li>Gestion centralisée des accès</li>
                            <li>Signature des documents internes</li>
                            <li>Authentification des employés</li>
                            <li>Révocation centralisée</li>
                        </ul>
                        <a href="#" class="apply-button">Demander un certificat</a>
                    </div>
                </div>
            </div>

            <div class="benefits-section">
                <h3>Avantages pour les Entreprises</h3>
                <div class="benefits-grid">
                    <div class="benefit-item">
                        <h4>Sécurité Renforcée</h4>
                        <p>Protection des communications et des données sensibles de l'entreprise contre les menaces cybernétiques.</p>
                    </div>

                    <div class="benefit-item">
                        <h4>Conformité Réglementaire</h4>
                        <p>Respect des obligations légales en matière de signature électronique et de protection des données.</p>
                    </div>

                    <div class="benefit-item">
                        <h4>Efficacité Opérationnelle</h4>
                        <p>Accélération des processus de signature et d'authentification, réduisant les délais et les coûts.</p>
                    </div>

                    <div class="benefit-item">
                        <h4>Confiance des Clients</h4>
                        <p>Renforcement de la confiance des clients grâce à des communications sécurisées et authentifiées.</p>
                    </div>

                    <div class="benefit-item">
                        <h4>Interopérabilité</h4>
                        <p>Compatibilité avec les systèmes existants et les standards internationaux.</p>
                    </div>

                    <div class="benefit-item">
                        <h4>Support Technique</h4>
                        <p>Assistance technique dédiée et formation pour une intégration optimale.</p>
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