document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.form-crear-sala');
    const nombreSalaInput = document.querySelector('input[name="nombre_sala"]');
    const capacidadInput = document.querySelector('input[name="capacidad"]');
    const tipoSalaSelect = document.querySelector('select[name="tipo_sala"]');

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

    nombreSalaInput.addEventListener('blur', function() {
        const nombreSala = nombreSalaInput.value.trim();
        if (!nombreSala) {
            mostrarError(nombreSalaInput, "El nombre de la sala es obligatorio.");
        } else {
            limpiarErrores(nombreSalaInput);
        }
    });

    capacidadInput.addEventListener('blur', function() {
        const capacidad = capacidadInput.value.trim();
        if (!capacidad) {
            mostrarError(capacidadInput, "La capacidad es obligatoria.");
        } else if (isNaN(capacidad) || capacidad <= 0) {
            mostrarError(capacidadInput, "La capacidad debe ser un número positivo.");
        } else {
            limpiarErrores(capacidadInput);
        }
    });

    tipoSalaSelect.addEventListener('blur', function() {
        if (tipoSalaSelect.value === "") {
            mostrarError(tipoSalaSelect, "Debes seleccionar un tipo de sala.");
        } else {
            limpiarErrores(tipoSalaSelect);
        }
    });

    form.addEventListener('submit', function(event) {
        limpiarErrores(nombreSalaInput);
        limpiarErrores(capacidadInput);
        limpiarErrores(tipoSalaSelect);

        const nombreSala = nombreSalaInput.value.trim();
        const capacidad = capacidadInput.value.trim();
        const tipoSala = tipoSalaSelect.value;
        let errores = false;

        if (!nombreSala) {
            mostrarError(nombreSalaInput, "El nombre de la sala es obligatorio.");
            errores = true;
        }
        if (!capacidad) {
            mostrarError(capacidadInput, "La capacidad es obligatoria.");
            errores = true;
        } else if (isNaN(capacidad) || capacidad <= 0) {
            mostrarError(capacidadInput, "La capacidad debe ser un número positivo.");
            errores = true;
        }
        if (!tipoSala) {
            mostrarError(tipoSalaSelect, "Debes seleccionar un tipo de sala.");
            errores = true;
        }

        if (errores) {
            event.preventDefault();
        }
    });
});