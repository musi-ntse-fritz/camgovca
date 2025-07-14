<?php
$page_title = "Émettre un Certificat - ANTIC";
$page_description = "Émettez un nouveau certificat électronique pour sécuriser vos communications";
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

        .operation-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            overflow: hidden;
            margin-bottom: 30px;
        }

        .operation-header {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .operation-icon {
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

        .operation-body {
            padding: 40px;
        }

        .operation-intro {
            font-size: 1.2em;
            color: #1e3c72;
            margin-bottom: 30px;
            text-align: center;
            font-weight: 500;
        }

        .operation-section {
            margin-bottom: 35px;
        }

        .operation-section h3 {
            color: #1e3c72;
            margin-bottom: 20px;
            font-size: 1.4em;
            border-bottom: 2px solid #1e3c72;
            padding-bottom: 10px;
        }

        .operation-section p {
            margin-bottom: 15px;
            text-align: justify;
        }

        .process-steps {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin: 30px 0;
        }

        .step-card {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 8px;
            border-left: 4px solid #1e3c72;
            text-align: center;
        }

        .step-number {
            width: 40px;
            height: 40px;
            background: #1e3c72;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-weight: bold;
        }

        .step-card h4 {
            color: #1e3c72;
            margin-bottom: 10px;
        }

        .requirements-list {
            background: #f8f9fa;
            border-left: 4px solid #1e3c72;
            padding: 20px;
            margin: 25px 0;
            border-radius: 5px;
        }

        .requirements-list h4 {
            color: #1e3c72;
            margin-bottom: 15px;
        }

        .requirements-list ul {
            list-style: none;
            padding-left: 0;
        }

        .requirements-list li {
            margin-bottom: 8px;
            padding-left: 20px;
            position: relative;
        }

        .requirements-list li:before {
            content: "✓";
            color: #1e3c72;
            font-weight: bold;
            position: absolute;
            left: 0;
        }

        .admin-notice {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            padding: 25px;
            border-radius: 8px;
            margin: 25px 0;
        }

        .admin-notice h4 {
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

        @media (max-width: 768px) {
            .process-steps {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="container">
            <h1>Émettre un Certificat</h1>
            <p>Processus d'Émission de Certificats Électroniques</p>
        </div>
    </div>

    <div class="breadcrumb">
        <div class="container">
            <a href="dynamic_home.php">Accueil</a> > 
            <a href="operations.php">Opérations sur Certificats</a> > 
            Émettre un Certificat
        </div>
    </div>

    <div class="main-content">
        <div class="container">
            <div class="operation-card">
                <div class="operation-header">
                    <div class="operation-icon">🔐</div>
                    <h2>Émission de Certificat Électronique</h2>
                    <p>Processus sécurisé d'émission de certificats par l'ANTIC</p>
                </div>
                
                <div class="operation-body">
                    <div class="operation-intro">
                        L'émission d'un certificat électronique est un processus rigoureux qui garantit 
                        l'authenticité et la sécurité des communications numériques.
                    </div>

                    <div class="operation-section">
                        <h3>Processus d'Émission</h3>
                        <p>
                            L'émission d'un certificat électronique suit un processus standardisé 
                            pour garantir la qualité et la sécurité du certificat délivré.
                        </p>
                    </div>

                    <div class="process-steps">
                        <div class="step-card">
                            <div class="step-number">1</div>
                            <h4>Validation de la Demande</h4>
                            <p>Vérification de l'identité du demandeur et validation des documents fournis</p>
                        </div>

                        <div class="step-card">
                            <div class="step-number">2</div>
                            <h4>Génération des Clés</h4>
                            <p>Création sécurisée de la paire de clés cryptographiques</p>
                        </div>

                        <div class="step-card">
                            <div class="step-number">3</div>
                            <h4>Création du Certificat</h4>
                            <p>Génération du certificat avec les informations du demandeur</p>
                        </div>

                        <div class="step-card">
                            <div class="step-number">4</div>
                            <h4>Signature par la CA</h4>
                            <p>Signature du certificat par l'Autorité de Certification</p>
                        </div>

                        <div class="step-card">
                            <div class="step-number">5</div>
                            <h4>Publication</h4>
                            <p>Publication du certificat dans le répertoire public</p>
                        </div>

                        <div class="step-card">
                            <div class="step-number">6</div>
                            <h4>Livraison</h4>
                            <p>Livraison sécurisée du certificat au demandeur</p>
                        </div>
                    </div>

                    <div class="operation-section">
                        <h3>Exigences Techniques</h3>
                        <p>
                            L'émission de certificats nécessite le respect de standards techniques 
                            et de sécurité stricts.
                        </p>
                    </div>

                    <div class="requirements-list">
                        <h4>Standards et Normes</h4>
                        <ul>
                            <li>Conformité aux standards X.509</li>
                            <li>Utilisation d'algorithmes cryptographiques approuvés</li>
                            <li>Génération sécurisée des clés privées</li>
                            <li>Protection des modules de sécurité matériels (HSM)</li>
                            <li>Audit trail complet des opérations</li>
                            <li>Validation de l'identité du demandeur</li>
                        </ul>
                    </div>

                    <div class="admin-notice">
                        <h4>⚠️ Accès Administrateur Requis</h4>
                        <p>
                            Cette opération nécessite des privilèges d'administrateur. 
                            Seuls les opérateurs autorisés peuvent émettre des certificats. 
                            Veuillez vous connecter à l'interface d'administration pour effectuer cette opération.
                        </p>
                    </div>

                    <div class="operation-section">
                        <h3>Types de Certificats Émissibles</h3>
                        <p>
                            L'ANTIC peut émettre différents types de certificats selon les besoins :
                        </p>
                        <ul style="margin-left: 20px; margin-top: 15px;">
                            <li><strong>Certificats Individuels :</strong> Pour les particuliers</li>
                            <li><strong>Certificats Organisationnels :</strong> Pour les entreprises</li>
                            <li><strong>Certificats SSL/TLS :</strong> Pour les sites web</li>
                            <li><strong>Certificats de Signature de Code :</strong> Pour les développeurs</li>
                            <li><strong>Certificats Email :</strong> Pour le chiffrement des emails</li>
                        </ul>
                    </div>

                    <div class="operation-section">
                        <h3>Durée de Traitement</h3>
                        <p>
                            Le délai de traitement varie selon le type de certificat et le niveau de validation requis :
                        </p>
                        <ul style="margin-left: 20px; margin-top: 15px;">
                            <li><strong>Certificats Standard :</strong> 2-3 jours ouvrables</li>
                            <li><strong>Certificats Organisationnels :</strong> 3-5 jours ouvrables</li>
                            <li><strong>Certificats Qualifiés :</strong> 5-10 jours ouvrables</li>
                            <li><strong>Certificats Urgents :</strong> 24-48 heures (sur demande spéciale)</li>
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