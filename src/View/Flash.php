<?php

declare(strict_types=1);

namespace App\View;

/**
 * Messages "flash" : stockes en session avant une redirection, puis
 * consommes (lus et effaces) une seule fois sur la page suivante.
 */
final class Flash
{
    private const CLE_SUCCES = 'flash_succes';
    private const CLE_ERREUR = 'flash_erreur';

    public static function success(string $message): void
    {
        self::demarrerSession();
        $_SESSION[self::CLE_SUCCES] = $message;
    }

    public static function error(string $message): void
    {
        self::demarrerSession();
        $_SESSION[self::CLE_ERREUR] = $message;
    }

    public static function consumeSuccess(): ?string
    {
        return self::consume(self::CLE_SUCCES);
    }

    public static function consumeError(): ?string
    {
        return self::consume(self::CLE_ERREUR);
    }

    private static function consume(string $cle): ?string
    {
        self::demarrerSession();

        $message = $_SESSION[$cle] ?? null;
        unset($_SESSION[$cle]);

        return $message;
    }

    private static function demarrerSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
}
