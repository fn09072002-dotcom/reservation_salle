<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($titre ?? 'Reservation de salles', ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="stylesheet" href="/assets/style.css">
</head>
<body>
    <header>
        <nav>
            <a href="/">Accueil</a>
            <a href="/salles">Salles</a>
            <a href="/reservations">Reservations</a>
        </nav>
    </header>
    <main>
              <?php if (!empty($messageSucces)): ?>
            <p class="succes"><?= htmlspecialchars($messageSucces, ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>

        <?php if (!empty($messageErreur)): ?>
            <p class="erreur-globale"><?= htmlspecialchars($messageErreur, ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>

        <?= $contenu ?>
    </main>
</body>
</html>
