<?php
$page_title = "Livre Blanc de la PKI - ANTIC";
$page_description = "Document de référence sur l'Infrastructure à Clés Publiques (PKI) de l'ANTIC";
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

        .whitepaper-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            overflow: hidden;
            margin-bottom: 30px;
        }

        .whitepaper-header {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .document-icon {
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

        .whitepaper-body {
            padding: 40px;
        }

        .whitepaper-intro {
            font-size: 1.2em;
            color: #1e3c72;
            margin-bottom: 30px;
            text-align: center;
            font-weight: 500;
        }

        .chapter {
            margin-bottom: 40px;
        }

        .chapter h3 {
            color: #1e3c72;
            margin-bottom: 20px;
            font-size: 1.5em;
            border-bottom: 2px solid #1e3c72;
            padding-bottom: 10px;
        }

        .chapter p {
            margin-bottom: 15px;
            text-align: justify;
        }

        .chapter ul {
            margin-left: 20px;
            margin-bottom: 15px;
        }

        .chapter li {
            margin-bottom: 8px;
        }

        .highlight-box {
            background: #f8f9fa;
            border-left: 4px solid #1e3c72;
            padding: 20px;
            margin: 25px 0;
            border-radius: 5px;
        }

        .highlight-box h4 {
            color: #1e3c72;
            margin-bottom: 10px;
        }

        .download-section {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            padding: 30px;
            border-radius: 8px;
            margin: 30px 0;
            text-align: center;
        }

        .download-button {
            display: inline-block;
            background: #fcd116;
            color: #1e3c72;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin-top: 15px;
            transition: all 0.3s ease;
        }

        .download-button:hover {
            background: #e6c200;
            transform: translateY(-2px);
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
            <h1>Livre Blanc de la PKI</h1>
            <p>Document de Référence - Infrastructure à Clés Publiques</p>
        </div>
    </div>

    <div class="breadcrumb">
        <div class="container">
            <a href="dynamic_home.php">Accueil</a> > 
            <a href="generalites-sur-la-pki.php">Généralités de la PKI</a> > 
            Livre Blanc
        </div>
    </div>

    <div class="main-content">
        <div class="container">
            <div class="whitepaper-card">
                <div class="whitepaper-header">
                    <div class="document-icon">📄</div>
                    <h2>Livre Blanc de la PKI</h2>
                    <p>Document de référence complet sur l'Infrastructure à Clés Publiques</p>
                </div>
                
                <div class="whitepaper-body">
                    <div class="whitepaper-intro">
                        Ce livre blanc présente une vue d'ensemble complète de l'Infrastructure à Clés Publiques (PKI) 
                        mise en place par l'ANTIC pour sécuriser les communications électroniques au Cameroun.
                    </div>

                    <div class="chapter">
                        <h3>Chapitre 1 : Introduction à la PKI</h3>
                        <p>
                            L'Infrastructure à Clés Publiques (PKI) constitue l'épine dorsale de la sécurité numérique moderne. 
                            Elle fournit un cadre de confiance pour l'authentification, l'intégrité et la confidentialité des 
                            communications électroniques.
                        </p>
                        
                        <div class="highlight-box">
                            <h4>Définition</h4>
                            <p>
                                Une PKI est un ensemble de matériels, logiciels, personnes, politiques et procédures nécessaires 
                                pour créer, gérer, stocker, distribuer et révoquer des certificats numériques.
                            </p>
                        </div>

                        <h4>Composants Principaux</h4>
                        <ul>
                            <li><strong>Autorité de Certification (CA) :</strong> Émet et gère les certificats numériques</li>
                            <li><strong>Autorité d'Enregistrement (RA) :</strong> Valide l'identité des demandeurs</li>
                            <li><strong>Autorité d'Horodatage (TSA) :</strong> Fournit des preuves temporelles</li>
                            <li><strong>Répertoire :</strong> Stocke et publie les certificats</li>
                            <li><strong>Protocole OCSP :</strong> Valide le statut des certificats en temps réel</li>
                        </ul>
                    </div>

                    <div class="chapter">
                        <h3>Chapitre 2 : Architecture de la PKI ANTIC</h3>
                        <p>
                            L'architecture PKI de l'ANTIC est conçue pour répondre aux besoins spécifiques du contexte camerounais, 
                            tout en respectant les standards internationaux.
                        </p>

                        <h4>Structure Hiérarchique</h4>
                        <ul>
                            <li><strong>CA Racine :</strong> Autorité de certification principale</li>
                            <li><strong>CA Intermédiaire :</strong> Autorités de certification spécialisées</li>
                            <li><strong>CA Finale :</strong> Émission des certificats utilisateurs</li>
                        </ul>

                        <h4>Services Offerts</h4>
                        <ul>
                            <li>Certificats de signature électronique</li>
                            <li>Certificats d'authentification</li>
                            <li>Certificats de chiffrement</li>
                            <li>Services d'horodatage</li>
                            <li>Validation OCSP</li>
                        </ul>
                    </div>

                    <div class="chapter">
                        <h3>Chapitre 3 : Sécurité et Conformité</h3>
                        <p>
                            La sécurité de l'infrastructure PKI est une priorité absolue pour l'ANTIC. 
                            Notre approche combine des mesures techniques, organisationnelles et physiques.
                        </p>

                        <div class="highlight-box">
                            <h4>Mesures de Sécurité</h4>
                            <ul>
                                <li>Modules de sécurité matériels (HSM)</li>
                                <li>Chiffrement AES-256 pour les clés</li>
                                <li>Accès contrôlé aux infrastructures</li>
                                <li>Surveillance continue 24/7</li>
                                <li>Audit de sécurité régulier</li>
                            </ul>
                        </div>

                        <h4>Conformité Réglementaire</h4>
                        <ul>
                            <li>Respect des normes ISO 27001</li>
                            <li>Conformité aux directives eIDAS</li>
                            <li>Respect des lois camerounaises</li>
                            <li>Certification par des organismes accrédités</li>
                        </ul>
                    </div>

                    <div class="chapter">
                        <h3>Chapitre 4 : Applications et Cas d'Usage</h3>
                        <p>
                            La PKI de l'ANTIC trouve des applications dans de nombreux domaines, 
                            contribuant à la transformation numérique du Cameroun.
                        </p>

                        <h4>Administration Électronique</h4>
                        <ul>
                            <li>Authentification des fonctionnaires</li>
                            <li>Signature électronique des documents officiels</li>
                            <li>Sécurisation des communications gouvernementales</li>
                            <li>Gestion des identités numériques</li>
                        </ul>

                        <h4>Commerce Électronique</h4>
                        <ul>
                            <li>Sécurisation des transactions en ligne</li>
                            <li>Authentification des sites web</li>
                            <li>Protection des données personnelles</li>
                            <li>Conformité aux réglementations</li>
                        </ul>

                        <h4>Santé et Finance</h4>
                        <ul>
                            <li>Sécurisation des dossiers médicaux</li>
                            <li>Protection des transactions bancaires</li>
                            <li>Authentification des professionnels</li>
                            <li>Audit trail et traçabilité</li>
                        </ul>
                    </div>

                    <div class="chapter">
                        <h3>Chapitre 5 : Perspectives d'Avenir</h3>
                        <p>
                            L'évolution technologique continue d'influencer le développement de la PKI. 
                            L'ANTIC s'engage à rester à la pointe de l'innovation.
                        </p>

                        <h4>Évolutions Technologiques</h4>
                        <ul>
                            <li>Intégration de la blockchain</li>
                            <li>Certificats post-quantiques</li>
                            <li>Authentification biométrique</li>
                            <li>Internet des Objets (IoT)</li>
                        </ul>

                        <h4>Développements Futurs</h4>
                        <ul>
                            <li>Extension des services cloud</li>
                            <li>Interopérabilité internationale</li>
                            <li>Services mobiles avancés</li>
                            <li>Intelligence artificielle</li>
                        </ul>
                    </div>

                    <div class="download-section">
                        <h3>Télécharger le Livre Blanc Complet</h3>
                        <p>
                            Consultez la version complète du livre blanc de la PKI ANTIC, 
                            incluant tous les détails techniques, les spécifications et les guides d'implémentation.
                        </p>
                        <a href="#" class="download-button">📥 Télécharger le PDF</a>
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