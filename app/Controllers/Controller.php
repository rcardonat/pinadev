<?php
declare(strict_types=1);
namespace App\Controllers;
use Base;
use View;
/**
 * Controlador base: acceso a F3, generación de URL y sistema de layouts.
 *
 * Estructura de vistas (app/Views/):
 *   layouts/   Esqueleto HTML completo (<html>, <head>, <body>). Uno por zona del sitio.
 *   partials/  Fragmentos reutilizables incluidos por los layouts (cabecera, pie...).
 *   pages/     Contenido de cada página; se inserta en el <main> del layout.
 */
abstract class Controller
{
    /** Menú principal: alias de ruta => texto visible. */
    private const MENU = [
        'inicio'    => 'Inicio',
        'nosotros'  => 'Nosotros',
        'productos' => 'Productos',
        'contacto'  => 'Contacto',
    ];
    /** Layout por defecto (app/Views/layouts/{nombre}.php). Un controlador hijo puede cambiarlo. */
    protected string $layout = 'main';
    protected Base $fw;
    // F3 instancia los controladores pasando ($fw, $params).
    public function __construct(Base $fw)
    {
        $this->fw = $fw;
    }
    /** URL (relativa al dominio) de una ruta con nombre. */
    protected function url(string $alias, array $params = []): string
    {
        return $this->fw->get('BASE') . $this->fw->alias($alias, $params);
    }
    /**
     * Renderiza app/Views/pages/{$page}.php dentro de un layout.
     *
     * @param string      $page   Nombre de la página, p. ej. 'productos'.
     * @param array       $data   Variables para la vista. F3 las escapa automáticamente
     *                            (ESCAPE=true): nunca pasar HTML ya construido como dato.
     *                            Claves opcionales: 'title', 'description',
     *                            'styles' y 'scripts' (rutas bajo /assets, p. ej. 'js/app.js').
     * @param string|null $layout Layout a usar; null = $this->layout; '' = sin layout.
     */
    protected function render(string $page, array $data = [], ?string $layout = null): void
    {
        $layout ??= $this->layout;
        $data += [
            'title'       => '',
            'description' => '',
            'styles'      => [],
            'scripts'     => [],
        ];
        $data['app_name'] = $this->fw->get('APP_NAME');
        $data['base']     = $this->fw->get('BASE');
        $data['home_url'] = $this->url('inicio');
        $data['menu']     = $this->menu();
        $data['year']     = date('Y');
        $data['theme']    = $this->theme();
        $data['view']     = 'pages/' . $page . '.php';
        $file = $layout === '' ? $data['view'] : 'layouts/' . $layout . '.php';
        echo View::instance()->render($file, 'text/html', $data);
    }
    /**
     * Tema elegido por el usuario (cookie "theme", escrita por app.js).
     * Solo se aceptan valores de la lista permitida; por defecto, oscuro.
     */
    private function theme(): string
    {
        $theme = $this->fw->get('COOKIE.theme');
        return in_array($theme, ['dark', 'light'], true) ? $theme : 'dark';
    }
    /** @return list<array{url: string, label: string, active: bool}> */
    private function menu(): array
    {
        $current = $this->fw->get('ALIAS');
        $menu = [];
        foreach (self::MENU as $alias => $label) {
            $menu[] = [
                'url'    => $this->url($alias),
                'label'  => $label,
                'active' => $alias === $current,
            ];
        }
        return $menu;
    }
}
