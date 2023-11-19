// ============================ Onglet dashboard ============================ //

document.addEventListener('DOMContentLoaded', function () {
    const tabItems = document.querySelectorAll('.tab li');
    const tabPanes = document.querySelectorAll('.section-wrapper > div');

    tabItems.forEach((item, index) => {
        item.addEventListener('click', () => {
            tabItems.forEach((tab) => tab.classList.remove('active'));
            tabPanes.forEach((pane) => pane.style.display = 'none');

            item.classList.add('active');
            tabPanes[index].style.display = 'block';
        });
    });
});


// Script pour l'ajout de formations

        // Ajouter un lien de suppression à toutes les formations
        const addFormationFormDeleteLink = (item) => {
            const removeFormButton = document.createElement('button');
            removeFormButton.innerHTML = 'Remove this formation';

            item.appendChild(removeFormButton);

            removeFormButton.addEventListener('click', (e) => {
                e.preventDefault();
                item.remove();
            });
        }

        // Ajouter un lien de suppression à toutes les expériences
        const addExperienceFormDeleteLink = (item) => {
            const removeFormButton = document.createElement('button');
            removeFormButton.innerHTML = 'Remove this experience';

            item.appendChild(removeFormButton);

            removeFormButton.addEventListener('click', (e) => {
                e.preventDefault();
                item.remove();
            });
        }

        // Ajouter le formulaire d'ajout de formation au collectionHolder
        const addFormToCollection = (e) => {
            const collectionHolder = document.querySelector('.' + e.currentTarget.dataset.collectionHolderClass);
          
            const item = document.createElement('li');
          
            item.innerHTML = collectionHolder
              .dataset
              .prototype
              .replace(
                /__name__/g,
                collectionHolder.dataset.index
              );
          
            collectionHolder.appendChild(item);
          
            collectionHolder.dataset.index++;
            
            // add a delete link to the new form
            addFormationFormDeleteLink(item);

        };

        // Ajouter le formulaire d'ajout d'expérience au collectionHolder
        const addFormToCollection2 = (e) => {
            const collectionHolder = document.querySelector('.' + e.currentTarget.dataset.collectionHolderClass);
          
            const item = document.createElement('li');
          
            item.innerHTML = collectionHolder
              .dataset
              .prototype
              .replace(
                /__name__/g,
                collectionHolder.dataset.index
              );
          
            collectionHolder.appendChild(item);
          
            collectionHolder.dataset.index++;
            
            // add a delete link to the new form
            addExperienceFormDeleteLink(item);

        };

        // Ajouter un écouteur d'évènement sur le bouton d'ajout de formation
        document
        .querySelectorAll('.add_formation_link')
        .forEach(btn => {
            btn.addEventListener("click", addFormToCollection)
        });

        // Ajouter un écouteur d'évènement sur le bouton d'ajout d'expérience  
        document
        .querySelectorAll('.add_experience_link')
        .forEach(btn => {
            btn.addEventListener("click", addFormToCollection2)
        });

        // Ajouter un bouton de suppression à tout les formulaires d'expérience
        document
        .querySelectorAll('div.experiences .formfield')
        .forEach((experience) => {
            addExperienceFormDeleteLink(experience)
        });

        // Ajouter un bouton de suppression à tout les formulaires de formation
        document
        .querySelectorAll('div.formations .formfield')
        .forEach((formation) => {
            addFormationFormDeleteLink(formation)
        });

// ============================ Action edit / delet emploi ============================ //

document.addEventListener('DOMContentLoaded', function () {
    const ellipsis = document.querySelectorAll('.fa-ellipsis');
    const action = document.querySelectorAll('.action');

    ellipsis.forEach((el, index) => {
        el.addEventListener('click', () => {
            action[index].classList.toggle('active');
        });
    });
});