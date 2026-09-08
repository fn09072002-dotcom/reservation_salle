<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Validation\SalleValidator;
use PHPUnit\Framework\TestCase;

final class SalleValidatorTest extends TestCase
{
    private SalleValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new SalleValidator();
    }

    public function testSalleValideEstAcceptee(): void
    {
        $resultat = $this->validator->validate([
            'nom' => 'Salle B12',
            'batiment' => 'Batiment B',
            'capacite' => 40,
            'type' => 'cours',
            'active' => true,
        ]);

        self::assertTrue($resultat->isValid());
    }

    public function testCapaciteNegativeEstRejetee(): void
    {
        $resultat = $this->validator->validate([
            'nom' => 'Salle B12',
            'batiment' => 'Batiment B',
            'capacite' => -5,
            'type' => 'cours',
        ]);

        self::assertFalse($resultat->isValid());
        self::assertArrayHasKey('capacite', $resultat->errors());
    }

    public function testTypeDeSalleInconnuEstRejete(): void
    {
        $resultat = $this->validator->validate([
            'nom' => 'Salle B12',
            'batiment' => 'Batiment B',
            'capacite' => 40,
            'type' => 'piscine',
        ]);

        self::assertFalse($resultat->isValid());
        self::assertArrayHasKey('type', $resultat->errors());
    }
}
