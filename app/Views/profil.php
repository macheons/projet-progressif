<?php
/** @var array $utilisateur */
/** @var array $favoris */
$utilisateur ??= [];
$favoris     ??= [];
$e = fn(mixed $v): string => htmlspecialchars((string)$v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
?>

<section class="section">
    <div class="container">

        <!-- Carte profil -->
        <div class="profile-card">
            <div class="profile-card__avatar">
                <?= strtoupper(substr($utilisateur['uti_pseudo'] ?? '?', 0, 1)) ?>
            </div>
            <div class="profile-card__info">
                <h1 class="profile-card__name"><?= $e($utilisateur['uti_pseudo'] ?? '') ?></h1>
                <p class="profile-card__email">
                    <span aria-label="Adresse e-mail">✉</span>
                    <?= $e($utilisateur['uti_email'] ?? '') ?>
                </p>
                <?php if (!empty($utilisateur['uti_date_inscription'])): ?>
                <p class="profile-card__date">
                    Membre depuis le <?= date('d/m/Y', strtotime($utilisateur['uti_date_inscription'])) ?>
                </p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Favoris -->
        <div class="favorites-section">
            <div class="favorites-section__header">
                <h2 class="section__title">Mes favoris
                    <span class="badge-count"><?= count($favoris) ?></span>
                </h2>

                <!-- Ajout d'un favori via Fetch -->
                <form id="form-add-favori" class="add-favori-form">
                    <input
                        type="text"
                        id="new-favori-input"
                        name="domaine"
                        placeholder="ex. notion.so"
                        class="form-control form-control--inline"
                        aria-label="Nouveau favori"
                    >
                    <button type="submit" class="btn btn--primary btn--sm">Ajouter</button>
                    <p class="form-message" id="favori-msg" aria-live="polite"></p>
                </form>
            </div>

            <?php if (empty($favoris)): ?>
                <p class="empty-state">Vous n'avez pas encore de favoris. Ajoutez un domaine ci-dessus !</p>
            <?php else: ?>
                <div class="favicon-grid" id="favoris-grid">
                    <?php foreach ($favoris as $fav): ?>
                    <article
                        class="favicon-card"
                        data-fav-id="<?= (int)$fav['fav_id'] ?>"
                        data-domaine="<?= $e($fav['fav_domaine']) ?>"
                    >
                        <div class="favicon-card__img-wrap">
                            <img
                                src="https://icon.horse/icon/<?= $e($fav['fav_domaine']) ?>"
                                alt="Favicon de <?= $e($fav['fav_domaine']) ?>"
                                class="favicon-card__img"
                                loading="lazy"
                                width="64"
                                height="64"
                                onerror="this.src='<?= BASE_URL ?>/assets/img/fallback.svg'"
                            >
                        </div>
                        <span class="favicon-card__domain"><?= $e($fav['fav_domaine']) ?></span>
                        <button
                            class="favicon-card__remove btn--icon"
                            data-fav-id="<?= (int)$fav['fav_id'] ?>"
                            title="Supprimer ce favori"
                            aria-label="Supprimer <?= $e($fav['fav_domaine']) ?>"
                        >✕</button>
                    </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

    </div>
</section>

<script>
window.BASE_URL   = <?= json_encode(BASE_URL) ?>;
window.CSRF_TOKEN = <?= json_encode(\Core\CsrfManager::obtenir()) ?>;
</script>
