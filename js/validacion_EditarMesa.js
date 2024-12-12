document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.form-editar-mesa');
    const numeroMesaInput = document.querySelector('input[name="numero_mesa"]');
    const idSalaSelect = document.querySelector('select[name="id_sala"]');
    const numeroSillasInput = document.querySelector('input[name="numero_sillas"]');

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

    numeroMesaInput.addEventListener('blur', function() {
        const numeroMesa = numeroMesaInput.value.trim();
        if (!numeroMesa) {
            mostrarError(numeroMesaInput, "El número de la mesa es obligatorio.");
        } else if (isNaN(numeroMesa) || numeroMesa <= 0) {
            mostrarError(numeroMesaInput, "El número de la mesa debe ser un número positivo.");
        } else {
            limpiarErrores(numeroMesaInput);
        }
    });


    idSalaSelect.addEventListener('blur', function() {
        if (idSalaSelect.value === "") {
            mostrarError(idSalaSelect, "Debes seleccionar una sala.");
        } else {
            limpiarErrores(idSalaSelect);
        }
    });

    numeroSillasInput.addEventListener('blur', function() {
        const numeroSillas = numeroSillasInput.value.trim();
        if (!numeroSillas) {
            mostrarError(numeroSillasInput, "El número de sillas es obligatorio.");
        } else if (isNaN(numeroSillas) || numeroSillas <= 0) {
            mostrarError(numeroSillasInput, "El número de sillas debe ser un número positivo.");
        } else {
            limpiarErrores(numeroSillasInput);
        }
    });

    form.addEventListener('submit', function(event) {
        limpiarErrores(numeroMesaInput);
        limpiarErrores(idSalaSelect);
        limpiarErrores(numeroSillasInput);

        const numeroMesa = numeroMesaInput.value.trim();
        const idSala = idSalaSelect.value;
        const numeroSillas = numeroSillasInput.value.trim();
        let errores = false;

        if (!numeroMesa) {
            mostrarError(numeroMesaInput, "El número de la mesa es obligatorio.");
            errores = true;
        } else if (isNaN(numeroMesa) || numeroMesa <= 0) {
            mostrarError(numeroMesaInput, "El número de la mesa debe ser un número positivo.");
            errores = true;
        }
        if (!idSala) {
            mostrarError(idSalaSelect, "Debes seleccionar una sala.");
            errores = true;
        }
        if (!numeroSillas) {
            mostrarError(numeroSillasInput, "El número de sillas es obligatorio.");
            errores = true;
        } else if (isNaN(numeroSillas) || numeroSillas <= 0) {
            mostrarError(numeroSillasInput, "El número de sillas debe ser un número positivo.");
            errores = true;
        }

        if (errores) {
            event.preventDefault();
        }
    });
});