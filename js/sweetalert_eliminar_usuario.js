function eliminarUsuario(idUsuario) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "No podrás revertir esta acción",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        customClass: {
            popup: 'swal-custom',
            title: 'swal-title',
            content: 'swal-text'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`php/eliminar_usuario.php?id=${idUsuario}`, {
                method: 'POST'
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: '¡Eliminado!',
                        text: 'El usuario ha sido eliminado correctamente',
                        icon: 'success',
                        confirmButtonText: 'Aceptar',
                        customClass: {
                            popup: 'swal-custom',
                            title: 'swal-title',
                            content: 'swal-text'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = 'gestionar_usuarios.php?deleted=1';
                        }
                    });
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: data.message || 'Ha ocurrido un error al eliminar el usuario',
                        icon: 'error',
                        confirmButtonText: 'Aceptar'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'Error',
                    text: 'Ha ocurrido un error en la comunicación con el servidor: ' + error.message,
                    icon: 'error',
                    confirmButtonText: 'Aceptar'
                });
            });
        }
    });
} 