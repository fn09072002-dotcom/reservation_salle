<?php

declare(strict_types=1);
use App\Session\SessionManager;
use App\View\Flash;
use App\View\JsonRenderer;

use App\Application;
use App\Controller\ReservationController;
use App\Controller\SalleController;
use App\Repository\EloquentReservationRepository;
use App\Repository\EloquentSalleRepository;
use App\Repository\ReservationRepositoryInterface;
use App\Repository\SalleRepositoryInterface;
use App\Service\AnnulerReservationService;
use App\Service\CreerReservationService;
use App\Validation\ReservationValidator;
use App\Validation\SalleValidator;
use App\View\View;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use Illuminate\Database\Capsule\Manager as Capsule;

use function DI\autowire;
use function DI\factory;
use function FastRoute\simpleDispatcher;

return [
    Capsule::class => factory(function (): Capsule {
        $dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
        $dotenv->load();

        $configurerCapsule = require __DIR__ . '/database.php';

        return $configurerCapsule();
    }),

    SalleRepositoryInterface::class => autowire(EloquentSalleRepository::class),
    ReservationRepositoryInterface::class => autowire(EloquentReservationRepository::class),

    SalleValidator::class => autowire(),
    ReservationValidator::class => autowire(),

    CreerReservationService::class => autowire(),
    AnnulerReservationService::class => autowire(),
    View::class => autowire(),
    JsonRenderer::class => autowire(),

    SalleController::class => autowire(),
    ReservationController::class => autowire(),

    SessionManager::class => autowire(),
    Flash::class => autowire(),

    Dispatcher::class => factory(function (): Dispatcher {
        $definirRoutes = require dirname(__DIR__) . '/routes/web.php';

        return simpleDispatcher($definirRoutes);
    }),

    Application::class => autowire(),
];
