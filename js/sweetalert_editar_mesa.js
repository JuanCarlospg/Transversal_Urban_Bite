document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('formEditarMesa');
    
    if (!form) {
        console.error('No se encontró el formulario con ID formEditarMesa');
        return;
    }

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        console.log('Formulario enviado'); // Para depuración
        
        let formData = new FormData(this);
        
        fetch(window.location.href, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            console.log('Respuesta recibida:', data); // Para depuración
            
            if (data.success) {
                Swal.fire({
                    title: '¡Éxito!',
                    text: 'La mesa se ha actualizado correctamente',
                    icon: 'success',
                    confirmButtonText: 'Aceptar',
                    customClass: {
                        popup: 'swal-custom',
                        title: 'swal-title',
                        content: 'swal-text'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = `añadir_mesa.php?id_sala=${data.id_sala}`;
                    }
                });
            } else {
                Swal.fire({
                    title: 'Error',
                    text: data.message || 'Ha ocurrido un error al actualizar la mesa',
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
});

// Prueba rápida para verificar que SweetAlert2 está funcionando
document.addEventListener('DOMContentLoaded', function() {
    console.log('Script cargado');
    console.log('SweetAlert2 disponible:', typeof Swal !== 'undefined');
}); 