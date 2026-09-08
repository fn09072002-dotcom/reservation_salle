# Gestion des reservations de salles universitaires

Application PHP orientee objet permettant de consulter les salles
disponibles et de gerer leurs reservations, en remplacement du
systeme actuel par courriel (source de doublons).

Developpee sans framework complet, avec des composants specialises
installes via Composer : routage (FastRoute), validation
(Respect\Validation), ORM (Eloquent via illuminate/database),
conteneur d'injection de dependances (PHP-DI), variables
d'environnement (vlucas/phpdotenv).

## Prerequis

- PHP 8.2 ou superieur
- Extensions PHP : pdo_mysql, pdo_sqlite, dom (necessaire pour PHPUnit)
- Composer
- Un serveur MySQL 8.0 (ou Docker, voir plus bas)

Verifier les extensions installees : php -m | grep -iE "pdo_mysql|pdo_sqlite|dom"

## Installation des dependances

composer install

## Configuration de la base

Copier le fichier d'exemple puis l'adapter : cp .env.example .env

Variables attendues dans .env :
APP_ENV=development
APP_DEBUG=true
DB_DRIVER=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=reservation_salles
DB_USERNAME=root
DB_PASSWORD=

### Option A - MySQL local

Creer la base et un utilisateur dedie, puis renseigner ces identifiants dans .env.

### Option B - MySQL via Docker (recommande)

Un docker-compose.yml est fourni : il expose MySQL sur le port 3307 de la machine hote (pour eviter tout conflit avec un MySQL deja installe localement).

docker compose up -d mysql

Dans ce cas, .env doit utiliser :
DB_HOST=127.0.0.1
DB_PORT=3307
DB_DATABASE=reservation_salles
DB_USERNAME=fatou_app
DB_PASSWORD=fatou123

## Creation des tables

Le script fatou (a la racine du projet) fournit les commandes d'administration. Il est rejouable sans erreur si les tables existent deja.

php fatou migrate

## Ajout des donnees initiales

Insere les 5 salles de base (sans jamais creer de doublon, meme en relancant le script plusieurs fois) :

php fatou seed

## Lancement du serveur

Avec le serveur de developpement integre a PHP :

php -S localhost:8000 -t public public/index.php

L'application est alors accessible sur http://localhost:8000.

## Execution des tests

La suite PHPUnit (tests unitaires et d'integration) ne necessite aucun serveur MySQL : elle utilise SQLite en memoire.

vendor/bin/phpunit

## Fonctionnalites

### Salles

- consulter la liste des salles ;
- afficher le detail d'une salle ;
- ajouter une salle ;
- modifier une salle ;
- activer ou desactiver une salle.

### Reservations

- consulter toutes les reservations ;
- afficher une reservation ;
- creer une reservation (avec verification des chevauchements, de la duree maximale de 4 heures, et de la disponibilite de la salle) ;
- annuler une reservation.

## Documentation complementaire

- ARCHITECTURE.md - analyse des choix architecturaux (MVC, Repository, DTO, injection de dependances, principes SOLID...).
- NOTES-ETAPES.md - reponses aux questions pedagogiques posees a chaque etape du sujet.
- CHANGELOG.md - historique des versions.
