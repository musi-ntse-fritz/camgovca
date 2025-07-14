<?php
// Page template for handling all placeholder pages
function renderPage($page_title, $page_description, $content_function) {
    ?>
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo $page_title; ?> - ANTIC</title>
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

            .content-card {
                background: white;
                border-radius: 10px;
                box-shadow: 0 5px 15px rgba(0,0,0,0.1);
                overflow: hidden;
                margin-bottom: 30px;
            }

            .content-header {
                background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
                color: white;
                padding: 30px;
                text-align: center;
            }

            .content-icon {
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

            .content-body {
                padding: 40px;
            }

            .content-intro {
                font-size: 1.2em;
                color: #1e3c72;
                margin-bottom: 30px;
                text-align: center;
                font-weight: 500;
            }

            .content-section {
                margin-bottom: 35px;
            }

            .content-section h3 {
                color: #1e3c72;
                margin-bottom: 20px;
                font-size: 1.4em;
                border-bottom: 2px solid #1e3c72;
                padding-bottom: 10px;
            }

            .content-section p {
                margin-bottom: 15px;
                text-align: justify;
            }

            .content-section ul {
                margin-left: 20px;
                margin-bottom: 15px;
            }

            .content-section li {
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
                <h1><?php echo $page_title; ?></h1>
                <p>ANTIC - Agence Nationale des Technologies de l'Information et de la Communication</p>
            </div>
        </div>

        <div class="breadcrumb">
            <div class="container">
                <a href="dynamic_home.php">Accueil</a> > 
                <?php echo $page_title; ?>
            </div>
        </div>

        <div class="main-content">
            <div class="container">
                <div class="content-card">
                    <div class="content-header">
                        <div class="content-icon">📄</div>
                        <h2><?php echo $page_title; ?></h2>
                        <p>Service en cours de développement</p>
                    </div>
                    
                    <div class="content-body">
                        <div class="content-intro">
                            <?php echo $page_description; ?>
                        </div>

                        <?php $content_function(); ?>

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
    <?php
}

// Content functions for different pages
function getGeneralContent() {
    ?>
    <div class="content-section">
        <h3>À Propos de ce Service</h3>
        <p>
            Cette page présente les généralités sur l'Infrastructure à Clés Publiques (PKI) 
            mise en place par l'ANTIC pour sécuriser les communications électroniques au Cameroun.
        </p>
        
        <div class="highlight-box">
            <h4>Qu'est-ce que la PKI ?</h4>
            <p>
                L'Infrastructure à Clés Publiques (PKI) est un ensemble de matériels, logiciels, 
                personnes, politiques et procédures nécessaires pour créer, gérer, stocker, 
                distribuer et révoquer des certificats numériques.
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
    <?php
}

function getOperationsContent() {
    ?>
    <div class="content-section">
        <h3>Opérations sur les Certificats</h3>
        <p>
            Cette section présente toutes les opérations disponibles pour la gestion 
            des certificats électroniques émis par l'ANTIC.
        </p>
        
        <div class="highlight-box">
            <h4>Opérations Disponibles</h4>
            <ul>
                <li><a href="emettre-certificat.php">Émettre un certificat</a></li>
                <li><a href="remettre-certificat.php">Remettre un certificat</a></li>
                <li><a href="changer-mot-passe-certificat.php">Changer le mot de passe d'un certificat</a></li>
                <li><a href="suspendre-certificat.php">Suspendre un certificat</a></li>
                <li><a href="revoquer-certificat.php">Révoquer un certificat</a></li>
                <li><a href="copier-certificat.php">Copier un certificat</a></li>
                <li><a href="renouveler-certificat.php">Renouveler un certificat</a></li>
                <li><a href="verifier-identite-certificat.php">Vérifier le numéro d'identité d'un certificat</a></li>
                <li><a href="verifier-certificat.php">Vérifier un certificat</a></li>
            </ul>
        </div>
    </div>
    <?php
}

function getRegulationsContent() {
    ?>
    <div class="content-section">
        <h3>Réglementation et Politiques</h3>
        <p>
            Cette section présente les réglementations et politiques en vigueur 
            concernant la certification électronique au Cameroun.
        </p>
        
        <div class="highlight-box">
            <h4>Cadre Réglementaire</h4>
            <ul>
                <li>Charte d'abonnement</li>
                <li>Lois et règlements</li>
                <li>Politique de certificats</li>
                <li>Déclaration des pratiques de certification</li>
            </ul>
        </div>
    </div>
    <?php
}
?> 