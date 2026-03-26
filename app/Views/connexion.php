<?php
$erreurs        ??= [];
$ancienneValeur ??= [];
$e = fn(mixed $v): string => htmlspecialchars((string)$v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
?>

<section class="section">
    <div class="container container--narrow">
        <h1 class="page-title">Se connecter</h1>

        <?php if (!empty($erreurs['global'])): ?>
        <div class="alert alert--danger" role="alert">
            <?= $e($erreurs['global']) ?>
        </div>
        <?php endif; ?>

        <form id="form-connexion" class="form" novalidate
              data-url="<?= BASE_URL ?>/?page=connexion">
            <?= $csrf ?>

            <!-- Pseudo -->
            <div class="form-group<?= !empty($erreurs['pseudo']) ? ' form-group--error' : '' ?>">
                <label for="connexion_pseudo" class="form-label">Pseudo <span aria-hidden="true">*</span></label>
                <input
                    type="text"
                    id="connexion_pseudo"
                    name="connexion_pseudo"
                    class="form-control"
                    value="<?= $e($ancienneValeur['pseudo'] ?? '') ?>"
                    required
                    minlength="2"
                    maxlength="255"
                    autocomplete="username"
                    aria-describedby="err-pseudo"
                >
                <p class="form-error" id="err-pseudo" aria-live="polite">
                    <?= $e($erreurs['pseudo'] ?? '') ?>
                </p>
            </div>

            <!-- Mot de passe -->
            <div class="form-group<?= !empty($erreurs['motdepasse']) ? ' form-group--error' : '' ?>">
                <label for="connexion_motDePasse" class="form-label">Mot de passe <span aria-hidden="true">*</span></label>
                <input
                    type="password"
                    id="connexion_motDePasse"
                    name="connexion_motDePasse"
                    class="form-control"
                    required
                    minlength="8"
                    maxlength="72"
                    autocomplete="current-password"
                    aria-describedby="err-motdepasse"
                >
                <p class="form-error" id="err-motdepasse" aria-live="polite">
                    <?= $e($erreurs['motdepasse'] ?? '') ?>
                </p>
            </div>

            <button type="submit" class="btn btn--primary btn--block" id="btn-connexion">
                Se connecter
            </button>

            <p class="form-message" id="form-connexion-msg" aria-live="polite"></p>
        </form>

        <p class="form-footer-link">
            Pas encore de compte ?
            <a href="<?= BASE_URL ?>/?page=inscription">S'inscrire</a>
        </p>
    </div>
</section>

<script>window.BASE_URL = <?= json_encode(BASE_URL) ?>;</script>
