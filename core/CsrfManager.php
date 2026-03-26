<?php
declare(strict_types=1);

namespace Core;

/**
 * Gestionnaire de jeton CSRF
 * Génère et valide des jetons pour protéger les formulaires.
 */
class CsrfManager
{
    private const SESSION_KEY = 'csrf_token';

    /** Retourne le jeton courant, en génère un si nécessaire */
    public static function obtenir(): string
    {
        if (empty($_SESSION[self::SESSION_KEY])) {
            $_SESSION[self::SESSION_KEY] = bin2hex(random_bytes(32));
        }
        return $_SESSION[self::SESSION_KEY];
    }

    /** Valide le jeton soumis depuis le formulaire */
    public static function valider(string $jeton): bool
    {
        if (empty($_SESSION[self::SESSION_KEY])) {
            return false;
        }
        return hash_equals($_SESSION[self::SESSION_KEY], $jeton);
    }

    /** Renouvelle le jeton après validation réussie */
    public static function renouveler(): void
    {
        $_SESSION[self::SESSION_KEY] = bin2hex(random_bytes(32));
    }

    /** Génère le champ HTML caché à insérer dans les formulaires */
    public static function champ(): string
    {
        $jeton = self::obtenir();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($jeton, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '">';
    }
}
