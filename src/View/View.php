<?php

declare(strict_types=1);

namespace App\View;

class View
{
    public function __construct(
        private readonly Flash $flash,
        private readonly JsonRenderer $jsonRenderer
    ) {
    }

    public function renderView(string $template, array $donnees = []): void
    {
        if (($_GET['format'] ?? null) === 'json') {
            $this->jsonRenderer->render($donnees);
            return;
        }

        $messageSucces = $this->flash->consumeSuccess();
        $messageErreur = $this->flash->consumeError();

        extract($donnees);

        ob_start();
        require dirname(__DIR__, 2) . "/templates/{$template}.php";
        $contenu = ob_get_clean();

        require dirname(__DIR__, 2) . '/templates/layout/base.php';
    }
}
