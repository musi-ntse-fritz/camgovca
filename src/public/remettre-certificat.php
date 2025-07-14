<?php
$page_title = "Remettre un Certificat - ANTIC";
$page_description = "Remettez un certificat existant avec de nouveaux paramètres";
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

        .form-container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #1e3c72;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            box-sizing: border-box;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }

        .btn {
            background: #1e3c72;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .btn:hover {
            background: #2a5298;
        }

        .btn-secondary {
            background: #6c757d;
            margin-left: 10px;
        }

        .btn-secondary:hover {
            background: #5a6268;
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

        @media (max-width: 768px) {
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
                <h1>Remettre un Certificat</h1>
                <div class="nav-links">
                    <a href="index.php">Accueil</a>
                    <a href="operations.php">Opérations</a>
                    <a href="contact-info.php">Contact</a>
                </div>
            </div>
        </div>
    </div>

    <div class="main-content">
        <div class="container">
            <div class="page-header">
                <h2 class="page-title">Remettre un Certificat Électronique</h2>
                <p class="page-description">Remettez un certificat existant avec de nouveaux paramètres ou pour corriger des erreurs.</p>
            </div>

            <div class="highlight-box">
                <h4>À propos de la Remise de Certificat</h4>
                <p>La remise de certificat permet de créer un nouveau certificat basé sur un certificat existant, avec la possibilité de modifier certains paramètres tout en conservant l'identité du titulaire.</p>
            </div>

            <div class="form-container">
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="certificate_id">Numéro de Série du Certificat Original *</label>
                        <input type="text" id="certificate_id" name="certificate_id" required placeholder="Ex: CERT-20241201-ABC12345">
                    </div>

                    <div class="form-group">
                        <label for="reason">Raison de la Remise *</label>
                        <select id="reason" name="reason" required>
                            <option value="">Sélectionnez une raison</option>
                            <option value="key_compromise">Compromission de la clé privée</option>
                            <option value="ca_compromise">Compromission de l'autorité de certification</option>
                            <option value="affiliation_change">Changement d'affiliation</option>
                            <option value="superseded">Certificat remplacé</option>
                            <option value="cessation_of_operation">Cessation d'opération</option>
                            <option value="certificate_hold">Suspension temporaire</option>
                            <option value="other">Autre raison</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="new_validity">Nouvelle Période de Validité (jours)</label>
                        <input type="number" id="new_validity" name="new_validity" min="30" max="3650" value="365" placeholder="365">
                    </div>

                    <div class="form-group">
                        <label for="additional_notes">Notes Additionnelles</label>
                        <textarea id="additional_notes" name="additional_notes" placeholder="Détails supplémentaires sur la remise du certificat..."></textarea>
                    </div>

                    <div style="text-align: center; margin-top: 30px;">
                        <button type="submit" class="btn">Remettre le Certificat</button>
                        <a href="operations.php" class="btn btn-secondary">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>