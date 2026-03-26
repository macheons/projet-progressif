/**
 * FavoriManager - Gestion AJAX des favoris
 *
 * Ajoute et supprime des favoris via Fetch vers le FavoriController PHP.
 * Met à jour la grille sans rechargement de page.
 */
export class FavoriManager {
    #baseUrl;
    #csrf;
    #grid;
    #msgEl;

    constructor(baseUrl = '', csrf = '') {
        this.#baseUrl = baseUrl;
        this.#csrf    = csrf;
        this.#grid    = document.querySelector('#favoris-grid');
        this.#msgEl   = document.querySelector('#favori-msg');

        // Formulaire d'ajout (page profil)
        const formAjout = document.querySelector('#form-add-favori');
        formAjout?.addEventListener('submit', (e) => {
            e.preventDefault();
            const input = document.querySelector('#new-favori-input');
            if (input?.value.trim()) this.#ajouter(input.value.trim(), input);
        });

        // Suppression depuis la grille profil
        this.#grid?.addEventListener('click', (e) => {
            const btn = e.target.closest('.favicon-card__remove');
            if (btn) this.#supprimer(parseInt(btn.dataset.favId), btn.closest('.favicon-card'));
        });

        // Ajout depuis la page d'accueil (bouton ou événement FaviconSearch)
        document.querySelector('#favicon-grid')?.addEventListener('click', (e) => {
            const btn = e.target.closest('.favicon-card__fav');
            if (btn) this.#ajouterSimple(btn.dataset.domaine, btn);
        });

        window.addEventListener('favori:ajouter', (e) => {
            this.#ajouterSimple(e.detail.domaine, null);
        });
    }

    // Met à jour le CSRF depuis une réponse serveur
    #majCsrf(newToken) {
        if (newToken) {
            this.#csrf = newToken;
            window.CSRF_TOKEN = newToken;
        }
    }

    async #ajouter(domaine, inputEl) {
        const formData = new FormData();
        formData.append('action',     'ajouter');
        formData.append('domaine',    domaine);
        formData.append('csrf_token', this.#csrf);

        try {
            const res  = await fetch(`${this.#baseUrl}/?page=favori`, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
            });
            const data = await res.json();

            this.#afficherMsg(data.message ?? '', data.succes ? 'success' : 'error');
            this.#majCsrf(data.csrfToken);

            if (data.succes) {
                if (inputEl) inputEl.value = '';
                this.#ajouterCarte(data);
            }

        } catch {
            this.#afficherMsg('Erreur réseau.', 'error');
        }
    }

    async #ajouterSimple(domaine, btnEl) {
        if (btnEl) {
            btnEl.textContent = '★';
            btnEl.disabled = true;
        }

        const formData = new FormData();
        formData.append('action',     'ajouter');
        formData.append('domaine',    domaine);
        formData.append('csrf_token', window.CSRF_TOKEN ?? this.#csrf);

        try {
            const res  = await fetch(`${this.#baseUrl}/?page=favori`, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
            });
            const data = await res.json();
            this.#majCsrf(data.csrfToken);

            if (!data.succes && btnEl) {
                btnEl.textContent = '☆';
                btnEl.disabled = false;
            }
        } catch {
            if (btnEl) {
                btnEl.textContent = '☆';
                btnEl.disabled = false;
            }
        }
    }

    async #supprimer(favId, cardEl) {
        if (!favId) return;

        const formData = new FormData();
        formData.append('action',     'supprimer');
        formData.append('fav_id',     favId);
        formData.append('csrf_token', this.#csrf);

        if (cardEl) cardEl.style.opacity = '.4';

        try {
            const res  = await fetch(`${this.#baseUrl}/?page=favori`, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
            });
            const data = await res.json();
            this.#majCsrf(data.csrfToken);

            if (data.succes) {
                cardEl?.remove();
                this.#majCompteur(-1);
            } else {
                if (cardEl) cardEl.style.opacity = '1';
                this.#afficherMsg(data.message ?? 'Erreur.', 'error');
            }

        } catch {
            if (cardEl) cardEl.style.opacity = '1';
            this.#afficherMsg('Erreur réseau.', 'error');
        }
    }

    #ajouterCarte({ domaine, iconUrl }) {
        if (!this.#grid) return;

        const article = document.createElement('article');
        article.className = 'favicon-card fade-in';
        article.dataset.domaine = domaine;
        article.innerHTML = `
            <div class="favicon-card__img-wrap">
                <img src="${iconUrl}"
                     alt="Favicon de ${domaine}"
                     class="favicon-card__img"
                     width="64" height="64"
                     onerror="this.src='${this.#baseUrl}/assets/img/fallback.svg'">
            </div>
            <span class="favicon-card__domain">${domaine}</span>
            <button class="favicon-card__remove btn--icon"
                    data-fav-id="0"
                    title="Supprimer" aria-label="Supprimer ${domaine}">✕</button>
        `;
        this.#grid.prepend(article);
        this.#majCompteur(1);
    }

    #majCompteur(delta) {
        const badge = document.querySelector('.badge-count');
        if (badge) badge.textContent = Math.max(0, parseInt(badge.textContent || '0') + delta);
    }

    #afficherMsg(texte, type = '') {
        if (!this.#msgEl) return;
        this.#msgEl.textContent = texte;
        this.#msgEl.className = 'form-message';
        if (type) this.#msgEl.classList.add(`form-message--${type}`);
        setTimeout(() => { this.#msgEl.textContent = ''; this.#msgEl.className = 'form-message'; }, 4000);
    }
}
