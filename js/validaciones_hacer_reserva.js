document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.form-reserva');
    const nombreInput = document.querySelector('input[name="nombre"]');
    const fechaInput = document.querySelector('input[name="fecha"]');
    const franjaSelect = document.querySelector('select[name="id_franja"]');

    // Función para mostrar mensajes de error
    function mostrarError(input, mensaje) {
        limpiarErrores(input);
        const errorElement = document.createElement('span');
        errorElement.className = 'error-message';
        errorElement.style.color = 'red';
        errorElement.textContent = mensaje;
        input.parentNode.insertBefore(errorElement, input.nextSibling);
    }

    // Función para limpiar mensajes de error
    function limpiarErrores(input) {
        const errorMessages = input.parentNode.querySelectorAll('.error-message');
        errorMessages.forEach(error => error.remove());
    }

    // Validar nombre
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

    // Validar fecha
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

        // Mantener la funcionalidad existente de actualización
        const urlParams = new URLSearchParams(window.location.search);
        const idMesa = urlParams.get('id_mesa');
        window.location.href = `hacer_reserva.php?id_mesa=${idMesa}&filtro_fecha=${this.value}`;
    });

    // Validar franja horaria
    franjaSelect.addEventListener('blur', function() {
        if (franjaSelect.value === "") {
            mostrarError(franjaSelect, "Debes seleccionar una franja horaria.");
        } else {
            limpiarErrores(franjaSelect);
        }
    });

    // También validar al cambiar el valor del select
    franjaSelect.addEventListener('change', function() {
        if (franjaSelect.value !== "") {
            limpiarErrores(franjaSelect);
        }
    });

    // Validar formulario al enviar
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