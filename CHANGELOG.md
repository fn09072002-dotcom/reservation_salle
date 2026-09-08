# Journal des versions

## v0.16.0

Documentation complete du projet.

- README.md reecrit : prerequis, installation, configuration base (MySQL local ou Docker), creation des tables, donnees initiales, lancement serveur, execution des tests
- ARCHITECTURE.md : analyse des 14 concepts architecturaux demandes (MVC, Front Controller, Router, Validator, DTO, ORM, Active Record, Repository, Service, injection par constructeur, conteneur, autowiring, inversion de controle, SOLID)
- NOTES-ETAPES.md : ancien contenu du README (reponses aux questions pedagogiques par etape) conserve ici

## v0.15.0

Messages flash et gestion renforcee des exceptions.

- Nouvelle classe Flash (session PHP) pour les messages de succes/erreur apres redirection
- Messages de succes sur creation/modification de salle, creation/annulation de reservation
- Correction : les pages 404/405 passent desormais par View::renderView (layout complet au lieu d'un rendu nu)
- Nouveau filet de securite : exceptions imprevues capturees, page 500 stylee au lieu d'un crash PHP brut

## v0.14.0

Suite de tests PHPUnit complete.

- 15 tests unitaires : les 8 scenarios metier du service de creation de reservation, plus les validateurs Salle et Reservation
- 4 tests d'integration avec les vrais Repository Eloquent (creation, relation salle/reservations, recherche de chevauchement, annulation), via SQLite en memoire
- Aucun serveur MySQL requis pour lancer la suite

## v0.13.0

Harmonisation visuelle de toutes les vues.

- Nouvelle page d'accueil (bandeau + cartes cliquables Salles/Reservations)
- Composants CSS ajoutes : en-tete de page, badges de statut, etat vide, pages d'erreur centrees
- Listes et details de salles/reservations restyles avec la charte marine/or

## v0.11.4

Correction du pattern Builder pour les DTO.

- Ajout des classes CreerSalleDTOBuilder et CreerReservationDTOBuilder (manquantes, provoquaient une erreur fatale)
- Nouvelle DonneesInvalidesException transportant les erreurs par champ
- Simplification des Controllers : suppression de la double validation

## v0.11.3

Script CLI d'administration.

- Nouveau script fatou avec les commandes migrate et seed
- Simplification de database/seed.php (pur script de donnees)

## v0.11.2

Correctif migrations.

- Migrations decoupees par table (une classe par table)

## v0.11.1

Correctif environnement Docker.

- Mise en place de MySQL via Docker pour le developpement local

## v0.11.0

Configuration du conteneur d'injection de dependances.

- config/container.php : autowiring pour les classes simples, definitions explicites pour les interfaces, factories pour Capsule et Dispatcher
- public/index.php devient minimal (construction du conteneur, recuperation de Application::class)

## v0.10.0

Configuration du routeur FastRoute.

- Routes declarees dans routes/web.php
- Gestion des reponses NOT_FOUND (404), METHOD_NOT_ALLOWED (405, avec en-tete Allow) et FOUND

## v0.9.0

Controleurs et vues.

- SalleController et ReservationController avec actions completes (index, show, create, store, edit, update, cancel)
- View::renderView() centralise le rendu (injection dans le layout commun)
- 9 templates crees, toutes les sorties dynamiques echappees

## v0.8.0

Regles metier.

- CreerReservationService : verifie la salle, son activite, la coherence des dates, la duree maximale de 4h, la date future, et les chevauchements
- AnnulerReservationService
- SalleIndisponibleException, ReservationIntrouvableException

## v0.7.0

Acces aux donnees.

- SalleRepositoryInterface, ReservationRepositoryInterface et leurs implementations Eloquent
- rechercherConflit() : traduit la regle de chevauchement (debut < finExistante ET fin > debutExistante), en ignorant les reservations annulees

## v0.6.0

Objets de transport (DTO).

- CreerSalleDTO et CreerReservationDTO, avec conversion des types bruts de $_POST vers des types reels (int, bool, DateTimeImmutable)

## v0.5.0

Validation des donnees entrantes.

- ValidatorInterface, SalleValidator, ReservationValidator (via Respect\Validation)
- ValidationResult : donnees valides ou non, erreurs par champ, donnees acceptees

## v0.4.0

Donnees initiales.

- Script de seed inserant 5 salles via firstOrCreate() (rejouable sans doublon)

## v0.3.0

Modeles Eloquent.

- Salle (hasMany vers Reservation) et Reservation (belongsTo vers Salle)
- $fillable pour l'assignation en masse, $casts pour les types (booleen, dates)

## v0.2.0

Configuration d'Eloquent.

- Chargement des variables d'environnement (.env)
- Capsule\Manager configure et demarre hors Laravel
- Tables salles et reservations creees via le Schema Builder

## v0.1.0

Initialisation du projet Composer.

- composer.json avec autoload PSR-4 (App\ vers src/)
- Arborescence complete du projet
- Dependances imposees installees (FastRoute, Respect\Validation, Illuminate\Database, PHP-DI, phpdotenv)

## v0.0.0

Initialisation du depot.

- Git initialise, branche main, .gitignore, README.md et CHANGELOG.md crees
