<?php

declare(strict_types=1);

namespace App;

use App\Controller\ReservationController;
use App\Controller\SalleController;
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
        $methode = $_SERVER['REQUEST_METHOD'];
        $uri = rawurldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

        $routeInfo = $this->dispatcher->dispatch($methode, $uri);

        $controllers = [
            SalleController::class => $this->salleController,
            ReservationController::class => $this->reservationController,
        ];

        switch ($routeInfo[0]) {
            case Dispatcher::NOT_FOUND:
                http_response_code(404);
                require dirname(__DIR__) . '/templates/error/404.php';
                break;

            case Dispatcher::METHOD_NOT_ALLOWED:
                http_response_code(405);
                header('Allow: ' . implode(', ', $routeInfo[1]));
                require dirname(__DIR__) . '/templates/error/405.php';
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
