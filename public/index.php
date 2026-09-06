<?php

declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

if (PHP_SAPI === 'cli-server') {
    $chemin = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
    $fichier = __DIR__ . $chemin;

    if ($chemin !== '/' && is_file($fichier)) {
        return false;
    }
}

use App\Controller\ReservationController;
use App\Controller\SalleController;
use App\Repository\EloquentReservationRepository;
use App\Repository\EloquentSalleRepository;
use App\Service\AnnulerReservationService;
use App\Service\CreerReservationService;
use App\Validation\ReservationValidator;
use App\Validation\SalleValidator;
use Dotenv\Dotenv;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;

use function FastRoute\simpleDispatcher;

$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$configurerCapsule = require dirname(__DIR__) . '/config/database.php';
$configurerCapsule();

$salleRepository = new EloquentSalleRepository();
$reservationRepository = new EloquentReservationRepository();
$salleValidator = new SalleValidator();
$reservationValidator = new ReservationValidator();
$creerService = new CreerReservationService($salleRepository, $reservationRepository);
$annulerService = new AnnulerReservationService($reservationRepository);

$salleController = new SalleController($salleRepository, $salleValidator);
$reservationController = new ReservationController(
    $reservationRepository,
    $salleRepository,
    $reservationValidator,
    $creerService,
    $annulerService
);

$controllers = [
    SalleController::class => $salleController,
    ReservationController::class => $reservationController,
];

$definirRoutes = require dirname(__DIR__) . '/routes/web.php';
$dispatcher = simpleDispatcher($definirRoutes);

$methode = $_SERVER['REQUEST_METHOD'];
$uri = rawurldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

$routeInfo = $dispatcher->dispatch($methode, $uri);

switch ($routeInfo[0]) {
    case Dispatcher::NOT_FOUND:
        http_response_code(404);
        require dirname(__DIR__) . '/templates/error/404.php';
        break;

    case Dispatcher::METHOD_NOT_ALLOWED:
        $methodesAutorisees = $routeInfo[1];
        http_response_code(405);
        header('Allow: ' . implode(', ', $methodesAutorisees));
        require dirname(__DIR__) . '/templates/error/405.php';
        break;

    case Dispatcher::FOUND:
        [$classeControleur, $action] = $routeInfo[1];
        $parametres = $routeInfo[2];

        $controleur = $controllers[$classeControleur];

        if (isset($parametres['id'])) {
            $id = (int) $parametres['id'];
            if ($action === 'store' || $action === 'update') {
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
