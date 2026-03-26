<?php
declare(strict_types=1);

namespace Core;

/**
 * Gestionnaire de session sécurisée
 * Configure les paramètres du cookie de session et démarre la session.
 */
class SessionManager
{
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        // Mode strict : PHP refusera tout identifiant de session non généré par lui-même
        ini_set('session.use_strict_mode', '1');

        // Configuration du cookie de session
        session_set_cookie_params([
            'lifetime' => 0,         // Fermeture du navigateur
            'path'     => '/',
            'domain'   => '',
            'secure'   => false,    
            'httponly' => true,      // Inaccessible en JavaScript
            'samesite' => 'Lax',     // Protection CSRF partielle
        ]);

        session_start();
    }

    public static function regenerer(): void
    {
        session_regenerate_id(true);
    }

    public static function detruire(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(), '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }
        session_destroy();
    }
}
