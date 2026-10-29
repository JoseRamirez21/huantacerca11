<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($titulo ?? 'Huanta Cerca') ?> | Huanta Cerca</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= e(url('/assets/css/app.css')) ?>">
</head>
<body data-api="<?= e(API_URL) ?>" data-base="<?= e(BASE_URL) ?>">

<header class="site-header">
    <nav class="navbar">
        <div class="container">
            <a class="navbar-brand" href="<?= e(url('/')) ?>">Huanta Cerca</a>
            <a class="nav-link" href="<?= e(url('/')) ?>">Inicio</a>
        </div>
    </nav>
</header>

<main>
    <?= $contenido ?>
</main>

<footer class="site-footer">
    <div class="container">
        Huanta Cerca · Negocios y servicios locales de Huanta, Ayacucho
    </div>
</footer>

<script src="<?= e(url('/assets/js/api.js')) ?>"></script>
<?php foreach (($scripts ?? []) as $script): ?>
<script src="<?= e(url('/assets/js/' . $script)) ?>"></script>
<?php endforeach; ?>
</body>
</html>