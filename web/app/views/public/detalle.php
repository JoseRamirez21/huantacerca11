<div class="container py-4">
    <a href="<?= e(url('/')) ?>" class="btn btn-link px-0 mb-3">← Volver al listado</a>

    <div id="detalle" data-id="<?= (int) $id ?>">

        <div id="d-carga" class="text-center py-5">
            <div class="spinner-border" role="status">
                <span class="visually-hidden">Cargando…</span>
            </div>
        </div>

        <div id="d-error" class="estado-caja text-center" role="alert" hidden>
            <h2 class="h5">No pudimos cargar el negocio</h2>
            <p id="d-mensaje-error" class="text-muted mb-3"></p>
            <button type="button" id="d-reintentar" class="btn btn-primary btn-sm">Reintentar</button>
        </div>

        <div id="d-nodisponible" class="estado-caja text-center" hidden>
            <h2 class="h5">Este negocio no está disponible</h2>
            <p class="text-muted mb-3">Puede que ya no esté publicado o que la dirección sea incorrecta.</p>
            <a href="<?= e(url('/')) ?>" class="btn btn-primary btn-sm">Ver otros negocios</a>
        </div>

        <article id="d-contenido" class="card shadow-sm detalle-card" hidden>
            <div class="card-body p-4 p-md-5">
                <span id="d-categoria" class="badge etiqueta-categoria mb-3"></span>
                <h1 id="d-nombre" class="h2"></h1>
                <p id="d-descripcion" class="text-muted"></p>

                <dl id="d-datos" class="row mt-4"></dl>
                <p id="d-sin-datos" class="text-muted" hidden>Este negocio aún no registra datos de contacto.</p>

                <div id="d-acciones" class="d-flex flex-wrap gap-2 mt-2"></div>
            </div>
        </article>

    </div>
</div>