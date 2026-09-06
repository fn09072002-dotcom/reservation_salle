<?php

declare(strict_types=1);

namespace Tests\Fakes;

use App\Model\Salle;
use App\Repository\SalleRepositoryInterface;
use Illuminate\Support\Collection;

final class SalleRepositoryEnMemoire implements SalleRepositoryInterface
{
    private array $salles = [];

    public function ajouter(Salle $salle): void
    {
        $this->salles[$salle->id] = $salle;
    }

    public function lister(): Collection
    {
        return new Collection(array_values($this->salles));
    }

    public function trouver(int $id): ?Salle
    {
        return $this->salles[$id] ?? null;
    }

    public function enregistrer(Salle $salle): Salle
    {
        $this->salles[$salle->id] = $salle;

        return $salle;
    }
}
