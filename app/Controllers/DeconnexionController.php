<?php
declare(strict_types=1);

namespace App\Controllers;

use Core\Controller;
use Core\SessionManager;

/**
 * Contrôleur de déconnexion
 */
class DeconnexionController extends Controller
{
    public function executer(): void
    {
        $this->exigerConnexion();
        SessionManager::detruire();
        $this->redirigerVers('connexion');
    }
}
