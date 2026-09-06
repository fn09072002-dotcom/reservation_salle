<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\DTO\CreerReservationDTO;
use App\Exception\SalleIndisponibleException;
use App\Model\Salle;
use App\Service\AnnulerReservationService;
use App\Service\CreerReservationService;
use Illuminate\Database\Capsule\Manager as Capsule;
use Tests\Fakes\ReservationRepositoryEnMemoire;
use Tests\Fakes\SalleRepositoryEnMemoire;

// Eloquent a besoin d'une connexion pour construire des objets (formatage
// des dates), meme sans persister quoi que ce soit. On utilise SQLite en
// memoire : aucun serveur MySQL n'est requis pour ce test.
$capsule = new Capsule();
$capsule->addConnection([
    'driver' => 'sqlite',
    'database' => ':memory:',
]);
$capsule->setAsGlobal();
$capsule->bootEloquent();

function verifier(string $intitule, bool $condition): void
{
    if (!$condition) {
        throw new \RuntimeException("Echec : $intitule");
    }
    echo "OK - $intitule\n";
}


$salleRepo = new SalleRepositoryEnMemoire();
$reservationRepo = new ReservationRepositoryEnMemoire();

$salleActive = new Salle(['nom' => 'Salle B12', 'batiment' => 'B', 'capacite' => 40, 'type' => 'cours', 'active' => true]);
$salleActive->id = 1;
$salleRepo->ajouter($salleActive);

$salleInactive = new Salle(['nom' => 'Salle Fermee', 'batiment' => 'C', 'capacite' => 10, 'type' => 'reunion', 'active' => false]);
$salleInactive->id = 2;
$salleRepo->ajouter($salleInactive);

$service = new CreerReservationService($salleRepo, $reservationRepo);

$demain = (new DateTimeImmutable())->modify('+1 day');

// Cas 1 : reservation valide
$dto = new CreerReservationDTO(
    salleId: 1,
    responsable: 'Awa Ndiaye',
    email: 'awa.ndiaye@universite.sn',
    motif: 'Cours de test',
    dateDebut: $demain->setTime(10, 0),
    dateFin: $demain->setTime(12, 0)
);
$reservation = $service->creer($dto);
verifier('reservation valide creee', $reservation->id !== null);

// Cas 2 : salle inactive
try {
    $service->creer(new CreerReservationDTO(2, 'X', 'x@x.sn', 'Motif test', $demain->setTime(10, 0), $demain->setTime(11, 0)));
    verifier('salle inactive rejetee', false);
} catch (SalleIndisponibleException $e) {
    verifier('salle inactive rejetee', true);
}

// Cas 3 : fin avant debut
try {
    $service->creer(new CreerReservationDTO(1, 'X', 'x@x.sn', 'Motif test', $demain->setTime(12, 0), $demain->setTime(10, 0)));
    verifier('fin avant debut rejetee', false);
} catch (\InvalidArgumentException $e) {
    verifier('fin avant debut rejetee', true);
}

// Cas 4 : duree excessive
try {
    $service->creer(new CreerReservationDTO(1, 'X', 'x@x.sn', 'Motif test', $demain->setTime(14, 0), $demain->setTime(20, 0)));
    verifier('duree excessive rejetee', false);
} catch (\InvalidArgumentException $e) {
    verifier('duree excessive rejetee', true);
}

// Cas 5 : date passee
try {
    $hier = (new DateTimeImmutable())->modify('-1 day');
    $service->creer(new CreerReservationDTO(1, 'X', 'x@x.sn', 'Motif test', $hier->setTime(10, 0), $hier->setTime(11, 0)));
    verifier('date passee rejetee', false);
} catch (\InvalidArgumentException $e) {
    verifier('date passee rejetee', true);
}

// Cas 6 : conflit avec la reservation existante (10h-12h), nouvelle 11h-13h
try {
    $service->creer(new CreerReservationDTO(1, 'X', 'x@x.sn', 'Motif test', $demain->setTime(11, 0), $demain->setTime(13, 0)));
    verifier('conflit detecte', false);
} catch (SalleIndisponibleException $e) {
    verifier('conflit detecte', true);
}

// Cas 7 : reservation voisine, 12h-14h ne chevauche pas 10h-12h
$dtoVoisine = new CreerReservationDTO(1, 'X', 'x@x.sn', 'Motif voisin', $demain->setTime(12, 0), $demain->setTime(14, 0));
$reservationVoisine = $service->creer($dtoVoisine);
verifier('reservation voisine acceptee', $reservationVoisine->id !== null);

// Annulation
$annulerService = new AnnulerReservationService($reservationRepo);
$annulerService->annuler($reservation->id);
$reservationAnnulee = $reservationRepo->trouver($reservation->id);
verifier('reservation annulee', $reservationAnnulee->statut === 'annulee');

echo "Tous les tests sont passes.\n";
