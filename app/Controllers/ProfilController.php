<?php
declare(strict_types=1);

namespace App\Controllers;

use Core\Controller;
use Core\CsrfManager;
use App\Models\UtilisateurModel;
use App\Models\FavoriModel;

/**
 * Contrôleur du profil utilisateur
 */
class ProfilController extends Controller
{
    public function executer(): void
    {
        $this->exigerConnexion();

        $utiId  = (int) $_SESSION['utilisateur_id'];
        $modele = new UtilisateurModel();
        $utilisateur = $modele->trouverParId($utiId);

        $favoriModele = new FavoriModel();
        $favoris      = $favoriModele->listerParUtilisateur($utiId);

        $this->afficherVue('profil', [
            'pageTitle'   => 'Mon Profil',
            'utilisateur' => $utilisateur,
            'favoris'     => $favoris,
            'csrf'        => CsrfManager::champ(),
        ]);
    }
}
