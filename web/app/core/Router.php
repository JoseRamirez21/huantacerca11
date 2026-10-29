<?php
declare(strict_types=1);

/**
 * Enrutador de la web. Mismo principio que el de la API,
 * pero cuando no encuentra la ruta muestra una página HTML.
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

    private function agregar(string $metodo, string $patron, callable $accion): void
    {
        $regex = '#^' . preg_replace('#\{([a-z_]+)\}#', '(?P<$1>[^/]+)', $patron) . '$#';

        $this->rutas[] = [
            'metodo' => $metodo,
            'regex'  => $regex,
            'accion' => $accion,
        ];
    }

    public function despachar(string $metodo, string $uri, callable $noEncontrado): void
    {
        $ruta = parse_url($uri, PHP_URL_PATH) ?: '/';

        if ($this->base !== '' && str_starts_with($ruta, $this->base)) {
            $ruta = substr($ruta, strlen($this->base));
        }

        $ruta = '/' . trim($ruta, '/');

        $rutaExiste = false;

        foreach ($this->rutas as $r) {
            if (preg_match($r['regex'], $ruta, $coincidencias) === 1) {
                $rutaExiste = true;

                if ($r['metodo'] === $metodo) {
                    $parametros = array_filter($coincidencias, 'is_string', ARRAY_FILTER_USE_KEY);
                    ($r['accion'])($parametros);
                    return;
                }
            }
        }

        if ($rutaExiste) {
            http_response_code(405);
            echo 'Método no permitido.';
            return;
        }

        $noEncontrado();
    }
}