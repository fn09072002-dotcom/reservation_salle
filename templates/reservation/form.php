<h1><?= htmlspecialchars($titre, ENT_QUOTES, 'UTF-8') ?></h1>
        <?php foreach ($erreurs as $champ => $messages): ?>
            <?php foreach ($messages as $message): ?>
                <li><?php if ($champ !== 'general'): ?><?= htmlspecialchars($champ, ENT_QUOTES, 'UTF-8') ?> : <?php endif; ?><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></li>
            <?php endforeach; ?>
        <?php endforeach; ?>

<form action="/reservations" method="post">
    <label for="salle_id">Salle</label><br>
    <select id="salle_id" name="salle_id" required>
        <?php $salleActuelle = $anciennesValeurs['salle_id'] ?? ''; ?>
        <?php foreach ($salles as $salle): ?>
            <?php if (!$salle->active): continue; endif; ?>
            <option value="<?= (int) $salle->id ?>" <?= (string) $salleActuelle === (string) $salle->id ? 'selected' : '' ?>>
                <?= htmlspecialchars($salle->nom, ENT_QUOTES, 'UTF-8') ?> (<?= (int) $salle->capacite ?> places)
            </option>
        <?php endforeach; ?>
    </select><br>

    <label for="responsable">Responsable</label><br>
    <input type="text" id="responsable" name="responsable" value="<?= htmlspecialchars($anciennesValeurs['responsable'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required><br>

    <label for="email">Email</label><br>
    <input type="email" id="email" name="email" value="<?= htmlspecialchars($anciennesValeurs['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required><br>

    <label for="motif">Motif</label><br>
    <input type="text" id="motif" name="motif" value="<?= htmlspecialchars($anciennesValeurs['motif'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required><br>

    <label for="date_debut">Date de debut</label><br>
    <input type="datetime-local" id="date_debut" name="date_debut" value="<?= htmlspecialchars($anciennesValeurs['date_debut'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required><br>

    <label for="date_fin">Date de fin</label><br>
    <input type="datetime-local" id="date_fin" name="date_fin" value="<?= htmlspecialchars($anciennesValeurs['date_fin'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required><br>

    <button type="submit">Reserver</button>
</form>

<p><a href="/reservations">Annuler</a></p>
