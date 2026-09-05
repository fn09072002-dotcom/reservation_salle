<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

$configurerCapsule = require __DIR__ . '/../config/database.php';
$capsule = $configurerCapsule();

try {
    $pdo = $capsule->getConnection()->getPdo();
    echo "Connexion Eloquent/MySQL reussie." . PHP_EOL;
} catch (\Throwable $e) {
    echo "Erreur de connexion : " . $e->getMessage() . PHP_EOL;
}
