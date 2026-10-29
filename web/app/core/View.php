<?php
declare(strict_types=1);

/**
 * Renderiza una vista dentro de un layout.
 * Las vistas son plantillas HTML con un poco de PHP; no consultan la base de datos.
 */
final class View
{
    public static function render(string $vista, array $datos = [], string $layout = 'public'): void
    {
        // Convierte las claves del arreglo en variables para la vista ($titulo, etc.).
        extract($datos, EXTR_SKIP);

        ob_start();
        require BASE_PATH . '/app/views/' . $vista . '.php';
        $contenido = ob_get_clean();

        require BASE_PATH . '/app/views/layouts/' . $layout . '.php';
    }
}