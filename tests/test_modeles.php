<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\Model\Salle;
use App\Model\Reservation;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

$configurerCapsule = require __DIR__ . '/../config/database.php';
$configurerCapsule();

Reservation::query()->delete();
Salle::query()->delete();

$salle = Salle::create([
    'nom' => 'Salle Test',
    'batiment' => 'Batiment A',
    'capacite' => 30,
    'type' => 'cours',
    'active' => true,
]);

echo "Salle creee, id={$salle->id}, active=" . var_export($salle->active, true) . PHP_EOL;

$reservation = Reservation::create([
    'salle_id' => $salle->id,
    'responsable' => 'Awa Ndiaye',
    'email' => 'awa.ndiaye@universite.sn',
    'motif' => 'Cours de test',
    'date_debut' => '2026-06-10 10:00:00',
    'date_fin' => '2026-06-10 12:00:00',
]);

echo "Reservation creee, date_debut type=" . get_class($reservation->date_debut) . PHP_EOL;

echo "Salle -> reservations : " . $salle->reservations()->count() . " reservation(s)" . PHP_EOL;
echo "Reservation -> salle : " . $reservation->salle->nom . PHP_EOL;
