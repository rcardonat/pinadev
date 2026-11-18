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
    /**
     * Efecto de transición del slider. Valores permitidos:
     *   'slide' (deslizar), 'fade' (desvanecer), 'zoom', 'cube' (cubo 3D),
     *   'flip' (voltear) o 'random' (uno distinto al azar en cada cambio).
     */
    public const EFFECT = 'cube';

    private const EFFECTS = ['slide', 'fade', 'zoom', 'cube', 'flip', 'random'];

    /** Efecto validado contra la lista permitida (por defecto, 'slide'). */
    public static function effect(): string
    {
        return in_array(self::EFFECT, self::EFFECTS, true) ? self::EFFECT : 'slide';
    }

    /** @return list<array{title: string, text: string, cta: string, link: string, theme: int}> */
    public static function all(): array
    {
        return [
            ['title' => 'Juegos indie hechos a mano',        'text' => 'Pequeños mundos creados de principio a fin por una sola persona.', 'cta' => 'Ver mis juegos', 'link' => 'juegos',   'theme' => 1],
            ['title' => 'Ya disponible: Ecos del Bosque',    'text' => 'Mi lanzamiento más reciente. Pruébalo en Steam o itch.io.',        'cta' => 'Descúbrelo',     'link' => 'juegos',   'theme' => 2],
            ['title' => '¿Prensa o creador de contenido?',   'text' => 'Pide una clave, una entrevista o propón una colaboración.',        'cta' => 'Escríbeme',      'link' => 'contacto', 'theme' => 3],
        ];
    }
}
