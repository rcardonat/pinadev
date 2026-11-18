<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Company;
use App\Models\Game;
use App\Models\Slide;

/**
 * Páginas públicas del sitio: Inicio, Juegos y Sobre mí.
 * (Contacto, con su formulario, está en ContactController.)
 */
final class PageController extends Controller
{
    private const SITE_DESCRIPTION = 'Videojuegos independientes creados de principio a fin por una sola persona. Juégalos en Steam, itch.io y el navegador.';

    public function inicio(): void
    {
        $slides = array_map(
            fn (array $s): array => ['url' => $this->url($s['link'])] + $s,
            Slide::all()
        );

        // Apartado destacado: la imagen solo se usa si el archivo existe.
        $highlight = Company::highlight();
        $highlight['url'] = $this->url($highlight['link']);
        $img = $this->image($highlight['image']);
        $highlight['image_url'] = $img['url'] ?? '';
        $highlight['image_w'] = $img['w'] ?? 0;
        $highlight['image_h'] = $img['h'] ?? 0;

        $this->render('inicio', [
            'title'        => 'Juegos indie',
            'description'  => self::SITE_DESCRIPTION,
            'slides'       => $slides,
            'slider_effect' => Slide::effect(),
            'highlight'    => $highlight,
            'games'        => $this->prepareGames(Game::featured()),
            'company'      => Company::info(),
            'availability' => Company::availability(),
            'stats'        => Company::stats(),
            'games_url'    => $this->url('juegos'),
            'about_url'    => $this->url('sobremi'),
            'contact_url'  => $this->url('contacto'),
            'itch_url'     => $this->socialUrl('itch.io'),
        ]);
    }

    public function juegos(): void
    {
        $this->render('juegos', [
            'title'       => 'Juegos',
            'description' => 'Todos mis juegos: disponibles, en desarrollo y próximos lanzamientos.',
            'hero'        => ['subtitle' => 'Todos mis proyectos: los que ya puedes jugar y los que estoy creando.'],
            'games'       => $this->prepareGames(Game::all()),
            'contact_url' => $this->url('contacto'),
        ]);
    }

    public function sobreMi(): void
    {
        $this->render('sobre-mi', [
            'title'       => 'Sobre mí',
            'description' => 'Quién está detrás de ' . $this->fw->get('APP_NAME') . ' y por qué hago juegos.',
            'hero'        => ['subtitle' => 'Quién está detrás de los juegos, por qué los hago y hacia dónde voy.'],
            'company'     => Company::info(),
            'games_url'   => $this->url('juegos'),
        ]);
    }

    /**
     * Añade a cada juego lo que la vista necesita: portada (si existe),
     * texto del estado e índice de color para la portada de respaldo.
     */
    private function prepareGames(array $games): array
    {
        $out = [];
        foreach (array_values($games) as $i => $g) {
            $cover = $this->image('img/juegos/' . $g['slug'] . '.webp');
            $out[] = $g + [
                'status_label' => Game::STATUSES[$g['status']] ?? '',
                'cover_url'    => $cover['url'] ?? '',
                'cover_w'      => $cover['w'] ?? 0,
                'cover_h'      => $cover['h'] ?? 0,
                'variant'      => $i % 3 + 1,
            ];
        }
        return $out;
    }

    /** URL de una red social configurada en Company::social() (por nombre). */
    private function socialUrl(string $name): string
    {
        foreach (Company::social() as $net) {
            if ($net['name'] === $name) {
                return $net['url'];
            }
        }
        return '';
    }
}
