<?php
declare(strict_types=1);

namespace App\Models;

/**
 * Solicitud de contacto: opciones del formulario, validación y guardado.
 *
 * Campos: nombre (obligatorio), necesidad (obligatoria), teléfono (opcional),
 * correo (obligatorio) y comentario (opcional).
 *
 * Guardado temporal: un archivo JSONL privado fuera de public/
 * (storage/contact/messages.jsonl). Cuando haya MySQL o SMTP, solo cambia save().
 */
final class ContactRequest
{
    /** ¿De qué quieres hablar? */
    public const NEEDS = [
        'soporte'      => ['label' => 'Ayuda con un juego',      'text' => 'Errores, compras o problemas para jugar.'],
        'prensa'       => ['label' => 'Prensa o contenido',      'text' => 'Claves para reseñas, streams o entrevistas.'],
        'colaboracion' => ['label' => 'Colaboración',            'text' => 'Música, arte, traducción o publicación.'],
        'otro'         => ['label' => 'Otro tema',               'text' => 'Ideas, sugerencias o solo saludar.'],
    ];

    /** Campos del formulario (orden de las preguntas). */
    public const FIELDS = ['name', 'need', 'phone', 'email', 'comment'];

    public const COMMENT_MAX = 1000;

    private const STORAGE_DIR  = '/storage/contact';
    private const STORAGE_FILE = 'messages.jsonl';
    private const MAX_BYTES    = 1048576;   // 1 MB: al superarlo se rota (máx. 2 archivos)

    /**
     * Valida y normaliza la entrada. Nunca confía en el cliente.
     * @return array{data: array<string, string>, errors: array<string, string>}
     */
    public static function validate(array $in): array
    {
        $errors = [];
        $str = static fn (string $k): string => is_string($in[$k] ?? null) ? trim($in[$k]) : '';

        $name    = preg_replace('/\s+/u', ' ', $str('name')) ?? '';
        $need    = $str('need');
        $phone   = $str('phone');
        $email   = mb_strtolower($str('email'), 'UTF-8');
        // Comentario: saltos de línea normalizados y sin caracteres de control.
        $comment = preg_replace(['/\r\n?/', '/[^\P{C}\n\t]/u'], ["\n", ''], $str('comment')) ?? '';

        if ($name === '') {
            $errors['name'] = 'Escribe tu nombre.';
        } elseif (mb_strlen($name, 'UTF-8') < 2 || mb_strlen($name, 'UTF-8') > 80
            || !preg_match("/^\p{L}[\p{L}\p{M} '.-]*$/u", $name)) {
            $errors['name'] = 'Usa solo letras (entre 2 y 80 caracteres).';
        }

        if (!isset(self::NEEDS[$need])) {
            $errors['need'] = 'Elige una opción.';
        }

        // Teléfono opcional: solo se valida si se escribió.
        if ($phone !== '') {
            $digits = preg_replace('/\D/', '', $phone) ?? '';
            if (!preg_match('/^\+?[0-9\s().-]{7,20}$/', $phone) || strlen($digits) < 7 || strlen($digits) > 15) {
                $errors['phone'] = 'Revisa el número (entre 7 y 15 dígitos) o pulsa «Paso».';
            }
        }

        if ($email === '') {
            $errors['email'] = 'Escribe tu correo.';
        } elseif (strlen($email) > 190 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Revisa el correo (ejemplo: nombre@dominio.com).';
        }

        if (mb_strlen($comment, 'UTF-8') > self::COMMENT_MAX) {
            $errors['comment'] = 'El comentario puede tener hasta ' . self::COMMENT_MAX . ' caracteres.';
        }

        return [
            'data'   => compact('name', 'need', 'phone', 'email', 'comment'),
            'errors' => $errors,
        ];
    }

    /** Guarda la solicitud validada. Devuelve false si no se pudo escribir. */
    public static function save(array $data): bool
    {
        $dir = ROOT . self::STORAGE_DIR;
        if (!is_dir($dir) && !@mkdir($dir, 0750, true) && !is_dir($dir)) {
            return false;
        }

        $file = $dir . '/' . self::STORAGE_FILE;
        // Rotación simple para no crecer sin límite (reporte §3.6).
        if (is_file($file) && filesize($file) > self::MAX_BYTES) {
            @rename($file, $dir . '/messages.1.jsonl');
        }

        $record = [
            'received_at' => date('c'),
            'name'    => $data['name'],
            'need'    => self::NEEDS[$data['need']]['label'],
            'phone'   => $data['phone'],
            'email'   => $data['email'],
            'comment' => $data['comment'],
        ];
        $line = json_encode($record, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n";

        return @file_put_contents($file, $line, FILE_APPEND | LOCK_EX) !== false;
    }
}
