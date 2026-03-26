/**
 * Classe CharCounter - Compteur de caractères
 *
 * Met à jour un compteur en temps réel.
 * Change de couleur si on approche ou dépasse la limite.
 */
export class CharCounter {
    #textarea;
    #counter;
    #max;

    /**
     * @param {string} textareaSelector  Sélecteur du textarea
     * @param {string} counterSelector   Sélecteur du conteneur du compteur
     * @param {number} max               Limite maximale de caractères
     */
    constructor(textareaSelector, counterSelector, max) {
        this.#textarea = document.querySelector(textareaSelector);
        this.#counter  = document.querySelector(counterSelector);
        this.#max      = max;

        if (!this.#textarea || !this.#counter) return;

        this.#textarea.addEventListener('input', () => this.#mettreAJour());
        this.#mettreAJour(); // État initial
    }

    #mettreAJour() {
        const longueur = this.#textarea.value.length;
        this.#counter.textContent = `${longueur} / ${this.#max}`;

        // Classes de couleur progressives
        this.#counter.classList.remove('char-count--warn', 'char-count--max');
        if (longueur >= this.#max) {
            this.#counter.classList.add('char-count--max');
        } else if (longueur >= this.#max * 0.85) {
            this.#counter.classList.add('char-count--warn');
        }
    }
}
