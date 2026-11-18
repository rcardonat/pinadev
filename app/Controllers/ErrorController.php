<?php
declare(strict_types=1);

namespace App\Controllers;

/**
 * Manejador de errores (ONERROR). Muestra una página genérica con el diseño
 * del sitio; nunca el detalle interno del error. F3 ya registra el error con
 * error_log() según LOGGABLE.
 */
final class ErrorController extends Controller
{
    private const MESSAGES = [
        403 => 'No tienes permiso para acceder a esta página.',
        404 => 'La página que buscas no existe o fue movida.',
        405 => 'Método no permitido.',
    ];

    public function show(): void
    {
        $code = (int) $this->fw->get('ERROR.code');

        // Limpia cualquier salida parcial de la vista que falló.
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        $data = [
            'title'   => $code . ' ' . $this->fw->get('ERROR.status'),
            'code'    => $code,
            'message' => self::MESSAGES[$code] ?? 'Ocurrió un error inesperado. Inténtalo más tarde.',
            'trace'   => '',
            'hero'    => false,     // la página de error tiene su propio diseño
        ];

        // Solo en desarrollo se muestra el detalle del error.
        if ($this->fw->get('DEBUG') > 0) {
            $data['trace'] = $this->fw->get('ERROR.text');
        }

        $this->render('error', $data);
    }
}
