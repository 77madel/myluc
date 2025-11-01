<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Politique de Confidentialité - {{ config('app.name') }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background: #f5f5f5;
        }
        .container {
            max-width: 900px;
            margin: 40px auto;
            background: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #10b981;
            margin-bottom: 20px;
            font-size: 32px;
        }
        h2 {
            color: #2c5282;
            margin-top: 30px;
            margin-bottom: 15px;
            font-size: 24px;
        }
        h3 {
            color: #1a3a52;
            margin-top: 20px;
            margin-bottom: 10px;
            font-size: 18px;
        }
        p {
            margin-bottom: 15px;
            text-align: justify;
        }
        ul {
            margin-left: 30px;
            margin-bottom: 15px;
        }
        li {
            margin-bottom: 8px;
        }
        .last-updated {
            color: #666;
            font-style: italic;
            margin-bottom: 30px;
        }
        .contact {
            background: #f0f9ff;
            padding: 20px;
            border-left: 4px solid #10b981;
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Politique de Confidentialité</h1>
        <p class="last-updated">Dernière mise à jour : {{ date('d/m/Y') }}</p>

        <h2>1. Introduction</h2>
        <p>
            Bienvenue sur {{ config('app.name') }}. Nous nous engageons à protéger votre vie privée et vos données personnelles. 
            Cette politique de confidentialité explique comment nous collectons, utilisons et protégeons vos informations lorsque vous utilisez notre plateforme d'apprentissage en ligne.
        </p>

        <h2>2. Données Collectées</h2>
        <h3>2.1 Informations que vous nous fournissez</h3>
        <ul>
            <li>Nom et prénom</li>
            <li>Adresse email</li>
            <li>Informations de profil</li>
            <li>Progression dans les cours</li>
            <li>Certificats obtenus</li>
        </ul>

        <h3>2.2 Informations collectées automatiquement</h3>
        <ul>
            <li>Adresse IP</li>
            <li>Type de navigateur</li>
            <li>Pages visitées</li>
            <li>Durée des sessions</li>
        </ul>

        <h2>3. Utilisation des Données</h2>
        <p>Nous utilisons vos données pour :</p>
        <ul>
            <li>Fournir et améliorer nos services d'enseignement</li>
            <li>Suivre votre progression dans les cours</li>
            <li>Générer des certificats de formation</li>
            <li>Communiquer avec vous concernant votre compte</li>
            <li>Personnaliser votre expérience d'apprentissage</li>
        </ul>

        <h2>4. Partage sur les Réseaux Sociaux</h2>
        <p>
            Lorsque vous choisissez de partager votre certificat sur LinkedIn ou d'autres réseaux sociaux :
        </p>
        <ul>
            <li>Nous utilisons l'API LinkedIn pour publier en votre nom (avec votre autorisation explicite)</li>
            <li>Nous générons un lien public vers votre certificat</li>
            <li>Nous enregistrons la date et la plateforme de partage à des fins statistiques</li>
            <li>Vous gardez le contrôle total sur le contenu partagé</li>
        </ul>

        <h2>5. Protection des Données</h2>
        <p>
            Nous mettons en œuvre des mesures de sécurité appropriées pour protéger vos données personnelles contre 
            tout accès, modification, divulgation ou destruction non autorisés.
        </p>

        <h2>6. Vos Droits</h2>
        <p>Vous avez le droit de :</p>
        <ul>
            <li>Accéder à vos données personnelles</li>
            <li>Rectifier vos données</li>
            <li>Supprimer votre compte</li>
            <li>Retirer votre consentement à tout moment</li>
            <li>Exporter vos données</li>
        </ul>

        <h2>7. Cookies</h2>
        <p>
            Nous utilisons des cookies pour améliorer votre expérience sur notre plateforme. 
            Vous pouvez gérer vos préférences de cookies dans les paramètres de votre navigateur.
        </p>

        <h2>8. Modifications</h2>
        <p>
            Nous nous réservons le droit de modifier cette politique de confidentialité à tout moment. 
            Les modifications seront publiées sur cette page avec une date de mise à jour.
        </p>

        <div class="contact">
            <h2>9. Contact</h2>
            <p>
                Pour toute question concernant cette politique de confidentialité ou vos données personnelles, 
                veuillez nous contacter à :
            </p>
            <p>
                <strong>Email :</strong> privacy@{{ parse_url(config('app.url'), PHP_URL_HOST) ?? 'example.com' }}<br>
                <strong>Site web :</strong> {{ config('app.url') }}
            </p>
        </div>
    </div>
</body>
</html>

