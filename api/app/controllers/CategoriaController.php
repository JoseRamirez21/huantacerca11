<?php
declare(strict_types=1);

/**
 * Controlador de categorías (parte pública).
 * Recibe la petición, pide los datos al modelo y responde en JSON.
 */
final class CategoriaController
{
    /** GET /categorias */
    public function listar(array $params): void
    {
        $categorias = (new Categoria())->listarActivas();

        $datos = array_map(static fn (array $c): array => [
            'id'          => (int) $c['id'],
            'nombre'      => $c['nombre'],
            'descripcion' => $c['descripcion'],
        ], $categorias);

        Response::ok($datos, 'Categorías obtenidas');
    }
}