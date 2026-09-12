<?php

declare(strict_types=1);

namespace App\View;

use App\Session\SessionManager;

final class Flash
{
    private const CLE_SUCCES = 'flash_succes';
    private const CLE_ERREUR = 'flash_erreur';

    public function __construct(
        private readonly SessionManager $session
    ) {
    }

    public function success(string $message): void
    {
        $this->session->set(self::CLE_SUCCES, $message);
    }

    public function error(string $message): void
    {
        $this->session->set(self::CLE_ERREUR, $message);
    }

    public function consumeSuccess(): ?string
    {
        return $this->session->consume(self::CLE_SUCCES);
    }

    public function consumeError(): ?string
    {
        return $this->session->consume(self::CLE_ERREUR);
    }
}
