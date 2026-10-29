<?php
declare(strict_types=1);

/**
 * Modelo Categoria: todo lo que se hace con la tabla "categorias".
 * Los modelos solo manejan datos; no conocen HTTP ni JSON.
 */
final class Categoria
{
    private PDO $db;

    public function __construct()
    {
        // Reutiliza la conexión única creada por Database.
        $this->db = Database::conexion();
    }

    /** Categorías activas, ordenadas por nombre (para el filtro público). */
    public function listarActivas(): array
    {
        $sql = 'SELECT id, nombre, descripcion
                FROM categorias
                WHERE estado = 1
                ORDER BY nombre ASC';

        return $this->db->query($sql)->fetchAll();
    }
}