document.getElementById('formReserva').addEventListener('submit', function(e) {
    e.preventDefault();
    
    let formData = new FormData(this);
    const idMesa = new URLSearchParams(window.location.search).get('id_mesa');
    
    fetch(window.location.href, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Error en la respuesta del servidor');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            Swal.fire({
                title: '¡Éxito!',
                text: 'La reserva se ha realizado correctamente',
                icon: 'success',
                confirmButtonText: 'Aceptar',
                customClass: {
                    popup: 'swal-custom',
                    title: 'swal-title',
                    content: 'swal-text'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `reservar_mesa.php?id_mesa=${idMesa}&success=1`;
                }
            });
        } else {
            Swal.fire({
                title: 'Error de Validación',
                text: 'Por favor, complete todos los campos requeridos',
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
            title: 'Error de Validación',
            text: 'Por favor, complete todos los campos requeridos',
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