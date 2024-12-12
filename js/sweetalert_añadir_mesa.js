document.getElementById('formMesa').addEventListener('submit', function(e) {
    e.preventDefault();
    
    let formData = new FormData(this);
    const urlParams = new URLSearchParams(window.location.search);
    const id_sala = urlParams.get('id_sala');
    formData.append('id_sala', id_sala);
    
    fetch('form_añadir_mesa.php?id_sala=' + id_sala, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                title: '¡Éxito!',
                text: 'La mesa se ha añadido correctamente',
                icon: 'success',
                confirmButtonText: 'Aceptar',
                customClass: {
                    popup: 'swal-custom',
                    title: 'swal-title',
                    content: 'swal-text'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `añadir_mesa.php?id_sala=${id_sala}`;
                }
            });
        } else {
            Swal.fire({
                title: 'Error',
                text: data.message || 'Ha ocurrido un error al añadir la mesa',
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
        console.error('Error:', error);
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