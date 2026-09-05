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
