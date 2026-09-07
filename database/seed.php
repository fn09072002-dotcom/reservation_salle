<?php

declare(strict_types=1);

use App\Model\Salle;

$salles = [
    ['nom' => 'Amphitheatre A', 'batiment' => 'Batiment principal', 'capacite' => 250, 'type' => 'amphitheatre', 'active' => true],
    ['nom' => 'Salle B12', 'batiment' => 'Batiment B', 'capacite' => 40, 'type' => 'cours', 'active' => true],
    ['nom' => 'Laboratoire Chimie', 'batiment' => 'Batiment Sciences', 'capacite' => 24, 'type' => 'laboratoire', 'active' => true],
    ['nom' => 'Salle Informatique 1', 'batiment' => 'Batiment Informatique', 'capacite' => 30, 'type' => 'informatique', 'active' => true],
    ['nom' => 'Salle de reunion', 'batiment' => 'Batiment Administration', 'capacite' => 12, 'type' => 'reunion', 'active' => true],
];

foreach ($salles as $donneesSalle) {
    $salle = Salle::firstOrCreate(
        ['nom' => $donneesSalle['nom']],
        $donneesSalle
    );

    $etait = $salle->wasRecentlyCreated ? 'creee' : 'deja existante';
    echo "Salle '{$salle->nom}' : {$etait}." . PHP_EOL;
}
