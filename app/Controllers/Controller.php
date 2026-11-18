<?php
declare(strict_types=1);
namespace App\Controllers;
use App\Models\Company;
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
        'inicio'   => 'Inicio',
        'juegos'   => 'Juegos',
        'sobremi'  => 'Sobre mí',
        'contacto' => 'Contacto',
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
        try {
            return $this->fw->get('BASE') . $this->fw->alias($alias, $params);
        } catch (\Exception $e) {
            // Un error ocurrido antes de registrar las rutas no debe provocar
            // otro error al dibujar el menú de la página de error.
            return $this->fw->get('BASE') . '/';
        }
    }

    /**
     * Imagen opcional bajo public/assets/. Si el archivo existe devuelve su URL
     * (con versión para caché) y sus dimensiones, para que el navegador reserve
     * el espacio; si no existe, null (la vista muestra un respaldo).
     *
     * @return array{url: string, w: int, h: int}|null
     */
    protected function image(string $path): ?array
    {
        if ($path === '' || str_contains($path, '..')) {
            return null;
        }
        $file = ROOT . '/public/assets/' . $path;
        if (!is_file($file) || ($size = @getimagesize($file)) === false) {
            return null;
        }
        return [
            'url' => $this->fw->get('BASE') . '/assets/' . $path . '?v=' . filemtime($file),
            'w'   => (int) $size[0],
            'h'   => (int) $size[1],
        ];
    }
    /**
     * Renderiza app/Views/pages/{$page}.php dentro de un layout.
     *
     * @param string      $page   Nombre de la página, p. ej. 'juegos'.
     * @param array       $data   Variables para la vista. F3 las escapa automáticamente
     *                            (ESCAPE=true): nunca pasar HTML ya construido como dato.
     *                            Claves opcionales: 'title', 'description',
     *                            'styles' y 'scripts' (rutas bajo /assets, p. ej. 'js/app.js'),
     *                            'hero': ['title' => …, 'subtitle' => …] para personalizar el
     *                            encabezado de página, o false para no mostrarlo.
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
            'hero'        => null,
        ];
        // Encabezado de página (partials/hero.php): en todas las páginas salvo
        // Inicio. Por defecto usa el título y la descripción de la página.
        if ($data['hero'] === null) {
            $data['hero'] = $page === 'inicio' ? false : [];
        }
        if (is_array($data['hero'])) {
            $data['hero'] += ['title' => $data['title'], 'subtitle' => $data['description']];
        }
        $data['app_name'] = $this->fw->get('APP_NAME');
        $data['base']     = $this->fw->get('BASE');
        $data['home_url'] = $this->url('inicio');
        $data['menu']     = $this->menu();
        $data['year']     = date('Y');
        $data['theme']    = $this->theme();
        $data['social']   = Company::social();   // pie de página (todas las páginas)
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
