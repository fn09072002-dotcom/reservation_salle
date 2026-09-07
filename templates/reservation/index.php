<div class="page-header">
    <h1>Reservations</h1>
    <a href="/reservations/create">Nouvelle reservation</a>
</div>

<?php if ($reservations->isEmpty()): ?>
    <div class="etat-vide">Aucune reservation enregistree.</div>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Salle</th>
                <th>Responsable</th>
                <th>Debut</th>
                <th>Fin</th>
                <th>Statut</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($reservations as $reservation): ?>
                <tr>
                    <td><?= htmlspecialchars($reservation->salle->nom ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($reservation->responsable, ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($reservation->date_debut->format('d/m/Y H:i'), ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($reservation->date_fin->format('d/m/Y H:i'), ENT_QUOTES, 'UTF-8') ?></td>
                    <td>
                        <?php if ($reservation->statut === 'confirmee'): ?>
                            <span class="badge badge-confirmee">Confirmee</span>
                        <?php else: ?>
                            <span class="badge badge-annulee">Annulee</span>
                        <?php endif; ?>
                    </td>
                    <td><a href="/reservations/<?= (int) $reservation->id ?>">Voir</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
