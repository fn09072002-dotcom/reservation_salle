<?php

declare(strict_types=1);

namespace App\View;

final class JsonRenderer
{
    public function render(array $donnees): void
    {
        header('Content-Type: application/json; charset=UTF-8');

        unset($donnees['titre']);

        echo json_encode($donnees, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
