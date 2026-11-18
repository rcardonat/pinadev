<?php
declare(strict_types=1);

namespace App;

/**
 * Configuración de la aplicación (config/config.php), accesible desde PHP.
 *
 * Se guarda aquí y NO en el hive de F3 a propósito: así las credenciales
 * (base de datos, app_key) nunca pueden llegar a una vista ni a una traza.
 */
final class Config
{
    private static array $values = [];

    public static function load(array $values): void
    {
        self::$values = $values;
    }

    /** Lee una clave; admite notación con puntos: Config::get('db.host'). */
    public static function get(string $key, mixed $default = null): mixed
    {
        $value = self::$values;
        foreach (explode('.', $key) as $part) {
            if (!is_array($value) || !array_key_exists($part, $value)) {
                return $default;
            }
            $value = $value[$part];
        }
        return $value;
    }

    /**
     * Hash con clave secreta (HMAC-SHA256) para guardar datos sensibles de
     * forma no reversible, p. ej. IPs para límites de envío.
     */
    public static function keyedHash(string $data): string
    {
        return hash_hmac('sha256', $data, (string) self::get('app_key', ''));
    }
}
