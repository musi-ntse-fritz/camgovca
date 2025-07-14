<?php
$page_title = "Certificats Administration - ANTIC";
$page_description = "Certificats électroniques pour les fonctionnaires et administrateurs publics";
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

        .admin-intro {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            text-align: center;
        }

        .admin-intro h2 {
            color: #1e3c72;
            margin-bottom: 20px;
            font-size: 1.8em;
        }

        .admin-intro p {
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

        .requirements-section {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .requirements-section h3 {
            color: #1e3c72;
            margin-bottom: 20px;
            font-size: 1.5em;
        }

        .requirements-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }

        .requirement-item {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #1e3c72;
        }

        .requirement-item h4 {
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
            
            .requirements-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="container">
            <h1>Certificats Administration</h1>
            <p>Certificats Électroniques pour Fonctionnaires et Administrateurs</p>
        </div>
    </div>

    <div class="breadcrumb">
        <div class="container">
            <a href="dynamic_home.php">Accueil</a> > 
            <a href="demande-de-certificats-fr.php">Obtenir un Certificat</a> > 
            Administration
        </div>
    </div>

    <div class="main-content">
        <div class="container">
            <div class="admin-intro">
                <h2>Certificats pour l'Administration Publique</h2>
                <p>
                    L'ANTIC propose des certificats électroniques spécialement conçus pour les fonctionnaires 
                    et administrateurs publics, permettant de sécuriser les communications officielles 
                    et d'authentifier les actes administratifs.
                </p>
            </div>

            <div class="certificate-types">
                <div class="cert-type-card">
                    <div class="cert-icon">👨‍💼</div>
                    <div class="cert-content">
                        <h3 class="cert-title">Certificat Fonctionnaire</h3>
                        <p class="cert-description">
                            Certificat électronique destiné aux fonctionnaires pour l'authentification 
                            et la signature des documents administratifs.
                        </p>
                        <ul class="cert-features">
                            <li>Signature électronique qualifiée</li>
                            <li>Authentification forte</li>
                            <li>Accès aux systèmes gouvernementaux</li>
                            <li>Valorisation des actes administratifs</li>
                        </ul>
                        <a href="#" class="apply-button">Demander un certificat</a>
                    </div>
                </div>

                <div class="cert-type-card">
                    <div class="cert-icon">🏛️</div>
                    <div class="cert-content">
                        <h3 class="cert-title">Certificat Institutionnel</h3>
                        <p class="cert-description">
                            Certificat pour les institutions publiques permettant de représenter 
                            légalement l'organisme dans les transactions électroniques.
                        </p>
                        <ul class="cert-features">
                            <li>Représentation légale</li>
                            <li>Signature institutionnelle</li>
                            <li>Gestion des accès multiples</li>
                            <li>Audit trail complet</li>
                        </ul>
                        <a href="#" class="apply-button">Demander un certificat</a>
                    </div>
                </div>

                <div class="cert-type-card">
                    <div class="cert-icon">🔐</div>
                    <div class="cert-content">
                        <h3 class="cert-title">Certificat Sécurisé</h3>
                        <p class="cert-description">
                            Certificat haute sécurité pour les fonctions sensibles nécessitant 
                            un niveau de protection renforcé.
                        </p>
                        <ul class="cert-features">
                            <li>Niveau de sécurité élevé</li>
                            <li>Support matériel sécurisé</li>
                            <li>Authentification multi-facteurs</li>
                            <li>Chiffrement avancé</li>
                        </ul>
                        <a href="#" class="apply-button">Demander un certificat</a>
                    </div>
                </div>
            </div>

            <div class="requirements-section">
                <h3>Exigences et Procédures</h3>
                <div class="requirements-grid">
                    <div class="requirement-item">
                        <h4>Documents Requis</h4>
                        <ul>
                            <li>Attestation de fonction</li>
                            <li>Pièce d'identité valide</li>
                            <li>Autorisation hiérarchique</li>
                            <li>Formulaire de demande complété</li>
                        </ul>
                    </div>

                    <div class="requirement-item">
                        <h4>Procédure de Demande</h4>
                        <ul>
                            <li>Soumission en ligne</li>
                            <li>Vérification d'identité</li>
                            <li>Validation hiérarchique</li>
                            <li>Émission du certificat</li>
                        </ul>
                    </div>

                    <div class="requirement-item">
                        <h4>Délais de Traitement</h4>
                        <ul>
                            <li>Demande standard : 5 jours ouvrables</li>
                            <li>Demande urgente : 48 heures</li>
                            <li>Certificat haute sécurité : 10 jours</li>
                            <li>Suivi en ligne disponible</li>
                        </ul>
                    </div>

                    <div class="requirement-item">
                        <h4>Support et Formation</h4>
                        <ul>
                            <li>Formation obligatoire</li>
                            <li>Support technique dédié</li>
                            <li>Documentation complète</li>
                            <li>Assistance téléphonique</li>
                        </ul>
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