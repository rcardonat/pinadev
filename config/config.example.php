<?php
/**
 * Plantilla de configuración. Copiar como config/config.php y ajustar.
 * config/config.php NO se versiona (contiene credenciales).
 */
return [
    // Nombre visible del sitio.
    'app_name' => 'PinaDev',

    // true solo en desarrollo local. En producción SIEMPRE false.
    'debug' => false,

    // Zona horaria de la aplicación.
    'timezone' => 'America/Guayaquil',

    // Clave secreta para firmar/derivar hashes (p. ej. de IPs). 64 caracteres
    // hexadecimales aleatorios. Generar con:  php -r "echo bin2hex(random_bytes(32));"
    'app_key' => 'CAMBIAR',
];
