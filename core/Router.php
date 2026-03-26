<?php
declare(strict_types=1);

namespace Core;

/**
 * Routeur  associe chaque valeur de ?page= à un contrôleur.
 */
class Router
{
    /** Table de routage : page => classe contrôleur complète */
    private array $routes = [
        'accueil'     => \App\Controllers\AccueilController::class,
        'contact'     => \App\Controllers\ContactController::class,
        'inscription' => \App\Controllers\InscriptionController::class,
        'connexion'   => \App\Controllers\ConnexionController::class,
        'profil'      => \App\Controllers\ProfilController::class,
        'deconnexion' => \App\Controllers\DeconnexionController::class,
        'favori'      => \App\Controllers\FavoriController::class,
    ];

    /**
     * Résout la page demandée et invoque le contrôleur.
     */
    public function dispatch(): void
    {
        $page = trim($_GET['page'] ?? 'accueil');

        // Nettoyer l'entrée  uniquement lettres, chiffres et tirets
        $page = preg_replace('/[^a-z0-9\-]/', '', strtolower($page));

        $classeControleur = $this->routes[$page] ?? null;

        if ($classeControleur === null) {
            http_response_code(404);
            $classeControleur = \App\Controllers\AccueilController::class;
            // On retombe sur l'accueil
        }

        /** @var \Core\Controller $controleur */
        $controleur = new $classeControleur();
        $controleur->executer();
    }
}
