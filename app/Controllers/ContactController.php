<?php
declare(strict_types=1);

namespace App\Controllers;

use Core\Controller;
use Core\CsrfManager;
use Core\RateLimiter;

/**
 * Contrôleur du formulaire de contact
 */
class ContactController extends Controller
{
    public function executer(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->traiterContact();
            return;
        }

        $this->afficherVue('contact', [
            'pageTitle' => 'Contact',
            'csrf'      => CsrfManager::champ(),
        ]);
    }

    private function traiterContact(): void
    {
        $erreurs = [];

        //Rate Limiting
        if (!RateLimiter::autoriser('contact')) {
            $restant = RateLimiter::secondesRestantes('contact');
            $msg = "Trop de tentatives. Réessayez dans {$restant} secondes.";
            if ($this->estRequeteAjax()) {
                $this->repondreJson(['succes' => false, 'message' => $msg], 429);
            }
            $erreurs['global'] = $msg;
        }

        // CSRF 
        if (empty($erreurs) && !CsrfManager::valider($_POST['csrf_token'] ?? '')) {
            $erreurs['global'] = 'Jeton de sécurité invalide.';
        }

        // Champs 
        $nom     = trim($_POST['nom']     ?? '');
        $prenom  = trim($_POST['prenom']  ?? '');
        $email   = trim($_POST['email']   ?? '');
        $message = trim($_POST['message'] ?? '');

        if (empty($nom)) {
            $erreurs['nom'] = 'Le nom est requis.';
        } elseif (mb_strlen($nom) < 2) {
            $erreurs['nom'] = 'Le nom doit contenir au moins 2 caractères.';
        } elseif (mb_strlen($nom) > 255) {
            $erreurs['nom'] = 'Le nom ne doit pas dépasser 255 caractères.';
        }

        if (!empty($prenom) && mb_strlen($prenom) < 2) {
            $erreurs['prenom'] = 'Le prénom doit contenir au moins 2 caractères.';
        }

        if (empty($email)) {
            $erreurs['email'] = 'L\'adresse e-mail est requise.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $erreurs['email'] = 'L\'adresse e-mail est invalide.';
        }

        if (empty($message)) {
            $erreurs['message'] = 'Le message est requis.';
        } elseif (mb_strlen($message) < 10) {
            $erreurs['message'] = 'Le message doit contenir au moins 10 caractères.';
        } elseif (mb_strlen($message) > 3000) {
            $erreurs['message'] = 'Le message ne doit pas dépasser 3000 caractères.';
        }

        if (empty($erreurs)) {

            CsrfManager::renouveler();

            if ($this->estRequeteAjax()) {
                $this->repondreJson([
                    'succes'  => true,
                    'message' => 'Votre message a bien été envoyé. Merci !',
                ]);
            }

            $this->afficherVue('contact', [
                'pageTitle'     => 'Contact',
                'messageGlobal' => 'Votre message a bien été envoyé. Merci !',
                'csrf'          => CsrfManager::champ(),
            ]);
            return;
        }

        if ($this->estRequeteAjax()) {
            $this->repondreJson(['succes' => false, 'erreurs' => $erreurs], 422);
        }

        $this->afficherVue('contact', [
            'pageTitle'      => 'Contact',
            'erreurs'        => $erreurs,
            'ancienneValeur' => compact('nom', 'prenom', 'email', 'message'),
            'csrf'           => CsrfManager::champ(),
        ]);
    }
}
