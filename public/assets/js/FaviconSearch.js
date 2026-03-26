/**
 * Classe FaviconSearch
 *
 * Gère la recherche de favicon en interrogeant le contrôleur PHP en AJAX
 * et affiche le résultat avec l'image Icon Horse + lien vers le favori.
 */
export class FaviconSearch {
    #input;
    #btn;
    #result;
    #baseUrl;

    /**
     * @param {string} baseUrl  BASE_URL défini côté PHP
     */
    constructor(baseUrl = '') {
        this.#input  = document.querySelector('#search-input');
        this.#btn    = document.querySelector('#search-btn');
        this.#result = document.querySelector('#search-result');
        this.#baseUrl = baseUrl;

        if (!this.#input || !this.#btn || !this.#result) return;

        this.#btn.addEventListener('click', () => this.#lancer());
        this.#input.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') this.#lancer();
        });

        // Clic sur une carte de la grille → pré-remplir le champ
        document.querySelector('#favicon-grid')?.addEventListener('click', (e) => {
            const card = e.target.closest('.favicon-card');
            if (card) {
                const domaine = card.dataset.domaine;
                if (domaine) {
                    this.#input.value = domaine;
                    this.#lancer();
                    this.#input.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        });
    }

    async #lancer() {
        const valeur = this.#input.value.trim();
        if (!valeur) {
            this.#afficherErreur('Veuillez entrer un domaine.');
            return;
        }

        this.#result.hidden = false;
        this.#result.innerHTML = '<p style="color:var(--clr-text-muted)">Chargement…</p>';

        // Nettoyage du domaine côté client aussi
        const domaine = valeur.replace(/^https?:\/\//, '').replace(/\/$/, '');

        try {
            const res = await fetch(
                `${this.#baseUrl}/?page=accueil&favicon=${encodeURIComponent(domaine)}`,
                { headers: { 'X-Requested-With': 'XMLHttpRequest' } }
            );
            const data = await res.json();

            if (!res.ok || data.erreur) {
                this.#afficherErreur(data.erreur ?? 'Erreur serveur.');
                return;
            }

            this.#afficherResultat(data);

        } catch {
            this.#afficherErreur('Impossible de contacter le serveur.');
        }
    }

    #afficherResultat({ domaine, url }) {
        const estConnecte = window.EST_CONNECTE ?? false;

        this.#result.innerHTML = `
            <img
                src="${url}"
                alt="Favicon de ${this.#esc(domaine)}"
                class="search-result__img fade-in"
                onerror="this.src='${this.#baseUrl}/assets/img/fallback.svg'"
                width="96" height="96"
            >
            <p class="search-result__domain">${this.#esc(domaine)}</p>
            <a href="https://${this.#esc(domaine)}" target="_blank" rel="noopener noreferrer" class="btn btn--ghost btn--sm">
                Visiter le site ↗
            </a>
            ${estConnecte ? `<button class="btn btn--primary btn--sm" id="add-from-search" data-domaine="${this.#esc(domaine)}">☆ Ajouter aux favoris</button>` : ''}
        `;

        document.querySelector('#add-from-search')?.addEventListener('click', (e) => {
            window.dispatchEvent(new CustomEvent('favori:ajouter', { detail: { domaine: e.target.dataset.domaine } }));
        });
    }

    #afficherErreur(msg) {
        this.#result.innerHTML = `<p class="form-message form-message--error">${this.#esc(msg)}</p>`;
    }

    /** Échappe le HTML pour l'injection dans le DOM */
    #esc(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }
}
