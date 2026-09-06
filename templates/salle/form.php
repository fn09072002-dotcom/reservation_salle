<h1><?= htmlspecialchars($titre, ENT_QUOTES, 'UTF-8') ?></h1>

<?php if (!empty($erreurs)): ?>
    <ul class="erreurs">
        <?php foreach ($erreurs as $champ => $messages): ?>
            <?php foreach ($messages as $message): ?>
                <li><?= htmlspecialchars($champ, ENT_QUOTES, 'UTF-8') ?> : <?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></li>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<?php $action = isset($salle) ? "/salles/{$salle->id}/edit" : "/salles"; ?>
<form action="<?= htmlspecialchars($action, ENT_QUOTES, 'UTF-8') ?>" method="post">
    <label for="nom">Nom</label><br>
    <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($anciennesValeurs['nom'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required><br>

    <label for="batiment">Batiment</label><br>
    <input type="text" id="batiment" name="batiment" value="<?= htmlspecialchars($anciennesValeurs['batiment'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required><br>

    <label for="capacite">Capacite</label><br>
    <input type="number" id="capacite" name="capacite" value="<?= htmlspecialchars((string) ($anciennesValeurs['capacite'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required><br>

    <label for="type">Type</label><br>
    <select id="type" name="type" required>
        <?php $typeActuel = $anciennesValeurs['type'] ?? ''; ?>
        <?php foreach (['cours', 'informatique', 'laboratoire', 'amphitheatre', 'reunion'] as $type): ?>
            <option value="<?= $type ?>" <?= $typeActuel === $type ? 'selected' : '' ?>><?= ucfirst($type) ?></option>
        <?php endforeach; ?>
    </select><br>

    <label for="active">
        <input type="checkbox" id="active" name="active" value="1" <?= !empty($anciennesValeurs['active']) ? 'checked' : '' ?>>
        Active
    </label><br>

    <button type="submit">Enregistrer</button>
</form>

<p><a href="/salles">Annuler</a></p>