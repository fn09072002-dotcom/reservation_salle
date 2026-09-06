<h1><?= htmlspecialchars($salle->nom, ENT_QUOTES, 'UTF-8') ?></h1>

<ul>
    <li>Batiment : <?= htmlspecialchars($salle->batiment, ENT_QUOTES, 'UTF-8') ?></li>
    <li>Capacite : <?= htmlspecialchars((string) $salle->capacite, ENT_QUOTES, 'UTF-8') ?></li>
    <li>Type : <?= htmlspecialchars($salle->type, ENT_QUOTES, 'UTF-8') ?></li>
    <li>Statut : <?= $salle->active ? 'Active' : 'Inactive' ?></li>
</ul>

<p><a href="/salles/<?= (int) $salle->id ?>/edit">Modifier</a></p>
<p><a href="/salles">Retour a la liste</a></p>