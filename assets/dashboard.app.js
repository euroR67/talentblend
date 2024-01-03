import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉')

// function setupDashboardApp() {
//     function ongletProfil() {
//         // ============================ Onglet profil ============================ //
//         const tabItems = document.querySelectorAll('.tab li');
//         const tabPanes = document.querySelectorAll('.section-wrapper > div');

//         tabItems.forEach((item, index) => {
//             item.addEventListener('click', () => {
//                 tabItems.forEach((tab) => tab.classList.remove('active'));
//                 tabPanes.forEach((pane) => pane.style.display = 'none');

//                 item.classList.add('active');
//                 tabPanes[index].style.display = 'block';
//             });
//         });
//     }

//     function addFormationExperience() {
//         // Script pour l'ajout de formations
        
//             // Ajouter un lien de suppression à toutes les formations
//             const addFormationFormDeleteLink = (item) => {
//                 const removeFormButton = document.createElement('button');
//                 removeFormButton.innerHTML = 'Remove this formation';
        
//                 item.appendChild(removeFormButton);
        
//                 removeFormButton.addEventListener('click', (e) => {
//                     e.preventDefault();
//                     item.remove();
//                 });
//             }
        
//             // Ajouter un lien de suppression à toutes les expériences
//             const addExperienceFormDeleteLink = (item) => {
//                 const removeFormButton = document.createElement('button');
//                 removeFormButton.innerHTML = 'Remove this experience';
        
//                 item.appendChild(removeFormButton);
        
//                 removeFormButton.addEventListener('click', (e) => {
//                     e.preventDefault();
//                     item.remove();
//                 });
//             }
        
//             // Ajouter le formulaire d'ajout de formation au collectionHolder
//             const addFormToCollection = (e) => {
//                 const collectionHolder = document.querySelector('.' + e.currentTarget.dataset.collectionHolderClass);
                
//                 const item = document.createElement('li');
                
//                 item.innerHTML = collectionHolder
//                     .dataset
//                     .prototype
//                     .replace(
//                     /__name__/g,
//                     collectionHolder.dataset.index
//                     );
                
//                 collectionHolder.appendChild(item);
                
//                 collectionHolder.dataset.index++;
                
//                 // add a delete link to the new form
//                 addFormationFormDeleteLink(item);
        
//             };
        
//             // Ajouter le formulaire d'ajout d'expérience au collectionHolder
//             const addFormToCollection2 = (e) => {
//                 const collectionHolder = document.querySelector('.' + e.currentTarget.dataset.collectionHolderClass);
                
//                 const item = document.createElement('li');
                
//                 item.innerHTML = collectionHolder
//                     .dataset
//                     .prototype
//                     .replace(
//                     /__name__/g,
//                     collectionHolder.dataset.index
//                     );
                
//                 collectionHolder.appendChild(item);
                
//                 collectionHolder.dataset.index++;
                
//                 // add a delete link to the new form
//                 addExperienceFormDeleteLink(item);
        
//             };
        
//             // Ajouter un écouteur d'évènement sur le bouton d'ajout de formation
//             document
//             .querySelectorAll('.add_formation_link')
//             .forEach(btn => {
//                 btn.addEventListener("click", addFormToCollection)
//             });
        
//             // Ajouter un écouteur d'évènement sur le bouton d'ajout d'expérience  
//             document
//             .querySelectorAll('.add_experience_link')
//             .forEach(btn => {
//                 btn.addEventListener("click", addFormToCollection2)
//             });
        
//             // Ajouter un bouton de suppression à tout les formulaires d'expérience
//             document
//             .querySelectorAll('div.experiences .formfield')
//             .forEach((experience) => {
//                 addExperienceFormDeleteLink(experience)
//             });
        
//             // Ajouter un bouton de suppression à tout les formulaires de formation
//             document
//             .querySelectorAll('div.formations .formfield')
//             .forEach((formation) => {
//                 addFormationFormDeleteLink(formation)
//             });
//     }

//     function actions() {
//         // ============================ Action edit / delet emploi ============================ //
//         const ellipsis = document.querySelectorAll('.fa-ellipsis');
//         const action = document.querySelectorAll('.action');
    
//         ellipsis.forEach((el, index) => {
//             el.addEventListener('click', (event) => {
//                 event.stopPropagation(); // Prevent this click from triggering the document click event below
//                 action[index].classList.toggle('active');
//             });
//         });
    
//         document.addEventListener('click', () => {
//             action.forEach((el) => {
//                 el.classList.remove('active');
//             });
//         });
//     }

//     function showBySalaire() {
//         // ============================ Champ type de salaire ============================ //
//         var showBy = document.getElementById('showBy');
//         var salaire = document.getElementById('salaire');
//         var minimum = document.getElementById('minimum');
    
//         function handleShowByChange() {
//             var showByValue = showBy.value;
    
//             // Cachez tous les conteneurs par défaut
//             document.getElementById('echelle').style.display = 'none';
//             document.getElementById('montantDepart').style.display = 'none';
//             document.getElementById('montantMaxi').style.display = 'none';
    
//             // Rendre les champs salaire et salaireMinimum facultatifs ou obligatoires en fonction de showByValue
//             if (showByValue === 'negociable') {
//                 salaire.required = false;
//                 minimum.required = false;
//             } else if (showByValue === 'montantMinimum') {
//                 salaire.required = false;
//                 minimum.required = true;
//             } else {
//                 salaire.required = true;
//                 minimum.required = false;
//             }
    
//             // Déplacez le champ de formulaire dans le bon conteneur et affichez-le
//             if (showByValue === 'echelle') {
//                 document.querySelector('#echelle div').appendChild(minimum);
//                 document.querySelector('#echelle div:nth-child(2)').appendChild(salaire);
//                 document.getElementById('echelle').style.display = 'flex';
//             } else if (showByValue === 'montantMinimum') {
//                 document.querySelector('#montantDepart div').appendChild(minimum);
//                 document.getElementById('montantDepart').style.display = 'flex';
//             } else if (showByValue === 'montantMaximum') {
//                 document.querySelector('#montantMaxi div').appendChild(salaire);
//                 document.getElementById('montantMaxi').style.display = 'flex';
//             }
//         }
    
//         // Appeler la fonction lorsque l'événement change est déclenché
//         showBy.addEventListener('change', function() {
//             // Videz les valeurs des champs salaire et salaireMinimum
//             salaire.value = '';
//             minimum.value = '';
    
//             handleShowByChange();
//         });
    
//         // Appeler la fonction lorsque la page est chargée
//         handleShowByChange();
//     }

//     ongletProfil();
//     addFormationExperience();
//     actions();
//     showBySalaire();
// }

// // Exécutez le script lorsque le document est prêt
// document.addEventListener('DOMContentLoaded', setupDashboardApp);

// // Exécutez le script chaque fois que Turbo Drive charge une nouvelle page
// document.addEventListener('turbo:load', setupDashboardApp);