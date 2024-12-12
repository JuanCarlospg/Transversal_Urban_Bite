document.getElementById('formReserva').addEventListener('submit', function(e) {
    e.preventDefault();
    
    let formData = new FormData(this);
    const urlParams = new URLSearchParams(window.location.search);
    const idReserva = urlParams.get('id_reserva');
    
    fetch('', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                title: '¡Éxito!',
                text: 'La reserva se ha actualizado correctamente',
                icon: 'success',
                confirmButtonText: 'Aceptar',
                customClass: {
                    popup: 'swal-custom',
                    title: 'swal-title',
                    content: 'swal-text'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `reservar_mesa.php?id_mesa=${data.id_mesa}&updated=1`;
                }
            });
        } else {
            Swal.fire({
                title: 'Error',
                text: data.message || 'Ha ocurrido un error al actualizar la reserva',
                icon: 'error',
                confirmButtonText: 'Aceptar'
            });
        }
    })
    .catch(error => {
        Swal.fire({
            title: 'Error',
            text: 'Ha ocurrido un error en la comunicación con el servidor',
            icon: 'error',
            confirmButtonText: 'Aceptar'
        });
    });
}); 