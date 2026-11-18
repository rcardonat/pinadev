<?php
/**
 * Plantilla de credenciales de base de datos.
 * Copiar como config/database.php y completar. config/database.php NO se
 * versiona y vive fuera de public/ (nunca se sirve por HTTP).
 *
 * Local (Laragon):  host 127.0.0.1, puerto 3306.
 * cPanel:           host "localhost"; nombre y usuario llevan el prefijo de la
 *                   cuenta, p. ej. "cpusuario_pinadev" / "cpusuario_app".
 *
 * Crear la base con: CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
 * (compatible con MySQL y MariaDB). Importar luego database/schema.sql.
 *
 * El usuario de la aplicación solo necesita: SELECT, INSERT, UPDATE, DELETE
 * sobre esta base (no CREATE/DROP/GRANT).
 */
return [
    'host' => '127.0.0.1',
    'port' => 3306,
    'name' => '',       // nombre de la base
    'user' => '',       // usuario de la base
    'pass' => '',       // contraseña del usuario
];
