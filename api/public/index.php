<?php
declare(strict_types=1);

/**
 * Punto de entrada único de la API (front controller).
 * Todas las peticiones llegan aquí gracias al .htaccess.
 */

// Carpeta raíz de la API (un nivel arriba de public/)
define('BASE_PATH', dirname(__DIR__));

require BASE_PATH . '/app/core/Logger.php';
require BASE_PATH . '/app/core/Response.php';
require BASE_PATH . '/app/core/Database.php';
require BASE_PATH . '/app/core/Router.php';

// Carga automática: cuando el código usa una clase (ej. Negocio), PHP la busca
// en estas carpetas y la incluye solo si hace falta.
spl_autoload_register(static function (string $clase): void {
    foreach (['/app/models/', '/app/controllers/'] as $carpeta) {
        $archivo = BASE_PATH . $carpeta . $clase . '.php';

        if (is_file($archivo)) {
            require $archivo;
            return;
        }
    }
});

Logger::configurar(BASE_PATH . '/storage/logs');
date_default_timezone_set('America/Lima');

// Nunca se muestran errores de PHP en pantalla: van a un archivo de registro.
ini_set('display_errors', '0');
ini_set('log_errors', '1');
ini_set('error_log', BASE_PATH . '/storage/logs/php-error.log');
error_reporting(E_ALL);

// Cualquier excepción no controlada termina en un JSON 500 genérico.
$config = null;
set_exception_handler(function (Throwable $e) use (&$config): void {
    Logger::error(get_class($e) . ': ' . $e->getMessage());

    $detalle = [];
    if ($config !== null && $config['app']['debug']) {
        $detalle = ['mensaje' => $e->getMessage()]; // solo en desarrollo
    }

    Response::error(500, 'ERROR_INTERNO', 'Ocurrió un error interno en el servidor.', $detalle);
});

$config = require BASE_PATH . '/app/config/config.php';

// CORS: solo la web autorizada puede consultar la API desde el navegador.
header('Access-Control-Allow-Origin: ' . $config['cors_origin']);
header('Vary: Origin');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

Database::configurar($config['db']);

// Carpeta donde está public/ respecto al dominio.
// XAMPP: /huantacerca11/api/public   |   Producción (raíz del dominio): vacío.
$base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');

$router = new Router($base);
require BASE_PATH . '/routes/api.php';
$router->despachar($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);