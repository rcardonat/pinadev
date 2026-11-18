<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Company;
use App\Models\Product;
use App\Models\Slide;

/**
 * Páginas públicas del sitio.
 */
final class PageController extends Controller
{
    public function inicio(): void
    {
        $slides = array_map(
            fn (array $s): array => ['url' => $this->url($s['link'])] + $s,
            Slide::all()
        );

        $this->render('inicio', [
            'title'        => 'Inicio',
            'description'  => 'Bienvenido a PinaDev.',
            'slides'       => $slides,
            'slider_effect' => Slide::effect(),
            'featured'     => array_slice(Product::all(), 0, 3),
            'company'      => Company::info(),
            'availability' => Company::availability(),
            'products_url' => $this->url('productos'),
            'about_url'    => $this->url('nosotros'),
            'contact_url'  => $this->url('contacto'),
        ]);
    }

    public function nosotros(): void
    {
        $this->render('nosotros', [
            'title'       => 'Nosotros',
            'description' => 'Conoce quiénes somos.',
            'company'     => Company::info(),
        ]);
    }

    public function productos(): void
    {
        $this->render('productos', [
            'title'       => 'Productos',
            'description' => 'Catálogo de productos.',
            'products'    => Product::all(),
        ]);
    }

    public function contacto(): void
    {
        $this->render('contacto', [
            'title'        => 'Contacto',
            'description'  => 'Ponte en contacto con nosotros.',
            'company'      => Company::info(),
            'availability' => Company::availability(),
        ]);
    }
}
