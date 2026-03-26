# FaviconHub  Projet Progressif

> Application web PHP/MySQL permettant d'explorer les favicons de n'importe quel site via **Icon Horse**, avec authentification utilisateur et gestion de favoris.

## Stack technique

| Côté | Technologies |
|------|-------------|
| Serveur | PHP 8.2+ vanilla, MySQL 8, architecture MVC |
| Client | JavaScript ES Modules (vanilla), AOS (bibliothèque tierce) |
| Sécurité | CSRF, rate limiting, requêtes préparées PDO, sessions sécurisées |
| API externe | DummyJSON (citations) |
| API interne | FavoriController (endpoint AJAX JSON) |
| Favicons | Icon Horse (https://icon.horse) |

## Structure du projet


projet-progressif/
├── config/
│   └── config.php              ← BASE_URL, BDD, constantes
├── core/
│   ├── Autoloader.php          ← Chargement automatique
│   ├── Controller.php          ← Contrôleur de base
│   ├── CsrfManager.php         ← Jetons CSRF
│   ├── Database.php            ← Singleton PDO
│   ├── RateLimiter.php         ← Limitation de requêtes
│   ├── Router.php              ← Routeur
│   └── SessionManager.php      ← Session sécurisée
├── app/
│   ├── Controllers/            ← Un contrôleur par page
│   ├── Models/                 ← Logique BDD
│   └── Views/                  ← Vues HTML/PHP
├── templates/
│   ├── header.php              ← Template réutilisable
│   └── footer.php
├── public/                     ← SEUL dossier web-accessible
│   ├── index.php               ← Front controller
│   ├── .htaccess
│   └── assets/
│       ├── css/style.css       ← CSS responsive
│       ├── js/                 ← Modules JS
│       └── img/
├── bdd_projet_web.sql          ← Script SQL à importer
└── liens.txt                   ← URLs GitHub