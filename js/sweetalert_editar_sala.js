document.getElementById('formSala').addEventListener('submit', function(e) {
    e.preventDefault();
    
    let formData = new FormData(this);
    
    fetch('', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                title: '¡Éxito!',
                text: 'La sala se ha actualizado correctamente',
                icon: 'success',
                confirmButtonText: 'Aceptar',
                customClass: {
                    popup: 'swal-custom',
                    title: 'swal-title',
                    content: 'swal-text'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `../gestionar_salas.php?tipo=${tipo}&updated=1`;
                }
            });
        } else {
            Swal.fire({
                title: 'Error',
                text: data.message || 'Ha ocurrido un error al actualizar la sala',
                icon: 'error',
                confirmButtonText: 'Aceptar',
                customClass: {
                    popup: 'swal-custom',
                    title: 'swal-title',
                    content: 'swal-text'
                }
            });
        }
    })
    .catch(error => {
        Swal.fire({
            title: 'Error',
            text: 'Ha ocurrido un error en la comunicación con el servidor',
            icon: 'error',
            confirmButtonText: 'Aceptar',
            customClass: {
                popup: 'swal-custom',
                title: 'swal-title',
                content: 'swal-text'
            }
        });
    });
}); 