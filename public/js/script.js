// DOM Content Loaded
document.addEventListener('DOMContentLoaded', function() {

    // ============================ Slide menu  ============================ //



// ========================= Modal formulaire de recherche ======================================== //

const glass = document.querySelector('.fa-magnifying-glass');
const searchModal = document.querySelector('.search-modal');
const closeSearchModal = document.querySelector('.search-modal .fa-times');
const overlay = document.querySelector('.overlay'); // Add this line to define overlay

glass.addEventListener('click', () => {
    searchModal.style.display = 'flex';
    overlay.classList.add('active-overlay');
    // disable scroll
    document.body.style.overflow = 'hidden';
});

closeSearchModal.addEventListener('click', () => {
    searchModal.style.display = 'none';
    overlay.classList.remove('active-overlay');
    // enable scroll
    document.body.style.overflow = 'auto';
});

overlay.addEventListener('click', () => {
    searchModal.style.display = 'none';
    overlay.classList.remove('active-overlay');
    // enable scroll
    document.body.style.overflow = 'auto';
});

// ========================= Dropdown du footer ======================================== //

// Gérer les dropdowns du footer et les chevrons
const dropdowns = document.querySelectorAll('.footer-dropdown-head');
const chevron = document.querySelectorAll('.footer-dropdown-head i');

dropdowns.forEach((dropdown, index) => {
    dropdown.addEventListener('click', () => {
        dropdown.nextElementSibling.classList.toggle('active');
        chevron[index].classList.toggle('active');
    });
});

// ========================= Voir plus d'info offre d'emploi ======================================== //

// Show more or less info
const showHide = document.querySelector('.show-hide');
const showBtn = document.querySelector('.show-btn');
const hideBtn = document.querySelector('.hide-btn');

// Vérifier que les éléments existent avant d'attacher les écouteurs d'événements
if (showHide && showBtn && hideBtn) {
    showBtn.addEventListener('click', () => {
        showHide.classList.add('show');
        showBtn.style.display = 'none';
    });

    hideBtn.addEventListener('click', () => {
        showHide.classList.remove('show');
        showBtn.style.display = 'block';
    });
}


// // Gestion de l'affichage des informations de l'entreprise
// const tabInfoEntreprise = document.querySelector('.tab-info-entreprise');
// const tabEmploiEntreprise = document.querySelector('.tab-emploi-entreprise');
// const tabInfoEntrepriseBtn = document.querySelector('.entreprise-info-head ul li:first-child');
// const tabEmploiEntrepriseBtn = document.querySelector('.entreprise-info-head ul li:last-child');

// // Vérifier que les éléments existent avant d'attacher les écouteurs d'événements
// if (tabInfoEntreprise && tabEmploiEntreprise && tabInfoEntrepriseBtn && tabEmploiEntrepriseBtn) {
//     tabInfoEntrepriseBtn.addEventListener('click', () => {
//         tabInfoEntrepriseBtn.classList.add('active');
//         tabEmploiEntrepriseBtn.classList.remove('active');
//         tabInfoEntreprise.style.display = 'flex';
//         tabEmploiEntreprise.style.display = 'none';
//     });

//     tabEmploiEntrepriseBtn.addEventListener('click', () => {
//         tabEmploiEntrepriseBtn.classList.add('active');
//         tabInfoEntrepriseBtn.classList.remove('active');
//         tabEmploiEntreprise.style.display = 'flex';
//         tabInfoEntreprise.style.display = 'none';
//     });
// }


});