<?php

declare(strict_types=1);

namespace Tests\Fakes;

use App\Model\Reservation;
use App\Repository\ReservationRepositoryInterface;
use DateTimeImmutable;
use Illuminate\Support\Collection;

final class ReservationRepositoryEnMemoire implements ReservationRepositoryInterface
{
    private array $reservations = [];
    private int $prochainId = 1;

    public function lister(): Collection
    {
        return new Collection(array_values($this->reservations));
    }

    public function listerParSalle(int $salleId): Collection
    {
        return new Collection(array_values(array_filter(
            $this->reservations,
            fn (Reservation $r) => $r->salle_id === $salleId
        )));
    }

    public function trouver(int $id): ?Reservation
    {
        return $this->reservations[$id] ?? null;
    }

    public function rechercherConflit(
        int $salleId,
        DateTimeImmutable $dateDebut,
        DateTimeImmutable $dateFin,
        ?int $excludeId = null
    ): ?Reservation {
        foreach ($this->reservations as $reservation) {
            if ($reservation->salle_id !== $salleId) {
                continue;
            }
            if ($reservation->statut !== 'confirmee') {
                continue;
            }
            if ($excludeId !== null && $reservation->id === $excludeId) {
                continue;
            }

            $debutExistant = DateTimeImmutable::createFromInterface($reservation->date_debut);
            $finExistant = DateTimeImmutable::createFromInterface($reservation->date_fin);

            if ($dateDebut < $finExistant && $dateFin > $debutExistant) {
                return $reservation;
            }
        }

        return null;
    }

    public function enregistrer(Reservation $reservation): Reservation
    {
        if ($reservation->id === null) {
            $reservation->id = $this->prochainId++;
        }

        $this->reservations[$reservation->id] = $reservation;

        return $reservation;
    }

    public function annuler(Reservation $reservation): Reservation
    {
        $reservation->statut = 'annulee';
        $this->reservations[$reservation->id] = $reservation;

        return $reservation;
    }
}
