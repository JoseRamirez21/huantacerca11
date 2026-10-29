/**
 * Página de inicio: buscador, filtros por categoría y tarjetas de negocios.
 * Para mostrar texto usamos textContent (nunca innerHTML): así, aunque un dato
 * contenga código HTML, el navegador lo muestra como texto y no lo ejecuta (XSS).
 */
(() => {
    'use strict';

    const base = document.body.dataset.base;
    const $ = (id) => document.getElementById(id);

    const ui = {
        form: $('form-busqueda'),
        input: $('buscar'),
        categorias: $('categorias'),
        contador: $('contador'),
        carga: $('estado-carga'),
        vacio: $('estado-vacio'),
        mensajeVacio: $('mensaje-vacio'),
        limpiar: $('btn-limpiar'),
        error: $('estado-error'),
        mensajeError: $('mensaje-error'),
        reintentar: $('btn-reintentar'),
        lista: $('lista'),
    };

    const estado = { buscar: '', categoria: '' };
    let numeroSolicitud = 0;   // evita que una respuesta lenta pise a una más reciente
    let temporizador = null;   // espera breve mientras el usuario escribe

    /* ---------- Utilidades ---------- */

    function el(etiqueta, clases, texto) {
        const nodo = document.createElement(etiqueta);
        if (clases) nodo.className = clases;
        if (texto !== undefined && texto !== null) nodo.textContent = texto;
        return nodo;
    }

    function mostrarSolo(nombre) {
        ui.carga.hidden = nombre !== 'carga';
        ui.vacio.hidden = nombre !== 'vacio';
        ui.error.hidden = nombre !== 'error';
        ui.lista.hidden = nombre !== 'lista';
    }

    function hayFiltros() {
        return estado.buscar !== '' || estado.categoria !== '';
    }

    function esWhatsappValido(enlace) {
        return typeof enlace === 'string' && enlace.startsWith('https://wa.me/');
    }

    function sincronizarUrl() {
        const params = new URLSearchParams();
        if (estado.buscar) params.set('buscar', estado.buscar);
        if (estado.categoria) params.set('categoria', estado.categoria);
        const consulta = params.toString();
        history.replaceState(null, '', location.pathname + (consulta ? '?' + consulta : ''));
    }

    /* ---------- Tarjetas ---------- */

    function filaDato(icono, texto) {
        const li = el('li', 'mb-1');
        li.append(el('span', 'me-2', icono), document.createTextNode(texto));
        return li;
    }

    function crearTarjeta(n) {
        const columna = el('div', 'col-12 col-md-6 col-lg-4');
        const tarjeta = el('article', 'card h-100 negocio-card');
        const cuerpo = el('div', 'card-body d-flex flex-column');

        cuerpo.append(
            el('span', 'badge etiqueta-categoria mb-2 align-self-start', n.categoria.nombre),
            el('h2', 'h5 card-title', n.nombre),
            el('p', 'card-text text-muted descripcion', n.descripcion || 'Sin descripción disponible.')
        );

        const datos = el('ul', 'list-unstyled small mb-3 mt-auto');
        if (n.direccion) datos.append(filaDato('📍', n.direccion));
        if (n.horario) datos.append(filaDato('🕒', n.horario));
        cuerpo.append(datos);

        const acciones = el('div', 'd-flex flex-wrap gap-2');

        const detalle = el('a', 'btn btn-primary btn-sm', 'Ver detalles');
        detalle.href = base + '/negocio/' + encodeURIComponent(n.id);
        acciones.append(detalle);

        if (esWhatsappValido(n.whatsapp)) {
            const wa = el('a', 'btn btn-whatsapp btn-sm', 'WhatsApp');
            wa.href = n.whatsapp;
            wa.target = '_blank';
            wa.rel = 'noopener noreferrer';
            acciones.append(wa);
        }

        cuerpo.append(acciones);
        tarjeta.append(cuerpo);
        columna.append(tarjeta);
        return columna;
    }

    function pintarNegocios(negocios) {
        ui.lista.replaceChildren();

        if (negocios.length === 0) {
            ui.contador.textContent = '';
            ui.mensajeVacio.textContent = hayFiltros()
                ? 'Prueba con otra palabra o elige otra categoría.'
                : 'Todavía no hay negocios publicados.';
            ui.limpiar.hidden = !hayFiltros();
            mostrarSolo('vacio');
            return;
        }

        negocios.forEach((n) => ui.lista.append(crearTarjeta(n)));
        ui.contador.textContent = negocios.length === 1 ? '1 resultado' : negocios.length + ' resultados';
        mostrarSolo('lista');
    }

    /* ---------- Carga de datos ---------- */

    async function cargarNegocios() {
        const solicitud = ++numeroSolicitud;
        mostrarSolo('carga');
        ui.contador.textContent = '';

        try {
            const negocios = await Api.get('/negocios', {
                buscar: estado.buscar,
                categoria: estado.categoria,
            });

            if (solicitud !== numeroSolicitud) return;
            pintarNegocios(negocios);
        } catch (error) {
            if (solicitud !== numeroSolicitud) return;
            ui.mensajeError.textContent = error.message;
            mostrarSolo('error');
        }
    }

    function marcarChips() {
        ui.categorias.querySelectorAll('.chip').forEach((boton) => {
            const activo = boton.dataset.id === estado.categoria;
            boton.classList.toggle('chip-activo', activo);
            boton.setAttribute('aria-pressed', String(activo));
        });
    }

    function pintarCategorias(categorias) {
        ui.categorias.replaceChildren();

        [{ id: '', nombre: 'Todas' }, ...categorias].forEach((c) => {
            const boton = el('button', 'btn btn-sm chip', c.nombre);
            boton.type = 'button';
            boton.dataset.id = String(c.id);
            boton.addEventListener('click', () => {
                estado.categoria = String(c.id);
                marcarChips();
                sincronizarUrl();
                cargarNegocios();
            });
            ui.categorias.append(boton);
        });

        marcarChips();
    }

    /* ---------- Eventos ---------- */

    function actualizarBusqueda() {
        estado.buscar = ui.input.value.trim();
        sincronizarUrl();
        cargarNegocios();
    }

    // Busca mientras se escribe, pero espera 350 ms de pausa para no saturar la API.
    ui.input.addEventListener('input', () => {
        clearTimeout(temporizador);
        temporizador = setTimeout(actualizarBusqueda, 350);
    });

    ui.form.addEventListener('submit', (evento) => {
        evento.preventDefault();
        clearTimeout(temporizador);
        actualizarBusqueda();
    });

    ui.reintentar.addEventListener('click', cargarNegocios);

    ui.limpiar.addEventListener('click', () => {
        estado.buscar = '';
        estado.categoria = '';
        ui.input.value = '';
        marcarChips();
        sincronizarUrl();
        cargarNegocios();
    });

    /* ---------- Inicio ---------- */

    function iniciar() {
        // Si la dirección trae ?buscar= o ?categoria=, se respetan (con validación).
        const params = new URLSearchParams(location.search);
        estado.buscar = (params.get('buscar') || '').slice(0, 100).trim();

        const categoria = params.get('categoria') || '';
        estado.categoria = /^[1-9][0-9]*$/.test(categoria) ? categoria : '';

        ui.input.value = estado.buscar;

        Api.get('/categorias')
            .then(pintarCategorias)
            .catch(() => {
                ui.categorias.replaceChildren(
                    el('span', 'text-muted small', 'No se pudieron cargar las categorías.')
                );
            });

        cargarNegocios();
    }

    iniciar();
})();