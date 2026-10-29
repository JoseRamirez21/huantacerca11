<?php
declare(strict_types=1);

/**
 * Escapa texto antes de mostrarlo en HTML (protección contra XSS).
 * Toda variable que se imprime en una vista debe pasar por aquí.
 */
function e(?string $texto): string
{
    return htmlspecialchars($texto ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Construye una dirección interna de la web: url('/negocio/3'). */
function url(string $ruta = ''): string
{
    return BASE_URL . $ruta;
}