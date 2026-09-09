<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\DTO\CreerReservationDTO;
use App\Exception\SalleIndisponibleException;
use App\Model\Reservation;
use App\Model\Salle;
use App\Repository\ReservationRepositoryInterface;
use App\Repository\SalleRepositoryInterface;
use App\Service\CreerReservationService;
use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Tests\Support\BooteEloquentPourLesCasts;

final class CreerReservationServiceTest extends TestCase
{
    use BooteEloquentPourLesCasts;

    private SalleRepositoryInterface&MockObject $salles;
    private ReservationRepositoryInterface&MockObject $reservations;
    private CreerReservationService $service;
    private DateTimeImmutable $demain;
    private Salle $salleActive;
    private Salle $salleInactive;

    protected function setUp(): void
    {
        $this->booterEloquentPourLesCasts();

        $this->salles = $this->createMock(SalleRepositoryInterface::class);
        $this->reservations = $this->createMock(ReservationRepositoryInterface::class);

        $this->reservations->method('creerAvecVerrou')
            ->willReturnCallback(fn (int $salleId, callable $callback) => $callback());

        $this->service = new CreerReservationService($this->salles, $this->reservations);
        $this->demain = (new DateTimeImmutable())->modify('+1 day');

        $this->salleActive = new Salle([
            'nom' => 'Salle B12',
            'batiment' => 'Batiment B',
            'capacite' => 40,
            'type' => 'cours',
            'active' => true,
        ]);
        $this->salleActive->id = 1;

        $this->salleInactive = new Salle([
            'nom' => 'Salle Fermee',
            'batiment' => 'Batiment C',
            'capacite' => 10,
            'type' => 'reunion',
            'active' => false,
        ]);
        $this->salleInactive->id = 2;
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
        $this->salles->method('trouver')->with(1)->willReturn($this->salleActive);
        $this->reservations->method('rechercherConflit')->willReturn(null);
        $this->reservations->method('enregistrer')->willReturnArgument(0);

        $reservation = $this->service->creer(
            $this->dto(1, $this->demain->setTime(10, 0), $this->demain->setTime(12, 0))
        );

        self::assertSame('confirmee', $reservation->statut);
    }

    public function testSalleInexistanteEstRejetee(): void
    {
        $this->salles->method('trouver')->with(999)->willReturn(null);

        $this->expectException(InvalidArgumentException::class);

        $this->service->creer(
            $this->dto(999, $this->demain->setTime(10, 0), $this->demain->setTime(12, 0))
        );
    }

    public function testSalleInactiveEstRejetee(): void
    {
        $this->salles->method('trouver')->with(2)->willReturn($this->salleInactive);

        $this->expectException(SalleIndisponibleException::class);

        $this->service->creer(
            $this->dto(2, $this->demain->setTime(10, 0), $this->demain->setTime(11, 0))
        );
    }

    public function testFinAnterieureAuDebutEstRejetee(): void
    {
        $this->salles->method('trouver')->willReturn($this->salleActive);

        $this->expectException(InvalidArgumentException::class);

        $this->service->creer(
            $this->dto(1, $this->demain->setTime(12, 0), $this->demain->setTime(10, 0))
        );
    }

    public function testDureeSuperieureAQuatreHeuresEstRejetee(): void
    {
        $this->salles->method('trouver')->willReturn($this->salleActive);

        $this->expectException(InvalidArgumentException::class);

        $this->service->creer(
            $this->dto(1, $this->demain->setTime(8, 0), $this->demain->setTime(14, 0))
        );
    }

    public function testDatePasseeEstRejetee(): void
    {
        $this->salles->method('trouver')->willReturn($this->salleActive);

        $hier = (new DateTimeImmutable())->modify('-1 day');

        $this->expectException(InvalidArgumentException::class);

        $this->service->creer(
            $this->dto(1, $hier->setTime(10, 0), $hier->setTime(11, 0))
        );
    }

    public function testConflitAvecReservationExistanteEstDetecte(): void
    {
        $this->salles->method('trouver')->willReturn($this->salleActive);

        $conflitExistant = new Reservation([
            'salle_id' => 1,
            'responsable' => 'Quelqu\'un d\'autre',
            'email' => 'autre@universite.sn',
            'motif' => 'Deja reservee',
            'date_debut' => $this->demain->setTime(10, 0),
            'date_fin' => $this->demain->setTime(12, 0),
            'statut' => 'confirmee',
        ]);

        $this->reservations->method('rechercherConflit')->willReturn($conflitExistant);

        $this->expectException(SalleIndisponibleException::class);

        $this->service->creer(
            $this->dto(1, $this->demain->setTime(11, 0), $this->demain->setTime(13, 0))
        );
    }

    public function testReservationVoisineSansChevauchementEstAcceptee(): void
    {
        $this->salles->method('trouver')->willReturn($this->salleActive);
        $this->reservations->method('rechercherConflit')->willReturn(null);
        $this->reservations->method('enregistrer')->willReturnArgument(0);

        $reservationVoisine = $this->service->creer(
            $this->dto(1, $this->demain->setTime(12, 0), $this->demain->setTime(14, 0))
        );

        self::assertSame('confirmee', $reservationVoisine->statut);
    }
}
