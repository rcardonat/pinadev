<?php
declare(strict_types=1);

namespace App\Models;

/**
 * Diapositivas del slider de inicio.
 *
 * Datos fijos de ejemplo; más adelante pueden venir de MySQL sin cambiar
 * controlador ni vista. 'link' es un alias de ruta (ver app/bootstrap.php)
 * y 'theme' una variante visual definida en app.css (.slide--1, .slide--2...).
 */
final class Slide
{
    /** @return list<array{title: string, text: string, cta: string, link: string, theme: int}> */
    public static function all(): array
    {
        return [
            ['title' => 'Bienvenido a PinaDev',    'text' => 'Soluciones digitales pensadas para crecer contigo.',     'cta' => 'Conócenos',       'link' => 'nosotros',  'theme' => 1],
            ['title' => 'Nuestros productos',      'text' => 'Descubre el catálogo y encuentra lo que necesitas.',     'cta' => 'Ver productos',   'link' => 'productos', 'theme' => 2],
            ['title' => 'Hablemos de tu proyecto', 'text' => 'Cuéntanos tu idea y te ayudamos a hacerla realidad.',    'cta' => 'Contáctanos',     'link' => 'contacto',  'theme' => 3],
        ];
    }
}
