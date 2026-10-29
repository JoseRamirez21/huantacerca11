<?php
declare(strict_types=1);

/**
 * Registro de errores técnicos en storage/logs/app.log.
 * El usuario final nunca ve estos detalles; solo quien administra el servidor.
 */
final class Logger
{
    private static string $carpeta = '';

    public static function configurar(string $carpeta): void
    {
        self::$carpeta = $carpeta;
    }

    public static function error(string $mensaje): void
    {
        if (self::$carpeta === '' || !is_dir(self::$carpeta)) {
            return;
        }

        $linea = sprintf('[%s] %s%s', date('Y-m-d H:i:s'), $mensaje, PHP_EOL);
        @file_put_contents(self::$carpeta . '/app.log', $linea, FILE_APPEND | LOCK_EX);
    }
}