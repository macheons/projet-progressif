<?php
declare(strict_types=1);

namespace Core;

/**
 * Limiteur des requêtes POST
 * Empêche le spam de formulaires en limitant le nombre de soumissions.
 */
class RateLimiter
{
    private const MAX_REQUETES   = 5;
    private const DELTA_SECONDES = 60;

    /**
     * Vérifie si la requête est autorisée.
     * @return bool true = autorisée, false = bloquée
     */
    public static function autoriser(string $cle = 'global'): bool
    {
        $sessionCle     = 'rl_' . $cle;
        $sessionCompteur = 'rl_count_' . $cle;
        $maintenant     = time();

        // Première requête ou delta expiré
        if (
            !isset($_SESSION[$sessionCle]) ||
            ($maintenant - $_SESSION[$sessionCle]) >= self::DELTA_SECONDES
        ) {
            $_SESSION[$sessionCle]     = $maintenant;
            $_SESSION[$sessionCompteur] = 1;
            return true;
        }

        // Incrémenter le compteur
        $_SESSION[$sessionCompteur]++;

        // Vérifier la limite
        if ($_SESSION[$sessionCompteur] > self::MAX_REQUETES) {
            return false;
        }

        return true;
    }

    /** Secondes restantes avant réinitialisation */
    public static function secondesRestantes(string $cle = 'global'): int
    {
        $sessionCle = 'rl_' . $cle;
        if (!isset($_SESSION[$sessionCle])) {
            return 0;
        }
        $restant = self::DELTA_SECONDES - (time() - $_SESSION[$sessionCle]);
        return max(0, $restant);
    }
}
