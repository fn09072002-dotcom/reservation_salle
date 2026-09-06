<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\Salle;
use Illuminate\Support\Collection;

interface SalleRepositoryInterface
{
    public function lister(): Collection;

    public function trouver(int $id): ?Salle;

    public function enregistrer(Salle $salle): Salle;
}
