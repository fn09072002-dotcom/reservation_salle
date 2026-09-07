<?php

declare(strict_types=1);

namespace App\DTO;

use App\Exception\DonneesInvalidesException;
use App\Validation\ReservationValidator;
use App\Validation\ValidatorInterface;
use DateTimeImmutable;

final class CreerReservationDTO
{
    public function __construct(
        public readonly int $salleId,
        public readonly string $responsable,
        public readonly string $email,
        public readonly string $motif,
        public readonly DateTimeImmutable $dateDebut,
        public readonly DateTimeImmutable $dateFin
    ) {
    }

    public static function builder(): CreerReservationDTOBuilder
    {
        return new CreerReservationDTOBuilder();
    }

    public static function fromArray(array $data, ?ValidatorInterface $validator = null): self
    {
        $validator ??= new ReservationValidator();

        $resultat = $validator->validate($data);

        if (!$resultat->isValid()) {
            throw new DonneesInvalidesException($resultat->errors(), $data);
        }

        $donnees = $resultat->donneesAcceptees();

        return self::builder()
            ->salleId((int) $donnees['salle_id'])
            ->responsable((string) $donnees['responsable'])
            ->email((string) $donnees['email'])
            ->motif((string) $donnees['motif'])
            ->dateDebut(new DateTimeImmutable((string) $donnees['date_debut']))
            ->dateFin(new DateTimeImmutable((string) $donnees['date_fin']))
            ->build();
    }
}
