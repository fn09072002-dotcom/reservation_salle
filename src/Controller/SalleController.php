<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\CreerSalleDTO;
use App\Model\Salle;
use App\Repository\SalleRepositoryInterface;
use App\Validation\SalleValidator;
use App\View\View;

final class SalleController
{
    public function __construct(
        private readonly SalleRepositoryInterface $salles,
        private readonly SalleValidator $validator
    ) {
    }

    public function index(): void
    {
        $salles = $this->salles->lister();

        View::renderView('salle/index', ['salles' => $salles, 'titre' => 'Liste des salles']);
    }

    public function show(int $id): void
    {
        $salle = $this->salles->trouver($id);

        if ($salle === null) {
            View::renderView('error/404', ['titre' => 'Salle introuvable']);
            return;
        }

        View::renderView('salle/show', ['salle' => $salle, 'titre' => $salle->nom]);
    }

    public function create(): void
    {
        View::renderView('salle/form', ['erreurs' => [], 'anciennesValeurs' => [], 'titre' => 'Ajouter une salle']);
    }

    public function store(array $donneesPost): void
    {
        $resultat = $this->validator->validate($donneesPost);

        if (!$resultat->isValid()) {
            View::renderView('salle/form', [
                'erreurs' => $resultat->errors(),
                'anciennesValeurs' => $donneesPost,
                'titre' => 'Ajouter une salle',
            ]);
            return;
        }

        $dto = CreerSalleDTO::fromArray($resultat->donneesAcceptees());

        $salle = new Salle([
            'nom' => $dto->nom,
            'batiment' => $dto->batiment,
            'capacite' => $dto->capacite,
            'type' => $dto->type,
            'active' => $dto->active,
        ]);

        $this->salles->enregistrer($salle);

        header('Location: /salles/' . $salle->id);
        exit;
    }

    public function edit(int $id): void
    {
        $salle = $this->salles->trouver($id);

        if ($salle === null) {
            View::renderView('error/404', ['titre' => 'Salle introuvable']);
            return;
        }

        View::renderView('salle/form', [
            'erreurs' => [],
            'anciennesValeurs' => $salle->toArray(),
            'salle' => $salle,
            'titre' => 'Modifier ' . $salle->nom,
        ]);
    }

    public function update(int $id, array $donneesPost): void
    {
        $salle = $this->salles->trouver($id);

        if ($salle === null) {
            View::renderView('error/404', ['titre' => 'Salle introuvable']);
            return;
        }

        $resultat = $this->validator->validate($donneesPost);

        if (!$resultat->isValid()) {
            View::renderView('salle/form', [
                'erreurs' => $resultat->errors(),
                'anciennesValeurs' => $donneesPost,
                'salle' => $salle,
                'titre' => 'Modifier ' . $salle->nom,
            ]);
            return;
        }

        $dto = CreerSalleDTO::fromArray($resultat->donneesAcceptees());

        $salle->nom = $dto->nom;
        $salle->batiment = $dto->batiment;
        $salle->capacite = $dto->capacite;
        $salle->type = $dto->type;
        $salle->active = $dto->active;

        $this->salles->enregistrer($salle);

        header('Location: /salles/' . $salle->id);
        exit;
    }
}
