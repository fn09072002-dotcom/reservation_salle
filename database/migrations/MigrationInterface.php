<?php

declare(strict_types=1);

namespace Database\Migrations;

use Illuminate\Database\Schema\Builder;

interface MigrationInterface
{
    public function up(Builder $schema): void;
}
