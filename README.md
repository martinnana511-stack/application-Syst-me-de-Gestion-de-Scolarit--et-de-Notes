<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

In addition, [Laracasts](https://laracasts.com) contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

You can also watch bite-sized lessons with real-world projects on [Laravel Learn](https://laravel.com/learn), where you will be guided through building a Laravel application from scratch while learning PHP fundamentals.

## Agentic Development

Laravel's predictable structure and conventions make it ideal for AI coding agents like Claude Code, Cursor, and GitHub Copilot. Install [Laravel Boost](https://laravel.com/docs/ai) to supercharge your AI workflow:

```bash
composer require laravel/boost --dev

php artisan boost:install
```

Boost provides your agent 15+ tools and skills that help agents build Laravel applications while following best practices.

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).


==========================================================================================================
====================Projet : Système de Gestion de Scolarité et de Notes (Cycle Primaire)====================

Cahier des charges

L’objectif est de concevoir une application web pour la gestion d’un établissement d’enseignement primaire (du CP1 au CM2). L’application doit permettre un suivi rigoureux tant sur le plan financier que pédagogique.

Les fonctionnalités attendues sont les suivantes :

    Authentification (Gestionnaire/Enseignant) : 
    -Accès sécurisé pour l’administration de l’école.

    Gestion Administrative et Financière :
    — Inscription des élèves avec informations de base et photo ;
    — Configuration des classes et définition des frais de scolarité par classe ;
    — Enregistrement des versements effectués par les parents ;
    — Génération d’un reçu de paiement (format PDF) et calcul du reste à payer.

    Gestion Pédagogique (Notes et Moyennes) :
    — Saisie des notes par matière pour chaque élève ;
    — Calcul automatique des moyennes trimestrielles ;
    — Tableau de bord affichant le classement des élèves par classe.

    Tableau de Bord Global :
    — Statistique sur les frais collectés vs frais attendus ;
    — Liste des élèves en retard de paiement (impayés).

Travail à faire
En vous basant sur vos connaissances en PHP, Laravel, HTML, CSS, MySQL et JavaScript, vous
devez concevoir et développer cette application web fonctionnelle. Une attention particulière sera
portée à la structure de la base de données et aux relations entre les modèles (Eloquent).

====================Groupe 1====================

====================Membres====================
-NANA Martin Wendyam
-LANKOANDE Yourman

====================Procedure d'installation====================

#  Cloner le projet
git clone <url-du-repo> app-gestion-scolaire
cd app-gestion-scolaire

1) Logiciel à installer

-xampp et verifier si php est bien installer (ouvrir cmd et taper php --version)
-composer et verifier si composer est bien installer (ouvrir cmd et taper composer --version)
-node.js et verifier si c'est bien installer (ouvrir cmd et taper node --version puis npm --version)
-mysql (ici on utilise mysql comme base de donnée)

NB : "Si l'installation c'est bien passée, vous verez les version de chaque logiciel installer"

2) Installation des dependences

-Ourvrir le projet avec VS code. Ensuite, ouvrir le terminal de VS code et s'assurer d'être dans le dossier racine du projet (PSC:\Users\dell\Desktop\Système_Gestion_Scolarité_Notes\app-gestion-scolaire>)

-Taper "composer install", Si ça ne marche pas, ouvrir l'explorateur du fichier et aller dans C:\xampp\php\ puis chercher et ouvrir le fichier php.ini. ENsuite cherchez les lignes ci-dessous les décommanté en retirant le point virgule (;) au debut de chaque ligne et enregistrer puis relancer "composer install".

;extension_dir=C:\xampp\php\ext (Ici on peut souvent trouver uniquement ;extension_dir=C:.\ dans ce cas le changer par ;extension_dir=C:\xampp\php\ext)
;extension=openssl
;extension=polo-mysql
;extension=mysqli
;extension=fileinfo
;extension=mbstring

NB : On peut taper ces commandes ci-dessous pour verifier si ces lignes sont décommantéS (Vous verez le nom de chaque élément dans la liste)

php - | Findstr /i openssl
php - | Findstr /i polo
php - | Findstr /i mysql
php - | Findstr /i Fileinfo
php - | Findstr /i mbstring

3) Configuration de la base de donnée

# Copier le fichier d'environnement
cp .env.example .env

Ouvrir le dossier .env du projet et chercher les lignes qui resemble aux lignes  ci-desous puis remplacer par ces lignes

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel (nom de la base de donnée qui doit être creer dans mysql avec la commande CREATE DATABASE laravel; puis executer)
DB_USERNAME=root
DB_PASSWORD=mot_de_passe (c'est le mot de passe que l'on utilise pour se connecter a la base de donnée mysql)

4) Création des tables et insertion des données dans la base de donnée

-Toujours dans le terminal taper et executer la commande suivante : "php artisan migrate:fresh --seed"
-Lancer la commande "php artisan storage:link" afin de pouvoir afficher les photos des élèves

5) Lancer le serveur avec la commande php artisan serve puis ouvrir le lien suivant dans un navigateur http://127.0.0.1:8000

6) Connexion

-Directeur ( Gestionnaire)
email : "adminecole@gmail.com"
mot de passe : "password"

-Enseignants
nom :'Jean OUEDRAOGO'
classe : 'CP1-A'
email : 'jeanecole@gmail.com'
mot de passe : 'password'

Nom : 'Marie KABORÉ'
classe : 'CP2-A'
email : 'marieecole@gmail.com'
mot de passe : 'password'

Nom : 'Paul TRAORÉ'
classe : 'CE1-A'
email : 'paulecole@gmail.com'
mot de passe : 'password'

Nom : 'Fatima SAWADOGO'
classe : 'CE2-A'
email : 'fatimaecole@gmail.com'
mot de passe : 'password'

Nom : 'Pierre COMPAORÉ'
classe : 'CM1-A'
email : 'pierreecole@gmail.com'
mot de passe : 'password'

Nom : 'Awa TAPSOBA'
classe : 'CM2-A'
email : 'awaecole@gmail.com'
mot de passe : 'password'

====================Fonctionnalités====================

1) Authentification (Gestionnaire/Enseignant)

Les utilisateurs (Gestionnaire/Enseignant) doivent s'authentifier à l'aide d'un "email" et un "mot de passe" afin de pouvoir accéder à leurs compte.

2) Gestion Administrative et Financière

-Configuration des classes et définition des frais de scolarité par classe
Le gestionnaire peut ajouter des classes en choisissant le niveau (par exemple: CP1, CP2...), associer un nom à chaque classe (par exemple: CP1-A, CP2-A... Mais on ne peut pas avoir deux classe qui portent le même nom par exemple: CP1-A et CP1-A ), associer une année scolaire à chaque classe, attribuer un enseignant à chaque classe, définir un effectif maximum de chaque classe, définir les frais d'inscription et frais de scolarité annuel de chaque classe, il a aussi la possibiité de sélection les matières enseignées dans chaque classe.

-Création d'une matière
Le gestionnaire peut créer une matieère tout en ajoutant un nom et un code de la matière comme Français => FR, définir le coeficient et la note maximum de la matière.

-Ajout d'un utilisateur 
Le gestionnaire peut ajouter un utilisateur en remplissant les champs suivant : Nom complet, email, numéro de téléphone, le rôle (Gestionnaire ou enseignant) et attribuer un mot de passse.

-Inscription des élèves avec informations de base et photo

-Enregistrement des versements effectués par les parents 

-Génération d’un reçu de paiement (format PDF) et calcul du reste à payer.

3) — Gestion Pédagogique (Notes et Moyennes)

-Saisie des notes par matière pour chaque élève
Chaque enseignant peut saisir uniquement les notes de la classe qu'il enseigne. Une note ne peut être vide que si la personne est absent.

-Calcul automatique des moyennes trimestrielles
Les moyennes sont calculées automatiquement après avoir saisie et enregistrer toutes les notes.

-Tableau de bord affichant le classement des élèves par classe.
Affichage de la liste des élèves ordonnée de la meilleure à la moins bonne moyenne pour une classe donnée.

-Page de classement par classe et trimestre
-Affichage du rang avec médailles 🥇🥈🥉 pour les trois premiers
-Statistiques de la classe : effectif, moyenne de la classe, meilleure et plus faible moyenne
-Graphique de répartition des mentions (donut)
-Barres de progression par mention

4) Tableau de Bord Global

-Statistique sur les frais collectés vs frais attendus

-Liste des élèves en retard de paiement (impayés)

