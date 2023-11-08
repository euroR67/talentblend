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


        let $collectionHolder;

        let $addNewItem = $('<button type="button" class="add_item_link">Ajouter une formation</button>');

        $(document).ready(function () {
            // get the collectionHolder
            $collectionHolder = $('#formation_list');

            // append the add new item to the collectionHolder
            $collectionHolder.append($addNewItem);

            $collectionHolder.data('index', $collectionHolder.find(':input').length);

            // add remove button to existing items
            $collectionHolder.find('.panel').each(function () {
                addRemoveButton($(this));
            });

            // Handle the click event for add new item button
            $addNewItem.click(function (e) {
                e.preventDefault();

                // create a new form and append it to the collectionHolder
                addNewForm();
            });
        });

        function addNewForm() {
            // getting the prototype
            let prototype = $collectionHolder.data('prototype');
            // get the new index
            let index = $collectionHolder.data('index');
            // create the new form
            let newForm = prototype;

            newForm = newForm.replace(/__name__/g, index);

            $collectionHolder.data('index', index + 1);

            // create the panel
            let $panel = $('<div class="panel panel-warning"><div class="panel-heading"></div></div>');

            // create the panel-body and append it to the panel
            let $panelBody = $('<div class="panel-body"></div>').append(newForm);

            // append the body to the panel
            $panel.append($panelBody);

            // append the remove button to the new panel
            addRemoveButton($panel);

            // append the panel to the addNewItem
            $addNewItem.before($panel); 
        }

        function addRemoveButton($panel) {
            // Create remove button
            let $removeButton = $('<button type="button" class="btn btn-danger">Remove</button>');

            // Append remove button to panel footer
            let $panelFooter = $('<div class="panel-footer"></div>').append($removeButton);

            // Append the footer to the panel
            $panel.append($panelFooter);

            // handle click event of remove button
            $removeButton.click(function (e) {
                e.preventDefault();
                $(this).parents('.panel').slideUp(100, function () {
                    $(this).remove();
                });
            });
        }