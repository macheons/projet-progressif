/**
 * Classe FetchForm - Soumission des formulaires via Fetch
 *
 * Intercepte la soumission d'un formulaire, envoie les données en POST
 * via Fetch, et gère l'affichage des erreurs et du succès.
 */
export class FetchForm {
    #form;
    #msgEl;
    #onSuccess;

    /**
     * @param {string}   formSelector  Sélecteur CSS du formulaire
     * @param {string}   msgSelector   Sélecteur de l'élément message global
     * @param {Function} onSuccess     Callback appelé en cas de succès avec la réponse JSON
     */
    constructor(formSelector, msgSelector, onSuccess = null) {
        this.#form    = document.querySelector(formSelector);
        this.#msgEl   = document.querySelector(msgSelector);
        this.#onSuccess = onSuccess;

        if (!this.#form) return;

        this.#form.addEventListener('submit', (e) => {
            e.preventDefault();
            this.#soumettre();
        });
    }

    async #soumettre() {
        const url     = this.#form.dataset.url ?? this.#form.action ?? window.location.href;
        const donnees = new FormData(this.#form);
        const btn     = this.#form.querySelector('[type="submit"]');

        if (btn) {
            btn.disabled = true;
            btn.textContent = 'Envoi…';
        }

        this.#effacerErreurs();
        this.#afficherMsg('', '');

        try {
            const reponse = await fetch(url, {
                method: 'POST',
                body: donnees,
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
            });

            const data = await reponse.json();

            if (data.succes) {
                this.#afficherMsg(data.message ?? 'Succès !', 'success');
                this.#form.reset();

                if (data.redirection) {
                    setTimeout(() => { window.location.href = data.redirection; }, 1200);
                }

                if (this.#onSuccess) this.#onSuccess(data);

            } else {
                this.#afficherMsg(data.message ?? 'Une erreur est survenue.', 'error');
                if (data.erreurs) this.#afficherErreurs(data.erreurs);
            }

            // Mettre à jour le jeton CSRF si renvoyé
            if (data.csrfToken) {
                const csrfInput = this.#form.querySelector('[name="csrf_token"]');
                if (csrfInput) csrfInput.value = data.csrfToken;
                window.CSRF_TOKEN = data.csrfToken;
            }

        } catch {
            this.#afficherMsg('Erreur de connexion. Réessayez.', 'error');
        } finally {
            if (btn) {
                btn.disabled = false;
                btn.textContent = btn.dataset.label ?? 'Envoyer';
            }
        }
    }

    #afficherMsg(texte, type) {
        if (!this.#msgEl) return;
        this.#msgEl.textContent = texte;
        this.#msgEl.className = 'form-message';
        if (type) this.#msgEl.classList.add(`form-message--${type}`);
    }

    #afficherErreurs(erreurs) {
        for (const [champ, message] of Object.entries(erreurs)) {
            const el = this.#form.querySelector(`#err-${champ}`);
            if (el) el.textContent = message;
            const input = this.#form.querySelector(`[name*="${champ}"]`);
            if (input) input.closest('.form-group')?.classList.add('form-group--error');
        }
    }

    #effacerErreurs() {
        this.#form.querySelectorAll('.form-error').forEach(el => { el.textContent = ''; });
        this.#form.querySelectorAll('.form-group--error').forEach(el => {
            el.classList.remove('form-group--error');
        });
    }
}
