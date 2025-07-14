<?php
$page_title = "Missions de la PKI - ANTIC";
$page_description = "Découvrez les missions et objectifs de l'Infrastructure à Clés Publiques (PKI) de l'ANTIC";
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

        .mission-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            overflow: hidden;
            margin-bottom: 30px;
        }

        .mission-header {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .pki-icon {
            width: 80px;
            height: 80px;
            background: #fcd116;
            border-radius: 50%;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5em;
            color: #1e3c72;
        }

        .mission-body {
            padding: 40px;
        }

        .mission-intro {
            font-size: 1.2em;
            color: #1e3c72;
            margin-bottom: 30px;
            text-align: center;
            font-weight: 500;
        }

        .mission-section {
            margin-bottom: 35px;
        }

        .mission-section h3 {
            color: #1e3c72;
            margin-bottom: 20px;
            font-size: 1.4em;
            border-bottom: 2px solid #1e3c72;
            padding-bottom: 10px;
        }

        .mission-section p {
            margin-bottom: 15px;
            text-align: justify;
        }

        .mission-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            margin: 30px 0;
        }

        .mission-item {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 8px;
            border-left: 4px solid #1e3c72;
        }

        .mission-item h4 {
            color: #1e3c72;
            margin-bottom: 15px;
            font-size: 1.2em;
        }

        .mission-item ul {
            list-style: none;
            padding-left: 0;
        }

        .mission-item li {
            margin-bottom: 8px;
            padding-left: 20px;
            position: relative;
        }

        .mission-item li:before {
            content: "✓";
            color: #1e3c72;
            font-weight: bold;
            position: absolute;
            left: 0;
        }

        .highlight-box {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            padding: 25px;
            border-radius: 8px;
            margin: 25px 0;
        }

        .highlight-box h4 {
            margin-bottom: 15px;
            font-size: 1.3em;
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
    </style>
</head>
<body>
    <div class="header">
        <div class="container">
            <h1>Missions de la PKI</h1>
            <p>Infrastructure à Clés Publiques - ANTIC</p>
        </div>
    </div>

    <div class="breadcrumb">
        <div class="container">
            <a href="dynamic_home.php">Accueil</a> > 
            <a href="about.php">Présentation</a> > 
            Missions de la PKI
        </div>
    </div>

    <div class="main-content">
        <div class="container">
            <div class="mission-card">
                <div class="mission-header">
                    <div class="pki-icon">🔐</div>
                    <h2>Infrastructure à Clés Publiques (PKI)</h2>
                    <p>Pilier de la sécurité numérique au Cameroun</p>
                </div>
                
                <div class="mission-body">
                    <div class="mission-intro">
                        L'Infrastructure à Clés Publiques (PKI) de l'ANTIC constitue la base technologique de la confiance numérique au Cameroun. 
                        Découvrez nos missions principales et nos objectifs stratégiques.
                    </div>

                    <div class="mission-section">
                        <h3>Mission Principale</h3>
                        <p>
                            La PKI de l'ANTIC a pour mission de fournir une infrastructure de confiance numérique robuste et sécurisée, 
                            permettant l'authentification, l'intégrité et la confidentialité des communications électroniques au niveau national. 
                            Notre infrastructure sert de fondation pour l'administration électronique, le commerce électronique et la 
                            transformation numérique du Cameroun.
                        </p>
                    </div>

                    <div class="mission-grid">
                        <div class="mission-item">
                            <h4>Certification Électronique</h4>
                            <ul>
                                <li>Émission de certificats électroniques qualifiés</li>
                                <li>Gestion du cycle de vie des certificats</li>
                                <li>Révocation et suspension des certificats</li>
                                <li>Validation en temps réel (OCSP)</li>
                            </ul>
                        </div>

                        <div class="mission-item">
                            <h4>Sécurité Numérique</h4>
                            <ul>
                                <li>Protection des communications électroniques</li>
                                <li>Authentification forte des utilisateurs</li>
                                <li>Signature électronique sécurisée</li>
                                <li>Chiffrement des données sensibles</li>
                            </ul>
                        </div>

                        <div class="mission-item">
                            <h4>Administration Électronique</h4>
                            <ul>
                                <li>Support aux services gouvernementaux</li>
                                <li>Authentification des fonctionnaires</li>
                                <li>Sécurisation des transactions publiques</li>
                                <li>Interopérabilité avec les systèmes existants</li>
                            </ul>
                        </div>

                        <div class="mission-item">
                            <h4>Innovation Technologique</h4>
                            <ul>
                                <li>Recherche et développement</li>
                                <li>Adoption des nouvelles technologies</li>
                                <li>Formation et sensibilisation</li>
                                <li>Partenariats technologiques</li>
                            </ul>
                        </div>
                    </div>

                    <div class="mission-section">
                        <h3>Objectifs Stratégiques</h3>
                        <p>
                            Notre PKI poursuit plusieurs objectifs stratégiques alignés sur la vision numérique du Cameroun :
                        </p>
                        
                        <div class="highlight-box">
                            <h4>Objectif 1 : Couverture Nationale</h4>
                            <p>
                                Assurer une couverture complète du territoire national avec des services de certification 
                                accessibles à tous les citoyens, entreprises et institutions gouvernementales.
                            </p>
                        </div>

                        <div class="highlight-box">
                            <h4>Objectif 2 : Interopérabilité</h4>
                            <p>
                                Garantir l'interopérabilité avec les infrastructures PKI internationales et régionales, 
                                facilitant ainsi les échanges numériques transfrontaliers.
                            </p>
                        </div>

                        <div class="highlight-box">
                            <h4>Objectif 3 : Innovation Continue</h4>
                            <p>
                                Maintenir un niveau d'innovation technologique élevé pour répondre aux évolutions 
                                des menaces cybernétiques et des besoins des utilisateurs.
                            </p>
                        </div>
                    </div>

                    <div class="mission-section">
                        <h3>Services Principaux</h3>
                        <p>
                            La PKI de l'ANTIC offre une gamme complète de services de certification électronique :
                        </p>
                        <ul style="margin-left: 20px; margin-top: 15px;">
                            <li><strong>Certificats Personnels :</strong> Pour les citoyens et les fonctionnaires</li>
                            <li><strong>Certificats Serveur :</strong> Pour les sites web et applications sécurisés</li>
                            <li><strong>Certificats d'Entreprise :</strong> Pour les organisations et entreprises</li>
                            <li><strong>Services d'Horodatage :</strong> Pour la preuve temporelle des documents</li>
                            <li><strong>Validation OCSP :</strong> Pour la vérification en temps réel des certificats</li>
                        </ul>
                    </div>

                    <div class="mission-section">
                        <h3>Engagement Qualité</h3>
                        <p>
                            L'ANTIC s'engage à maintenir les plus hauts standards de qualité dans la gestion de sa PKI. 
                            Notre infrastructure respecte les normes internationales et les bonnes pratiques du secteur, 
                            garantissant ainsi la confiance et la fiabilité de nos services.
                        </p>
                    </div>

                    <a href="dynamic_home.php" class="back-button">← Retour à l'accueil</a>
                </div>
            </div>
        </div>
    </div>

    <div class="footer">
        <div class="container">
            <p>&copy; 2024 ANTIC - Agence Nationale des Technologies de l'Information et de la Communication. Tous droits réservés.</p>
        </div>
    </div>
</body>
</html>