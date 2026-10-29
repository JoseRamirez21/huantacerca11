<section class="hero">
    <div class="container">
        <h1 class="display-6 fw-bold">Encuentra negocios y servicios en Huanta</h1>
        <p class="lead mb-0">Busca por nombre o explora por categoría y contáctalos directamente.</p>
    </div>
</section>

<section class="container buscador-wrap">
    <div class="card buscador-card shadow-sm">
        <div class="card-body p-3 p-md-4">
            <form id="form-busqueda" role="search" novalidate>
                <label for="buscar" class="visually-hidden">Buscar negocios</label>
                <div class="input-group input-group-lg">
                    <input type="search" id="buscar" class="form-control"
                           placeholder="Ej.: imprenta, farmacia, restaurante"
                           maxlength="100" autocomplete="off">
                    <button class="btn btn-primary" type="submit">Buscar</button>
                </div>
            </form>

            <div id="categorias" class="d-flex flex-wrap gap-2 mt-3" aria-label="Filtrar por categoría"></div>
        </div>
    </div>
</section>

<section class="container py-4">
    <p id="contador" class="text-muted small mb-3" aria-live="polite"></p>

    <div id="estado-carga" class="text-center py-5" hidden>
        <div class="spinner-border" role="status">
            <span class="visually-hidden">Cargando…</span>
        </div>
    </div>

    <div id="estado-vacio" class="estado-caja text-center" hidden>
        <h2 class="h5">No encontramos negocios</h2>
        <p id="mensaje-vacio" class="text-muted mb-3"></p>
        <button type="button" id="btn-limpiar" class="btn btn-outline-secondary btn-sm" hidden>Limpiar filtros</button>
    </div>

    <div id="estado-error" class="estado-caja text-center" role="alert" hidden>
        <h2 class="h5">No pudimos cargar los negocios</h2>
        <p id="mensaje-error" class="text-muted mb-3"></p>
        <button type="button" id="btn-reintentar" class="btn btn-primary btn-sm">Reintentar</button>
    </div>

    <div id="lista" class="row g-3" hidden></div>
</section>