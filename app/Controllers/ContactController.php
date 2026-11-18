<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Company;
use App\Models\ContactRequest;

/**
 * Página de contacto con formulario por preguntas.
 *
 * Seguridad (reporte §9): token CSRF de sesión, validación en servidor,
 * campo trampa (honeypot), tiempo mínimo de llenado, límite de envíos por
 * sesión y patrón PRG (redirección tras enviar) para evitar reenvíos.
 */
final class ContactController extends Controller
{
    private const MIN_SECONDS  = 3;     // menos que esto = probablemente un bot
    private const MAX_SENDS    = 3;     // envíos permitidos…
    private const WINDOW_SECS  = 900;   // …cada 15 minutos por sesión

    public function show(): void
    {
        $this->renderForm();
    }

    public function send(): void
    {
        $post = (array) $this->fw->get('POST');

        // 1) CSRF: token de la sesión, comparación en tiempo constante.
        $token = (string) $this->fw->get('SESSION.csrf');
        if ($token === '' || !is_string($post['csrf'] ?? null) || !hash_equals($token, $post['csrf'])) {
            $this->renderForm([], [], 'La sesión del formulario caducó. Vuelve a intentarlo.', 400);
            return;
        }

        // 2) Campo trampa: los humanos no lo ven; si viene lleno, se descarta en silencio.
        $issuedAt = (int) $this->fw->get('SESSION.contact_ts');
        if (($post['website'] ?? '') !== '' || time() - $issuedAt < self::MIN_SECONDS) {
            $this->flashAndRedirect(['ok' => true, 'name' => '']);
            return;
        }

        // 3) Límite de envíos por sesión.
        $sent = array_filter(
            (array) $this->fw->get('SESSION.contact_sent'),
            fn ($t) => is_int($t) && $t > time() - self::WINDOW_SECS
        );
        if (count($sent) >= self::MAX_SENDS) {
            $this->renderForm($post, [], 'Has enviado varios mensajes seguidos. Espera unos minutos e inténtalo de nuevo.', 429);
            return;
        }

        // 4) Validación en servidor.
        ['data' => $data, 'errors' => $errors] = ContactRequest::validate($post);
        if ($errors) {
            $this->renderForm($data, $errors, 'Revisa las respuestas marcadas.', 422);
            return;
        }

        // 5) Guardado.
        if (!ContactRequest::save($data)) {
            error_log('Contacto: no se pudo guardar la solicitud.');
            $this->renderForm($data, [], 'No pude registrar tu mensaje. Inténtalo más tarde o escríbeme por correo.', 500);
            return;
        }

        $sent[] = time();
        $this->fw->set('SESSION.contact_sent', array_values($sent));
        $this->fw->set('SESSION.csrf', bin2hex(random_bytes(32)));   // token nuevo tras un envío

        $this->flashAndRedirect([
            'ok'      => true,
            'name'    => $data['name'],
        ]);
    }

    /** Guarda un mensaje para la siguiente petición y redirige (303) a /contacto. */
    private function flashAndRedirect(array $flash): void
    {
        $this->fw->set('SESSION.contact_flash', $flash);
        // URL relativa (sin usar la cabecera Host del cliente).
        header('Location: ' . $this->url('contacto'), true, 303);
    }

    private function renderForm(array $old = [], array $errors = [], string $alert = '', int $status = 200): void
    {
        if ($status !== 200) {
            $this->fw->status($status);
        }

        if ((string) $this->fw->get('SESSION.csrf') === '') {
            $this->fw->set('SESSION.csrf', bin2hex(random_bytes(32)));
        }
        $this->fw->set('SESSION.contact_ts', time());

        $flash = $this->fw->get('SESSION.contact_flash');
        $this->fw->clear('SESSION.contact_flash');

        // Valores previos: solo las claves esperadas y solo cadenas (nunca arrays del cliente).
        $fields = ContactRequest::FIELDS;
        $clean = [];
        foreach ($fields as $f) {
            $clean[$f] = is_string($old[$f] ?? null) ? mb_substr($old[$f], 0, ContactRequest::COMMENT_MAX, 'UTF-8') : '';
        }

        $this->render('contacto', [
            'title'        => 'Contacto',
            'description'  => 'Escríbeme: ayuda con un juego, prensa, colaboraciones o simplemente para saludar.',
            'hero'         => ['subtitle' => '¿Problemas con un juego, una propuesta o una idea? Responde unas preguntas rápidas y te respondo pronto.'],
            'company'      => Company::info(),
            'availability' => Company::availability(),
            'contact_url'  => $this->url('contacto'),
            'csrf'         => (string) $this->fw->get('SESSION.csrf'),
            'needs'        => ContactRequest::NEEDS,
            'comment_max'  => ContactRequest::COMMENT_MAX,
            'old'          => $clean,
            'errors'       => $errors + array_fill_keys($fields, ''),
            'alert'        => $alert,
            'flash'        => is_array($flash) ? $flash : null,
        ]);
    }
}
