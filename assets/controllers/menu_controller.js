import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ["bars", "slideMenu", "close", "overlay", "pseudo", "dashSubmenu", "categorie", "subMenu"]

    connect() {
        // Vérifiez si les cibles existent avant d'ajouter des classes
        if (this.hasSlideMenuTarget && this.hasOverlayTarget) {
            this.slideMenuTarget.classList.remove('slide-menu-active');
            this.overlayTarget.classList.remove('active-overlay');
        }

        if (this.hasDashSubmenuTarget) {
            this.dashSubmenuTarget.classList.remove('dash-submenu-active');
        }

        if (this.hasSubMenuTarget) {
            this.subMenuTarget.classList.remove('sub-menu-active');
        }
    }

    openMenu() {
        this.slideMenuTarget.classList.add('slide-menu-active');
        this.overlayTarget.classList.add('active-overlay');
    }

    closeMenu() {
        this.slideMenuTarget.classList.remove('slide-menu-active');
        this.overlayTarget.classList.remove('active-overlay');
    }

    toggleDashSubmenu() {
        this.dashSubmenuTarget.classList.toggle('dash-submenu-active');
    }

    toggleSubMenu() {
        this.subMenuTarget.classList.toggle('sub-menu-active');
    }
}