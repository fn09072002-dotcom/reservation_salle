<?php

declare(strict_types=1);

namespace App\Session;

class SessionManager
{
    public function demarrer(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function set(string $cle, mixed $valeur): void
    {
        $this->demarrer();
        $_SESSION[$cle] = $valeur;
    }

    public function get(string $cle): mixed
    {
        $this->demarrer();

        return $_SESSION[$cle] ?? null;
    }

    public function consume(string $cle): mixed
    {
        $this->demarrer();

        $valeur = $_SESSION[$cle] ?? null;
        unset($_SESSION[$cle]);

        return $valeur;
    }

    public function has(string $cle): bool
    {
        $this->demarrer();

        return isset($_SESSION[$cle]);
    }
}
