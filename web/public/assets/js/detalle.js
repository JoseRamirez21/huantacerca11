/**
 * Página de detalle de un negocio.
 * Igual que en inicio.js: se usa textContent, nunca innerHTML.
 */
(() => {
    'use strict';

    const $ = (id) => document.getElementById(id);

    const id = $('detalle').dataset.id;

    const ui = {
        carga: $('d-carga'),
        error: $('d-error'),
        mensajeError: $('d-mensaje-error'),
        reintentar: $('d-reintentar'),
        noDisponible: $('d-nodisponible'),
        contenido: $('d-contenido'),
        categoria: $('d-categoria'),
        nombre: $('d-nombre'),
        descripcion: $('d-descripcion'),
        datos: $('d-datos'),
        sinDatos: $('d-sin-datos'),
        acciones: $('d-acciones'),
    };

    function el(etiqueta, clases, texto) {
        const nodo = document.createElement(etiqueta);
        if (clases) nodo.className = clases;
        if (texto !== undefined && texto !== null) nodo.textContent = texto;
        return nodo;
    }

    function mostrarSolo(nombre) {
        ui.carga.hidden = nombre !== 'carga';
        ui.error.hidden = nombre !== 'error';
        ui.noDisponible.hidden = nombre !== 'nodisponible';
        ui.contenido.hidden = nombre !== 'contenido';
    }

    function agregarFila(titulo, nodoValor) {
        const dd = el('dd', 'col-sm-9');
        dd.append(nodoValor);
        ui.datos.append(el('dt', 'col-sm-3', titulo), dd);
    }

    function pintar(n) {
        document.title = n.nombre + ' | Huanta Cerca';

        ui.categoria.textContent = n.categoria.nombre;
        ui.nombre.textContent = n.nombre;
        ui.descripcion.textContent = n.descripcion || 'Este negocio aún no tiene descripción.';

        ui.datos.replaceChildren();
        ui.acciones.replaceChildren();

        if (n.direccion) {
            agregarFila('Dirección', document.createTextNode(n.direccion));
        }

        if (n.telefono) {
            const enlace = el('a', null, n.telefono);
            enlace.href = 'tel:' + String(n.telefono).replace(/\D/g, '');
            agregarFila('Teléfono', enlace);
        }

        if (n.horario) {
            agregarFila('Horario', document.createTextNode(n.horario));
        }

        ui.sinDatos.hidden = ui.datos.children.length > 0;

        if (typeof n.whatsapp === 'string' && n.whatsapp.startsWith('https://wa.me/')) {
            const wa = el('a', 'btn btn-whatsapp', 'Escribir por WhatsApp');
            wa.href = n.whatsapp;
            wa.target = '_blank';
            wa.rel = 'noopener noreferrer';
            ui.acciones.append(wa);
        }

        mostrarSolo('contenido');
    }

    async function cargar() {
        mostrarSolo('carga');

        try {
            const negocio = await Api.get('/negocios/' + encodeURIComponent(id));
            pintar(negocio);
        } catch (error) {
            if (error.status === 404) {
                mostrarSolo('nodisponible');
            } else {
                ui.mensajeError.textContent = error.message;
                mostrarSolo('error');
            }
        }
    }

    ui.reintentar.addEventListener('click', cargar);

    cargar();
})();