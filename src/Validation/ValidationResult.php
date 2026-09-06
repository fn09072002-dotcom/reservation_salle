<?php

declare(strict_types=1);

namespace App\Validation;

final class ValidationResult
{
    /** @param array<string, string[]> $erreurs */
    private function __construct(
        private readonly bool $valide,
        private readonly array $erreurs,
        private readonly array $donneesAcceptees
    ) {
    }

    public static function succes(array $donneesAcceptees): self
    {
        return new self(true, [], $donneesAcceptees);
    }

    /** @param array<string, string[]> $erreurs */
    public static function echec(array $erreurs, array $donneesAcceptees = []): self
    {
        return new self(false, $erreurs, $donneesAcceptees);
    }

    public function isValid(): bool
    {
        return $this->valide;
    }

    /** @return array<string, string[]> */
    public function errors(): array
    {
        return $this->erreurs;
    }

    public function donneesAcceptees(): array
    {
        return $this->donneesAcceptees;
    }
}
