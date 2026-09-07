<?php

declare(strict_types=1);

namespace App\DTO;

final class CreerSalleDTOBuilder
{
    private string $nom = '';
    private string $batiment = '';
    private int $capacite = 0;
    private string $type = '';
    private bool $active = true;

    public function nom(string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    public function batiment(string $batiment): self
    {
        $this->batiment = $batiment;
        return $this;
    }

    public function capacite(int $capacite): self
    {
        $this->capacite = $capacite;
        return $this;
    }

    public function type(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function active(bool $active): self
    {
        $this->active = $active;
        return $this;
    }

    public function build(): CreerSalleDTO
    {
        return new CreerSalleDTO(
            nom: $this->nom,
            batiment: $this->batiment,
            capacite: $this->capacite,
            type: $this->type,
            active: $this->active
        );
    }
}
