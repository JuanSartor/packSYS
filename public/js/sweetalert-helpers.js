/**
 * Muestra un diálogo de confirmación para eliminar
 * @param {Event} event - El evento del formulario
 * @param {string} message - Mensaje personalizado (opcional)
 * @returns {boolean}
 */
function confirmDelete(event, message = '¿Está seguro de eliminar este elemento?') {
    event.preventDefault();

    const form = event.target.closest('form');

    Swal.fire({
        title: '¿Está seguro?',
        text: message,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit();
        }
    });

    return false;
}

/**
 * Muestra una notificación de éxito
 * @param {string} message - Mensaje a mostrar
 */
function showSuccess(message) {
    Swal.fire({
        icon: 'success',
        title: 'Éxito',
        text: message,
        timer: 3500,
        showConfirmButton: false
    });
}

/**
 * Muestra una notificación de error
 * @param {string} message - Mensaje a mostrar
 */
function showError(message) {
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: message
    });
}
