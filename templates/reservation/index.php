<h1>Reservations</h1>

<p><a href="/reservations/create">Nouvelle reservation</a></p>

<?php if ($reservations->isEmpty()): ?>
    <p>Aucune reservation enregistree.</p>
<?php else: ?>
    <table border="1" cellpadding="8">
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
                    <td><?= htmlspecialchars($reservation->statut, ENT_QUOTES, 'UTF-8') ?></td>
                    <td><a href="/reservations/<?= (int) $reservation->id ?>">Voir</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
