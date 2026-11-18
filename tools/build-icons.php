<?php
declare(strict_types=1);

/**
 * Genera public/assets/icons.svg (sprite) a partir de public/assets/icons/*.svg.
 *
 * Uso (desde la raíz del proyecto, con PHP 8.1+):
 *     php tools/build-icons.php
 *
 * - El id de cada icono es el nombre del archivo sin extensión ni prefijo de
 *   Iconify ("bi--chevron-left.svg" -> "chevron-left").
 * - Se sanea cada SVG con listas permitidas de elementos y atributos: se
 *   descartan scripts, eventos (on*), enlaces, estilos e ids.
 * - En producción solo se sube icons.svg (1 archivo); la carpeta icons/ es la
 *   fuente y se excluye del paquete de despliegue.
 *
 * Esta herramienta es de desarrollo: no se sube al hosting.
 */

$root    = dirname(__DIR__);
// Argumentos opcionales (para pruebas): [carpeta origen] [archivo destino].
$srcDir  = $argv[1] ?? $root . '/public/assets/icons';
$outFile = $argv[2] ?? $root . '/public/assets/icons.svg';

const SVG_NS = 'http://www.w3.org/2000/svg';

const ALLOWED_ELEMENTS = [
    'g', 'path', 'circle', 'ellipse', 'rect', 'line', 'polyline', 'polygon',
];

const ALLOWED_ATTRS = [
    'd', 'points', 'transform',
    'cx', 'cy', 'r', 'rx', 'ry', 'x', 'y', 'x1', 'y1', 'x2', 'y2', 'width', 'height',
    'fill', 'fill-rule', 'fill-opacity', 'clip-rule', 'opacity',
    'stroke', 'stroke-width', 'stroke-linecap', 'stroke-linejoin',
    'stroke-miterlimit', 'stroke-opacity', 'stroke-dasharray', 'stroke-dashoffset',
];

// Atributos de presentación del <svg> raíz que deben conservarse
// (en un <symbol> se perderían, así que se pasan a un <g> envolvente).
const INHERITED_ATTRS = [
    'fill', 'fill-rule', 'stroke', 'stroke-width', 'stroke-linecap', 'stroke-linejoin',
];

function iconId(string $file): string
{
    $name = basename($file, '.svg');
    $name = preg_replace('/^.*--/', '', $name);           // quita prefijo de Iconify
    $name = strtolower(preg_replace('/[^A-Za-z0-9-]+/', '-', $name));
    return trim($name, '-');
}

/** Copia recursivamente solo nodos y atributos permitidos. */
function copyClean(DOMElement $from, DOMElement $to, DOMDocument $doc): void
{
    foreach ($from->childNodes as $child) {
        if (!$child instanceof DOMElement) {
            continue;
        }
        if (!in_array($child->localName, ALLOWED_ELEMENTS, true)) {
            continue;
        }
        $el = $doc->createElementNS(SVG_NS, $child->localName);
        foreach ($child->attributes as $attr) {
            if (in_array($attr->name, ALLOWED_ATTRS, true)) {
                $value = $attr->value;
                if (preg_match('/url\s*\(|javascript:|expression\s*\(/i', $value)) {
                    continue;
                }
                $el->setAttribute($attr->name, $value);
            }
        }
        copyClean($child, $el, $doc);
        $to->appendChild($el);
    }
}

$files = glob($srcDir . '/*.svg') ?: [];
sort($files);

if (!$files) {
    fwrite(STDERR, "No hay archivos SVG en $srcDir\n");
    exit(1);
}

$out = new DOMDocument('1.0', 'UTF-8');
$out->formatOutput = true;
$sprite = $out->createElementNS(SVG_NS, 'svg');
$sprite->setAttribute('aria-hidden', 'true');
$out->appendChild($sprite);

$seen = [];
$errors = 0;

foreach ($files as $file) {
    $id = iconId($file);
    if ($id === '') {
        fwrite(STDERR, "Nombre inválido: $file\n");
        $errors++;
        continue;
    }
    if (isset($seen[$id])) {
        fwrite(STDERR, "Id duplicado \"$id\": $file y {$seen[$id]}\n");
        $errors++;
        continue;
    }

    $src = new DOMDocument();
    // LIBXML_NONET: sin acceso a red; sin LIBXML_NOENT: no se expanden entidades.
    if (!@$src->load($file, LIBXML_NONET)) {
        fwrite(STDERR, "SVG no válido: $file\n");
        $errors++;
        continue;
    }
    $svg = $src->documentElement;
    if (!$svg || $svg->localName !== 'svg') {
        fwrite(STDERR, "No es un SVG: $file\n");
        $errors++;
        continue;
    }

    $viewBox = $svg->getAttribute('viewBox');
    if (!preg_match('/^[\d.\s,-]+$/', $viewBox)) {
        fwrite(STDERR, "Falta viewBox válido: $file\n");
        $errors++;
        continue;
    }

    $symbol = $out->createElementNS(SVG_NS, 'symbol');
    $symbol->setAttribute('id', $id);
    $symbol->setAttribute('viewBox', $viewBox);

    $target = $symbol;
    $inherited = array_filter(INHERITED_ATTRS, fn ($a) => $svg->hasAttribute($a));
    if ($inherited) {
        $g = $out->createElementNS(SVG_NS, 'g');
        foreach ($inherited as $a) {
            $g->setAttribute($a, $svg->getAttribute($a));
        }
        $symbol->appendChild($g);
        $target = $g;
    }

    copyClean($svg, $target, $out);
    $sprite->appendChild($symbol);
    $seen[$id] = basename($file);
}

if ($errors) {
    fwrite(STDERR, "Se encontraron $errors error(es); no se generó el sprite.\n");
    exit(1);
}

$xml = $out->saveXML($sprite);
file_put_contents($outFile, $xml . "\n");

printf("Generado %s con %d iconos:\n  %s\n",
    str_replace($root . '/', '', $outFile), count($seen), implode(', ', array_keys($seen)));
