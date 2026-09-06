<?php

declare(strict_types=1);

namespace App\Validation;

use Respect\Validation\Validator as v;
use Respect\Validation\Exceptions\ValidationException;

final class SalleValidator implements ValidatorInterface
{
    private const TYPES_AUTORISES = ['cours', 'informatique', 'laboratoire', 'amphitheatre', 'reunion'];

    public function validate(array $data): ValidationResult
    {
        $erreurs = [];

        $this->verifierChamp($erreurs, 'nom', $data['nom'] ?? null,
            v::stringType()->length(2, 100));

        $this->verifierChamp($erreurs, 'batiment', $data['batiment'] ?? null,
            v::stringType()->length(2, 100));

        $this->verifierChamp($erreurs, 'capacite', $data['capacite'] ?? null,
            v::intVal()->between(1, 1000));

        $this->verifierChamp($erreurs, 'type', $data['type'] ?? null,
            v::in(self::TYPES_AUTORISES));

        if (array_key_exists('active', $data)) {
            $this->verifierChamp($erreurs, 'active', $data['active'],
                v::boolVal());
        }

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
