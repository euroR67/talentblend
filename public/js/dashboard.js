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