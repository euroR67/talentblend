import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ["info", "emploi", "infoBtn", "emploiBtn"]

    connect() {
        this.infoTarget.style.display = 'flex';
        this.emploiTarget.style.display = 'none';
    }

    showInfo() {
        this.infoBtnTarget.classList.add('active');
        this.emploiBtnTarget.classList.remove('active');
        this.infoTarget.style.display = 'flex';
        this.emploiTarget.style.display = 'none';
    }

    showEmploi() {
        this.emploiBtnTarget.classList.add('active');
        this.infoBtnTarget.classList.remove('active');
        this.emploiTarget.style.display = 'flex';
        this.infoTarget.style.display = 'none';
    }
}