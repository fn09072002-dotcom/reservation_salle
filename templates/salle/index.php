<div class="page-header">
    <h1>Liste des salles</h1>
    <a href="/salles/create">Ajouter une salle</a>
</div>

<?php if ($salles->isEmpty()): ?>
    <div class="etat-vide">Aucune salle enregistree.</div>
<?php else: ?>
    <table>
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
                    <td>
                        <?php if ($salle->active): ?>
                            <span class="badge badge-actif">Active</span>
                        <?php else: ?>
                            <span class="badge badge-inactif">Inactive</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="/salles/<?= (int) $salle->id ?>">Voir</a>
                        <a href="/salles/<?= (int) $salle->id ?>/edit">Modifier</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
