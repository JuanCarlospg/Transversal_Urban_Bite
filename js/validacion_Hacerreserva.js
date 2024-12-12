document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.form-reserva');
    const nombreInput = document.querySelector('input[name="nombre"]');
    const fechaInput = document.querySelector('input[name="fecha"]');
    const franjaSelect = document.querySelector('select[name="id_franja"]');

    function mostrarError(input, mensaje) {
        limpiarErrores(input);
        const errorElement = document.createElement('span');
        errorElement.className = 'error-message';
        errorElement.style.color = 'red';
        errorElement.textContent = mensaje;
        input.parentNode.insertBefore(errorElement, input.nextSibling);
    }

    function limpiarErrores(input) {
        const errorMessages = input.parentNode.querySelectorAll('.error-message');
        errorMessages.forEach(error => error.remove());
    }

    nombreInput.addEventListener('blur', function() {
        const nombre = nombreInput.value.trim();
        if (!nombre) {
            mostrarError(nombreInput, "El nombre es obligatorio.");
        } else if (!/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]{3,}$/.test(nombre)) {
            mostrarError(nombreInput, "El nombre debe contener solo letras y tener al menos 3 caracteres.");
        } else {
            limpiarErrores(nombreInput);
        }
    });

    fechaInput.addEventListener('change', function() {
        const fecha = fechaInput.value;
        const hoy = new Date().toISOString().split('T')[0];
        
        if (!fecha) {
            mostrarError(fechaInput, "La fecha es obligatoria.");
        } else if (fecha < hoy) {
            mostrarError(fechaInput, "La fecha no puede ser anterior a hoy.");
        } else {
            limpiarErrores(fechaInput);
        }

        const urlParams = new URLSearchParams(window.location.search);
        const idMesa = urlParams.get('id_mesa');
        window.location.href = `hacer_reserva.php?id_mesa=${idMesa}&filtro_fecha=${this.value}`;
    });

    franjaSelect.addEventListener('blur', function() {
        if (franjaSelect.value === "") {
            mostrarError(franjaSelect, "Debes seleccionar una franja horaria.");
        } else {
            limpiarErrores(franjaSelect);
        }
    });

    franjaSelect.addEventListener('change', function() {
        if (franjaSelect.value !== "") {
            limpiarErrores(franjaSelect);
        }
    });

    form.addEventListener('submit', function(event) {
        limpiarErrores(nombreInput);
        limpiarErrores(fechaInput);
        limpiarErrores(franjaSelect);

        const nombre = nombreInput.value.trim();
        const fecha = fechaInput.value;
        const franja = franjaSelect.value;
        const hoy = new Date().toISOString().split('T')[0];
        let errores = false;

        if (!nombre) {
            mostrarError(nombreInput, "El nombre es obligatorio.");
            errores = true;
        } else if (!/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]{3,}$/.test(nombre)) {
            mostrarError(nombreInput, "El nombre debe contener solo letras y tener al menos 3 caracteres.");
            errores = true;
        }

        if (!fecha) {
            mostrarError(fechaInput, "La fecha es obligatoria.");
            errores = true;
        } else if (fecha < hoy) {
            mostrarError(fechaInput, "La fecha no puede ser anterior a hoy.");
            errores = true;
        }

        if (!franja) {
            mostrarError(franjaSelect, "Debes seleccionar una franja horaria.");
            errores = true;
        }

        if (errores) {
            event.preventDefault();
        }
    });
}); 