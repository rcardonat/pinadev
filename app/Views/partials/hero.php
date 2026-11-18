<?php
/**
 * Encabezado de página (hero) para todas las páginas salvo Inicio y errores.
 * Lo activa Controller::render(); contiene el único <h1> de la página.
 * @var array  $hero     ['title' => string, 'subtitle' => string]
 * @var string $home_url
 */
?>
<section class="page-hero" aria-labelledby="page-title">
    <div class="container page-hero-inner">
        <nav class="breadcrumb" aria-label="Ruta de navegación">
            <ol>
                <li><a href="<?= $home_url ?>">Inicio</a></li>
                <li><span aria-current="page"><?= $hero['title'] ?></span></li>
            </ol>
        </nav>
        <h1 id="page-title"><?= $hero['title'] ?></h1>
        <?php if ($hero['subtitle'] !== ''): ?>
        <p class="page-hero-subtitle"><?= $hero['subtitle'] ?></p>
        <?php endif ?>
    </div>
</section>
