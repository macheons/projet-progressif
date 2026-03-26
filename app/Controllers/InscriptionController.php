<?php
declare(strict_types=1);

namespace App\Controllers;

use Core\Controller;
use Core\CsrfManager;
use Core\RateLimiter;
use Core\SessionManager;
use App\Models\UtilisateurModel;

/**
 * Contrôleur d'inscription
 */
class InscriptionController extends Controller
{
    public function executer(): void
    {
        $this->exigerDeconnexion();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->traiterInscription();
            return;
        }

        $this->afficherVue('inscription', [
            'pageTitle' => 'Inscription',
            'csrf'      => CsrfManager::champ(),
        ]);
    }

    private function traiterInscription(): void
    {
        $erreurs = [];

        // Rate Limiting 
        if (!RateLimiter::autoriser('inscription')) {
            $restant = RateLimiter::secondesRestantes('inscription');
            if ($this->estRequeteAjax()) {
                $this->repondreJson([
                    'succes'  => false,
                    'message' => "Trop de tentatives. Réessayez dans {$restant} secondes.",
                ], 429);
            }
            return;
        }

        // Validation CSRF
        $csrfSoumis = $_POST['csrf_token'] ?? '';
        if (!CsrfManager::valider($csrfSoumis)) {
            $erreurs['global'] = 'Jeton de sécurité invalide. Rechargez la page.';
        }

        // Récupération et nettoyage
        $pseudo              = trim($_POST['inscription_pseudo']       ?? '');
        $email               = trim($_POST['inscription_email']        ?? '');
        $motDePasse          = $_POST['inscription_motDePasse']        ?? '';
        $motDePasseConfirm   = $_POST['inscription_motDePasse_confirmation'] ?? '';

        //  Validation des champs 
        if (empty($pseudo)) {
            $erreurs['pseudo'] = 'Le pseudo est requis.';
        } elseif (mb_strlen($pseudo) < 2) {
            $erreurs['pseudo'] = 'Le pseudo doit contenir au moins 2 caractères.';
        } elseif (mb_strlen($pseudo) > 255) {
            $erreurs['pseudo'] = 'Le pseudo ne doit pas dépasser 255 caractères.';
        }

        if (empty($email)) {
            $erreurs['email'] = 'L\'adresse e-mail est requise.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $erreurs['email'] = 'L\'adresse e-mail est invalide.';
        }

        if (empty($motDePasse)) {
            $erreurs['motdepasse'] = 'Le mot de passe est requis.';
        } elseif (mb_strlen($motDePasse) < 8) {
            $erreurs['motdepasse'] = 'Le mot de passe doit contenir au moins 8 caractères.';
        } elseif (mb_strlen($motDePasse) > 72) {
            $erreurs['motdepasse'] = 'Le mot de passe ne doit pas dépasser 72 caractères.';
        }

        if ($motDePasse !== $motDePasseConfirm) {
            $erreurs['confirmation'] = 'Les mots de passe ne correspondent pas.';
        }

        if (empty($erreurs)) {
            $modele = new UtilisateurModel();

            if ($modele->pseudoExiste($pseudo)) {
                $erreurs['pseudo'] = 'Ce pseudo est déjà utilisé.';
            }
            if ($modele->emailExiste($email)) {
                $erreurs['email'] = 'Cette adresse e-mail est déjà utilisée.';
            }
        }

        // Traitement 
        if (empty($erreurs)) {
            $modele = $modele ?? new UtilisateurModel();
            $modele->creer($pseudo, $email, $motDePasse);
            CsrfManager::renouveler();

            if ($this->estRequeteAjax()) {
                $this->repondreJson([
                    'succes'      => true,
                    'message'     => 'Compte créé avec succès ! Vous pouvez vous connecter.',
                    'redirection' => BASE_URL . '/?page=connexion',
                ]);
            }

            $this->redirigerVers('connexion');
        }

        if ($this->estRequeteAjax()) {
            $this->repondreJson([
                'succes'  => false,
                'erreurs' => $erreurs,
            ], 422);
        }

        $this->afficherVue('inscription', [
            'pageTitle'      => 'Inscription',
            'erreurs'        => $erreurs,
            'ancienneValeur' => compact('pseudo', 'email'),
            'csrf'           => CsrfManager::champ(),
        ]);
    }
}
