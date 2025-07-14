<?php
$page_title = "Autorité de Certification - ANTIC";
$page_description = "Découvrez l'Autorité de Certification (CA) de l'ANTIC et son rôle dans la PKI nationale";
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

        .ca-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            overflow: hidden;
            margin-bottom: 30px;
        }

        .ca-header {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .ca-icon {
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

        .ca-body {
            padding: 40px;
        }

        .ca-intro {
            font-size: 1.2em;
            color: #1e3c72;
            margin-bottom: 30px;
            text-align: center;
            font-weight: 500;
        }

        .ca-section {
            margin-bottom: 35px;
        }

        .ca-section h3 {
            color: #1e3c72;
            margin-bottom: 20px;
            font-size: 1.4em;
            border-bottom: 2px solid #1e3c72;
            padding-bottom: 10px;
        }

        .ca-section p {
            margin-bottom: 15px;
            text-align: justify;
        }

        .ca-hierarchy {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            margin: 30px 0;
        }

        .ca-level {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 8px;
            border-left: 4px solid #1e3c72;
        }

        .ca-level h4 {
            color: #1e3c72;
            margin-bottom: 15px;
            font-size: 1.2em;
        }

        .ca-level ul {
            list-style: none;
            padding-left: 0;
        }

        .ca-level li {
            margin-bottom: 8px;
            padding-left: 20px;
            position: relative;
        }

        .ca-level li:before {
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
            <h1>Autorité de Certification</h1>
            <p>Infrastructure de Certification Électronique de l'ANTIC</p>
        </div>
    </div>

    <div class="breadcrumb">
        <div class="container">
            <a href="dynamic_home.php">Accueil</a> > 
            <a href="generalites-sur-la-pki.php">Généralités de la PKI</a> > 
            Autorité de Certification
        </div>
    </div>

    <div class="main-content">
        <div class="container">
            <div class="ca-card">
                <div class="ca-header">
                    <div class="ca-icon">🔐</div>
                    <h2>Autorité de Certification (CA)</h2>
                    <p>Pilier de la Confiance Numérique au Cameroun</p>
                </div>
                
                <div class="ca-body">
                    <div class="ca-intro">
                        L'Autorité de Certification de l'ANTIC constitue le cœur de l'infrastructure PKI nationale, 
                        garantissant la confiance et la sécurité des communications électroniques au Cameroun.
                    </div>

                    <div class="ca-section">
                        <h3>Rôle et Responsabilités</h3>
                        <p>
                            L'Autorité de Certification (CA) de l'ANTIC est l'entité responsable de l'émission, 
                            de la gestion et de la révocation des certificats électroniques. Elle établit et maintient 
                            un environnement de confiance pour les transactions électroniques sécurisées.
                        </p>
                        
                        <div class="highlight-box">
                            <h4>Fonctions Principales</h4>
                            <ul>
                                <li>Émission de certificats électroniques qualifiés</li>
                                <li>Gestion du cycle de vie des certificats</li>
                                <li>Révocation et suspension des certificats</li>
                                <li>Publication des listes de révocation (CRL)</li>
                                <li>Maintenance de l'infrastructure de sécurité</li>
                            </ul>
                        </div>
                    </div>

                    <div class="ca-section">
                        <h3>Hiérarchie de Certification</h3>
                        <p>
                            L'infrastructure CA de l'ANTIC suit une architecture hiérarchique robuste, 
                            garantissant la sécurité et la scalabilité du système.
                        </p>
                    </div>

                    <div class="ca-hierarchy">
                        <div class="ca-level">
                            <h4>CA Racine</h4>
                            <ul>
                                <li>Autorité de certification principale</li>
                                <li>Clé privée ultra-sécurisée</li>
                                <li>Stockage hors ligne (air-gapped)</li>
                                <li>Accès physique strictement contrôlé</li>
                                <li>Signature des CA intermédiaires</li>
                            </ul>
                        </div>

                        <div class="ca-level">
                            <h4>CA Intermédiaire</h4>
                            <ul>
                                <li>Autorités de certification spécialisées</li>
                                <li>Gestion par domaine d'application</li>
                                <li>Certificats de signature et d'authentification</li>
                                <li>Révocation déléguée</li>
                                <li>Audit et monitoring</li>
                            </ul>
                        </div>

                        <div class="ca-level">
                            <h4>CA Finale</h4>
                            <ul>
                                <li>Émission des certificats utilisateurs</li>
                                <li>Validation des demandes</li>
                                <li>Gestion des accès</li>
                                <li>Support technique</li>
                                <li>Interface utilisateur</li>
                            </ul>
                        </div>
                    </div>

                    <div class="ca-section">
                        <h3>Sécurité et Conformité</h3>
                        <p>
                            La sécurité de l'Autorité de Certification est une priorité absolue. 
                            L'ANTIC met en œuvre des mesures de sécurité de niveau militaire pour protéger 
                            ses infrastructures critiques.
                        </p>

                        <h4>Mesures de Sécurité Physique</h4>
                        <ul style="margin-left: 20px; margin-top: 15px;">
                            <li>Locaux sécurisés avec accès contrôlé</li>
                            <li>Surveillance vidéo 24/7</li>
                            <li>Détection d'intrusion</li>
                            <li>Contrôle d'accès biométrique</li>
                            <li>Zones de sécurité multi-niveaux</li>
                        </ul>

                        <h4>Mesures de Sécurité Technique</h4>
                        <ul style="margin-left: 20px; margin-top: 15px;">
                            <li>Modules de sécurité matériels (HSM)</li>
                            <li>Chiffrement AES-256</li>
                            <li>Protocoles cryptographiques robustes</li>
                            <li>Surveillance continue des systèmes</li>
                            <li>Sauvegarde sécurisée des données</li>
                        </ul>
                    </div>

                    <div class="ca-section">
                        <h3>Certificats Émis</h3>
                        <p>
                            L'Autorité de Certification de l'ANTIC émet différents types de certificats 
                            pour répondre aux besoins variés des utilisateurs.
                        </p>

                        <h4>Types de Certificats</h4>
                        <ul style="margin-left: 20px; margin-top: 15px;">
                            <li><strong>Certificats de Signature :</strong> Pour la signature électronique de documents</li>
                            <li><strong>Certificats d'Authentification :</strong> Pour l'authentification des utilisateurs</li>
                            <li><strong>Certificats de Chiffrement :</strong> Pour le chiffrement des communications</li>
                            <li><strong>Certificats SSL/TLS :</strong> Pour la sécurisation des sites web</li>
                            <li><strong>Certificats d'Horodatage :</strong> Pour la preuve temporelle</li>
                        </ul>
                    </div>

                    <div class="ca-section">
                        <h3>Conformité et Audit</h3>
                        <p>
                            L'Autorité de Certification de l'ANTIC respecte les standards internationaux 
                            et fait l'objet d'audits réguliers pour garantir la conformité.
                        </p>

                        <h4>Standards Respectés</h4>
                        <ul style="margin-left: 20px; margin-top: 15px;">
                            <li>Normes ISO 27001 (Sécurité de l'information)</li>
                            <li>Directive eIDAS (Union Européenne)</li>
                            <li>Standards RFC pour les certificats X.509</li>
                            <li>Bonnes pratiques du secteur PKI</li>
                            <li>Réglementation camerounaise</li>
                        </ul>
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