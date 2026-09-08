<?php

declare(strict_types=1);

namespace App;

use App\Controller\ReservationController;
use App\Controller\SalleController;
use App\View\View;
use FastRoute\Dispatcher;
use Illuminate\Database\Capsule\Manager as Capsule;

final class Application
{
    public function __construct(
        private readonly Dispatcher $dispatcher,
        private readonly SalleController $salleController,
        private readonly ReservationController $reservationController,
        Capsule $capsule
    ) {
    }

    public function run(): void
    {
        try {
            $this->dispatchRequete();
        } catch (\Throwable $e) {
            error_log((string) $e);

            http_response_code(500);
            View::renderView('error/500', ['titre' => 'Erreur serveur']);
        }
    }

    private function dispatchRequete(): void
    {
        $methode = $_SERVER['REQUEST_METHOD'];
        $uri = rawurldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

        $routeInfo = $this->dispatcher->dispatch($methode, $uri);

        $controllers = [
            SalleController::class => $this->salleController,
            ReservationController::class => $this->reservationController,
        ];
        if ($routeInfo[0] === Dispatcher::FOUND && $routeInfo[1] === ['accueil', 'index']) {
            View::renderView('accueil', ['titre' => 'Accueil']);
            return;
        }

        switch ($routeInfo[0]) {
            case Dispatcher::NOT_FOUND:
                http_response_code(404);
                View::renderView('error/404', ['titre' => 'Page introuvable']);
                break;

            case Dispatcher::METHOD_NOT_ALLOWED:
                http_response_code(405);
                header('Allow: ' . implode(', ', $routeInfo[1]));
                View::renderView('error/405', ['titre' => 'Methode non autorisee']);
                break;

            case Dispatcher::FOUND:
                [$classeControleur, $action] = $routeInfo[1];
                $parametres = $routeInfo[2];
                $controleur = $controllers[$classeControleur];

                if (isset($parametres['id'])) {
                    $id = (int) $parametres['id'];
                    if (in_array($action, ['store', 'update'], true)) {
                        $controleur->$action($id, $_POST);
                    } else {
                        $controleur->$action($id);
                    }
                } elseif ($action === 'store') {
                    $controleur->$action($_POST);
                } else {
                    $controleur->$action();
                }
                break;
        }
    }
}
