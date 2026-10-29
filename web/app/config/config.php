<?php
declare(strict_types=1);

/**
 * Configuración de la web.
 * Lee el archivo .env (fuera de public/) y devuelve un arreglo.
 * Requiere que BASE_PATH esté definido (lo hace public/index.php).
 */

$leerEnv = static function (string $ruta): array {
    if (!is_readable($ruta)) {
        throw new RuntimeException('No se encontró el archivo .env de la web.');
    }

    $valores = [];
    $lineas = file($ruta, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lineas as $linea) {
        $linea = trim($linea);

        if ($linea === '' || $linea[0] === '#' || !str_contains($linea, '=')) {
            continue;
        }

        [$clave, $valor] = explode('=', $linea, 2);
        $valores[trim($clave)] = trim($valor);
    }

    return $valores;
};

$env = $leerEnv(BASE_PATH . '/.env');

$apiUrl = rtrim($env['API_URL'] ?? '', '/');

if ($apiUrl === '') {
    throw new RuntimeException('Falta API_URL en el archivo .env de la web.');
}

return [
    'app' => [
        'env'   => $env['APP_ENV'] ?? 'production',
        'debug' => ($env['APP_DEBUG'] ?? 'false') === 'true',
    ],
    'api_url' => $apiUrl,
];