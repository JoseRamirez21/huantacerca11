<?php
declare(strict_types=1);

/**
 * Conexión PDO única y reutilizable.
 *
 * Los modelos NO crean su propia conexión: todos llaman a Database::conexion().
 * La primera vez se conecta; las siguientes reutilizan la misma conexión.
 */
final class Database
{
    private static array $config = [];
    private static ?PDO $pdo = null;

    public static function configurar(array $db): void
    {
        self::$config = $db;
    }

    public static function conexion(): PDO
    {
        if (self::$pdo !== null) {
            return self::$pdo;
        }

        $c = self::$config;

        // utf8mb4 en el DSN: tildes, ñ y emojis se guardan y leen bien.
        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
            $c['host'],
            $c['port'],
            $c['name']
        );

        try {
            self::$pdo = new PDO($dsn, $c['user'], $c['pass'], [
                // Los errores de SQL lanzan excepciones, no fallan en silencio.
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                // Cada fila llega como arreglo asociativo: $fila['nombre'].
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                // Consultas preparadas reales en el servidor (no simuladas).
                PDO::ATTR_EMULATE_PREPARES   => false,
                // Si la BD no responde en 5 segundos, se corta.
                PDO::ATTR_TIMEOUT            => 5,
            ]);
        } catch (PDOException $e) {
            // El detalle técnico va al log; al usuario solo un mensaje genérico.
            Logger::error('Fallo de conexión PDO: ' . $e->getMessage());
            throw new RuntimeException('No se pudo conectar a la base de datos.');
        }

        return self::$pdo;
    }
}