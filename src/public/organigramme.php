<?php
$page_title = "Organigramme du Centre - ANTIC";
$page_description = "Découvrez l'organisation et la structure du Centre de Certification de l'ANTIC";
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

        .org-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            overflow: hidden;
            margin-bottom: 30px;
        }

        .org-header {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .org-icon {
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

        .org-body {
            padding: 40px;
        }

        .org-intro {
            font-size: 1.2em;
            color: #1e3c72;
            margin-bottom: 30px;
            text-align: center;
            font-weight: 500;
        }

        .org-chart {
            margin: 40px 0;
            text-align: center;
        }

        .org-level {
            margin-bottom: 30px;
        }

        .org-level-title {
            color: #1e3c72;
            font-size: 1.3em;
            margin-bottom: 20px;
            font-weight: bold;
        }

        .org-box {
            display: inline-block;
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin: 10px;
            min-width: 200px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.2);
        }

        .org-box h4 {
            margin-bottom: 8px;
            font-size: 1.1em;
        }

        .org-box p {
            font-size: 0.9em;
            opacity: 0.9;
        }

        .org-section {
            margin-bottom: 35px;
        }

        .org-section h3 {
            color: #1e3c72;
            margin-bottom: 20px;
            font-size: 1.4em;
            border-bottom: 2px solid #1e3c72;
            padding-bottom: 10px;
        }

        .org-section p {
            margin-bottom: 15px;
            text-align: justify;
        }

        .department-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            margin: 30px 0;
        }

        .department-card {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 8px;
            border-left: 4px solid #1e3c72;
        }

        .department-card h4 {
            color: #1e3c72;
            margin-bottom: 15px;
            font-size: 1.2em;
        }

        .department-card ul {
            list-style: none;
            padding-left: 0;
        }

        .department-card li {
            margin-bottom: 8px;
            padding-left: 20px;
            position: relative;
        }

        .department-card li:before {
            content: "•";
            color: #1e3c72;
            font-weight: bold;
            position: absolute;
            left: 0;
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
            .org-box {
                min-width: 150px;
                padding: 15px;
            }
            
            .department-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="container">
            <h1>Organigramme du Centre</h1>
            <p>Structure Organisationnelle de l'ANTIC</p>
        </div>
    </div>

    <div class="breadcrumb">
        <div class="container">
            <a href="dynamic_home.php">Accueil</a> > 
            <a href="about.php">Présentation</a> > 
            Organigramme du Centre
        </div>
    </div>

    <div class="main-content">
        <div class="container">
            <div class="org-card">
                <div class="org-header">
                    <div class="org-icon">🏢</div>
                    <h2>Structure Organisationnelle</h2>
                    <p>Centre de Certification Électronique de l'ANTIC</p>
                </div>
                
                <div class="org-body">
                    <div class="org-intro">
                        Découvrez l'organisation interne du Centre de Certification Électronique de l'ANTIC, 
                        conçue pour assurer une gestion efficace et sécurisée de l'infrastructure PKI nationale.
                    </div>

                    <div class="org-section">
                        <h3>Hiérarchie Organisationnelle</h3>
                        <p>
                            L'organigramme du Centre de Certification reflète une structure hiérarchique claire, 
                            optimisée pour la sécurité, l'efficacité opérationnelle et la conformité réglementaire.
                        </p>
                    </div>

                    <div class="org-chart">
                        <div class="org-level">
                            <div class="org-level-title">Direction Générale</div>
                            <div class="org-box">
                                <h4>Directeur Général</h4>
                                <p>Dr. Ebot Ebot Enaw</p>
                            </div>
                        </div>

                        <div class="org-level">
                            <div class="org-level-title">Direction Technique</div>
                            <div class="org-box">
                                <h4>Directeur Technique</h4>
                                <p>Ing. Jean-Pierre Nguemo</p>
                            </div>
                        </div>

                        <div class="org-level">
                            <div class="org-level-title">Services Opérationnels</div>
                            <div class="org-box">
                                <h4>Service Certification</h4>
                                <p>Gestion des certificats</p>
                            </div>
                            <div class="org-box">
                                <h4>Service Sécurité</h4>
                                <p>Protection des infrastructures</p>
                            </div>
                            <div class="org-box">
                                <h4>Service Support</h4>
                                <p>Assistance technique</p>
                            </div>
                        </div>
                    </div>

                    <div class="org-section">
                        <h3>Départements et Responsabilités</h3>
                        <p>
                            Chaque département du Centre de Certification a des responsabilités spécifiques 
                            pour assurer le bon fonctionnement de l'infrastructure PKI.
                        </p>
                    </div>

                    <div class="department-grid">
                        <div class="department-card">
                            <h4>Département Certification</h4>
                            <ul>
                                <li>Émission des certificats électroniques</li>
                                <li>Gestion du cycle de vie des certificats</li>
                                <li>Validation et révocation des certificats</li>
                                <li>Maintenance de l'autorité de certification</li>
                                <li>Gestion des politiques de certification</li>
                            </ul>
                        </div>

                        <div class="department-card">
                            <h4>Département Sécurité</h4>
                            <ul>
                                <li>Protection des infrastructures critiques</li>
                                <li>Surveillance des systèmes de sécurité</li>
                                <li>Gestion des incidents de sécurité</li>
                                <li>Audit et conformité</li>
                                <li>Formation à la sécurité</li>
                            </ul>
                        </div>

                        <div class="department-card">
                            <h4>Département Support</h4>
                            <ul>
                                <li>Assistance technique aux utilisateurs</li>
                                <li>Support aux partenaires</li>
                                <li>Formation et documentation</li>
                                <li>Gestion des demandes</li>
                                <li>Maintenance des systèmes</li>
                            </ul>
                        </div>

                        <div class="department-card">
                            <h4>Département Innovation</h4>
                            <ul>
                                <li>Recherche et développement</li>
                                <li>Évaluation des nouvelles technologies</li>
                                <li>Pilotage de projets innovants</li>
                                <li>Veille technologique</li>
                                <li>Partenariats technologiques</li>
                            </ul>
                        </div>
                    </div>

                    <div class="org-section">
                        <h3>Processus de Gouvernance</h3>
                        <p>
                            Le Centre de Certification fonctionne selon des processus de gouvernance rigoureux 
                            pour garantir la transparence, la responsabilité et la conformité aux normes internationales.
                        </p>
                        <ul style="margin-left: 20px; margin-top: 15px;">
                            <li><strong>Comité de Direction :</strong> Prise de décisions stratégiques</li>
                            <li><strong>Comité Technique :</strong> Validation des choix technologiques</li>
                            <li><strong>Comité de Sécurité :</strong> Évaluation des risques et menaces</li>
                            <li><strong>Comité d'Audit :</strong> Vérification de la conformité</li>
                        </ul>
                    </div>

                    <div class="org-section">
                        <h3>Partenariats et Collaborations</h3>
                        <p>
                            L'ANTIC collabore étroitement avec diverses institutions et partenaires pour 
                            assurer le succès de sa mission de certification électronique :
                        </p>
                        <ul style="margin-left: 20px; margin-top: 15px;">
                            <li><strong>Institutions Gouvernementales :</strong> Ministères, administrations publiques</li>
                            <li><strong>Partenaires Techniques :</strong> Fournisseurs de solutions PKI</li>
                            <li><strong>Organismes de Normalisation :</strong> ISO, ITU-T, ETSI</li>
                            <li><strong>Partenaires Internationaux :</strong> Autorités de certification étrangères</li>
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