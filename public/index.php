<?php
declare(strict_types=1);

// Raíz del proyecto (un nivel por encima de public/).
// En cPanel, si el contenido de public/ va en public_html y el resto del
// proyecto en /home/usuario/pinadev, cambiar por: define('ROOT', dirname(__DIR__) . '/pinadev');
define('ROOT', dirname(__DIR__));
$fw = require ROOT . '/app/bootstrap.php';
$fw->run();
