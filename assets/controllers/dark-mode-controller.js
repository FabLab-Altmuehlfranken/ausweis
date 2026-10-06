import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    toggle() {
        const isDarkModeEnabled = document.documentElement.classList.toggle('dark-mode');

        document.cookie = "dark_mode=" + (isDarkModeEnabled ? "1" : "0") + "; path=/; max-age=31536000; SameSite=Lax";
    }
}
