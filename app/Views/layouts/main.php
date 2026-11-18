<?php
/**
 * Layout principal del sitio público. Todas las páginas se renderizan aquí.
 *
 * Variables (ya escapadas por F3, ESCAPE=true):
 * @var string $title        Título de la página
 * @var string $description  Meta description
 * @var string $app_name     Nombre del sitio
 * @var string $base         Ruta base de la app ('' en la raíz del dominio)
 * @var array  $styles       Hojas CSS extra de la página (rutas bajo /assets)
 * @var array  $scripts      Scripts extra de la página (rutas bajo /assets)
 * @var string $view         Plantilla de contenido (pages/...)
 */
?><!DOCTYPE html>
<?php /* $theme llega validado ('dark' | 'light'); se fija desde el HTML inicial para evitar destellos. */ ?>
<html lang="es" data-theme="<?= $theme ?>" data-base="<?= $base ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="<?= $theme ?>">
    <meta name="theme-color" content="<?= $theme === 'light' ? '#F5F7FA' : '#101419' ?>">
    <title><?= $title !== '' ? $title . ' · ' : '' ?><?= $app_name ?></title>
    <?php if ($description !== ''): ?>
    <meta name="description" content="<?= $description ?>">
    <?php endif ?>
    <link rel="stylesheet" href="<?= $base ?>/assets/css/pico.min.css?v=2.0.6">
    <link rel="stylesheet" href="<?= $base ?>/assets/css/app.css?v=24">
    <?php foreach ($styles as $href): ?>
    <link rel="stylesheet" href="<?= $base ?>/assets/<?= $href ?>">
    <?php endforeach ?>
</head>
<body>
    <a class="skip-link" href="#contenido">Saltar al contenido</a>

    <?= $this->render('partials/header.php', null, get_defined_vars()) ?>

    <main id="contenido" tabindex="-1">
        <?php if ($hero): ?>
        <?= $this->render('partials/hero.php', null, get_defined_vars()) ?>
        <?php endif ?>
        <div class="container page-body">
            <?= $this->render($view, null, get_defined_vars()) ?>
        </div>
    </main>

    <?= $this->render('partials/footer.php', null, get_defined_vars()) ?>

    <!-- Volver arriba: app.js lo muestra al bajar y dibuja el progreso de lectura. -->
    <button type="button" class="to-top" data-to-top hidden aria-label="Volver arriba" title="Volver arriba">
        <svg class="to-top-ring" viewBox="0 0 48 48" aria-hidden="true" focusable="false">
            <circle class="to-top-track" cx="24" cy="24" r="22"></circle>
            <circle class="to-top-progress" cx="24" cy="24" r="22" data-to-top-progress></circle>
        </svg>
        <?= icon('transfer-top') ?>
    </button>

    <script src="<?= $base ?>/assets/js/app.js?v=17" defer></script>
    <?php foreach ($scripts as $src): ?>
    <script src="<?= $base ?>/assets/<?= $src ?>" defer></script>
    <?php endforeach ?>
</body>
</html>
