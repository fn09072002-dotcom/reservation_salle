<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\Validation\SalleValidator;
use App\Validation\ReservationValidator;

function verifier(string $intitule, bool $condition): void
{
    if (!$condition) {
        throw new \RuntimeException("Echec : $intitule");
    }
    echo "OK - $intitule\n";
}

$validateurSalle = new SalleValidator();

$resultat = $validateurSalle->validate([
    'nom' => 'Salle B12',
    'batiment' => 'Batiment B',
    'capacite' => 40,
    'type' => 'cours',
    'active' => true,
]);
verifier('salle valide', $resultat->isValid());

$resultat = $validateurSalle->validate([
    'nom' => 'A',
    'batiment' => 'Batiment B',
    'capacite' => -5,
    'type' => 'inconnu',
]);
verifier('salle invalide detectee', !$resultat->isValid());
verifier('erreur sur capacite', isset($resultat->errors()['capacite']));
verifier('erreur sur type', isset($resultat->errors()['type']));

$validateurReservation = new ReservationValidator();

$resultat = $validateurReservation->validate([
    'salle_id' => 1,
    'responsable' => 'Awa Ndiaye',
    'email' => 'awa.ndiaye@universite.sn',
    'motif' => 'Cours de test',
    'date_debut' => '2026-06-10 10:00:00',
    'date_fin' => '2026-06-10 12:00:00',
]);
verifier('reservation valide', $resultat->isValid());

$resultat = $validateurReservation->validate([
    'salle_id' => 1,
    'responsable' => '',
    'email' => 'pas-un-email',
    'motif' => 'TP',
    'date_debut' => '2026-06-10 10:00:00',
    'date_fin' => '2026-06-10 12:00:00',
]);
verifier('reservation invalide detectee', !$resultat->isValid());
verifier('erreur sur email', isset($resultat->errors()['email']));

echo "Tous les tests sont passes.\n";
