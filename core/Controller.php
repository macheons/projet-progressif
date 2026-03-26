<?php
declare(strict_types=1);

namespace Core;

/**
 * Contrôleur de base
 * Tous les contrôleurs héritent de cette classe.
 */
abstract class Controller
{
    /**
     * Affiche une vue en l'encadrant du header et du footer.
     * @param string $vue    Nom du fichier dans app/Views/
     * @param array  $donnees Variables transmises à la vue via extract()
     */
    protected function afficherVue(string $vue, array $donnees = []): void
    {
        extract($donnees, EXTR_SKIP);
        require_once ROOT_PATH . '/templates/header.php';
        require_once ROOT_PATH . '/app/Views/' . $vue . '.php';
        require_once ROOT_PATH . '/templates/footer.php';
    }

    /** Retourne true si la requête provient d'un appel Fetch/AJAX */
    protected function estRequeteAjax(): bool
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /** Envoie une réponse JSON et arrête l'exécution */
    protected function repondreJson(array $data, int $code = 200): never
    {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    /** Redirige vers une page du projet */
    protected function redirigerVers(string $page, array $params = []): never
    {
        $url = BASE_URL . '/?page=' . $page;
        foreach ($params as $cle => $val) {
            $url .= '&' . urlencode($cle) . '=' . urlencode((string)$val);
        }
        header('Location: ' . $url);
        exit;
    }

    /** Vérifie si l'utilisateur est connecté */
    protected function estConnecte(): bool
    {
        return !empty($_SESSION['utilisateur_id']);
    }

    /** Protège une page redirige vers connexion si non connecté */
    protected function exigerConnexion(): void
    {
        if (!$this->estConnecte()) {
            $this->redirigerVers('connexion');
        }
    }

    /** Redirige vers profil si déjà connecté */
    protected function exigerDeconnexion(): void
    {
        if ($this->estConnecte()) {
            $this->redirigerVers('profil');
        }
    }

    /** Échappe une valeur pour l'affichage HTML */
    protected function e(mixed $valeur): string
    {
        return htmlspecialchars((string)$valeur, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
