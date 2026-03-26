<?php
declare(strict_types=1);

namespace App\Models;

use Core\Database;
use PDO;

/**
 * Modèle Utilisateur  logique de persistance liée à t_utilisateur_uti
 */
class UtilisateurModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    // ─── Inscription ────────────────────────────────────────────────────────

    /** Vérifie si un pseudo est déjà pris */
    public function pseudoExiste(string $pseudo): bool
    {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) FROM t_utilisateur_uti WHERE uti_pseudo = ?'
        );
        $stmt->execute([$pseudo]);
        return $stmt->fetchColumn() > 0;
    }

    /** Vérifie si un email est déjà utilisé */
    public function emailExiste(string $email): bool
    {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) FROM t_utilisateur_uti WHERE uti_email = ?'
        );
        $stmt->execute([$email]);
        return $stmt->fetchColumn() > 0;
    }

    /** Crée un nouveau compte utilisateur */
    public function creer(string $pseudo, string $email, string $motDePasse): int
    {
        $hachage = password_hash($motDePasse, PASSWORD_BCRYPT);

        $stmt = $this->db->prepare(
            'INSERT INTO t_utilisateur_uti
             (uti_pseudo, uti_email, uti_motdepasse, uti_compte_active)
             VALUES (?, ?, ?, 1)'
        );
        $stmt->execute([$pseudo, $email, $hachage]);
        return (int) $this->db->lastInsertId();
    }

    // Connexion

    /**
     * Tente de connecter un utilisateur.
     * @return array|false Données utilisateur ou false si échec
     */
    public function connecter(string $pseudo, string $motDePasse): array|false
    {
        $stmt = $this->db->prepare(
            'SELECT uti_id, uti_pseudo, uti_email, uti_motdepasse, uti_compte_active
             FROM t_utilisateur_uti
             WHERE uti_pseudo = ?'
        );
        $stmt->execute([$pseudo]);
        $utilisateur = $stmt->fetch();

        if (!$utilisateur) {
            return false;
        }

        if (!$utilisateur['uti_compte_active']) {
            return false;
        }

        if (!password_verify($motDePasse, $utilisateur['uti_motdepasse'])) {
            return false;
        }

        // Ne pas retourner le hash du mot de passe
        unset($utilisateur['uti_motdepasse']);
        return $utilisateur;
    }

    //Profil

    /** Récupère un utilisateur par son ID */
    public function trouverParId(int $id): array|false
    {
        $stmt = $this->db->prepare(
            'SELECT uti_id, uti_pseudo, uti_email, uti_date_inscription
             FROM t_utilisateur_uti
             WHERE uti_id = ?'
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
}
