/*==================================================
        FUNCIONES DE VALIDACIÓN DEL PROYECTO SRIB
==================================================*/

/**
 * Verifica que un campo no esté vacío.
 * Recibe un elemento del formulario y comprueba
 * que el usuario haya ingresado información.
 *
 * @param {HTMLInputElement|HTMLTextAreaElement|HTMLSelectElement} input
 * Campo que será validado.
 *
 * @returns {boolean}
 * Devuelve true si el campo es válido y false si está vacío.
 */
function validarCampo(input){

    // Elimina los espacios al inicio y al final del texto
    // para evitar que solo se ingresen espacios en blanco.
    if(input.value.trim() === ""){

        // Muestra un mensaje de validación personalizado.
        input.setCustomValidity("Completa este campo.");

        // Presenta el mensaje al usuario.
        input.reportValidity();

        // Indica que la validación falló.
        return false;

    }

    // Elimina cualquier mensaje de error anterior.
    input.setCustomValidity("");

    // Indica que el campo es válido.
    return true;

}

/**
 * Verifica que el correo pertenezca a un dominio
 * institucional de la BUAP.
 *
 * @param {HTMLInputElement} input
 * Campo donde se captura el correo electrónico.
 *
 * @returns {boolean}
 * Devuelve true si el correo es válido y false en caso contrario.
 */
function validarCorreoBUAP(input){

    // Obtiene el correo, elimina espacios y lo convierte a minúsculas.
    const correo = input.value.trim().toLowerCase();

    // Lista de dominios institucionales permitidos.
    const dominios = [
        "@alumno.buap.mx",
        "@alm.buap.mx",
        "@correo.buap.mx",
        "@buap.mx"
    ];

    // Comprueba si el correo termina con alguno de los dominios permitidos.
    const valido = dominios.some(
        dominio => correo.endsWith(dominio)
    );

    // Si el correo no pertenece a la BUAP.
    if(!valido){

        // Muestra un mensaje personalizado.
        input.setCustomValidity(
            "Ingresa un correo institucional BUAP."
        );

        // Presenta el mensaje al usuario.
        input.reportValidity();

        // Indica que la validación falló.
        return false;

    }

    // Elimina el mensaje de error.
    input.setCustomValidity("");

    // Indica que el correo es válido.
    return true;

}

/**
 * Comprueba que un texto tenga una longitud mínima.
 *
 * @param {HTMLInputElement|HTMLTextAreaElement} input
 * Campo que será validado.
 *
 * @param {number} minimo
 * Cantidad mínima de caracteres permitidos.
 *
 * @returns {boolean}
 * Devuelve true si cumple la longitud mínima y false en caso contrario.
 */
function validarLongitud(input, minimo){

    // Comprueba si la longitud del texto es menor a la permitida.
    if(input.value.trim().length < minimo){

        // Muestra un mensaje indicando la longitud mínima requerida.
        input.setCustomValidity(
            "Debe contener al menos " + minimo + " caracteres."
        );

        // Presenta el mensaje al usuario.
        input.reportValidity();

        // Indica que la validación falló.
        return false;

    }

    // Elimina el mensaje de error.
    input.setCustomValidity("");

    // Indica que el campo es válido.
    return true;

}

/**
 * Limita un campo de tipo fecha para que solo
 * permita seleccionar fechas dentro de los
 * últimos días indicados.
 *
 * @param {HTMLInputElement} input
 * Campo de tipo fecha.
 *
 * @param {number} dias
 * Cantidad de días permitidos hacia atrás.
 */
function limitarFecha(input, dias){

    // Obtiene la fecha actual.
    const hoy = new Date();

    // Convierte la fecha actual al formato YYYY-MM-DD.
    const max = hoy.toISOString().split("T")[0];

    // Crea una copia de la fecha actual.
    const min = new Date();

    // Resta el número de días permitido.
    min.setDate(min.getDate() - dias);

    // Establece la fecha máxima permitida.
    input.max = max;

    // Establece la fecha mínima permitida.
    input.min = min.toISOString().split("T")[0];

}
