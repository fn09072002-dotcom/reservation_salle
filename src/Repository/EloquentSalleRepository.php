<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\Salle;
use Illuminate\Support\Collection;

final class EloquentSalleRepository implements SalleRepositoryInterface
{
    public function lister(): Collection
    {
        return Salle::all();
    }

    public function trouver(int $id): ?Salle
    {
        return Salle::find($id);
    }

    public function enregistrer(Salle $salle): Salle
    {
        $salle->save();

        return $salle;
    }
}
