document.addEventListener('DOMContentLoaded', function() {
    const fechaInput = document.getElementById('fecha');
    if (fechaInput) {
        fechaInput.addEventListener('change', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const idMesa = urlParams.get('id_mesa');
            window.location.href = `hacer_reserva.php?id_mesa=${idMesa}&filtro_fecha=${this.value}`;
        });
    }
});