<?php
declare(strict_types=1);

namespace App\Models;

use DateTimeImmutable;

/**
 * Información institucional: misión, visión, valores, contacto y horario.
 *
 * Datos fijos de ejemplo (editar aquí). Más adelante pueden venir de MySQL
 * sin cambiar controladores ni vistas.
 */
final class Company
{
    /** Horario de atención por día ISO (1 = lunes … 7 = domingo). null = cerrado. */
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
            'mission' => 'Desarrollar soluciones digitales seguras, accesibles y fáciles de usar que ayuden a nuestros clientes a crecer.',
            'vision'  => 'Ser un referente regional en desarrollo web de calidad, reconocido por la confianza y los resultados de quienes trabajan con nosotros.',
            // 'icon' es opcional: si el icono no existe en el sprite, no se muestra.
            'values'  => [
                ['name' => 'Compromiso',    'text' => 'Cumplimos lo que prometemos.'],
                ['name' => 'Innovación',    'text' => 'Buscamos siempre una mejor forma de hacerlo.'],
                ['name' => 'Transparencia', 'text' => 'Comunicación clara en cada etapa.'],
                ['name' => 'Calidad',       'text' => 'Cuidamos cada detalle del resultado.'],
                ['name' => 'Cercanía',      'text' => 'Escuchamos y acompañamos a cada cliente.'],
            ],
            'email'    => 'contacto@ejemplo.com',
            'phone'    => '+593 00 000 0000',
            'phone_href' => '+593000000000',
            'address'  => ['Calle Ejemplo 123', 'Ciudad, País'],
            'response_time' => 'Respondemos en menos de 24 horas hábiles.',
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
            $status = 'Abierto ahora';
            $detail = 'Atendemos hasta las ' . $hours[1] . ' de hoy.';
        } else {
            $open = false;
            $status = 'Cerrado ahora';
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
            return 'Abrimos hoy a las ' . $hours[0] . '.';
        }
        for ($i = 1; $i <= 7; $i++) {
            $day = ($today - 1 + $i) % 7 + 1;
            if (self::HOURS[$day] !== null) {
                $when = $i === 1 ? 'mañana' : 'el ' . self::DAY_NAMES[$day];
                return 'Abrimos ' . $when . ' a las ' . self::HOURS[$day][0] . '.';
            }
        }
        return 'Escríbenos y te responderemos pronto.';
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
