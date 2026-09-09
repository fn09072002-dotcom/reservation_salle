<?php

declare(strict_types=1);

namespace Tests\Support;

use Illuminate\Database\Capsule\Manager as Capsule;

/**
 * A utiliser dans les tests UNITAIRES qui construisent un modele
 * Eloquent (new Salle([...]), new Reservation([...])) sans jamais
 * toucher de vraie base de donnees ni passer par un Repository reel.
 *
 * Eloquent a besoin d'une connexion configuree pour formater les
 * champs de type date au moment de la simple construction d'un objet
 * - ce trait ne fait que satisfaire cette contrainte technique de
 * l'ORM. Il ne cree aucun schema et n'execute jamais de requete SQL :
 * ce n'est PAS un acces a une base de donnees, contrairement a
 * Tests\Support\DatabaseTestCase (reservee aux tests d'integration).
 */
trait BooteEloquentPourLesCasts
{
    protected function booterEloquentPourLesCasts(): void
    {
        $capsule = new Capsule();
        $capsule->addConnection([
            'driver' => 'sqlite',
            'database' => ':memory:',
        ]);
        $capsule->setAsGlobal();
        $capsule->bootEloquent();
    }
}
