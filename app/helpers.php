<?php
declare(strict_types=1);

/**
 * Funciones globales para las vistas. Cargado por Composer ("autoload.files").
 */

/**
 * Icono SVG del sprite public/assets/icons.svg (generado con tools/build-icons.php).
 *
 * Devuelve HTML seguro: el nombre se valida y todo se escapa aquí, por eso se
 * imprime directamente en las vistas: <?= icon('mail') ?>
 *
 * @param string $name  Id del icono (nombre del archivo sin prefijo), p. ej. 'chevron-left'.
 * @param string $label Texto accesible. Vacío = decorativo (oculto a lectores de pantalla);
 *                      úsalo solo si el icono va SIN texto visible al lado.
 * @param string $class Clases CSS extra.
 */
function icon(string $name, string $label = '', string $class = ''): string
{
    static $href = null;
    static $ids = [];

    if (!preg_match('/^[a-z0-9-]+$/', $name)) {
        return '';
    }

    if ($href === null) {
        $sprite = dirname(__DIR__) . '/public/assets/icons.svg';
        $version = '0';
        if (is_file($sprite)) {
            $version = (string) filemtime($sprite);
            preg_match_all('/<symbol id="([a-z0-9-]+)"/', (string) file_get_contents($sprite), $m);
            $ids = array_flip($m[1]);
        }
        $href = Base::instance()->get('BASE') . '/assets/icons.svg?v=' . $version;
    }

    // Si el icono aún no está en el sprite, no se dibuja un hueco vacío.
    if (!isset($ids[$name])) {
        return '';
    }

    $e = static fn (string $s): string => htmlspecialchars($s, ENT_QUOTES | ENT_HTML5, 'UTF-8');

    $classes = trim('icon icon-' . $name . ' ' . $class);
    $a11y = $label === ''
        ? 'aria-hidden="true"'
        : 'role="img" aria-label="' . $e($label) . '"';

    return '<svg class="' . $e($classes) . '" ' . $a11y . ' focusable="false">'
        . '<use href="' . $e($href) . '#' . $name . '"></use></svg>';
}
