<?php
declare(strict_types=1);

/**
 * Enrutador mínimo.
 * Asocia método HTTP + ruta con una función. Soporta parámetros como {id}.
 *
 * $base es la carpeta donde vive la API cuando no está en la raíz del dominio
 * (en XAMPP: /huantacerca11/api/public). Se descuenta de la dirección antes
 * de buscar la ruta. En producción suele ser cadena vacía.
 */
final class Router
{
    private array $rutas = [];

    public function __construct(private string $base = '')
    {
    }

    public function get(string $patron, callable $accion): void
    {
        $this->agregar('GET', $patron, $accion);
    }

    public function post(string $patron, callable $accion): void
    {
        $this->agregar('POST', $patron, $accion);
    }

    public function put(string $patron, callable $accion): void
    {
        $this->agregar('PUT', $patron, $accion);
    }

    public function delete(string $patron, callable $accion): void
    {
        $this->agregar('DELETE', $patron, $accion);
    }

    private function agregar(string $metodo, string $patron, callable $accion): void
    {
        // Convierte "/negocios/{id}" en una expresión regular con grupo "id".
        $regex = '#^' . preg_replace('#\{([a-z_]+)\}#', '(?P<$1>[^/]+)', $patron) . '$#';

        $this->rutas[] = [
            'metodo' => $metodo,
            'regex'  => $regex,
            'accion' => $accion,
        ];
    }

    public function despachar(string $metodo, string $uri): void
    {
        $ruta = parse_url($uri, PHP_URL_PATH) ?: '/';

        // Quita la carpeta base (ej. /huantacerca11/api/public en XAMPP).
        if ($this->base !== '' && str_starts_with($ruta, $this->base)) {
            $ruta = substr($ruta, strlen($this->base));
        }

        // Normaliza: siempre empieza con "/" y no termina con "/".
        $ruta = '/' . trim($ruta, '/');

        $rutaExiste = false;

        foreach ($this->rutas as $r) {
            if (preg_match($r['regex'], $ruta, $coincidencias) === 1) {
                $rutaExiste = true;

                if ($r['metodo'] === $metodo) {
                    // Solo los grupos con nombre (ej. "id"), sin los numéricos.
                    $parametros = array_filter($coincidencias, 'is_string', ARRAY_FILTER_USE_KEY);
                    ($r['accion'])($parametros);
                    return;
                }
            }
        }

        if ($rutaExiste) {
            Response::error(405, 'METODO_NO_PERMITIDO', 'Método HTTP no permitido para esta ruta.');
        }

        Response::error(404, 'NO_ENCONTRADO', 'La ruta solicitada no existe.');
    }
}