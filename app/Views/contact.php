<?php
$erreurs        ??= [];
$ancienneValeur ??= [];
$messageGlobal  ??= '';
$e = fn(mixed $v): string => htmlspecialchars((string)$v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
?>

<section class="section">
    <div class="container container--narrow">
        <h1 class="page-title">Nous contacter</h1>

        <?php if ($messageGlobal): ?>
        <div class="alert alert--success" role="alert"><?= $e($messageGlobal) ?></div>
        <?php endif; ?>

        <?php if (!empty($erreurs['global'])): ?>
        <div class="alert alert--danger" role="alert"><?= $e($erreurs['global']) ?></div>
        <?php endif; ?>

        <form id="form-contact" class="form" novalidate
              data-url="<?= BASE_URL ?>/?page=contact">
            <?= $csrf ?? '' ?>

            <!-- Nom -->
            <div class="form-group<?= !empty($erreurs['nom']) ? ' form-group--error' : '' ?>">
                <label for="nom" class="form-label">Nom <span aria-hidden="true">*</span></label>
                <input type="text" id="nom" name="nom" class="form-control"
                    value="<?= $e($ancienneValeur['nom'] ?? '') ?>"
                    required minlength="2" maxlength="255"
                    aria-describedby="err-nom">
                <p class="form-error" id="err-nom" aria-live="polite"><?= $e($erreurs['nom'] ?? '') ?></p>
            </div>

            <!-- Prénom -->
            <div class="form-group<?= !empty($erreurs['prenom']) ? ' form-group--error' : '' ?>">
                <label for="prenom" class="form-label">Prénom</label>
                <input type="text" id="prenom" name="prenom" class="form-control"
                    value="<?= $e($ancienneValeur['prenom'] ?? '') ?>"
                    minlength="2" maxlength="255"
                    aria-describedby="err-prenom">
                <p class="form-error" id="err-prenom" aria-live="polite"><?= $e($erreurs['prenom'] ?? '') ?></p>
            </div>

            <!-- Email -->
            <div class="form-group<?= !empty($erreurs['email']) ? ' form-group--error' : '' ?>">
                <label for="email" class="form-label">E-mail <span aria-hidden="true">*</span></label>
                <input type="email" id="email" name="email" class="form-control"
                    value="<?= $e($ancienneValeur['email'] ?? '') ?>"
                    required aria-describedby="err-email">
                <p class="form-error" id="err-email" aria-live="polite"><?= $e($erreurs['email'] ?? '') ?></p>
            </div>

            <!-- Message -->
            <div class="form-group<?= !empty($erreurs['message']) ? ' form-group--error' : '' ?>">
                <label for="message" class="form-label">Message <span aria-hidden="true">*</span></label>
                <textarea id="message" name="message" class="form-control form-control--textarea"
                    required minlength="10" maxlength="3000"
                    aria-describedby="err-message char-count"
                ><?= $e($ancienneValeur['message'] ?? '') ?></textarea>
                <!-- Compteur de caractères -->
                <p class="char-count" id="char-count" aria-live="polite">0 / 3000</p>
                <p class="form-error" id="err-message" aria-live="polite"><?= $e($erreurs['message'] ?? '') ?></p>
            </div>

            <button type="submit" class="btn btn--primary btn--block">Envoyer</button>
            <p class="form-message" id="form-contact-msg" aria-live="polite"></p>
        </form>
    </div>
</section>

<script>window.BASE_URL = <?= json_encode(BASE_URL) ?>;</script>
