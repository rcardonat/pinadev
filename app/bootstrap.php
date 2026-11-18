<?php
declare(strict_types=1);

/**
 * Arranque de la aplicación: configuración, ajustes de F3 y rutas.
 * Se carga desde public/index.php, que define la constante ROOT.
 */

require ROOT . '/vendor/autoload.php';

$configFile = ROOT . '/config/config.php';
if (!is_file($configFile)) {
    http_response_code(500);
    exit('Falta config/config.php (copiar desde config/config.example.php).');
}
$config = require $configFile;

$fw = Base::instance();

$fw->mset([
    'APP_NAME' => $config['app_name'],
    'DEBUG'    => $config['debug'] ? 3 : 0,
    'TZ'       => $config['timezone'],
    'UI'       => ROOT . '/app/Views/',
    // Escape automático de variables en las vistas (incluye comillas simples).
    'ESCAPE'   => true,
    'BITMASK'  => ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5,
    // No anunciar el framework; X-Frame-Options lo define public/.htaccess.
    'PACKAGE'  => '',
    'XFRAME'   => '',
    // Páginas de error con el diseño del sitio y sin datos internos.
    'ONERROR'  => 'App\Controllers\ErrorController->show',
]);

ini_set('display_errors', $config['debug'] ? '1' : '0');

// Cuando el .htaccess de la raíz reenvía a public/, SCRIPT_NAME es
// ".../public/index.php" y F3 calcula BASE=".../public". Se corrige para que
// las URL públicas y la cookie de sesión no incluyan "/public".
$base = preg_replace('#/public$#', '', $fw->get('BASE'));
if ($base !== $fw->get('BASE')) {
    $uriPath = (string) parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    if ($base !== '' && strpos($uriPath, $base) === 0) {
        $uriPath = substr($uriPath, strlen($base));
    }
    $fw->set('BASE', $base);
    $fw->set('PATH', $uriPath === '' ? '/' : $uriPath);
    $fw->set('JAR.path', $base === '' ? '/' : $base);
}

// ---------------------------------------------------------------------------
// Rutas (Método Ruta = Controlador->método). Nombre @alias para generar URLs.
// ---------------------------------------------------------------------------
$fw->route('GET|HEAD @inicio:    /',          'App\Controllers\PageController->inicio');
$fw->route('GET|HEAD @nosotros:  /nosotros',  'App\Controllers\PageController->nosotros');
$fw->route('GET|HEAD @productos: /productos', 'App\Controllers\PageController->productos');
$fw->route('GET|HEAD @contacto:  /contacto',  'App\Controllers\PageController->contacto');

return $fw;
