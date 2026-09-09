<?php

declare(strict_types=1);

namespace App\View;

class View
{
    public function renderView(string $template, array $donnees = []): void
    {
        if (($_GET['format'] ?? null) === 'json') {
            $this->renderJson($donnees);
            return;
        }

        $donnees['messageSucces'] ??= Flash::consumeSuccess();
        $donnees['messageErreur'] ??= Flash::consumeError();

        extract($donnees);

        ob_start();
        require dirname(__DIR__, 2) . "/templates/{$template}.php";
        $contenu = ob_get_clean();

        require dirname(__DIR__, 2) . '/templates/layout/base.php';
    }

    private function renderJson(array $donnees): void
    {
        // On retire les cles qui ne concernent que le rendu HTML
        // (titre de page, erreurs de formulaire, anciennes valeurs) :
        // seule la donnee metier nous interesse en JSON.
        unset($donnees['titre'], $donnees['erreurs'], $donnees['anciennesValeurs']);

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($donnees, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
