function eliminarReserva(idReserva) {
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
            fetch(`eliminar_reserva.php?id_reserva=${idReserva}`, {
                method: 'DELETE'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: '¡Eliminado!',
                        text: 'La reserva ha sido eliminada correctamente',
                        icon: 'success',
                        confirmButtonText: 'Aceptar',
                        customClass: {
                            popup: 'swal-custom',
                            title: 'swal-title',
                            content: 'swal-text'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = `reservar_mesa.php?id_mesa=${data.id_mesa}&deleted=1`;
                        }
                    });
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: data.message || 'Ha ocurrido un error al eliminar la reserva',
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