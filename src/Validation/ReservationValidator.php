<?php

declare(strict_types=1);

namespace App\Validation;

use Respect\Validation\Validator as v;
use Respect\Validation\Exceptions\ValidationException;

final class ReservationValidator implements ValidatorInterface
{
    public function validate(array $data): ValidationResult
    {
        $erreurs = [];

        $this->verifierChamp($erreurs, 'salle_id', $data['salle_id'] ?? null,
            v::intVal()->positive());

        $this->verifierChamp($erreurs, 'responsable', $data['responsable'] ?? null,
            v::stringType()->length(2, 120));

        $this->verifierChamp($erreurs, 'email', $data['email'] ?? null,
            v::email());

        $this->verifierChamp($erreurs, 'motif', $data['motif'] ?? null,
            v::stringType()->length(5, 255));

        $this->verifierChamp($erreurs, 'date_debut', $data['date_debut'] ?? null,
            v::dateTime());

        $this->verifierChamp($erreurs, 'date_fin', $data['date_fin'] ?? null,
            v::dateTime());

        if (!empty($erreurs)) {
            return ValidationResult::echec($erreurs, $data);
        }

        return ValidationResult::succes($data);
    }

    private function verifierChamp(array &$erreurs, string $champ, mixed $valeur, $regle): void
    {
        try {
            $regle->assert($valeur);
        } catch (ValidationException $e) {
            $erreurs[$champ] = $e->getMessages();
        }
    }
}
