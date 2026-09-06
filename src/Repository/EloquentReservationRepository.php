<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\Reservation;
use DateTimeImmutable;
use Illuminate\Support\Collection;

final class EloquentReservationRepository implements ReservationRepositoryInterface
{
    public function lister(): Collection
    {
        return Reservation::all();
    }

    public function listerParSalle(int $salleId): Collection
    {
        return Reservation::where('salle_id', $salleId)->get();
    }

    public function trouver(int $id): ?Reservation
    {
        return Reservation::find($id);
    }

    public function rechercherConflit(
        int $salleId,
        DateTimeImmutable $dateDebut,
        DateTimeImmutable $dateFin,
        ?int $excludeId = null
    ): ?Reservation {
        $requete = Reservation::where('salle_id', $salleId)
            ->where('statut', 'confirmee')
            ->where('date_debut', '<', $dateFin->format('Y-m-d H:i:s'))
            ->where('date_fin', '>', $dateDebut->format('Y-m-d H:i:s'));

        if ($excludeId !== null) {
            $requete->where('id', '!=', $excludeId);
        }

        return $requete->first();
    }

    public function enregistrer(Reservation $reservation): Reservation
    {
        $reservation->save();

        return $reservation;
    }

    public function annuler(Reservation $reservation): Reservation
    {
        $reservation->statut = 'annulee';
        $reservation->save();

        return $reservation;
    }
}
