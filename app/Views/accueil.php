<?php
/** @var array $sitesPopulaires */
$sitesPopulaires ??= [];
?>

<!-- Hero -->
<section class="hero">
    <div class="container">
        <h1 class="hero__title">
            <span class="hero__icon">⬡</span>
            Explorez les favicons du web
        </h1>
        <p class="hero__subtitle">
            Entrez n'importe quel nom de domaine et récupérez instantanément sa favicon,
            avec fallback automatique via <strong>Icon Horse</strong>.
        </p>

        <!-- Barre de recherche-->
        <div class="search-bar" id="favicon-search">
            <input
                type="text"
                id="search-input"
                class="search-bar__input"
                placeholder="ex. github.com ou https://openai.com"
                autocomplete="off"
                aria-label="Domaine à rechercher"
            >
            <button id="search-btn" class="btn btn--primary">
                <span>Rechercher</span>
            </button>
        </div>

        <!-- Résultat de recherche -->
        <div id="search-result" class="search-result" hidden aria-live="polite"></div>
    </div>
</section>

<!-- Grille de sites populaires -->
<section class="section">
    <div class="container">
        <h2 class="section__title">Sites populaires</h2>
        <p class="section__subtitle">Cliquez sur un site pour voir sa favicon en détail.</p>

        <div class="favicon-grid" id="favicon-grid">
            <?php foreach ($sitesPopulaires as $domaine => $nom): ?>
            <article class="favicon-card" data-domaine="<?= htmlspecialchars($domaine, ENT_QUOTES, 'UTF-8') ?>">
                <div class="favicon-card__img-wrap">
                    <img
                        src="https://icon.horse/icon/<?= htmlspecialchars($domaine, ENT_QUOTES, 'UTF-8') ?>"
                        alt="Favicon de <?= htmlspecialchars($nom, ENT_QUOTES, 'UTF-8') ?>"
                        class="favicon-card__img"
                        loading="lazy"
                        width="64"
                        height="64"
                        onerror="this.src='<?= BASE_URL ?>/assets/img/fallback.svg'"
                    >
                </div>
                <span class="favicon-card__label"><?= htmlspecialchars($nom, ENT_QUOTES, 'UTF-8') ?></span>
                <span class="favicon-card__domain"><?= htmlspecialchars($domaine, ENT_QUOTES, 'UTF-8') ?></span>

                <?php if (!empty($_SESSION['utilisateur_id'])): ?>
                <button
                    class="favicon-card__fav btn--icon"
                    data-domaine="<?= htmlspecialchars($domaine, ENT_QUOTES, 'UTF-8') ?>"
                    title="Ajouter aux favoris"
                    aria-label="Ajouter <?= htmlspecialchars($nom, ENT_QUOTES, 'UTF-8') ?> aux favoris"
                >☆</button>
                <?php endif; ?>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Section API externe : DummyJSON - citation aléatoire -->
<section class="section section--alt">
    <div class="container">
        <h2 class="section__title">Le saviez-vous ?</h2>
        <p class="section__subtitle">Citation chargée depuis l'API <strong>DummyJSON</strong>.</p>

        <div class="quote-card" id="quote-card">
            <p class="quote-card__text" id="quote-text">Chargement…</p>
            <cite class="quote-card__author" id="quote-author"></cite>
            <button id="quote-refresh" class="btn btn--ghost btn--sm">↻ Nouvelle citation</button>
        </div>
    </div>
</section>

<!-- Données pour le JS -->
<script>
window.BASE_URL   = <?= json_encode(BASE_URL) ?>;
window.CSRF_TOKEN = <?= json_encode(\Core\CsrfManager::obtenir()) ?>;
window.EST_CONNECTE = <?= json_encode(!empty($_SESSION['utilisateur_id'])) ?>;
</script>
