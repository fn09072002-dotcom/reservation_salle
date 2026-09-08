<?php

declare(strict_types=1);

namespace Tests\Support;

use Database\Migrations\CreateReservationsTable;
use Database\Migrations\CreateSallesTable;
use Illuminate\Database\Capsule\Manager as Capsule;
use PHPUnit\Framework\TestCase;


abstract class DatabaseTestCase extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $capsule = new Capsule();
        $capsule->addConnection([
            'driver' => 'sqlite',
            'database' => ':memory:',
        ]);
        $capsule->setAsGlobal();
        $capsule->bootEloquent();

        if ($this->avecSchema()) {
            $schema = $capsule->schema();

            (new CreateSallesTable())->up($schema);
            (new CreateReservationsTable())->up($schema);
        }
    }

    protected function avecSchema(): bool
    {
        return false;
    }
}
