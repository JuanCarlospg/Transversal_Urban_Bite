document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.form-crear-usuario');
    const nombreUserInput = document.querySelector('input[name="nombre_user"]');
    const contrasenaInput = document.querySelector('input[name="contrasena"]');
    const idRolSelect = document.querySelector('select[name="id_rol"]');

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

    nombreUserInput.addEventListener('blur', function() {
        const nombreUser = nombreUserInput.value.trim();
        if (!nombreUser) {
            mostrarError(nombreUserInput, "El nombre de usuario es obligatorio.");
        } else if (!/^[a-zA-Z0-9_]+$/.test(nombreUser)) {
            mostrarError(nombreUserInput, "El nombre de usuario solo puede contener letras, números y guiones bajos.");
        } else {
            limpiarErrores(nombreUserInput);
        }
    });

    contrasenaInput.addEventListener('blur', function() {
        const contrasena = contrasenaInput.value.trim();
        if (!contrasena) {
            mostrarError(contrasenaInput, "La contraseña es obligatoria.");
        } else if (contrasena.length < 6) {
            mostrarError(contrasenaInput, "La contraseña debe tener al menos 6 caracteres.");
        } else {
            limpiarErrores(contrasenaInput);
        }
    });

    idRolSelect.addEventListener('blur', function() {
        if (idRolSelect.value === "") {
            mostrarError(idRolSelect, "Debes seleccionar un rol.");
        } else {
            limpiarErrores(idRolSelect);
        }
    });

    form.addEventListener('submit', function(event) {
        limpiarErrores(nombreUserInput);
        limpiarErrores(contrasenaInput);
        limpiarErrores(idRolSelect);

        const nombreUser = nombreUserInput.value.trim();
        const contrasena = contrasenaInput.value.trim();
        const idRol = idRolSelect.value;
        let errores = false;

        if (!nombreUser) {
            mostrarError(nombreUserInput, "El nombre de usuario es obligatorio.");
            errores = true;
        }
        if (!contrasena) {
            mostrarError(contrasenaInput, "La contraseña es obligatoria.");
            errores = true;
        }
        if (contrasena.length < 6) {
            mostrarError(contrasenaInput, "La contraseña debe tener al menos 6 caracteres.");
            errores = true;
        }
        if (!idRol) {
            mostrarError(idRolSelect, "Debes seleccionar un rol.");
            errores = true;
        }

        if (errores) {
            event.preventDefault();
        }
    });
}); 