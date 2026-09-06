<?php

declare(strict_types=1);

namespace App\View;

final class View
{
    public static function renderView(string $template, array $donnees = []): void
    {
        extract($donnees);

        ob_start();
        require dirname(__DIR__, 2) . "/templates/{$template}.php";
        $contenu = ob_get_clean();

        require dirname(__DIR__, 2) . '/templates/layout/base.php';
    }
}
