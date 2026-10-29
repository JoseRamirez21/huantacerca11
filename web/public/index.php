<?php
declare(strict_types=1);

/**
 * Punto de entrada único de la web (front controller).
 */

define('BASE_PATH', dirname(__DIR__));

require BASE_PATH . '/app/core/helpers.php';
require BASE_PATH . '/app/core/View.php';
require BASE_PATH . '/app/core/Router.php';

// Carga automática de controladores.
spl_autoload_register(static function (string $clase): void {
    $archivo = BASE_PATH . '/app/controllers/' . $clase . '.php';

    if (is_file($archivo)) {
        require $archivo;
    }
});

date_default_timezone_set('America/Lima');

// Nunca se muestran errores de PHP en pantalla: van a un archivo de registro.
ini_set('display_errors', '0');
ini_set('log_errors', '1');
ini_set('error_log', BASE_PATH . '/storage/logs/php-error.log');
error_reporting(E_ALL);

// Cualquier excepción no controlada muestra una página genérica.
set_exception_handler(static function (Throwable $e): void {
    error_log(get_class($e) . ': ' . $e->getMessage());
    http_response_code(500);
    echo '<!DOCTYPE html><html lang="es"><meta charset="utf-8"><title>Error</title>'
       . '<body style="font-family:sans-serif;padding:2rem"><h1>Algo salió mal</h1>'
       . '<p>Ocurrió un error inesperado. Inténtalo de nuevo en unos minutos.</p></body></html>';
});

$config = require BASE_PATH . '/app/config/config.php';

// Carpeta donde está public/ respecto al dominio.
// XAMPP: /huantacerca11/web/public   |   Producción (raíz del dominio): vacío.
$base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');

define('BASE_URL', $base);
define('API_URL', $config['api_url']);

$router = new Router($base);
require BASE_PATH . '/routes/web.php';

$router->despachar(
    $_SERVER['REQUEST_METHOD'],
    $_SERVER['REQUEST_URI'],
    static function (): void {
        (new PublicController())->noEncontrado();
    }
);