<?php
declare(strict_types=1);

/**
 * Controlador de negocios (parte pública: solo lectura).
 */
final class NegocioController
{
    private const LIMITE = 50;          // máximo de resultados por consulta
    private const MAX_BUSQUEDA = 100;   // máximo de caracteres en ?buscar=

    /** GET /negocios   (opcional: ?buscar=texto  ?categoria=2) */
    public function listar(array $params): void
    {
        $errores = [];
        $buscar = null;
        $categoriaId = null;

        // --- Validación de ?buscar= ---
        if (isset($_GET['buscar'])) {
            if (!is_string($_GET['buscar'])) {
                $errores['buscar'] = 'Debe ser un texto.';
            } else {
                $texto = trim($_GET['buscar']);

                if (mb_strlen($texto) > self::MAX_BUSQUEDA) {
                    $errores['buscar'] = 'Máximo ' . self::MAX_BUSQUEDA . ' caracteres.';
                } elseif ($texto !== '') {
                    $buscar = $texto;
                }
            }
        }

        // --- Validación de ?categoria= ---
        if (isset($_GET['categoria']) && $_GET['categoria'] !== '') {
            $valor = $_GET['categoria'];

            if (!is_string($valor) || !ctype_digit($valor) || (int) $valor < 1) {
                $errores['categoria'] = 'Debe ser un número entero positivo.';
            } else {
                $categoriaId = (int) $valor;
            }
        }

        if ($errores !== []) {
            Response::error(400, 'DATOS_INVALIDOS', 'Los parámetros de búsqueda no son válidos.', $errores);
        }

        $filas = (new Negocio())->listarPublicos($buscar, $categoriaId, self::LIMITE);

        Response::ok(array_map([$this, 'formatear'], $filas), 'Negocios obtenidos');
    }

    /** GET /negocios/{id} */
    public function detalle(array $params): void
    {
        $id = $params['id'] ?? '';

        if (!ctype_digit($id) || (int) $id < 1) {
            Response::error(400, 'DATOS_INVALIDOS', 'El identificador del negocio no es válido.');
        }

        $fila = (new Negocio())->buscarPublico((int) $id);

        // Un negocio inactivo responde igual que uno inexistente: no se revela su existencia.
        if ($fila === null) {
            Response::error(404, 'NEGOCIO_NO_ENCONTRADO', 'El negocio solicitado no existe o no está disponible.');
        }

        Response::ok($this->formatear($fila), 'Negocio encontrado');
    }

    /** Da forma al JSON público: solo los datos destinados a publicarse. */
    private function formatear(array $f): array
    {
        $telefono = $f['telefono'];

        return [
            'id'          => (int) $f['id'],
            'nombre'      => $f['nombre'],
            'descripcion' => $f['descripcion'],
            'direccion'   => $f['direccion'],
            'telefono'    => $telefono,
            // El enlace de WhatsApp se arma aquí, en un solo lugar (51 = Perú).
            'whatsapp'    => $telefono !== null ? 'https://wa.me/51' . $telefono : null,
            'horario'     => $f['horario'],
            'imagen'      => $f['imagen'],
            'categoria'   => [
                'id'     => (int) $f['categoria_id'],
                'nombre' => $f['categoria_nombre'],
            ],
        ];
    }
}