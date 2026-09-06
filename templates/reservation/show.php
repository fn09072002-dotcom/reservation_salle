<h1>Reservation #<?= (int) $reservation->id ?></h1>

<ul>
    <li>Salle : <?= htmlspecialchars($reservation->salle->nom ?? '-', ENT_QUOTES, 'UTF-8') ?></li>
    <li>Responsable : <?= htmlspecialchars($reservation->responsable, ENT_QUOTES, 'UTF-8') ?></li>
    <li>Email : <?= htmlspecialchars($reservation->email, ENT_QUOTES, 'UTF-8') ?></li>
    <li>Motif : <?= htmlspecialchars($reservation->motif, ENT_QUOTES, 'UTF-8') ?></li>
    <li>Debut : <?= htmlspecialchars($reservation->date_debut->format('d/m/Y H:i'), ENT_QUOTES, 'UTF-8') ?></li>
    <li>Fin : <?= htmlspecialchars($reservation->date_fin->format('d/m/Y H:i'), ENT_QUOTES, 'UTF-8') ?></li>
    <li>Statut : <?= htmlspecialchars($reservation->statut, ENT_QUOTES, 'UTF-8') ?></li>
</ul>

<?php if ($reservation->statut === 'confirmee'): ?>
    <form action="/reservations/<?= (int) $reservation->id ?>/cancel" method="post">
        <button type="submit">Annuler cette reservation</button>
    </form>
<?php endif; ?>

<p><a href="/reservations">Retour a la liste</a></p>
