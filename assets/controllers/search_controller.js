import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
  static targets = [ "modal", "overlay", "close" ]

  connect() {
    this.element.style.overflow = 'auto';
  }

  open() {
    this.modalTarget.style.display = 'flex';
    this.overlayTarget.classList.add('active-overlay');
    this.element.style.overflow = 'hidden';
  }

  close() {
    this.modalTarget.style.display = 'none';
    this.overlayTarget.classList.remove('active-overlay');
    this.element.style.overflow = 'auto';
  }
}