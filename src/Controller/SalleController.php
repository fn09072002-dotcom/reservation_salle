<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\CreerSalleDTO;
use App\Exception\DonneesInvalidesException;
use App\Model\Salle;
use App\Repository\SalleRepositoryInterface;
use App\View\Flash;
use App\View\View;

final class SalleController
{
    public function __construct(
        private readonly SalleRepositoryInterface $salles,
        private readonly View $view
    ) {
    }

    public function index(): void
    {
        $salles = $this->salles->lister();

        $this->view->renderView('salle/index', ['salles' => $salles, 'titre' => 'Liste des salles']);
    }

    public function show(int $id): void
    {
        $salle = $this->salles->trouver($id);

        if ($salle === null) {
            $this->view->renderView('error/404', ['titre' => 'Salle introuvable']);
            return;
        }

        $this->view->renderView('salle/show', ['salle' => $salle, 'titre' => $salle->nom]);
    }

    public function create(): void
    {
        $this->view->renderView('salle/form', ['erreurs' => [], 'anciennesValeurs' => [], 'titre' => 'Ajouter une salle']);
    }

    public function store(array $donneesPost): void
    {
        try {
            $dto = CreerSalleDTO::fromArray($donneesPost);
        } catch (DonneesInvalidesException $e) {
            $this->view->renderView('salle/form', [
                'erreurs' => $e->erreurs(),
                'anciennesValeurs' => $e->anciennesValeurs(),
                'titre' => 'Ajouter une salle',
            ]);
            return;
        }

        $salle = new Salle([
            'nom' => $dto->nom,
            'batiment' => $dto->batiment,
            'capacite' => $dto->capacite,
            'type' => $dto->type,
            'active' => $dto->active,
        ]);

        $this->salles->enregistrer($salle);
        Flash::success('Salle ajoutée avec succès.');

        header('Location: /salles/' . $salle->id);
        exit;
    }

    public function edit(int $id): void
    {
        $salle = $this->salles->trouver($id);

        if ($salle === null) {
            $this->view->renderView('error/404', ['titre' => 'Salle introuvable']);
            return;
        }

        $this->view->renderView('salle/form', [
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
            $this->view->renderView('error/404', ['titre' => 'Salle introuvable']);
            return;
        }

        try {
            $dto = CreerSalleDTO::fromArray($donneesPost);
        } catch (DonneesInvalidesException $e) {
            $this->view->renderView('salle/form', [
                'erreurs' => $e->erreurs(),
                'anciennesValeurs' => $e->anciennesValeurs(),
                'salle' => $salle,
                'titre' => 'Modifier ' . $salle->nom,
            ]);
            return;
        }

        $salle->nom = $dto->nom;
        $salle->batiment = $dto->batiment;
        $salle->capacite = $dto->capacite;
        $salle->type = $dto->type;
        $salle->active = $dto->active;

        $this->salles->enregistrer($salle);
        Flash::success('Salle modifiee avec succes.');

        header('Location: /salles/' . $salle->id);
        exit;
    }
}
