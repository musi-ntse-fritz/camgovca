<?php
$page_title = "Opérations sur les Certificats - ANTIC";
$page_description = "Gérez vos certificats électroniques avec nos outils d'opérations avancées";
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
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
            padding: 20px 0;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            font-size: 1.8em;
            font-weight: 300;
        }

        .nav-links {
            display: flex;
            gap: 15px;
        }

        .nav-links a {
            color: white;
            text-decoration: none;
            padding: 8px 15px;
            background: rgba(255,255,255,0.1);
            border-radius: 5px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .nav-links a:hover {
            background: rgba(255,255,255,0.2);
        }

        .main-content {
            padding: 30px 0;
        }

        .page-header {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .page-title {
            color: #1e3c72;
            font-size: 1.8em;
            margin-bottom: 10px;
        }

        .page-description {
            color: #666;
            font-size: 1.1em;
        }

        .operations-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }

        .operation-card {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            text-decoration: none;
            color: inherit;
            display: block;
        }

        .operation-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }

        .operation-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
            color: white;
            font-size: 24px;
        }

        .operation-title {
            color: #1e3c72;
            font-size: 1.2em;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .operation-description {
            color: #666;
            font-size: 0.9em;
            line-height: 1.5;
        }

        .highlight-box {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-left: 4px solid #1e3c72;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
        }

        .highlight-box h4 {
            color: #1e3c72;
            margin-bottom: 10px;
        }

        .highlight-box ul {
            list-style: none;
            padding: 0;
        }

        .highlight-box li {
            padding: 8px 0;
            border-bottom: 1px solid #dee2e6;
        }

        .highlight-box li:last-child {
            border-bottom: none;
        }

        .highlight-box a {
            color: #1e3c72;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .highlight-box a:hover {
            color: #2a5298;
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .operations-grid {
                grid-template-columns: 1fr;
            }
            
            .header-content {
                flex-direction: column;
                gap: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="container">
            <div class="header-content">
                <h1>Opérations sur les Certificats</h1>
                <div class="nav-links">
                    <a href="index.php">Accueil</a>
                    <a href="demande-de-certificats-fr.php">Demande de Certificat</a>
                    <a href="contact-info.php">Contact</a>
                </div>
            </div>
        </div>
    </div>

    <div class="main-content">
        <div class="container">
            <div class="page-header">
                <h2 class="page-title">Gestion des Certificats Électroniques</h2>
                <p class="page-description">Accédez à toutes les opérations disponibles pour la gestion de vos certificats électroniques émis par l'ANTIC.</p>
            </div>

            <div class="highlight-box">
                <h4>À propos des Opérations sur les Certificats</h4>
                <p>Cette section vous permet d'effectuer toutes les opérations nécessaires sur vos certificats électroniques, de l'émission à la révocation, en passant par la gestion des mots de passe et la vérification.</p>
            </div>

            <div class="operations-grid">
                <a href="emettre-certificat.php" class="operation-card">
                    <div class="operation-icon">📄</div>
                    <h3 class="operation-title">Émettre un certificat</h3>
                    <p class="operation-description">Créez et émettez un nouveau certificat électronique pour sécuriser vos communications et transactions.</p>
                </a>

                <a href="remettre-certificat.php" class="operation-card">
                    <div class="operation-icon">🔄</div>
                    <h3 class="operation-title">Remettre un certificat</h3>
                    <p class="operation-description">Remettez un certificat existant avec de nouveaux paramètres ou pour corriger des erreurs.</p>
                </a>

                <a href="changer-mot-passe-certificat.php" class="operation-card">
                    <div class="operation-icon">🔐</div>
                    <h3 class="operation-title">Changer le mot de passe d'un certificat</h3>
                    <p class="operation-description">Modifiez le mot de passe de votre certificat pour renforcer la sécurité de vos accès.</p>
                </a>

                <a href="suspendre-certificat.php" class="operation-card">
                    <div class="operation-icon">⏸️</div>
                    <h3 class="operation-title">Suspendre un certificat</h3>
                    <p class="operation-description">Temporairement désactivez un certificat sans le révoquer définitivement.</p>
                </a>

                <a href="reprendre-certificat.php" class="operation-card">
                    <div class="operation-icon">▶️</div>
                    <h3 class="operation-title">Reprendre un certificat</h3>
                    <p class="operation-description">Réactivez un certificat précédemment suspendu.</p>
                </a>

                <a href="revoquer-certificat.php" class="operation-card">
                    <div class="operation-icon">❌</div>
                    <h3 class="operation-title">Révoquer un certificat</h3>
                    <p class="operation-description">Annulez définitivement un certificat électronique.</p>
                </a>

                <a href="copier-certificat.php" class="operation-card">
                    <div class="operation-icon">📋</div>
                    <h3 class="operation-title">Copier un certificat</h3>
                    <p class="operation-description">Copiez un certificat existant pour l'utiliser sur d'autres appareils ou systèmes.</p>
                </a>

                <a href="renouveler-certificat.php" class="operation-card">
                    <div class="operation-icon">🔄</div>
                    <h3 class="operation-title">Renouveler un certificat</h3>
                    <p class="operation-description">Renouvelez un certificat expiré ou proche de l'expiration pour maintenir la continuité du service.</p>
                </a>

                <a href="verifier-identite-certificat.php" class="operation-card">
                    <div class="operation-icon">🆔</div>
                    <h3 class="operation-title">Vérifier le numéro d'identité d'un certificat</h3>
                    <p class="operation-description">Vérifiez l'identité associée à un certificat pour confirmer son authenticité.</p>
                </a>

                <a href="verifier-certificat.php" class="operation-card">
                    <div class="operation-icon">✅</div>
                    <h3 class="operation-title">Vérifier un certificat</h3>
                    <p class="operation-description">Vérifiez la validité et l'authenticité d'un certificat électronique.</p>
                </a>
            </div>
        </div>
    </div>
</body>
</html>