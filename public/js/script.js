// ============================ Slide menu  ============================ //

// Récupération des element du DOM pour le menu slide responsive
const bars = document.querySelector('.fa-bars');
const slideMenu = document.querySelector('.slide-menu');
const close = document.querySelector('.fa-xmark');
const overlay = document.querySelector('.overlay');

// Ecouteur d'evenement pour ouvrir le menu
bars.addEventListener('click', () => {
    // On ajoute la classe active sur le slide menu
    slideMenu.classList.add('slide-menu-active');
    // On ajoute la classe active sur l'overlay
    overlay.classList.add('active-overlay');
    // On désactive le scroll du body
    document.body.style.overflow = 'hidden';
})

// Ecouteur d'evenement pour fermer le menu
close.addEventListener('click', () => {
    // On retire la classe active du slide menu
    slideMenu.classList.remove('slide-menu-active');
    // On ajoute la classe active sur l'overlay
    overlay.classList.remove('active-overlay');
    // On désactive le scroll du body
    document.body.style.overflow = 'auto';
})

// Ecouteur d'evenement pour fermer le menu si on clique sur l'overlay
overlay.addEventListener('click', () => {
    // On retire la classe active du slide menu
    slideMenu.classList.remove('slide-menu-active');
    // On ajoute la classe active sur l'overlay
    overlay.classList.remove('active-overlay');
    // On désactive le scroll du body
    document.body.style.overflow = 'auto';
})

// Pour le submenu du dashboard pseudo //
const pseudo = document.querySelector('.pseudo');
const dashSubmenu = document.querySelector('.dash-submenu');

pseudo.addEventListener('click', () => {
    dashSubmenu.classList.toggle('dash-submenu-active');
});

// Pour le submenu déroulant //
const categorie = document.querySelector('.categorie');
const subMenu = document.querySelector('.sub-menu');

categorie.addEventListener('click', () => {
    subMenu.classList.toggle('sub-menu-active')
})

// ================================================================= //

