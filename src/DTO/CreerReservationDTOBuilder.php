<?php

declare(strict_types=1);

namespace App\DTO;

use DateTimeImmutable;
use LogicException;

final class CreerReservationDTOBuilder
{
    private int $salleId = 0;
    private string $responsable = '';
    private string $email = '';
    private string $motif = '';
    private ?DateTimeImmutable $dateDebut = null;
    private ?DateTimeImmutable $dateFin = null;

    public function salleId(int $salleId): self
    {
        $this->salleId = $salleId;
        return $this;
    }

    public function responsable(string $responsable): self
    {
        $this->responsable = $responsable;
        return $this;
    }

    public function email(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function motif(string $motif): self
    {
        $this->motif = $motif;
        return $this;
    }

    public function dateDebut(DateTimeImmutable $dateDebut): self
    {
        $this->dateDebut = $dateDebut;
        return $this;
    }

    public function dateFin(DateTimeImmutable $dateFin): self
    {
        $this->dateFin = $dateFin;
        return $this;
    }

    public function build(): CreerReservationDTO
    {
        if ($this->dateDebut === null || $this->dateFin === null) {
            throw new LogicException('Les dates de debut et de fin sont obligatoires.');
        }

        return new CreerReservationDTO(
            salleId: $this->salleId,
            responsable: $this->responsable,
            email: $this->email,
            motif: $this->motif,
            dateDebut: $this->dateDebut,
            dateFin: $this->dateFin
        );
    }
}
