/**
 * Classe HamburgerMenu - Fonctionnalité côté client
 *
 * Gère l'ouverture/fermeture du menu mobile avec accessibilité.
 * Fermeture au clic en dehors, à l'appui sur Escape, et au resize.
 */
export class HamburgerMenu {
    #btn;
    #nav;
    #isOpen = false;

    /**
     * @param {string} btnSelector  Sélecteur du bouton hamburger
     * @param {string} navSelector  Sélecteur de l'élément <nav>
     */
    constructor(btnSelector = '#hamburger-btn', navSelector = '#main-nav') {
        this.#btn = document.querySelector(btnSelector);
        this.#nav = document.querySelector(navSelector);

        if (!this.#btn || !this.#nav) return;

        this.#btn.addEventListener('click', () => this.#toggle());
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.#isOpen) this.#fermer();
        });
        document.addEventListener('click', (e) => {
            if (this.#isOpen && !this.#nav.contains(e.target) && !this.#btn.contains(e.target)) {
                this.#fermer();
            }
        });
        window.addEventListener('resize', () => {
            if (window.innerWidth > 768 && this.#isOpen) this.#fermer();
        });

        // Fermer au clic sur un lien du menu
        this.#nav.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => this.#fermer());
        });
    }

    #toggle() {
        this.#isOpen ? this.#fermer() : this.#ouvrir();
    }

    #ouvrir() {
        this.#isOpen = true;
        this.#nav.classList.add('nav--open');
        this.#btn.setAttribute('aria-expanded', 'true');
        this.#btn.setAttribute('aria-label', 'Fermer le menu');
    }

    #fermer() {
        this.#isOpen = false;
        this.#nav.classList.remove('nav--open');
        this.#btn.setAttribute('aria-expanded', 'false');
        this.#btn.setAttribute('aria-label', 'Ouvrir le menu');
    }
}
