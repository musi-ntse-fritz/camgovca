<?php
$page_title = "Message du Directeur Général - ANTIC";
$page_description = "Message du Directeur Général de l'Agence Nationale des Technologies de l'Information et de la Communication";
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

        .message-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            overflow: hidden;
            margin-bottom: 30px;
        }

        .message-header {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .dg-photo {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background: #fcd116;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3em;
            color: #1e3c72;
        }

        .dg-name {
            font-size: 1.8em;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .dg-title {
            font-size: 1.1em;
            opacity: 0.9;
        }

        .message-body {
            padding: 40px;
            font-size: 1.1em;
            line-height: 1.8;
        }

        .message-intro {
            font-size: 1.2em;
            color: #1e3c72;
            margin-bottom: 30px;
            font-weight: 500;
        }

        .message-section {
            margin-bottom: 25px;
        }

        .message-section h3 {
            color: #1e3c72;
            margin-bottom: 15px;
            font-size: 1.3em;
        }

        .message-section p {
            margin-bottom: 15px;
            text-align: justify;
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
            <h1>Message du Directeur Général</h1>
            <p>Agence Nationale des Technologies de l'Information et de la Communication</p>
        </div>
    </div>

    <div class="breadcrumb">
        <div class="container">
            <a href="dynamic_home.php">Accueil</a> > 
            <a href="about.php">Présentation</a> > 
            Message du Directeur Général
        </div>
    </div>

    <div class="main-content">
        <div class="container">
            <div class="message-card">
                <div class="message-header">
                    <div class="dg-photo">👨‍💼</div>
                    <div class="dg-name">Dr. Ebot Ebot Enaw</div>
                    <div class="dg-title">Directeur Général de l'ANTIC</div>
                </div>
                
                <div class="message-body">
                    <div class="message-intro">
                        Chers partenaires, collaborateurs et citoyens,
                    </div>

                    <div class="message-section">
                        <p>
                            En tant que Directeur Général de l'Agence Nationale des Technologies de l'Information et de la Communication (ANTIC), 
                            j'ai le plaisir de vous accueillir sur notre plateforme de certification numérique et de partager avec vous notre vision 
                            pour l'avenir numérique du Cameroun.
                        </p>
                    </div>

                    <div class="message-section">
                        <h3>Notre Mission</h3>
                        <p>
                            L'ANTIC s'est engagée à être le pilier de la transformation numérique du Cameroun, en fournissant des services de 
                            certification électronique de confiance qui sécurisent les transactions numériques et facilitent l'administration 
                            électronique. Notre Infrastructure à Clés Publiques (PKI) représente l'épine dorsale de cette transformation.
                        </p>
                    </div>

                    <div class="highlight-box">
                        <h4>Vision 2035</h4>
                        <p>
                            Notre objectif est de faire du Cameroun un hub numérique de référence en Afrique Centrale, où chaque citoyen, 
                            entreprise et institution gouvernementale peut effectuer des transactions numériques en toute sécurité et confiance.
                        </p>
                    </div>

                    <div class="message-section">
                        <h3>Nos Réalisations</h3>
                        <p>
                            Depuis notre création, nous avons mis en place une infrastructure robuste de certification électronique, 
                            développé des services innovants comme Doc@authANTIC, et établi des partenariats stratégiques avec les 
                            principales institutions du pays. Nos certificats électroniques sont reconnus et utilisés par les 
                            administrations publiques, les entreprises privées et les citoyens.
                        </p>
                    </div>

                    <div class="message-section">
                        <h3>Innovation et Sécurité</h3>
                        <p>
                            L'innovation technologique et la sécurité sont au cœur de nos préoccupations. Nous investissons continuellement 
                            dans les technologies de pointe pour garantir que nos services restent à la hauteur des standards internationaux 
                            tout en répondant aux besoins spécifiques du contexte camerounais.
                        </p>
                    </div>

                    <div class="message-section">
                        <h3>Engagement envers l'Excellence</h3>
                        <p>
                            Notre équipe s'engage à maintenir les plus hauts standards de qualité dans tous nos services. Nous nous efforçons 
                            de fournir un support technique exceptionnel et de garantir la disponibilité de nos services 24h/24 et 7j/7.
                        </p>
                    </div>

                    <div class="message-section">
                        <h3>Perspectives d'Avenir</h3>
                        <p>
                            L'avenir numérique du Cameroun est prometteur, et l'ANTIC continuera à jouer un rôle central dans cette 
                            transformation. Nous nous engageons à développer de nouveaux services, à étendre notre infrastructure et à 
                            renforcer nos partenariats pour servir au mieux les intérêts de notre nation.
                        </p>
                    </div>

                    <div class="message-section">
                        <p>
                            Je vous invite à explorer nos services et à nous contacter pour toute question ou assistance. Ensemble, 
                            construisons un Cameroun numérique plus fort et plus connecté.
                        </p>
                    </div>

                    <div class="message-section">
                        <p style="font-style: italic; margin-top: 30px;">
                            Dr. Ebot Ebot Enaw<br>
                            <strong>Directeur Général de l'ANTIC</strong>
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