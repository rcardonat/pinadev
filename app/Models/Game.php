<?php
declare(strict_types=1);

namespace App\Models;

/**
 * Juegos publicados (o en desarrollo) del sitio.
 *
 * ⚠ DATOS DE EJEMPLO: reemplázalos por tus juegos reales. Más adelante pueden
 * venir de MySQL sin cambiar controladores ni vistas.
 *
 * Campos:
 *   slug      identificador (también nombre de la portada: assets/img/juegos/{slug}.webp)
 *   title     nombre del juego
 *   genre     género corto ("Aventura · Puzles")
 *   status    'available' | 'development' | 'soon'  (ver STATUSES)
 *   summary   descripción de una o dos frases
 *   platforms lista de plataformas
 *   price     texto libre: 'Gratis', '$4.99', '' (sin precio aún)
 *   steam     URL de la página en Steam ('' si no tiene)
 *   itch      URL de la página en itch.io ('' si no tiene)
 *   featured  true = aparece en Inicio
 */
final class Game
{
    public const STATUSES = [
        'available'   => 'Disponible',
        'development' => 'En desarrollo',
        'soon'        => 'Próximamente',
    ];

    /** @return list<array<string, mixed>> */
    public static function all(): array
    {
        return [
            [
                'slug'      => 'ecos-del-bosque',
                'title'     => 'Ecos del Bosque',
                'genre'     => 'Aventura · Puzles',
                'status'    => 'available',
                'summary'   => 'Explora un bosque que cambia con cada sonido y resuelve sus secretos a tu ritmo.',
                'platforms' => ['Windows', 'Web'],
                'price'     => '$4.99',
                'steam'     => 'https://store.steampowered.com/',
                'itch'      => 'https://itch.io/',
                'featured'  => true,
            ],
            [
                'slug'      => 'pixel-runner',
                'title'     => 'Pixel Runner',
                'genre'     => 'Plataformas · Arcade',
                'status'    => 'available',
                'summary'   => 'Corre, salta y supera tu propio récord en niveles cortos y frenéticos.',
                'platforms' => ['Web', 'Android'],
                'price'     => 'Gratis',
                'steam'     => '',
                'itch'      => 'https://itch.io/',
                'featured'  => true,
            ],
            [
                'slug'      => 'nave-errante',
                'title'     => 'Nave Errante',
                'genre'     => 'Roguelike · Espacial',
                'status'    => 'development',
                'summary'   => 'Pilota una nave perdida entre sistemas generados al azar. Cada viaje es distinto.',
                'platforms' => ['Windows', 'Linux'],
                'price'     => '',
                'steam'     => 'https://store.steampowered.com/',
                'itch'      => '',
                'featured'  => true,
            ],
        ];
    }

    /** @return list<array<string, mixed>> */
    public static function featured(int $limit = 3): array
    {
        return array_slice(array_values(array_filter(self::all(), fn (array $g): bool => $g['featured'])), 0, $limit);
    }
}
