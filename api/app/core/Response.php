<?php
declare(strict_types=1);

/**
 * Respuestas JSON con formato uniforme.
 *
 * Éxito: { "ok": true,  "message": "...", "data": ... }
 * Error: { "ok": false, "error": { "code": "...", "message": "...", "details": ... } }
 */
final class Response
{
    public static function ok(mixed $data = null, string $message = 'OK', int $status = 200): never
    {
        self::enviar($status, [
            'ok'      => true,
            'message' => $message,
            'data'    => $data,
        ]);
    }

    public static function error(int $status, string $code, string $message, array $details = []): never
    {
        $error = ['code' => $code, 'message' => $message];

        if ($details !== []) {
            $error['details'] = $details;
        }

        self::enviar($status, ['ok' => false, 'error' => $error]);
    }

    private static function enviar(int $status, array $payload): never
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
        exit;
    }
}