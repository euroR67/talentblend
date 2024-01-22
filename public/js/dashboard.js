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


// ============================ Action edit / delet emploi ============================ //

document.addEventListener('DOMContentLoaded', function () {
    const ellipsis = document.querySelectorAll('.fa-ellipsis');
    const action = document.querySelectorAll('.action');

    ellipsis.forEach((el, index) => {
        el.addEventListener('click', (event) => {
            event.stopPropagation(); // Prevent this click from triggering the document click event below
            action[index].classList.toggle('active');
        });
    });

    document.addEventListener('click', () => {
        action.forEach((el) => {
            el.classList.remove('active');
        });
    });
});

// ============================ Champ type de salaire ============================ //

document.addEventListener('DOMContentLoaded', function() {
    var showBy = document.getElementById('showBy');
    var salaire = document.getElementById('salaire');
    var minimum = document.getElementById('minimum');

    function handleShowByChange() {
        var showByValue = showBy.value;

        // Cachez tous les conteneurs par défaut
        document.getElementById('echelle').style.display = 'none';
        document.getElementById('montantDepart').style.display = 'none';
        document.getElementById('montantMaxi').style.display = 'none';

        // Rendre les champs salaire et salaireMinimum facultatifs ou obligatoires en fonction de showByValue
        if (showByValue === 'negociable') {
            salaire.required = false;
            minimum.required = false;
        } else if (showByValue === 'montantMinimum') {
            salaire.required = false;
            minimum.required = true;
        } else {
            salaire.required = true;
            minimum.required = false;
        }

        // Déplacez le champ de formulaire dans le bon conteneur et affichez-le
        if (showByValue === 'echelle') {
            document.querySelector('#echelle div').appendChild(minimum);
            document.querySelector('#echelle div:nth-child(2)').appendChild(salaire);
            document.getElementById('echelle').style.display = 'flex';
        } else if (showByValue === 'montantMinimum') {
            document.querySelector('#montantDepart div').appendChild(minimum);
            document.getElementById('montantDepart').style.display = 'flex';
        } else if (showByValue === 'montantMaximum') {
            document.querySelector('#montantMaxi div').appendChild(salaire);
            document.getElementById('montantMaxi').style.display = 'flex';
        }
    }

    // Appeler la fonction lorsque l'événement change est déclenché
    showBy.addEventListener('change', function() {
        // Videz les valeurs des champs salaire et salaireMinimum
        salaire.value = '';
        minimum.value = '';

        handleShowByChange();
    });

    // Appeler la fonction lorsque la page est chargée
    handleShowByChange();
});