<?php

declare(strict_types=1);

use App\Application;
use DI\ContainerBuilder;

require dirname(__DIR__) . '/vendor/autoload.php';

// Sert les fichiers statiques reels directement, sans passer par le routeur.
if (PHP_SAPI === 'cli-server') {
    $chemin = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
    $fichier = __DIR__ . $chemin;

    if ($chemin !== '/' && is_file($fichier)) {
        return false;
    }
}

$builder = new ContainerBuilder();
$builder->addDefinitions(
    dirname(__DIR__) . '/config/container.php'
);
$container = $builder->build();

$application = $container->get(Application::class);
$application->run();
