<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\DTO\CreerSalleDTO;
use App\DTO\CreerReservationDTO;

function verifier(string $intitule, bool $condition): void
{
    if (!$condition) {
        throw new \RuntimeException("Echec : $intitule");
    }
    echo "OK - $intitule\n";
}

$dtoSalle = CreerSalleDTO::fromArray([
    'nom' => 'Salle B12',
    'batiment' => 'Batiment B',
    'capacite' => '40',
    'type' => 'cours',
    'active' => '1',
]);
verifier('capacite convertie en int', is_int($dtoSalle->capacite));
verifier('active convertie en bool', is_bool($dtoSalle->active));

$dtoReservation = CreerReservationDTO::fromArray([
    'salle_id' => '3',
    'responsable' => 'Awa Ndiaye',
    'email' => 'awa.ndiaye@universite.sn',
    'motif' => 'Cours de test',
    'date_debut' => '2026-06-10 10:00:00',
    'date_fin' => '2026-06-10 12:00:00',
]);
verifier('salleId converti en int', is_int($dtoReservation->salleId));
verifier('dateDebut est un DateTimeImmutable', $dtoReservation->dateDebut instanceof DateTimeImmutable);
verifier('dateFin apres dateDebut', $dtoReservation->dateFin > $dtoReservation->dateDebut);

echo "Tous les tests sont passes.\n";
