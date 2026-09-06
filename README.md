# Gestion des reservations de salles universitaires

Application PHP orientee objet permettant de consulter les salles
et de gerer leurs reservations, en remplacement du systeme actuel
par courriel (source de doublons).

## Etape 1 — Initialiser le projet Composer

### 1. Quel est le role de Composer ?

Il gere les dependances externes du projet (bibliotheques tierces comme
FastRoute, Eloquent) et fournit un autoloader qui charge automatiquement
les classes selon leur namespace, sans require manuel pour chaque fichier.

### 2. Quelle difference existe entre require et require-dev ?

`require` liste les dependances necessaires en production (le code ne
fonctionne pas sans elles). `require-dev` liste les outils utiles
uniquement pendant le developpement (tests, linters), non installes
si on deploie avec `composer install --no-dev`.

### 3. Pourquoi faut-il versionner composer.lock ?

Il fige les versions exactes de chaque dependance (et de leurs propres
dependances) au moment de l'installation. Sans lui, deux machines
faisant `composer install` a des dates differentes pourraient recuperer
des versions differentes.

### 4. Pourquoi ne versionne-t-on pas vendor/ ?

Il est entierement regenerable a partir de composer.json et
composer.lock via `composer install` — le versionner alourdirait le
depot pour rien.

## Etape 2 — Configurer Eloquent

### 1. Quel role joue Capsule\Manager ?

Il configure et demarre Eloquent en dehors du framework Laravel — il gere
la connexion a la base de donnees et expose le Schema Builder et l'ORM,
exactement comme le ferait Laravel en interne.

### 2. Pourquoi Eloquent peut-il fonctionner sans Laravel ?

Parce qu'Eloquent est distribue comme une bibliotheque independante
(illuminate/database), pas couplee au reste du framework. Capsule\Manager
sert de point d'entree simplifie pour l'utiliser de facon autonome.

### 3. Ou doit se trouver le demarrage de l'ORM ?

Dans un seul endroit centralise (config/database.php), jamais disperse
dans plusieurs classes.

### 4. Quelle difference existe entre ORM et SQL ecrit a la main ?

L'ORM permet de manipuler les donnees comme des objets PHP sans ecrire de
requetes SQL directement — il genere lui-meme le SQL adapte au moteur de
base utilise. Le SQL a la main demande de connaitre la syntaxe exacte du
SGBD, mais offre un controle plus fin sur les requetes complexes.

## Etape 3 — Creer les modeles

### 1. Quel type de relation Eloquent avez-vous utilise ?

Une relation un-a-plusieurs (hasMany sur Salle, belongsTo sur Reservation),
reflet qu'une salle peut avoir plusieurs reservations mais qu'une
reservation n'appartient qu'a une seule salle.

### 2. Pourquoi declarer $fillable ?

Pour se proteger de l'assignation en masse non controlee : sans cette
liste blanche, Salle::create($_POST) pourrait ecraser des colonnes
sensibles si des champs inattendus etaient ajoutes dans le formulaire.

### 3. Pourquoi convertir active en booleen ?

MySQL stocke ce champ en tinyint(1), un entier brut. Le cast permet de
manipuler $salle->active comme un vrai booleen PHP dans le code metier.

### 4. Pourquoi convertir les dates en objets ?

Pour utiliser directement des methodes de comparaison et de calcul
(diffInHours, isPast...) sans reparser manuellement des chaines a chaque
utilisation — essentiel pour la regle des quatre heures maximum.

## Etape 4 — Ajouter les donnees initiales

### 1. Quelle difference existe entre migration et seeder ?

La migration definit la structure de la base (tables, colonnes,
contraintes). Le seeder insere des donnees dans une structure deja
existante. Une migration s'occupe du contenant, un seeder du contenu.

### 2. Pourquoi les donnees initiales doivent-elles etre reproductibles ?

Pour que n'importe qui (autre developpeur, serveur de test, CI) puisse
reconstituer un environnement identique en relancant le script, sans
risque d'obtenir un resultat different selon le nombre d'executions.

### 3. Comment empecher les doublons ?

En utilisant firstOrCreate() avant l'insertion, base sur un critere qui
identifie une salle de facon unique (ici le nom) — la ligne n'est creee
que si aucune correspondance n'existe deja.

## Etape 5 — Creer la validation

### 1. Pourquoi separer la validation syntaxique des regles metier ?

La validation syntaxique verifie la forme des donnees, sans dependre
d'aucune donnee externe. Les regles metier necessitent d'interroger la
base et le contexte applicatif. Les separer permet de tester la
validation de forme instantanement, sans base de donnees.

### 2. Pourquoi creer une interface de validation ?

ValidatorInterface garantit que tout validateur respecte le meme
contrat, ce qui permet au code appelant de traiter n'importe quel
validateur de facon uniforme.

### 3. Pourquoi le validateur ne doit-il pas enregistrer les donnees ?

Sa seule responsabilite est de dire si les donnees sont valides —
melanger validation et ecriture en base violerait le principe de
responsabilite unique.

### 4. Comment retourner plusieurs erreurs en une seule fois ?

En accumulant les erreurs dans un tableau au fur et a mesure des
verifications, plutot que de s'arreter a la premiere erreur trouvee.

## Etape 6 — Creer les objets de transport

### 1. Quelle difference existe entre DTO et modele Eloquent ?

Le modele Eloquent represente une ligne persistee en base, avec un
comportement et un cycle de vie lie a la base de donnees. Le DTO est
une structure de donnees passive, immuable, sans lien avec la
persistance — il transporte des valeurs typees entre deux couches.

### 2. Pourquoi le DTO ne doit-il pas appeler save() ?

Sa seule responsabilite est le transport de donnees deja validees et
typees — appeler save() melangerait transport et persistance, deux
responsabilites distinctes.

### 3. A quel moment transforme-t-on les chaines en dates ?

Dans le DTO lui-meme, au moment de sa construction (fromArray) — le
point de passage oblige entre les donnees brutes du formulaire et le
reste de l'application.

### 4. Le DTO doit-il contenir la regle de chevauchement ?

Non. Le DTO garantit uniquement que les donnees sont bien formees et
typees ; la regle de chevauchement necessite d'interroger la base et
appartient au futur Service.

## Etape 7 — Creer l'acces aux donnees

### 1. Eloquent constitue-t-il deja un acces aux donnees ?

Oui — Salle::find(), Reservation::where() sont deja un acces aux
donnees fonctionnel. Eloquent est un Repository generique fourni par
le framework (pattern Active Record).

### 2. Pourquoi ajouter un Repository au-dessus d'Eloquent ?

Pour isoler le reste de l'application de la syntaxe specifique
d'Eloquent. Sans cette couche, Salle::where(...) se retrouverait
disperse dans les Controllers, ce que l'enonce interdit explicitement.

### 3. Cette abstraction est-elle toujours necessaire ?

Pas toujours. Ici elle se justifie par l'interdiction explicite
d'appeler Eloquent depuis les Controllers, et le besoin de tester les
Services sans MySQL en substituant le Repository par une version en
memoire.

### 4. Quel avantage apporte-t-elle ?

Elle permet de remplacer l'implementation concrete par une autre (en
memoire pour les tests) sans toucher au code qui utilise l'interface.

## Etape 8 — Implementer les regles metier

### 1. Pourquoi ces regles ne sont-elles pas dans le controleur ?

Le controleur a pour seul role de traiter la requete HTTP. Mélanger la
logique metier dedans le rendrait impossible a tester sans simuler une
requete HTTP complete, et le coupleraitInutilement au web.

### 2. Pourquoi le service depend-il d'une interface de Repository ?

Pour ne jamais etre couple a une implementation concrete (Eloquent) —
ca permet de le tester avec une implementation en memoire, sans
dependre de MySQL.

### 3. Quelle exception doit etre levee en cas de conflit ?

SalleIndisponibleException — la meme que pour une salle inactive, car
les deux cas signifient que la salle n'est pas disponible pour cette
periode.

### 4. Comment tester le service sans MySQL ?

En injectant des implementations en memoire des interfaces de
Repository, et en demarrant Eloquent avec une connexion SQLite en
memoire plutot que MySQL.

## Etape 10 — Configurer FastRoute

### 1. Pourquoi FastRoute ne construit-il pas lui-meme le controleur ?

FastRoute fait correspondre une URL/methode a une reference de classe
et methode — il ne sait pas comment construire cette classe. Cette
responsabilite appartient au conteneur d'injection.

### 2. Quelle difference existe entre 404 et 405 ?

404 signifie que l'URL ne correspond a aucune route declaree. 405
signifie que l'URL existe, mais que la methode HTTP utilisee n'est
pas autorisee pour cette route.

### 3. Pourquoi contraindre {id} avec \d+ ?

Pour que seules les URLs avec un identifiant numerique matchent la
route — un id non numerique tombe naturellement en 404.

### 4. Quel composant doit interpreter le handler retourne ?

Le point d'entree (public/index.php) — il recoit la reference de
classe/methode, resout l'instance via le conteneur, et appelle la
methode avec les parametres extraits.

## Etape 11 — Configurer PHP-DI

### 1. Quelle difference existe entre injection et conteneur ?

L'injection est un principe : une classe recoit ses dependances de
l'exterieur. Le conteneur est un outil qui automatise la construction
et la fourniture de ces dependances.

### 2. Qu'est-ce que l'autowiring ?

La capacite de PHP-DI a construire automatiquement une classe en
lisant les types declares dans son constructeur, sans assemblage
explicite.

### 3. Pourquoi les interfaces necessitent-elles une definition ?

PHP-DI ne peut pas deviner quelle implementation choisir pour une
interface — il faut le dire explicitement.

### 4. Pourquoi limiter $container->get() au point d'entree ?

Sinon les dependances d'une classe deviennent invisibles, cachees
dans son code plutot qu'affichees dans son constructeur.

### 5. Quel anti-pattern apparait si toutes les classes interrogent le conteneur ?

Le Service Locator — chaque classe devient couplee au conteneur
plutot qu'a ses vraies dependances metier, rendant les tests plus
difficiles.
