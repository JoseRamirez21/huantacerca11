<?php
declare(strict_types=1);

/**
 * Controlador de las páginas públicas.
 * Solo prepara la página: los datos de negocios los pide el JavaScript a la API.
 */
final class PublicController
{
    /** GET / */
    public function inicio(array $params): void
    {
        View::render('public/inicio', [
            'titulo'  => 'Inicio',
            'scripts' => ['inicio.js'],
        ]);
    }

    /** GET /negocio/{id} */
    public function detalle(array $params): void
    {
        $id = $params['id'] ?? '';

        if (!ctype_digit($id) || (int) $id < 1) {
            $this->noEncontrado();
            return;
        }

        View::render('public/detalle', [
            'titulo'  => 'Detalle del negocio',
            'id'      => (int) $id,
            'scripts' => ['detalle.js'],
        ]);
    }

    /** Página 404 */
    public function noEncontrado(): void
    {
        http_response_code(404);

        View::render('public/404', ['titulo' => 'Página no encontrada']);
    }
}