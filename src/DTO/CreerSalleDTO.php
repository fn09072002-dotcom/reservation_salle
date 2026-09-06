<?php

declare(strict_types=1);

namespace App\DTO;

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

    public static function fromArray(array $data): self
    {
        return new self(
            nom: (string) $data['nom'],
            batiment: (string) $data['batiment'],
            capacite: (int) $data['capacite'],
            type: (string) $data['type'],
            active: (bool) ($data['active'] ?? true)
        );
    }
}
