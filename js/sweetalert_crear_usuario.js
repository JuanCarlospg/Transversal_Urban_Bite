document.getElementById('formUsuario').addEventListener('submit', function(e) {
    e.preventDefault();
    
    let formData = new FormData(this);
    
    fetch('crear_usuario.php', {
        method: 'POST',
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
                text: 'El usuario se ha creado correctamente',
                icon: 'success',
                confirmButtonText: 'Aceptar',
                customClass: {
                    popup: 'swal-custom',
                    title: 'swal-title',
                    content: 'swal-text'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '../gestionar_usuarios.php?created=1';
                }
            });
        } else {
            Swal.fire({
                title: 'Error',
                text: data.message || 'Ha ocurrido un error al crear el usuario',
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