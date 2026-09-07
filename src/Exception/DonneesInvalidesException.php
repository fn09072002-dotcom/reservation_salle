<?php

declare(strict_types=1);

namespace App\Exception;

use RuntimeException;

final class DonneesInvalidesException extends RuntimeException
{
    public function __construct(
        private readonly array $erreurs,
        private readonly array $anciennesValeurs
    ) {
        parent::__construct('Donnees invalides.');
    }

    public function erreurs(): array
    {
        return $this->erreurs;
    }

    public function anciennesValeurs(): array
    {
        return $this->anciennesValeurs;
    }
}
