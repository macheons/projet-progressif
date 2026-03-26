/**
 * Classe ScrollTopButton - Bouton retour en haut
 *
 * Affiche un bouton flottant après un scroll suffisant
 * et remonte la page en douceur au clic.
 */
export class ScrollTopButton {
    #btn;
    #seuilAffichage;

    /**
     * @param {string} btnSelector      Sélecteur du bouton
     * @param {number} seuilAffichage   Scroll en px avant apparition (défaut : 400)
     */
    constructor(btnSelector = '#scroll-top', seuilAffichage = 400) {
        this.#btn = document.querySelector(btnSelector);
        this.#seuilAffichage = seuilAffichage;

        if (!this.#btn) return;

        window.addEventListener('scroll', () => this.#gererVisibilite(), { passive: true });
        this.#btn.addEventListener('click', () => this.#remonter());
    }

    #gererVisibilite() {
        const visible = window.scrollY > this.#seuilAffichage;
        this.#btn.hidden = !visible;
    }

    #remonter() {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
}
