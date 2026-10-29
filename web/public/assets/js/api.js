/**
 * Cliente de la API. Todas las consultas del navegador pasan por aquí.
 * La dirección de la API viene del atributo data-api del <body>
 * (que sale del archivo .env de la web).
 */
const Api = (() => {
    'use strict';

    const base = document.body.dataset.api;
    const TIEMPO_MAXIMO_MS = 10000;

    async function get(ruta, parametros = {}) {
        const url = new URL(base + ruta);

        Object.entries(parametros).forEach(([clave, valor]) => {
            if (valor !== null && valor !== undefined && valor !== '') {
                url.searchParams.set(clave, valor);
            }
        });

        const controlador = new AbortController();
        const temporizador = setTimeout(() => controlador.abort(), TIEMPO_MAXIMO_MS);

        try {
            const respuesta = await fetch(url, {
                headers: { 'Accept': 'application/json' },
                signal: controlador.signal,
            });

            let cuerpo = null;
            try {
                cuerpo = await respuesta.json();
            } catch (_) {
                // La respuesta no era JSON; se maneja abajo.
            }

            if (!respuesta.ok || !cuerpo || cuerpo.ok !== true) {
                const error = new Error(
                    (cuerpo && cuerpo.error && cuerpo.error.message) || 'No se pudo completar la solicitud.'
                );
                error.status = respuesta.status;
                throw error;
            }

            return cuerpo.data;
        } catch (error) {
            if (error.name === 'AbortError') {
                throw new Error('El servidor tardó demasiado en responder.');
            }
            if (error instanceof TypeError) {
                throw new Error('No se pudo conectar con el servidor.');
            }
            throw error;
        } finally {
            clearTimeout(temporizador);
        }
    }

    return { get };
})();