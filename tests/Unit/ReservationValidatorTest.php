<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Validation\ReservationValidator;
use PHPUnit\Framework\TestCase;

final class ReservationValidatorTest extends TestCase
{
    private ReservationValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new ReservationValidator();
    }

    private function donneesValides(array $surcharge = []): array
    {
        return array_merge([
            'salle_id' => 1,
            'responsable' => 'Awa Ndiaye',
            'email' => 'awa.ndiaye@universite.sn',
            'motif' => 'Cours de test',
            'date_debut' => '2026-06-10 10:00:00',
            'date_fin' => '2026-06-10 12:00:00',
        ], $surcharge);
    }

    public function testReservationValideEstAcceptee(): void
    {
        $resultat = $this->validator->validate($this->donneesValides());

        self::assertTrue($resultat->isValid());
    }

    public function testAdresseEmailInvalideEstRejetee(): void
    {
        $resultat = $this->validator->validate(
            $this->donneesValides(['email' => 'pas-une-adresse'])
        );

        self::assertFalse($resultat->isValid());
        self::assertArrayHasKey('email', $resultat->errors());
    }

    public function testResponsableVideEstRejete(): void
    {
        $resultat = $this->validator->validate(
            $this->donneesValides(['responsable' => ''])
        );

        self::assertFalse($resultat->isValid());
        self::assertArrayHasKey('responsable', $resultat->errors());
    }

    public function testDateIncorrecteEstRejetee(): void
    {
        $resultat = $this->validator->validate(
            $this->donneesValides(['date_debut' => 'pas-une-date'])
        );

        self::assertFalse($resultat->isValid());
        self::assertArrayHasKey('date_debut', $resultat->errors());
    }
}
