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
            const MySwal = Swal.mixin({scrollbarPadding: false});
            if (data.error) {
                MySwal.fire({
                    icon: 'error',
                    title: data.error,
                    showConfirmButton: false,
                    timer: 2500
                });
            } else {
                MySwal.fire({
                    icon: 'success',
                    title: data.success,
                    showConfirmButton: false,
                    timer: 2500
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