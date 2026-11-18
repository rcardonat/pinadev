<?php
declare(strict_types=1);

namespace App;

use PDO;

/**
 * Conexión PDO a MySQL/MariaDB (reporte §2 y §9.2).
 *
 * - Perezosa: solo se conecta la primera vez que se usa (las páginas que no
 *   necesitan base de datos no abren conexión).
 * - Excepciones ante errores, sin emulación de consultas preparadas
 *   (parámetros enlazados reales) y utf8mb4.
 * - Usar SIEMPRE consultas preparadas: Db::pdo()->prepare('… WHERE id = ?').
 */
final class Db
{
    private static ?PDO $pdo = null;

    public static function pdo(): PDO
    {
        if (self::$pdo === null) {
            $c = (array) Config::get('db', []);
            foreach (['host', 'name', 'user'] as $required) {
                if (empty($c[$required])) {
                    throw new \RuntimeException('Falta la configuración de base de datos (db.' . $required . ').');
                }
            }

            $dsn = sprintf(
                'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
                $c['host'],
                (int) ($c['port'] ?? 3306),
                $c['name']
            );

            self::$pdo = new PDO($dsn, (string) $c['user'], (string) ($c['pass'] ?? ''), [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::ATTR_TIMEOUT            => 5,
            ]);

            // La base guarda fechas en la misma zona horaria que la aplicación.
            self::$pdo->exec("SET time_zone = '" . (new \DateTime())->format('P') . "'");
            // Modo estricto en esta conexión (local y hosting se comportan igual):
            // datos fuera de rango o truncados dan error en vez de guardarse mal.
            self::$pdo->exec("SET SESSION sql_mode = 'STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION'");
        }
        return self::$pdo;
    }

    /** ¿Hay base de datos configurada? (para funciones con respaldo). */
    public static function isConfigured(): bool
    {
        return (string) Config::get('db.name', '') !== '' && (string) Config::get('db.user', '') !== '';
    }
}
