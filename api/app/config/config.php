<?php
declare(strict_types=1);

/**
 * Configuración de la API.
 * Lee el archivo .env (que está fuera de public/, por lo que el navegador
 * no puede descargarlo) y devuelve un arreglo con los valores.
 * Requiere que BASE_PATH esté definido (lo hace public/index.php).
 */

$leerEnv = static function (string $ruta): array {
    if (!is_readable($ruta)) {
        throw new RuntimeException('No se encontró el archivo .env de la API.');
    }

    $valores = [];
    $lineas = file($ruta, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lineas as $linea) {
        $linea = trim($linea);

        // Ignora comentarios y líneas sin "="
        if ($linea === '' || $linea[0] === '#' || !str_contains($linea, '=')) {
            continue;
        }

        [$clave, $valor] = explode('=', $linea, 2);
        $valores[trim($clave)] = trim($valor);
    }

    return $valores;
};

$env = $leerEnv(BASE_PATH . '/.env');

return [
    'app' => [
        'env'   => $env['APP_ENV'] ?? 'production',
        'debug' => ($env['APP_DEBUG'] ?? 'false') === 'true',
    ],
    'db' => [
        'host' => $env['DB_HOST'] ?? '127.0.0.1',
        'port' => (int) ($env['DB_PORT'] ?? 3306),
        'name' => $env['DB_NAME'] ?? '',
        'user' => $env['DB_USER'] ?? '',
        'pass' => $env['DB_PASS'] ?? '',
    ],
    'cors_origin' => $env['CORS_ORIGIN'] ?? '',
];