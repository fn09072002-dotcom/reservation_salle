<h1>Liste des salles</h1>

<p><a href="/salles/create">Ajouter une salle</a></p>

<?php if ($salles->isEmpty()): ?>
    <p>Aucune salle enregistree.</p>
<?php else: ?>
    <table border="1" cellpadding="8">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Batiment</th>
                <th>Capacite</th>
                <th>Type</th>
                <th>Statut</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($salles as $salle): ?>
                <tr>
                    <td><?= htmlspecialchars($salle->nom, ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($salle->batiment, ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars((string) $salle->capacite, ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($salle->type, ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= $salle->active ? 'Active' : 'Inactive' ?></td>
                    <td>
                        <a href="/salles/<?= (int) $salle->id ?>">Voir</a>
                        <a href="/salles/<?= (int) $salle->id ?>/edit">Modifier</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>