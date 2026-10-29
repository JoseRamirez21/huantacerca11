<?php
declare(strict_types=1);

/**
 * Rutas de la API. Este archivo lo incluye public/index.php,
 * por eso aquí ya existen $router y $config.
 */

// Prueba más simple: la API responde (no usa base de datos).
$router->get('/', function (): void {
    Response::ok(['api' => 'Huanta Cerca', 'version' => '1.0'], 'API en funcionamiento');
});

// Diagnóstico: confirma que la base de datos conecta.
$router->get('/salud', function () use ($config): void {
    $pdo = Database::conexion();
    $total = (int) $pdo->query('SELECT COUNT(*) AS total FROM negocios')->fetch()['total'];

    $datos = ['base_de_datos' => 'conectada'];

    // Detalles internos solo en desarrollo; en producción no se exponen.
    if ($config['app']['debug']) {
        $datos['version_mysql']        = $pdo->query('SELECT VERSION() AS v')->fetch()['v'];
        $datos['negocios_registrados'] = $total;
    }

    Response::ok($datos, 'API operativa');
});

// ---- Lectura pública ----
$router->get('/categorias',     fn (array $p) => (new CategoriaController())->listar($p));
$router->get('/negocios',       fn (array $p) => (new NegocioController())->listar($p));
$router->get('/negocios/{id}',  fn (array $p) => (new NegocioController())->detalle($p));