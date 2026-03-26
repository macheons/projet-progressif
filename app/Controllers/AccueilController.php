<?php
declare(strict_types=1);

namespace App\Controllers;

use Core\Controller;

/**
 * Contrôleur de la page d'accueil
 * Gère l'affichage et la communication avec l'API DummyJSON (citation aléatoire).
 */
class AccueilController extends Controller
{
    /** Sites populaires affichés dans la grille de la homepage */
    private const SITES_POPULAIRES = [
        'google.com'    => 'Google',
        'github.com'    => 'GitHub',
        'stackoverflow.com' => 'Stack Overflow',
        'youtube.com'   => 'YouTube',
        'mdn.dev'       => 'MDN',
        'laravel.com'   => 'Laravel',
        'php.net'       => 'PHP',
        'mysql.com'     => 'MySQL',
        'npmjs.com'     => 'npm',
        'tailwindcss.com' => 'Tailwind',
        'vitejs.dev'    => 'Vite',
		'cours.cvmdev.be' => 'CVMDev',
        'react.dev'     => 'React',
    ];

    public function executer(): void
    {
        // Requête AJAX retourner la favicon d'un domaine via Icon Horse
        if ($this->estRequeteAjax() && isset($_GET['favicon'])) {
            $domaine = preg_replace('#^https?://#', '', trim($_GET['favicon']));
            $domaine = rtrim($domaine, '/');

            if (empty($domaine) || strlen($domaine) > 253) {
                $this->repondreJson(['erreur' => 'Domaine invalide.'], 422);
            }

            $this->repondreJson([
                'domaine' => $domaine,
                'url'     => ICON_HORSE_BASE . $domaine,
            ]);
        }

        $this->afficherVue('accueil', [
            'pageTitle'       => 'Accueil',
            'sitesPopulaires' => self::SITES_POPULAIRES,
        ]);
    }
}
