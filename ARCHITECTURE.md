# Analyse architecturale

Ce document identifie et explique les concepts d'architecture logicielle mis en oeuvre dans ce projet.

## MVC (Modele-Vue-Controleur)

**Classes concernees** : `App\Model\Salle`, `App\Model\Reservation` (Modele) ; `templates/*.php` (Vue) ; `App\Controller\SalleController`, `App\Controller\ReservationController` (Controleur).

**Role** : separer trois responsabilites distinctes : la donnee et ses regles de forme (Modele), sa presentation (Vue), et l'orchestration de la requete (Controleur).

**Avantage** : chaque couche peut evoluer independamment ; la vue peut changer sans toucher au modele, et inversement.

**Limite** : mal applique, le controleur devient un fourre-tout ("fat controller") qui accumule de la logique metier ne lui appartenant pas - c'est pourquoi ce projet deporte les regles metier dans une couche Service distincte.

**Extrait** : `SalleController::show()` ne fait que recuperer la donnee et la transmettre a la vue, sans aucune regle metier :
```php
public function show(int $id): void
{
    $salle = $this->salles->trouver($id);
    if ($salle === null) {
        View::renderView('error/404', ['titre' => 'Salle introuvable']);
        return;
    }
    View::renderView('salle/show', ['salle' => $salle, 'titre' => $salle->nom]);
}
```

## Front Controller

**Classes concernees** : `public/index.php`, `App\Application`.

**Role** : un point d'entree HTTP unique qui recoit toutes les requetes, construit le conteneur, puis delegue le traitement.

**Avantage** : centralise la configuration (autoload, conteneur, gestion des erreurs) en un seul endroit, evitant de dupliquer cette logique dans un fichier PHP par page.

**Limite** : ce point d'entree unique devient un goulot d'etranglement potentiel si toute la logique s'y accumule - ici, `index.php` reste minimal et delegue immediatement a `Application::run()`.

**Extrait** :
```php
$container = $builder->build();
$application = $container->get(Application::class);
$application->run();
```

## Router (Routeur)

**Classes concernees** : `routes/web.php`, `FastRoute\Dispatcher` (configure dans `config/container.php`), `App\Application::dispatchRequete()`.

**Role** : faire correspondre une methode HTTP et une URL a un handler (classe + methode), y compris les segments dynamiques (`{id:\d+}`).

**Avantage** : separe la definition des routes de leur execution ; ajouter une route ne demande pas de toucher au code de dispatch.

**Limite** : le routeur ne construit pas lui-meme les controleurs (il retourne juste une reference `[Classe::class, 'methode']`) - c'est le conteneur qui doit s'en charger, une responsabilite facile a mal repartir.

**Extrait** :
```php
$r->addRoute('GET', '/salles/{id:\d+}', [SalleController::class, 'show']);
```

## Validator (Validateur)

**Classes concernees** : `App\Validation\ValidatorInterface`, `App\Validation\SalleValidator`, `App\Validation\ReservationValidator`, `App\Validation\ValidationResult`.

**Role** : verifier la forme des donnees entrantes (type, longueur, format) avant qu'elles n'atteignent la couche metier.

**Avantage** : isole la validation syntaxique des regles metier - le validateur ne sait rien de la base de donnees ni des chevauchements de reservation.

**Limite** : ne remplace pas les regles metier (une date valide syntaxiquement peut rester une date passee, invalide metier-parlant) ; les deux couches sont complementaires, pas interchangeables.

**Extrait** :
```php
$this->verifierChamp($erreurs, 'capacite', $data['capacite'] ?? null,
    v::intVal()->between(1, 1000));
```

## DTO (Data Transfer Object)

**Classes concernees** : `App\DTO\CreerSalleDTO`, `App\DTO\CreerReservationDTO`, et leurs Builders associes.

**Role** : transporter des donnees deja validees et typees (int, bool, DateTimeImmutable) entre les couches, sans jamais transmettre le tableau brut `$_POST`.

**Avantage** : les proprietes `readonly` garantissent l'immutabilite - une fois construit, un DTO ne peut plus etre modifie, ce qui evite les effets de bord.

**Limite** : un DTO ne doit contenir aucune logique metier (pas de regle de chevauchement, pas d'appel a `save()`) - le risque est de le transformer en objet actif, ce qui brouille sa responsabilite unique de transport.

**Extrait** :
```php
public static function fromArray(array $data, ?ValidatorInterface $validator = null): self
{
    $validator ??= new ReservationValidator();
    $resultat = $validator->validate($data);
    if (!$resultat->isValid()) {
        throw new DonneesInvalidesException($resultat->errors(), $data);
    }
    return self::builder()
        ->dateDebut(new DateTimeImmutable((string) $resultat->donneesAcceptees()['date_debut']))
        ->build();
}
```

## ORM (Object-Relational Mapping)

**Classes concernees** : `Illuminate\Database\Capsule\Manager` (configure dans `config/database.php`), `App\Model\Salle`, `App\Model\Reservation`.

**Role** : manipuler les lignes d'une base relationnelle comme des objets PHP, sans ecrire de SQL a la main pour les operations courantes.

**Avantage** : `Salle::find($id)` ou `$salle->reservations` remplacent des jointures SQL explicites ; le code reste lisible et portable entre moteurs de base (MySQL, SQLite).

**Limite** : un ORM peut generer des requetes inefficaces sur des cas complexes (le probleme N+1 par exemple) ; il faut parfois redescendre au Query Builder ou au SQL brut pour des besoins precis.

**Extrait** :
```php
$capsule->addConnection([...]);
$capsule->setAsGlobal();
$capsule->bootEloquent();
```

## Active Record

**Classes concernees** : `App\Model\Salle`, `App\Model\Reservation` (heritent de `Illuminate\Database\Eloquent\Model`).

**Role** : chaque instance de modele porte a la fois la donnee et le comportement de persistance (`$salle->save()`, `Salle::find()`) - le modele EST son propre acces aux donnees.

**Avantage** : tres rapide a ecrire pour les cas simples ; pas de classe intermediaire necessaire pour une simple lecture/ecriture.

**Limite** : couple fortement le modele metier a la persistance - c'est exactement pourquoi ce projet ajoute une couche Repository par-dessus, pour que les Controllers et Services ne dependent jamais directement d'Eloquent.

**Extrait** :
```php
class Salle extends Model
{
    protected $fillable = ['nom', 'batiment', 'capacite', 'type', 'active'];
    protected $casts = ['capacite' => 'integer', 'active' => 'boolean'];
}
```

## Repository

**Classes concernees** : `App\Repository\SalleRepositoryInterface`, `App\Repository\ReservationRepositoryInterface`, et leurs implementations `EloquentSalleRepository` / `EloquentReservationRepository`.

**Role** : isoler le reste de l'application (Controllers, Services) de la syntaxe concrete d'Eloquent, derriere un contrat (interface) simple.

**Avantage** : permet de substituer l'implementation reelle par une implementation en memoire dans les tests unitaires, sans jamais toucher au code appelant.

**Limite** : ajoute une couche d'indirection qui peut sembler superflue quand Eloquent constitue deja un Repository generique (pattern Active Record) - elle se justifie ici par l'interdiction explicite du sujet d'appeler Eloquent depuis les Controllers.

**Extrait** :
```php
public function rechercherConflit(int $salleId, DateTimeImmutable $dateDebut, DateTimeImmutable $dateFin, ?int $excludeId = null): ?Reservation
{
    return Reservation::where('salle_id', $salleId)
        ->where('statut', 'confirmee')
        ->where('date_debut', '<', $dateFin->format('Y-m-d H:i:s'))
        ->where('date_fin', '>', $dateDebut->format('Y-m-d H:i:s'))
        ->first();
}
```

## Service

**Classes concernees** : `App\Service\CreerReservationService`, `App\Service\AnnulerReservationService`.

**Role** : porter les regles metier (verifications, orchestration) independamment de la requete HTTP, du routeur, des vues ou du conteneur.

**Avantage** : testable isolement, sans simuler de requete HTTP ni de vraie base de donnees (voir `tests/Unit/CreerReservationServiceTest.php`, qui utilise des Repository en memoire).

**Limite** : si le Service se met a dependre du conteneur ou de `$_POST`, il perd cette testabilite - c'est explicitement interdit par le sujet.

**Extrait** :
```php
public function creer(CreerReservationDTO $dto): Reservation
{
    $salle = $this->salles->trouver($dto->salleId);
    if (!$salle->active) {
        throw new SalleIndisponibleException('Cette salle ne peut pas etre reservee.');
    }
    // ... verifications de duree, de date future, de conflit ...
}
```

## Injection par constructeur

**Classes concernees** : toutes les classes du projet ayant des dependances (`CreerReservationService`, `SalleController`, `ReservationController`...).

**Role** : une classe recoit ses dependances de l'exterieur, via son constructeur, plutot que de les creer elle-meme ou d'aller les chercher.

**Avantage** : les dependances sont explicites et visibles directement dans la signature du constructeur - on sait ce dont une classe a besoin sans lire tout son code.

**Limite** : un constructeur avec trop de parametres est un signal que la classe a trop de responsabilites (a rapprocher du principe de responsabilite unique).

**Extrait** :
```php
public function __construct(
    private readonly SalleRepositoryInterface $salles,
    private readonly ReservationRepositoryInterface $reservations
) {}
```

## Conteneur d'injection de dependances

**Classes concernees** : `config/container.php`, `DI\ContainerBuilder` (PHP-DI).

**Role** : construire automatiquement les objets et leurs dependances, en resolvant recursivement toute la chaine necessaire.

**Avantage** : `public/index.php` n'a qu'une seule ligne a ecrire (`$container->get(Application::class)`) pour obtenir une application entierement assemblee.

**Limite** : si chaque classe se met a interroger le conteneur elle-meme (`$container->get(...)`), on retombe dans l'anti-pattern Service Locator (voir plus bas) - le sujet impose donc de limiter cet acces au point d'entree.

**Extrait** :
```php
SalleRepositoryInterface::class => autowire(EloquentSalleRepository::class),
Capsule::class => factory(function (): Capsule { /* ... */ }),
```

## Autowiring

**Classes concernees** : toute definition `autowire()` dans `config/container.php` (ex. `CreerReservationService::class => autowire()`).

**Role** : PHP-DI lit automatiquement les types declares dans le constructeur d'une classe et construit ses dependances sans configuration explicite.

**Avantage** : pour les classes concretes simples, aucune definition detaillee n'est necessaire - le conteneur devine tout seul.

**Limite** : ne fonctionne pas pour les interfaces (PHP-DI ne peut pas deviner quelle implementation choisir) ni pour les objets necessitant une configuration complexe (d'ou les `factory()` pour `Capsule` et `Dispatcher`).

## Inversion de controle (IoC)

**Classes concernees** : l'ensemble du projet, notamment `CreerReservationService` qui depend de `SalleRepositoryInterface` (l'abstraction) et non de `EloquentSalleRepository` (l'implementation concrete).

**Role** : le flux de controle traditionnel est invers - une classe ne decide plus elle-meme comment obtenir ses dependances, c'est le conteneur qui le fait a sa place et les lui fournit.

**Avantage** : permet de substituer une implementation par une autre (Eloquent en production, en memoire dans les tests) sans modifier la classe consommatrice.

**Limite** : rend le flux d'execution moins lineaire a suivre pour un lecteur non familier - il faut consulter `container.php` pour savoir quelle implementation concrete sera reellement utilisee.

## Principes SOLID

**S - Single Responsibility** : chaque classe a une seule raison de changer. `SalleValidator` ne verifie que la forme des donnees, `CreerReservationService` ne porte que les regles metier, `EloquentSalleRepository` ne gere que la persistance.

**O - Open/Closed** : `ValidatorInterface` permet d'ajouter un nouveau validateur (ex. pour un futur type de ressource) sans modifier le code qui consomme deja `ValidatorInterface`.

**L - Liskov Substitution** : `EloquentSalleRepository` et `SalleRepositoryEnMemoire` (utilise dans les tests) sont interchangeables partout ou `SalleRepositoryInterface` est attendu, sans casser le comportement attendu par les appelants.

**I - Interface Segregation** : `SalleRepositoryInterface` et `ReservationRepositoryInterface` sont deux interfaces distinctes et cibleles, plutot qu'une seule grande interface `RepositoryInterface` generique qui forcerait des methodes non pertinentes.

**D - Dependency Inversion** : `CreerReservationService` depend de l'abstraction `SalleRepositoryInterface`, jamais de la classe concrete `EloquentSalleRepository` - c'est le conteneur qui relie les deux a l'execution.

**Extrait representatif** (D + L) :
```php
final class CreerReservationService
{
    public function __construct(
        private readonly SalleRepositoryInterface $salles,
        private readonly ReservationRepositoryInterface $reservations
    ) {}
}
```
