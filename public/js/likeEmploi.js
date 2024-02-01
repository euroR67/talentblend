document.querySelectorAll('.save-emploi-link').forEach(function(link) {
    link.addEventListener('click', function(e) {
        e.preventDefault();

        let url = link.dataset.saved === 'false' ? link.dataset.saveUrl : link.dataset.deleteUrl;

        fetch(url, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.onmouseenter = Swal.stopTimer;
                    toast.onmouseleave = Swal.resumeTimer;
                }
            });
        
            if (data.error) {
                Toast.fire({
                    icon: 'error',
                    title: data.error
                });
            } else {
                Toast.fire({
                    icon: 'success',
                    title: data.success
                });
        
                if (link.dataset.saved === 'false') {
                    link.innerHTML = '<i class="fa-solid fa-heart"></i>';
                    link.dataset.saved = 'true';
                    link.href = link.dataset.deleteUrl;
                } else {
                    link.innerHTML = '<i class="fa-regular fa-heart"></i>';
                    link.dataset.saved = 'false';
                    link.href = link.dataset.saveUrl;
                }
            }
        });
    });
});

document.querySelectorAll('.action2').forEach(function(link) {
    link.addEventListener('click', function(e) {
        e.preventDefault();

        // Vérifiez si l'emploi est toujours considéré comme un emploi sauvegardé
        let row = link.closest('tr');
        if (!row.classList.contains('saved')) {
            return;
        }

        let url = link.href;

        fetch(url, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.onmouseenter = Swal.stopTimer;
                    toast.onmouseleave = Swal.resumeTimer;
                }
            });
        
            if (data.error) {
                Toast.fire({
                    icon: 'error',
                    title: data.error
                });
            } else {
                Toast.fire({
                    icon: 'success',
                    title: data.success
                });
        
                // Supprimer la ligne du tableau et la classe 'saved'
                row.parentNode.removeChild(row);
            }
        });
    });
});