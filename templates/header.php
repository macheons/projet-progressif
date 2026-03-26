<?php
declare(strict_types=1);

use Core\CsrfManager;

//Menu actif dynamique
function genererMenu(array $liens): string
{
    $pageActuelle = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
    $params       = [];
    parse_str(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_QUERY) ?? '', $params);
    $pageParam    = $params['page'] ?? 'accueil';

    ob_start();
    foreach ($liens as $lien):
        $estActif = ($lien['page'] === $pageParam);
        ?>
        <li class="nav__item<?= $estActif ? ' nav__item--active' : '' ?>">
            <a href="<?= htmlspecialchars($lien['url'], ENT_QUOTES, 'UTF-8') ?>"
               class="nav__link<?= $estActif ? ' nav__link--active' : '' ?>"
               <?= $estActif ? 'aria-current="page"' : '' ?>>
                <span class="nav__icon"><?= $lien['icone'] ?></span>
                <?= htmlspecialchars($lien['label'], ENT_QUOTES, 'UTF-8') ?>
            </a>
        </li>
    <?php
    endforeach;
    return ob_get_clean();
}

$connecte = !empty($_SESSION['utilisateur_id']);
$pseudo   = $connecte ? htmlspecialchars($_SESSION['utilisateur_pseudo'] ?? '', ENT_QUOTES, 'UTF-8') : '';

$liensMenu = [
    ['label' => 'Accueil',    'page' => 'accueil',    'icone' => '⌂', 'url' => BASE_URL . '/?page=accueil'],
    ['label' => 'Contact',    'page' => 'contact',    'icone' => '✉', 'url' => BASE_URL . '/?page=contact'],
];

if (!$connecte) {
    $liensMenu[] = ['label' => 'Connexion',    'page' => 'connexion',    'icone' => '→', 'url' => BASE_URL . '/?page=connexion'];
    $liensMenu[] = ['label' => 'Inscription',  'page' => 'inscription',  'icone' => '+', 'url' => BASE_URL . '/?page=inscription'];
} else {
    $liensMenu[] = ['label' => 'Mon profil',   'page' => 'profil',       'icone' => '◎', 'url' => BASE_URL . '/?page=profil'];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= htmlspecialchars($metaDescription ?? 'FaviconHub - explorez les favicons de tous les sites web.', ENT_QUOTES, 'UTF-8') ?>">
    <title><?= htmlspecialchars(($pageTitle ?? 'Accueil') . ' · ' . APP_NAME, ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
    <link rel="icon" href="<?= BASE_URL ?>/assets/img/favicon.svg" type="image/svg+xml">
</head>
<body>

<header class="site-header">
    <div class="container site-header__inner">
        <a href="<?= BASE_URL ?>/?page=accueil" class="site-header__logo">
            <span class="logo-icon">⬡</span>
            <span class="logo-text"><?= APP_NAME ?></span>
        </a>

        <!-- Navigation desktop + hamburger-->
        <nav class="nav" id="main-nav" aria-label="Navigation principale">
            <ul class="nav__list">
                <?= genererMenu($liensMenu) ?>
            </ul>
        </nav>

        <div class="site-header__actions">
            <?php if ($connecte): ?>
                <span class="header-user">Bonjour, <strong><?= $pseudo ?></strong></span>
                <form action="<?= BASE_URL ?>/?page=deconnexion" method="POST" class="logout-form">
                    <?= CsrfManager::champ() ?>
                    <button type="submit" class="btn btn--ghost btn--sm">Déconnexion</button>
                </form>
            <?php endif; ?>

            <!-- Bouton hamburger - contrôlé par la classe JS HamburgerMenu -->
            <button class="hamburger" id="hamburger-btn" aria-label="Ouvrir le menu" aria-expanded="false" aria-controls="main-nav">
                <span class="hamburger__bar"></span>
                <span class="hamburger__bar"></span>
                <span class="hamburger__bar"></span>
            </button>
        </div>
    </div>
</header>

<main class="main-content" id="main-content">
