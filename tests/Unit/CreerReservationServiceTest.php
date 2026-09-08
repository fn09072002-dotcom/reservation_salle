<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\DTO\CreerReservationDTO;
use App\Exception\SalleIndisponibleException;
use App\Model\Salle;
use App\Service\CreerReservationService;
use DateTimeImmutable;
use InvalidArgumentException;
use Tests\Fakes\ReservationRepositoryEnMemoire;
use Tests\Fakes\SalleRepositoryEnMemoire;
use Tests\Support\DatabaseTestCase;

final class CreerReservationServiceTest extends DatabaseTestCase
{
    private SalleRepositoryEnMemoire $salles;
    private ReservationRepositoryEnMemoire $reservations;
    private CreerReservationService $service;
    private DateTimeImmutable $demain;

    protected function setUp(): void
    {
        parent::setUp();

        $this->salles = new SalleRepositoryEnMemoire();
        $this->reservations = new ReservationRepositoryEnMemoire();
        $this->service = new CreerReservationService($this->salles, $this->reservations);
        $this->demain = (new DateTimeImmutable())->modify('+1 day');

        $salleActive = new Salle([
            'nom' => 'Salle B12',
            'batiment' => 'Batiment B',
            'capacite' => 40,
            'type' => 'cours',
            'active' => true,
        ]);
        $salleActive->id = 1;
        $this->salles->ajouter($salleActive);

        $salleInactive = new Salle([
            'nom' => 'Salle Fermee',
            'batiment' => 'Batiment C',
            'capacite' => 10,
            'type' => 'reunion',
            'active' => false,
        ]);
        $salleInactive->id = 2;
        $this->salles->ajouter($salleInactive);
    }

    private function dto(
        int $salleId,
        DateTimeImmutable $debut,
        DateTimeImmutable $fin,
        string $responsable = 'Awa Ndiaye',
        string $email = 'awa.ndiaye@universite.sn',
        string $motif = 'Cours de test'
    ): CreerReservationDTO {
        return new CreerReservationDTO($salleId, $responsable, $email, $motif, $debut, $fin);
    }

    public function testReservationValideEstCreee(): void
    {
        $reservation = $this->service->creer(
            $this->dto(1, $this->demain->setTime(10, 0), $this->demain->setTime(12, 0))
        );

        self::assertNotNull($reservation->id);
        self::assertSame('confirmee', $reservation->statut);
    }

    public function testSalleInexistanteEstRejetee(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->service->creer(
            $this->dto(999, $this->demain->setTime(10, 0), $this->demain->setTime(12, 0))
        );
    }

    public function testSalleInactiveEstRejetee(): void
    {
        $this->expectException(SalleIndisponibleException::class);

        $this->service->creer(
            $this->dto(2, $this->demain->setTime(10, 0), $this->demain->setTime(11, 0))
        );
    }

    public function testFinAnterieureAuDebutEstRejetee(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->service->creer(
            $this->dto(1, $this->demain->setTime(12, 0), $this->demain->setTime(10, 0))
        );
    }

    public function testDureeSuperieureAQuatreHeuresEstRejetee(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->service->creer(
            $this->dto(1, $this->demain->setTime(8, 0), $this->demain->setTime(14, 0))
        );
    }

    public function testDatePasseeEstRejetee(): void
    {
        $hier = (new DateTimeImmutable())->modify('-1 day');

        $this->expectException(InvalidArgumentException::class);

        $this->service->creer(
            $this->dto(1, $hier->setTime(10, 0), $hier->setTime(11, 0))
        );
    }

    public function testConflitAvecReservationExistanteEstDetecte(): void
    {
        $this->service->creer(
            $this->dto(1, $this->demain->setTime(10, 0), $this->demain->setTime(12, 0))
        );

        $this->expectException(SalleIndisponibleException::class);

        $this->service->creer(
            $this->dto(1, $this->demain->setTime(11, 0), $this->demain->setTime(13, 0))
        );
    }

    public function testReservationVoisineSansChevauchementEstAcceptee(): void
    {
        $this->service->creer(
            $this->dto(1, $this->demain->setTime(10, 0), $this->demain->setTime(12, 0))
        );

        $reservationVoisine = $this->service->creer(
            $this->dto(1, $this->demain->setTime(12, 0), $this->demain->setTime(14, 0))
        );

        self::assertNotNull($reservationVoisine->id);
    }
}
