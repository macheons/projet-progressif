
</main><!-- /.main-content -->

<footer class="site-footer">
    <div class="container site-footer__inner">
        <p class="site-footer__copy">
            &copy; <?= date('Y') ?> <strong><?= APP_NAME ?></strong>
            Projet Progressif  IFOSUP Wavre
        </p>
        <p class="site-footer__tech">
            Favicons via
            <a href="https://icon.horse" target="_blank" rel="noopener noreferrer">Icon Horse</a>
        </p>
    </div>
</footer>

<!-- Bibliothèque AOS-->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.min.js" defer></script>

<!-- Module JS principal -->
<script type="module" src="<?= BASE_URL ?>/assets/js/app.js"></script>
<!-- Bouton retour en haut-->
<button class="scroll-top" id="scroll-top" aria-label="Retour en haut" hidden>↑</button>
</body>
</html>
