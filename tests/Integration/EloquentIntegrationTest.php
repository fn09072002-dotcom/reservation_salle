<?php

declare(strict_types=1);

namespace Tests\Integration;

use App\DTO\CreerReservationDTO;
use App\Model\Reservation;
use App\Model\Salle;
use App\Repository\EloquentReservationRepository;
use App\Repository\EloquentSalleRepository;
use App\Service\AnnulerReservationService;
use App\Service\CreerReservationService;
use DateTimeImmutable;
use Tests\Support\DatabaseTestCase;

final class EloquentIntegrationTest extends DatabaseTestCase
{
    protected function avecSchema(): bool
    {
        return true;
    }

    public function testCreationDUneSalleAvecEloquent(): void
    {
        $repository = new EloquentSalleRepository();

        $salle = new Salle([
            'nom' => 'Salle B12',
            'batiment' => 'Batiment B',
            'capacite' => 40,
            'type' => 'cours',
            'active' => true,
        ]);

        $salleEnregistree = $repository->enregistrer($salle);

        self::assertNotNull($salleEnregistree->id);
        self::assertNotNull($repository->trouver($salleEnregistree->id));
    }

    public function testRelationSalleReservations(): void
    {
        $salleRepo = new EloquentSalleRepository();
        $reservationRepo = new EloquentReservationRepository();

        $salle = $salleRepo->enregistrer(new Salle([
            'nom' => 'Salle B12',
            'batiment' => 'Batiment B',
            'capacite' => 40,
            'type' => 'cours',
            'active' => true,
        ]));

        $demain = (new DateTimeImmutable())->modify('+1 day');

        $reservationRepo->enregistrer(new Reservation([
            'salle_id' => $salle->id,
            'responsable' => 'Awa Ndiaye',
            'email' => 'awa.ndiaye@universite.sn',
            'motif' => 'Cours de test',
            'date_debut' => $demain->setTime(10, 0),
            'date_fin' => $demain->setTime(12, 0),
            'statut' => 'confirmee',
        ]));

        $salleRechargee = $salleRepo->trouver($salle->id);

        self::assertCount(1, $salleRechargee->reservations);
        self::assertSame($salle->id, $salleRechargee->reservations->first()->salle->id);
    }

    public function testRechercheDeChevauchement(): void
    {
        $salleRepo = new EloquentSalleRepository();
        $reservationRepo = new EloquentReservationRepository();
        $service = new CreerReservationService($salleRepo, $reservationRepo);

        $salle = $salleRepo->enregistrer(new Salle([
            'nom' => 'Salle B12',
            'batiment' => 'Batiment B',
            'capacite' => 40,
            'type' => 'cours',
            'active' => true,
        ]));

        $demain = (new DateTimeImmutable())->modify('+1 day');

        $service->creer(new CreerReservationDTO(
            $salle->id,
            'Awa Ndiaye',
            'awa.ndiaye@universite.sn',
            'Cours de test',
            $demain->setTime(10, 0),
            $demain->setTime(12, 0)
        ));

        $conflit = $reservationRepo->rechercherConflit(
            $salle->id,
            $demain->setTime(11, 0),
            $demain->setTime(13, 0)
        );

        self::assertNotNull($conflit);
    }

    public function testAnnulationDUneReservation(): void
    {
        $salleRepo = new EloquentSalleRepository();
        $reservationRepo = new EloquentReservationRepository();
        $creerService = new CreerReservationService($salleRepo, $reservationRepo);
        $annulerService = new AnnulerReservationService($reservationRepo);

        $salle = $salleRepo->enregistrer(new Salle([
            'nom' => 'Salle B12',
            'batiment' => 'Batiment B',
            'capacite' => 40,
            'type' => 'cours',
            'active' => true,
        ]));

        $demain = (new DateTimeImmutable())->modify('+1 day');

        $reservation = $creerService->creer(new CreerReservationDTO(
            $salle->id,
            'Awa Ndiaye',
            'awa.ndiaye@universite.sn',
            'Cours de test',
            $demain->setTime(10, 0),
            $demain->setTime(12, 0)
        ));

        $reservationAnnulee = $annulerService->annuler($reservation->id);

        self::assertSame('annulee', $reservationAnnulee->statut);
        self::assertSame('annulee', $reservationRepo->trouver($reservation->id)->statut);
    }
}
