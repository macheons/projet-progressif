/**
 * ApiQuote - Communication avec l'API DummyJSON
 *
 * Charge une citation aléatoire depuis https://dummyjson.com/quotes/random
 * et met à jour l'interface au clic sur "Nouvelle citation".
 */
export class ApiQuote {
    #textEl;
    #authorEl;
    #btn;

    constructor() {
        this.#textEl   = document.querySelector('#quote-text');
        this.#authorEl = document.querySelector('#quote-author');
        this.#btn      = document.querySelector('#quote-refresh');

        if (!this.#textEl) return;

        this.#charger();

        this.#btn?.addEventListener('click', () => this.#charger());
    }

    async #charger() {
        if (this.#btn) {
            this.#btn.disabled = true;
            this.#btn.textContent = '…';
        }

        this.#textEl.textContent = 'Chargement…';
        if (this.#authorEl) this.#authorEl.textContent = '';

        try {
            const res  = await fetch('https://dummyjson.com/quotes/random');
            const data = await res.json();

            this.#textEl.textContent   = `« ${data.quote} »`;
            if (this.#authorEl) this.#authorEl.textContent = `- ${data.author}`;

        } catch {
            this.#textEl.textContent = 'Impossible de charger une citation.';
        } finally {
            if (this.#btn) {
                this.#btn.disabled = false;
                this.#btn.textContent = '↻ Nouvelle citation';
            }
        }
    }
}
