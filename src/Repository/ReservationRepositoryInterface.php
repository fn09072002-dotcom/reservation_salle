<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\Reservation;
use DateTimeImmutable;
use Illuminate\Support\Collection;

interface ReservationRepositoryInterface
{
    public function lister(): Collection;

    public function listerParSalle(int $salleId): Collection;

    public function trouver(int $id): ?Reservation;

    public function rechercherConflit(
        int $salleId,
        DateTimeImmutable $dateDebut,
        DateTimeImmutable $dateFin,
        ?int $excludeId = null
    ): ?Reservation;

    public function enregistrer(Reservation $reservation): Reservation;

    public function annuler(Reservation $reservation): Reservation;
}
