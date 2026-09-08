<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\CreerReservationDTO;
use App\Exception\DonneesInvalidesException;
use App\Exception\ReservationIntrouvableException;
use App\Exception\SalleIndisponibleException;
use App\Repository\ReservationRepositoryInterface;
use App\Repository\SalleRepositoryInterface;
use App\View\Flash;     
use App\Service\AnnulerReservationService;
use App\Service\CreerReservationService;
use App\View\View;
use InvalidArgumentException;

final class ReservationController
{
    public function __construct(
        private readonly ReservationRepositoryInterface $reservations,
        private readonly SalleRepositoryInterface $salles,
        private readonly CreerReservationService $creerService,
        private readonly AnnulerReservationService $annulerService
    ) {
    }

    public function index(): void
    {
        $reservations = $this->reservations->lister();

        View::renderView('reservation/index', ['reservations' => $reservations, 'titre' => 'Reservations']);
    }

    public function show(int $id): void
    {
        $reservation = $this->reservations->trouver($id);

        if ($reservation === null) {
            View::renderView('error/404', ['titre' => 'Reservation introuvable']);
            return;
        }

        View::renderView('reservation/show', ['reservation' => $reservation, 'titre' => 'Detail de la reservation']);
    }

    public function create(): void
    {
        $salles = $this->salles->lister();

        View::renderView('reservation/form', [
            'salles' => $salles,
            'erreurs' => [],
            'anciennesValeurs' => [],
            'titre' => 'Nouvelle reservation',
        ]);
    }

    public function store(array $donneesPost): void
    {
        try {
            $dto = CreerReservationDTO::fromArray($donneesPost);
        } catch (DonneesInvalidesException $e) {
            $this->reafficherFormulaireAvecErreurs($e->erreurs(), $e->anciennesValeurs());
            return;
        }

        try {
            $reservation = $this->creerService->creer($dto);
        } catch (SalleIndisponibleException|InvalidArgumentException $e) {
            $this->reafficherFormulaireAvecErreurs(['general' => [$e->getMessage()]], $donneesPost);
            return;
        }
        Flash::success('Reservation creee avec succes.');

        header('Location: /reservations/' . $reservation->id);
        exit;
    }

      public function cancel(int $id): void
    {
        try {
            $this->annulerService->annuler($id);
        } catch (ReservationIntrouvableException $e) {
            Flash::error('Cette reservation est introuvable.');
            header('Location: /reservations');
            exit;
        }

        Flash::success('Reservation annulee avec succes.');

        header('Location: /reservations/' . $id);
        exit;
    }

    private function reafficherFormulaireAvecErreurs(array $erreurs, array $donneesPost): void
    {
        $salles = $this->salles->lister();

        View::renderView('reservation/form', [
            'salles' => $salles,
            'erreurs' => $erreurs,
            'anciennesValeurs' => $donneesPost,
            'titre' => 'Nouvelle reservation',
        ]);
    }
}
