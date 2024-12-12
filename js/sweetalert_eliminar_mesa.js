function eliminarMesa(id_mesa, id_sala) {
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
            fetch(`eliminar_mesa.php?id=${id_mesa}&id_sala=${id_sala}`, {
                method: 'POST'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: '¡Eliminado!',
                        text: 'La mesa ha sido eliminada correctamente',
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
                        text: data.message || 'Ha ocurrido un error al eliminar la mesa',
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
        }
    });
} 