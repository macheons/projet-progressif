<?php
declare(strict_types=1);

namespace App\Models;

use Core\Database;
use PDO;

/**
 * Modèle Favori - domaines favoris de l'utilisateur connecté
 */
class FavoriModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /** Liste tous les favoris d'un utilisateur */
    public function listerParUtilisateur(int $utiId): array
    {
        $stmt = $this->db->prepare(
            'SELECT fav_id, fav_domaine, fav_date_ajout
             FROM t_favori_fav
             WHERE fav_uti_id = ?
             ORDER BY fav_date_ajout DESC'
        );
        $stmt->execute([$utiId]);
        return $stmt->fetchAll();
    }

    /** Ajoute un domaine aux favoris (ignore si déjà présent) */
    public function ajouter(int $utiId, string $domaine): bool
    {
        // Nettoyer le domaine
        $domaine = strtolower(trim($domaine));
        $domaine = preg_replace('#^https?://#', '', $domaine);
        $domaine = rtrim($domaine, '/');

        if (empty($domaine)) {
            return false;
        }

        $stmt = $this->db->prepare(
            'INSERT IGNORE INTO t_favori_fav (fav_uti_id, fav_domaine)
             VALUES (?, ?)'
        );
        $stmt->execute([$utiId, $domaine]);
        return $stmt->rowCount() > 0;
    }

    /** Supprime un favori (vérifie l'appartenance à l'utilisateur) */
    public function supprimer(int $favId, int $utiId): bool
    {
        $stmt = $this->db->prepare(
            'DELETE FROM t_favori_fav
             WHERE fav_id = ? AND fav_uti_id = ?'
        );
        $stmt->execute([$favId, $utiId]);
        return $stmt->rowCount() > 0;
    }

    /** Compte le nombre de favoris d'un utilisateur */
    public function compter(int $utiId): int
    {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) FROM t_favori_fav WHERE fav_uti_id = ?'
        );
        $stmt->execute([$utiId]);
        return (int) $stmt->fetchColumn();
    }
}
