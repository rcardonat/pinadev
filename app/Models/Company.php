<?php
declare(strict_types=1);

namespace App\Models;

use DateTimeImmutable;

/**
 * Información del sitio: quién hace los juegos, valores, contacto, horario
 * de respuesta, apartado destacado, cifras y redes.
 *
 * ⚠ Textos de ejemplo en primera persona: ajústalos a tu historia.
 * Más adelante pueden venir de MySQL sin cambiar controladores ni vistas.
 */
final class Company
{
    /** Horario en que respondo mensajes, por día ISO (1 = lunes … 7 = domingo). null = no respondo. */
    private const HOURS = [
        1 => ['08:00', '18:00'],
        2 => ['08:00', '18:00'],
        3 => ['08:00', '18:00'],
        4 => ['08:00', '18:00'],
        5 => ['08:00', '18:00'],
        6 => ['09:00', '13:00'],
        7 => null,
    ];

    private const DAY_NAMES = [
        1 => 'lunes', 2 => 'martes', 3 => 'miércoles', 4 => 'jueves',
        5 => 'viernes', 6 => 'sábado', 7 => 'domingo',
    ];

    public static function info(): array
    {
        return [
            // Presentación personal (página "Sobre mí").
            'bio'     => 'Hago videojuegos de forma independiente: diseño, programo, dibujo y publico cada proyecto por mi cuenta. Empecé creando pequeños juegos para amigos y hoy comparto mis mundos con jugadores de todas partes.',
            // "Por qué hago juegos" y "Hacia dónde voy".
            'mission' => 'Crear juegos pequeños pero memorables, con mecánicas claras y un estilo propio, que se disfruten en una tarde y se recuerden mucho después.',
            'vision'  => 'Seguir creciendo como creador independiente, publicar un juego más ambicioso en Steam y construir una comunidad que participe en cada proyecto.',
            // "Lo que guía mis juegos".
            'values'  => [
                ['name' => 'Jugabilidad primero',  'text' => 'Si no es divertido de jugar, no sale.'],
                ['name' => 'Hecho a mano',         'text' => 'Cada pixel y cada línea de código con intención.'],
                ['name' => 'Escuchar a quien juega', 'text' => 'Tus comentarios mejoran cada actualización.'],
                ['name' => 'Precio justo',         'text' => 'Juegos accesibles, sin trucos ni micropagos.'],
                ['name' => 'Constancia',           'text' => 'Avances y devlogs de forma regular.'],
            ],
            'email'    => 'contacto@ejemplo.com',
            'phone'    => '+593 00 000 0000',
            'phone_href' => '+593000000000',
            // Sin dirección física: es un sitio personal (privacidad).
            'location' => ['Ecuador', 'Desarrollo en remoto'],
            'response_time' => 'Suelo responder en menos de 48 horas.',
        ];
    }

    /**
     * Apartado destacado de Inicio (imagen + insignia flotante + beneficios).
     *
     * 'image': ruta bajo public/assets/ (se recomienda WebP, ~1000×1100 px).
     *          Si el archivo no existe, se muestra una ilustración CSS.
     * 'link':  alias de ruta del botón (ver app/bootstrap.php).
     * 'icon' de cada beneficio: opcional; si no existe en el sprite, se usa un punto.
     */
    public static function highlight(): array
    {
        return [
            'title'      => 'Una persona, muchos mundos',
            'subtitle'   => 'Desde la primera idea hasta el lanzamiento, cada juego lo diseño, programo y publico yo. Sin estudios ni intermediarios: solo ganas de crear buenos juegos.',
            'image'      => 'img/destacado.webp',
            'image_alt'  => 'Captura de uno de mis juegos',
            'badge_value' => '3 juegos',
            'badge_text'  => 'publicados y en camino',
            'features'   => [
                ['title' => 'Hecho a mano',             'text' => 'Diseño, código, arte y sonido creados por una sola persona.',  'icon' => 'home-lg'],
                ['title' => 'Actualizaciones constantes', 'text' => 'Escucho a quienes juegan y mejoro cada juego tras su lanzamiento.', 'icon' => 'calendar-days'],
                ['title' => 'En Steam e itch.io',       'text' => 'Encuentra mis juegos en tus tiendas favoritas o juégalos en el navegador.', 'icon' => 'steam'],
            ],
            'cta'        => 'Ver mis juegos',
            'link'       => 'juegos',
        ];
    }

    /**
     * Cifras del segmento "En números" de Inicio (datos de ejemplo; más
     * adelante pueden calcularse desde MySQL).
     * 'icon' es opcional: si no existe en el sprite, no se muestra.
     *
     * @return list<array{label: string, value: int, suffix: string, icon: string}>
     */
    public static function stats(): array
    {
        return [
            ['label' => 'Visitas',   'value' => 125400, 'suffix' => '+', 'icon' => 'eye'],
            ['label' => 'Descargas', 'value' => 8750,   'suffix' => '+', 'icon' => 'download'],
            ['label' => 'Ventas',    'value' => 1320,   'suffix' => '',  'icon' => 'shop'],
        ];
    }

    /**
     * Redes sociales del pie de página.
     * 'icon' = id en el sprite (archivo youtube.svg, instagram.svg…); si aún no
     * existe, el botón muestra la inicial. 'class' = color de marca en app.css.
     *
     * @return list<array{name: string, icon: string, url: string, class: string}>
     */
    public static function social(): array
    {
        return [
            ['name' => 'Instagram', 'icon' => 'insta',        'url' => 'https://www.instagram.com/ejemplo',            'class' => 'is-instagram'],
            ['name' => 'Facebook',  'icon' => 'facebook',     'url' => 'https://www.facebook.com/ejemplo',             'class' => 'is-facebook'],
            ['name' => 'YouTube',   'icon' => 'youtube-fill', 'url' => 'https://www.youtube.com/@ejemplo',             'class' => 'is-youtube'],
            ['name' => 'Steam',     'icon' => 'steam',        'url' => 'https://store.steampowered.com/developer/ejemplo', 'class' => 'is-steam'],
            ['name' => 'itch.io',   'icon' => 'shop',         'url' => 'https://ejemplo.itch.io',                      'class' => 'is-itchio'],
        ];
    }

    /**
     * Estado de atención en este momento (zona horaria de la app, ver config).
     *
     * @return array{open: bool, status: string, detail: string,
     *               schedule: list<array{days: string, time: string, today: bool}>}
     */
    public static function availability(?DateTimeImmutable $now = null): array
    {
        $now ??= new DateTimeImmutable('now');
        $today = (int) $now->format('N');
        $time  = $now->format('H:i');
        $hours = self::HOURS[$today];

        if ($hours !== null && $time >= $hours[0] && $time < $hours[1]) {
            $open = true;
            $status = 'Disponible ahora';
            $detail = 'Respondo mensajes hasta las ' . $hours[1] . ' de hoy.';
        } else {
            $open = false;
            $status = 'Fuera de horario';
            $detail = self::nextOpening($today, $time);
        }

        return [
            'open'     => $open,
            'status'   => $status,
            'detail'   => $detail,
            'schedule' => self::schedule($today),
        ];
    }

    private static function nextOpening(int $today, string $time): string
    {
        $hours = self::HOURS[$today];
        if ($hours !== null && $time < $hours[0]) {
            return 'Vuelvo a responder hoy a las ' . $hours[0] . '.';
        }
        for ($i = 1; $i <= 7; $i++) {
            $day = ($today - 1 + $i) % 7 + 1;
            if (self::HOURS[$day] !== null) {
                $when = $i === 1 ? 'mañana' : 'el ' . self::DAY_NAMES[$day];
                return 'Vuelvo a responder ' . $when . ' a las ' . self::HOURS[$day][0] . '.';
            }
        }
        return 'Escríbeme y te responderé pronto.';
    }

    /**
     * Agrupa días consecutivos con el mismo horario: "Lunes a viernes 08:00 – 18:00".
     * @return list<array{days: string, time: string, today: bool}>
     */
    private static function schedule(int $today): array
    {
        $groups = [];
        foreach (self::HOURS as $day => $hours) {
            $key = $hours === null ? 'closed' : implode('-', $hours);
            $last = count($groups) - 1;
            if ($last >= 0 && $groups[$last]['key'] === $key) {
                $groups[$last]['to'] = $day;
            } else {
                $groups[] = ['key' => $key, 'from' => $day, 'to' => $day, 'hours' => $hours];
            }
        }

        return array_map(static function (array $g) use ($today): array {
            $from = ucfirst(self::DAY_NAMES[$g['from']]);
            $days = $g['from'] === $g['to'] ? $from
                : $from . ($g['to'] - $g['from'] === 1 ? ' y ' : ' a ') . self::DAY_NAMES[$g['to']];
            return [
                'days'  => $days,
                'time'  => $g['hours'] === null ? 'Cerrado' : $g['hours'][0] . ' – ' . $g['hours'][1],
                'today' => $today >= $g['from'] && $today <= $g['to'],
            ];
        }, $groups);
    }
}
