import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
  static targets = ['modal', 'overlay'];

  connect() {
      this.modal = this.modalTarget;
      this.overlay = this.overlayTarget;
  }

  open() {
      this.modal.classList.add('active');
      this.overlay.classList.add('active');
  }

  close() {
      this.modal.classList.remove('active');
      this.overlay.classList.remove('active');
  }
}