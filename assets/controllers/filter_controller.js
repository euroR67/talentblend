import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ["panel", "overlay"]

    open() {
        this.panelTarget.style.display = 'block';
        this.overlayTarget.classList.add('active');
    }

    close() {
        this.panelTarget.style.display = 'none';
        this.overlayTarget.classList.remove('active');
    }
}