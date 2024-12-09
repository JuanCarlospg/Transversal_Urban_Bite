document.addEventListener("DOMContentLoaded", () => {
    const usuario = document.body.getAttribute('data-usuario');
    const sweetalertMostrado = document.body.getAttribute('data-sweetalert') === 'true';
    const mesaSweetalertMostrado = document.body.getAttribute('data-mesa-sweetalert') === 'true';

    if (usuario && !sweetalertMostrado) {
        Swal.fire({
            title: '¡Bienvenido!',
            text: `Hola ${usuario}, bienvenido/a al portal.`,
            icon: 'success',
            confirmButtonText: 'Gracias'
        }).then(() => {
            fetch('./php/marcar_sweetalert_mostrado.php');
        });
    }

    if (mesaSweetalertMostrado) {
        Swal.fire({
            title: 'Estado de la Mesa Cambiado',
            text: 'El estado de la mesa ha sido actualizado exitosamente.',
            icon: 'success',
            confirmButtonText: 'Aceptar'
        }).then(() => {
            fetch('./php/limpiar_mesa_sweetalert.php');
        });
    }

    window.reservarMesa = function (mesaId) {
        Swal.fire({
            title: 'Reservar Mesa',
            html: `
                <p>Nombre Reserva</p>
                <input type="text" id="nombreReserva" class="swal2-input" placeholder="Nombre">
                <p>Fecha Reserva</p>
                <input type="date" id="fechaReserva" class="swal2-input">
                <p>Hora Reserva</p>
                <input type="time" id="horaReserva" class="swal2-input">
            `,
            showCancelButton: true,
            confirmButtonText: 'Reservar',
            cancelButtonText: 'Cancelar',
        }).then((result) => {
            if (result.isConfirmed) {
                const nombreReserva = document.getElementById('nombreReserva').value;
                const fechaReserva = document.getElementById('fechaReserva').value;
                const horaReserva = document.getElementById('horaReserva').value;
    
                if (nombreReserva && fechaReserva && horaReserva) {
                    fetch(`./php/reservar_mesa.php?mesa_id=${mesaId}&nombre_reserva=${encodeURIComponent(nombreReserva)}&fecha=${fechaReserva}&hora=${horaReserva}`)
                        .then((response) => response.json())
                        .then((data) => {
                            if (data.success) {
                                Swal.fire('Éxito', 'La mesa ha sido reservada correctamente.', 'success').then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire('Error', data.message, 'error');
                            }
                        })
                        .catch((error) => {
                            Swal.fire('Error', 'Error de conexión.', 'error');
                            console.error(error);
                        });
                } else {
                    Swal.fire('Error', 'Complete todos los campos.', 'error');
                }
            }
        });
    }
}); 