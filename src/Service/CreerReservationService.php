<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\CreerReservationDTO;
use App\Exception\SalleIndisponibleException;
use App\Model\Reservation;
use App\Repository\ReservationRepositoryInterface;
use App\Repository\SalleRepositoryInterface;
use InvalidArgumentException;

final class CreerReservationService
{
    private const DUREE_MAXIMALE_HEURES = 4;

    public function __construct(
        private readonly SalleRepositoryInterface $salles,
        private readonly ReservationRepositoryInterface $reservations
    ) {
    }

    public function creer(CreerReservationDTO $dto): Reservation
    {
        $salle = $this->salles->trouver($dto->salleId);

        if ($salle === null) {
            throw new InvalidArgumentException('Cette salle n\'existe pas.');
        }

        if (!$salle->active) {
            throw new SalleIndisponibleException('Cette salle ne peut pas etre reservee.');
        }

        if ($dto->dateDebut >= $dto->dateFin) {
            throw new InvalidArgumentException('La date de debut doit preceder la date de fin.');
        }

        $dureeEnHeures = ($dto->dateFin->getTimestamp() - $dto->dateDebut->getTimestamp()) / 3600;

        if ($dureeEnHeures > self::DUREE_MAXIMALE_HEURES) {
            throw new InvalidArgumentException('Une reservation ne peut pas depasser quatre heures.');
        }

        if ($dto->dateDebut <= new \DateTimeImmutable()) {
            throw new InvalidArgumentException('La reservation doit commencer dans le futur.');
        }

        return $this->reservations->creerAvecVerrou($dto->salleId, function () use ($dto) {
            $conflit = $this->reservations->rechercherConflit(
                $dto->salleId,
                $dto->dateDebut,
                $dto->dateFin
            );

            if ($conflit !== null) {
                throw new SalleIndisponibleException('La salle est indisponible pendant cette periode.');
            }

            $reservation = new Reservation([
                'salle_id' => $dto->salleId,
                'responsable' => $dto->responsable,
                'email' => $dto->email,
                'motif' => $dto->motif,
                'date_debut' => $dto->dateDebut,
                'date_fin' => $dto->dateFin,
                'statut' => 'confirmee',
            ]);

            return $this->reservations->enregistrer($reservation);
        });
    }
}
