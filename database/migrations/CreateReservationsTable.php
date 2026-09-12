<?php

declare(strict_types=1);

namespace Database\Migrations;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;

final class CreateReservationsTable implements MigrationInterface
{
    public function up(Builder $schema): void
    {
        if ($schema->hasTable('reservations')) {
            echo "Table 'reservations' deja existante." . PHP_EOL;
            return;
        }

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
    }

    public function down(Builder $schema): void
    {
        if (!$schema->hasTable('reservations')) {
            echo "Table 'reservations' deja absente." . PHP_EOL;
            return;
        }

        $schema->drop('reservations');

        echo "Table 'reservations' supprimee." . PHP_EOL;
    }
}
