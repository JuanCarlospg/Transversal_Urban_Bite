function eliminarSala(id, tipo) {
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
            fetch(`php/eliminar_sala.php?id=${id}&tipo=${tipo}`, {
                method: 'POST'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: '¡Eliminado!',
                        text: 'El elemento ha sido eliminado correctamente',
                        icon: 'success',
                        confirmButtonText: 'Aceptar',
                        customClass: {
                            popup: 'swal-custom',
                            title: 'swal-title',
                            content: 'swal-text'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = `gestionar_salas.php?tipo=${tipo}&deleted=1`;
                        }
                    });
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: data.message || 'Ha ocurrido un error al eliminar',
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
        }
    });
} 