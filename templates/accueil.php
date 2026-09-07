
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Gestion des réservations</title>

    <style>
        /* Réinitialisation */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Corps de la page */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f8;
            min-height: 100vh;

            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* Conteneur principal */
        .container {
            background-color: white;
            padding: 40px;
            border-radius: 12px;
            text-align: center;
            width: 500px;
            max-width: 90%;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }

        /* Titre */
        h1 {
            color: #2c3e50;
            margin-bottom: 20px;
        }

        /* Texte */
        p {
            color: #555;
            margin-bottom: 20px;
        }

        /* Liens */
        a {
            display: block;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            padding: 12px;
            margin-top: 10px;
            border-radius: 6px;
            transition: 0.3s;
        }

        /* Effet au survol */
        a:hover {
            background-color: #2980b9;
        }
    </style>
</head>

<body>

    <div class="container">

        <h1>Gestion des réservations de salles</h1>

        <p>
            Bienvenue sur l'application de réservation de salles universitaires.
        </p>

        <p>
            <a href="/salles">Voir les salles</a>
        </p>

        <p>
            <a href="/reservations">Voir les réservations</a>
        </p>

    </div>

</body>
</html>
