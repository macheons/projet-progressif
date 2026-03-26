<?php
declare(strict_types=1);

namespace App\Controllers;

use Core\Controller;
use Core\CsrfManager;
use App\Models\FavoriModel;

/**
 * Contrôleur des favoris
 * Gère l'ajout et la suppression de domaines favoris.
 */
class FavoriController extends Controller
{
    public function executer(): void
    {
        $this->exigerConnexion();

        if (!$this->estRequeteAjax()) {
            $this->redirigerVers('profil');
        }

        // Validation CSRF
        $csrfSoumis = $_POST['csrf_token'] ?? $_GET['csrf_token'] ?? '';
        if (!CsrfManager::valider($csrfSoumis)) {
            $this->repondreJson(['succes' => false, 'message' => 'Jeton CSRF invalide.'], 403);
        }

        $action = $_POST['action'] ?? $_GET['action'] ?? '';
        $utiId  = (int) $_SESSION['utilisateur_id'];
        $modele = new FavoriModel();

        match ($action) {
            'ajouter'    => $this->ajouter($modele, $utiId),
            'supprimer'  => $this->supprimer($modele, $utiId),
            default      => $this->repondreJson(['succes' => false, 'message' => 'Action inconnue.'], 400),
        };
    }

    private function ajouter(FavoriModel $modele, int $utiId): never
    {
        $domaine = trim($_POST['domaine'] ?? '');

        if (empty($domaine)) {
            $this->repondreJson(['succes' => false, 'message' => 'Domaine requis.'], 422);
        }

        $ajout = $modele->ajouter($utiId, $domaine);
        $domainePropre = strtolower(preg_replace('#^https?://#', '', $domaine));

        if ($ajout) {
            CsrfManager::renouveler();
            $this->repondreJson([
                'succes'   => true,
                'message'  => "{$domainePropre} ajouté aux favoris.",
                'domaine'  => $domainePropre,
                'iconUrl'  => ICON_HORSE_BASE . $domainePropre,
                'csrfToken' => CsrfManager::obtenir(),
            ]);
        } else {
            $this->repondreJson(['succes' => false, 'message' => 'Déjà dans vos favoris.'], 409);
        }
    }

    private function supprimer(FavoriModel $modele, int $utiId): never
    {
        $favId = (int) ($_POST['fav_id'] ?? 0);

        if ($favId <= 0) {
            $this->repondreJson(['succes' => false, 'message' => 'ID invalide.'], 422);
        }

        $suppression = $modele->supprimer($favId, $utiId);

        if ($suppression) {
            CsrfManager::renouveler();
            $this->repondreJson([
                'succes'    => true,
                'message'   => 'Favori supprimé.',
                'csrfToken' => CsrfManager::obtenir(),
            ]);
        } else {
            $this->repondreJson(['succes' => false, 'message' => 'Suppression impossible.'], 404);
        }
    }
}
