<?php
declare(strict_types=1);

namespace App\Controllers;

use Core\Controller;
use Core\CsrfManager;
use Core\RateLimiter;
use Core\SessionManager;
use App\Models\UtilisateurModel;

/**
 * Contrôleur de connexion
 */
class ConnexionController extends Controller
{
    public function executer(): void
    {
        $this->exigerDeconnexion();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->traiterConnexion();
            return;
        }

        $this->afficherVue('connexion', [
            'pageTitle' => 'Connexion',
            'csrf'      => CsrfManager::champ(),
        ]);
    }

    private function traiterConnexion(): void
    {
        $erreurs = [];

        //Rate Limiting
        if (!RateLimiter::autoriser('connexion')) {
            $restant = RateLimiter::secondesRestantes('connexion');
            $msg = "Trop de tentatives. Réessayez dans {$restant} secondes.";
            if ($this->estRequeteAjax()) {
                $this->repondreJson(['succes' => false, 'message' => $msg], 429);
            }
            $erreurs['global'] = $msg;
            $this->afficherVue('connexion', ['pageTitle' => 'Connexion', 'erreurs' => $erreurs, 'csrf' => CsrfManager::champ()]);
            return;
        }

        //CSRF
        if (!CsrfManager::valider($_POST['csrf_token'] ?? '')) {
            $erreurs['global'] = 'Jeton de sécurité invalide. Rechargez la page.';
        }

        //Validation
        $pseudo     = trim($_POST['connexion_pseudo']     ?? '');
        $motDePasse = $_POST['connexion_motDePasse']      ?? '';

        if (empty($pseudo)) {
            $erreurs['pseudo'] = 'Le pseudo est requis.';
        }
        if (empty($motDePasse)) {
            $erreurs['motdepasse'] = 'Le mot de passe est requis.';
        }

        // Authentification 
        if (empty($erreurs)) {
            $modele      = new UtilisateurModel();
            $utilisateur = $modele->connecter($pseudo, $motDePasse);

            if ($utilisateur === false) {
                $erreurs['global'] = 'Pseudo ou mot de passe incorrect.';
            } else {
                // Régénérer l'ID de session
                SessionManager::regenerer();

                // Enregistrer l'utilisateur en session
                $_SESSION['utilisateur_id']     = $utilisateur['uti_id'];
                $_SESSION['utilisateur_pseudo']  = $utilisateur['uti_pseudo'];
                $_SESSION['utilisateur_email']   = $utilisateur['uti_email'];

                CsrfManager::renouveler();

                if ($this->estRequeteAjax()) {
                    $this->repondreJson([
                        'succes'      => true,
                        'message'     => 'Connexion réussie !',
                        'redirection' => BASE_URL . '/?page=profil',
                    ]);
                }

                $this->redirigerVers('profil');
            }
        }

        if ($this->estRequeteAjax()) {
            $this->repondreJson(['succes' => false, 'erreurs' => $erreurs], 422);
        }

        $this->afficherVue('connexion', [
            'pageTitle'      => 'Connexion',
            'erreurs'        => $erreurs ?? [],
            'ancienneValeur' => ['pseudo' => $pseudo],
            'csrf'           => CsrfManager::champ(),
        ]);
    }
}
