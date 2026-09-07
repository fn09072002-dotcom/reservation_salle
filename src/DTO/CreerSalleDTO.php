<?php

declare(strict_types=1);

namespace App\DTO;

use App\Exception\DonneesInvalidesException;
use App\Validation\SalleValidator;
use App\Validation\ValidatorInterface;

final class CreerSalleDTO
{
    public function __construct(
        public readonly string $nom,
        public readonly string $batiment,
        public readonly int $capacite,
        public readonly string $type,
        public readonly bool $active
    ) {
    }

    public static function builder(): CreerSalleDTOBuilder
    {
        return new CreerSalleDTOBuilder();
    }

    public static function fromArray(array $data, ?ValidatorInterface $validator = null): self
    {
        $validator ??= new SalleValidator();

        $resultat = $validator->validate($data);

        if (!$resultat->isValid()) {
            throw new DonneesInvalidesException($resultat->errors(), $data);
        }

        $donnees = $resultat->donneesAcceptees();

        return self::builder()
            ->nom((string) $donnees['nom'])
            ->batiment((string) $donnees['batiment'])
            ->capacite((int) $donnees['capacite'])
            ->type((string) $donnees['type'])
            ->active((bool) ($donnees['active'] ?? true))
            ->build();
    }
}
