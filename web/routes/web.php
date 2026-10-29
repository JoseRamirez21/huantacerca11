<?php
declare(strict_types=1);

/**
 * Rutas de la web. Este archivo lo incluye public/index.php,
 * por eso aquí ya existe $router.
 */

$router->get('/',              fn (array $p) => (new PublicController())->inicio($p));
$router->get('/negocio/{id}',  fn (array $p) => (new PublicController())->detalle($p));