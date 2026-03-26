<?php
$erreurs        ??= [];
$ancienneValeur ??= [];
$e = fn(mixed $v): string => htmlspecialchars((string)$v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
?>

<section class="section">
    <div class="container container--narrow">
        <h1 class="page-title">Créer un compte</h1>

        <!-- Message global d'erreur -->
        <?php if (!empty($erreurs['global'])): ?>
        <div class="alert alert--danger" role="alert">
            <?= $e($erreurs['global']) ?>
        </div>
        <?php endif; ?>

        <form id="form-inscription" class="form" novalidate
              data-url="<?= BASE_URL ?>/?page=inscription">
            <?= $csrf ?>

            <!-- Pseudo -->
            <div class="form-group<?= !empty($erreurs['pseudo']) ? ' form-group--error' : '' ?>">
                <label for="inscription_pseudo" class="form-label">Pseudo <span aria-hidden="true">*</span></label>
                <input
                    type="text"
                    id="inscription_pseudo"
                    name="inscription_pseudo"
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

            <!-- Email -->
            <div class="form-group<?= !empty($erreurs['email']) ? ' form-group--error' : '' ?>">
                <label for="inscription_email" class="form-label">Adresse e-mail <span aria-hidden="true">*</span></label>
                <input
                    type="email"
                    id="inscription_email"
                    name="inscription_email"
                    class="form-control"
                    value="<?= $e($ancienneValeur['email'] ?? '') ?>"
                    required
                    autocomplete="email"
                    aria-describedby="err-email"
                >
                <p class="form-error" id="err-email" aria-live="polite">
                    <?= $e($erreurs['email'] ?? '') ?>
                </p>
            </div>

            <!-- Mot de passe -->
            <div class="form-group<?= !empty($erreurs['motdepasse']) ? ' form-group--error' : '' ?>">
                <label for="inscription_motDePasse" class="form-label">Mot de passe <span aria-hidden="true">*</span></label>
                <input
                    type="password"
                    id="inscription_motDePasse"
                    name="inscription_motDePasse"
                    class="form-control"
                    required
                    minlength="8"
                    maxlength="72"
                    autocomplete="new-password"
                    aria-describedby="err-motdepasse"
                >
                <p class="form-error" id="err-motdepasse" aria-live="polite">
                    <?= $e($erreurs['motdepasse'] ?? '') ?>
                </p>
            </div>

            <!-- Confirmation mot de passe -->
            <div class="form-group<?= !empty($erreurs['confirmation']) ? ' form-group--error' : '' ?>">
                <label for="inscription_motDePasse_confirmation" class="form-label">Confirmer le mot de passe <span aria-hidden="true">*</span></label>
                <input
                    type="password"
                    id="inscription_motDePasse_confirmation"
                    name="inscription_motDePasse_confirmation"
                    class="form-control"
                    required
                    minlength="8"
                    maxlength="72"
                    autocomplete="new-password"
                    aria-describedby="err-confirmation"
                >
                <p class="form-error" id="err-confirmation" aria-live="polite">
                    <?= $e($erreurs['confirmation'] ?? '') ?>
                </p>
            </div>

            <button type="submit" class="btn btn--primary btn--block" id="btn-inscription">
                Créer mon compte
            </button>

            <!-- Message global de résultat -->
            <p class="form-message" id="form-inscription-msg" aria-live="polite"></p>
        </form>

        <p class="form-footer-link">
            Déjà un compte ?
            <a href="<?= BASE_URL ?>/?page=connexion">Se connecter</a>
        </p>
    </div>
</section>

<script>window.BASE_URL = <?= json_encode(BASE_URL) ?>;</script>
