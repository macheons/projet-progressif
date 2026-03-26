/**
 * app.js - Point d'entrée JavaScript
 *
 * Importe et instancie toutes les classes JS.
 * AOS (Animate On Scroll) est la bibliothèque tierce utilisée.
 */

import { HamburgerMenu  } from './HamburgerMenu.js';
import { FaviconSearch  } from './FaviconSearch.js';
import { CharCounter    } from './CharCounter.js';
import { ScrollTopButton} from './ScrollTopButton.js';
import { FetchForm      } from './FetchForm.js';
import { ApiQuote       } from './ApiQuote.js';
import { FavoriManager  } from './FavoriManager.js';

//Initialisation au chargement
document.addEventListener('DOMContentLoaded', () => {

    const BASE_URL   = window.BASE_URL   ?? '';
    const CSRF_TOKEN = window.CSRF_TOKEN ?? '';

    //Classes
    new HamburgerMenu('#hamburger-btn', '#main-nav');
    new FaviconSearch(BASE_URL);
    new CharCounter('#message', '#char-count', 3000);
    new ScrollTopButton('#scroll-top', 400);

    //Soumission des formulaires via Fetch
    new FetchForm('#form-inscription', '#form-inscription-msg');
    new FetchForm('#form-connexion',   '#form-connexion-msg', (data) => {
        if (data.redirection) window.location.href = data.redirection;
    });
    new FetchForm('#form-contact', '#form-contact-msg');

    //API interne : favoris
    new FavoriManager(BASE_URL, CSRF_TOKEN);

    //API externe : DummyJSON
    new ApiQuote();

    //Bibliothèque AOS - Animate On Scroll
    // AOS est chargé via CDN dans le footer
    if (typeof AOS !== 'undefined') {
        AOS.init({
            duration:  600,
            once:      true,
            easing:    'ease-out-cubic',
            offset:    60,
        });

        // Ajouter les attributs data-aos aux cartes après init
        document.querySelectorAll('.favicon-card').forEach((card, i) => {
            card.dataset.aos      = 'fade-up';
            card.dataset.aosDelay = String(Math.min(i * 40, 400));
        });

        AOS.refresh();
    }
});
