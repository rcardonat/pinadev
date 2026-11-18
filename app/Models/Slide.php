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
            ['title' => 'Bienvenido a PinaDev',    'text' => 'Soluciones digitales pensadas para crecer contigo.',     'cta' => 'Conócenos',       'link' => 'nosotros',  'theme' => 1],
            ['title' => 'Nuestros productos',      'text' => 'Descubre el catálogo y encuentra lo que necesitas.',     'cta' => 'Ver productos',   'link' => 'productos', 'theme' => 2],
            ['title' => 'Hablemos de tu proyecto', 'text' => 'Cuéntanos tu idea y te ayudamos a hacerla realidad.',    'cta' => 'Contáctanos',     'link' => 'contacto',  'theme' => 3],
        ];
    }
}
