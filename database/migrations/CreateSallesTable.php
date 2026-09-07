<?php

declare(strict_types=1);

namespace Database\Migrations;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;

final class CreateSallesTable implements MigrationInterface
{
    public function up(Builder $schema): void
    {
        if ($schema->hasTable('salles')) {
            echo "Table 'salles' deja existante." . PHP_EOL;
            return;
        }

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
    }
}
