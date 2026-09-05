<?php

declare(strict_types=1);

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

require __DIR__ . '/../../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../..');
$dotenv->load();

$configurerCapsule = require __DIR__ . '/../../config/database.php';
$capsule = $configurerCapsule();

$schema = $capsule->schema();

if (!$schema->hasTable('salles')) {
    $schema->create('salles', function (Blueprint $table) {
        $table->id();
        $table->string('nom', 100);
        $table->string('batiment', 100);
        $table->unsignedInteger('capacite');
        $table->enum('type', ['cours', 'informatique', 'laboratoire', 'amphitheatre', 'reunion']);
        $table->boolean('active')->default(true);
        $table->timestamps();
    });
    echo "Table 'salles' creee." . PHP_EOL;
} else {
    echo "Table 'salles' deja existante." . PHP_EOL;
}

if (!$schema->hasTable('reservations')) {
    $schema->create('reservations', function (Blueprint $table) {
        $table->id();
        $table->foreignId('salle_id')->constrained('salles');
        $table->string('responsable', 120);
        $table->string('email', 190);
        $table->string('motif', 255);
        $table->dateTime('date_debut');
        $table->dateTime('date_fin');
        $table->enum('statut', ['confirmee', 'annulee'])->default('confirmee');
        $table->timestamps();
    });
    echo "Table 'reservations' creee." . PHP_EOL;
} else {
    echo "Table 'reservations' deja existante." . PHP_EOL;
}
