<?php
declare(strict_types=1);

/**
 * Modelo Negocio: todo lo que se hace con la tabla "negocios".
 *
 * Regla de visibilidad pública: un negocio se muestra solo si
 * el negocio está activo Y su categoría también está activa.
 */
final class Negocio
{
    private PDO $db;

    /** Columnas que se devuelven (nunca estado ni fechas internas). */
    private const COLUMNAS = 'n.id, n.nombre, n.descripcion, n.direccion, n.telefono,
                              n.horario, n.imagen, n.categoria_id, c.nombre AS categoria_nombre';

    public function __construct()
    {
        $this->db = Database::conexion();
    }

    /**
     * Lista negocios públicos, con búsqueda y filtro opcionales.
     * Las condiciones se arman con textos fijos; los datos del usuario
     * viajan SIEMPRE como parámetros de la consulta preparada.
     */
    public function listarPublicos(?string $buscar, ?int $categoriaId, int $limite): array
    {
        $condiciones = ['n.estado = 1', 'c.estado = 1'];

        if ($categoriaId !== null) {
            $condiciones[] = 'n.categoria_id = :categoria';
        }

        if ($buscar !== null) {
            // Con consultas preparadas reales, un parámetro no puede repetirse:
            // por eso hay dos nombres distintos (:buscar_nombre y :buscar_desc).
            $condiciones[] = '(n.nombre LIKE :buscar_nombre OR n.descripcion LIKE :buscar_desc)';
        }

        $sql = 'SELECT ' . self::COLUMNAS . '
                FROM negocios n
                INNER JOIN categorias c ON c.id = n.categoria_id
                WHERE ' . implode(' AND ', $condiciones) . '
                ORDER BY n.nombre ASC
                LIMIT :limite';

        $stmt = $this->db->prepare($sql);

        if ($categoriaId !== null) {
            $stmt->bindValue(':categoria', $categoriaId, PDO::PARAM_INT);
        }

        if ($buscar !== null) {
            $patron = '%' . $this->escaparLike($buscar) . '%';
            $stmt->bindValue(':buscar_nombre', $patron);
            $stmt->bindValue(':buscar_desc', $patron);
        }

        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /** Un negocio público por id, o null si no existe o no es visible. */
    public function buscarPublico(int $id): ?array
    {
        $sql = 'SELECT ' . self::COLUMNAS . '
                FROM negocios n
                INNER JOIN categorias c ON c.id = n.categoria_id
                WHERE n.id = :id AND n.estado = 1 AND c.estado = 1';

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $fila = $stmt->fetch();

        return $fila === false ? null : $fila;
    }

    /** Evita que % y _ escritos por el usuario actúen como comodines de LIKE. */
    private function escaparLike(string $texto): string
    {
        return str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $texto);
    }
}