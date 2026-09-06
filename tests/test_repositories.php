<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\Repository\EloquentSalleRepository;
use App\Repository\EloquentReservationRepository;
use App\Model\Reservation;
use DateTimeImmutable;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

$configurerCapsule = require __DIR__ . '/../config/database.php';
$configurerCapsule();

function verifier(string $intitule, bool $condition): void
{
    if (!$condition) {
        throw new \RuntimeException("Echec : $intitule");
    }
    echo "OK - $intitule\n";
}

// Nettoyage prealable
Reservation::query()->delete();

$salleRepo = new EloquentSalleRepository();
$reservationRepo = new EloquentReservationRepository();

$salles = $salleRepo->lister();
verifier('lister() retourne au moins 5 salles', $salles->count() >= 5);

$salle = $salles->first();
$salleTrouvee = $salleRepo->trouver($salle->id);
verifier('trouver() retrouve la bonne salle', $salleTrouvee->id === $salle->id);

$reservation = new Reservation([
    'salle_id' => $salle->id,
    'responsable' => 'Awa Ndiaye',
    'email' => 'awa.ndiaye@universite.sn',
    'motif' => 'Cours de test',
    'date_debut' => '2026-06-10 10:00:00',
    'date_fin' => '2026-06-10 12:00:00',
]);
$reservationRepo->enregistrer($reservation);
verifier('reservation enregistree avec un id', $reservation->id !== null);

// Chevauchement : 11h-13h chevauche 10h-12h
$conflit = $reservationRepo->rechercherConflit(
    $salle->id,
    new DateTimeImmutable('2026-06-10 11:00:00'),
    new DateTimeImmutable('2026-06-10 13:00:00')
);
verifier('conflit detecte sur chevauchement', $conflit !== null);

// Pas de chevauchement : 12h-14h (voisine, ne chevauche pas 10h-12h)
$pasDeConflit = $reservationRepo->rechercherConflit(
    $salle->id,
    new DateTimeImmutable('2026-06-10 12:00:00'),
    new DateTimeImmutable('2026-06-10 14:00:00')
);
verifier('pas de conflit sur reservations voisines', $pasDeConflit === null);

$reservationRepo->annuler($reservation);
$reservationAnnulee = $reservationRepo->trouver($reservation->id);
verifier('reservation annulee', $reservationAnnulee->statut === 'annulee');

// Apres annulation, plus de conflit
$plusDeConflit = $reservationRepo->rechercherConflit(
    $salle->id,
    new DateTimeImmutable('2026-06-10 11:00:00'),
    new DateTimeImmutable('2026-06-10 13:00:00')
);
verifier('reservation annulee ne bloque plus', $plusDeConflit === null);

echo "Tous les tests sont passes.\n";
