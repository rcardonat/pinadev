<?php
/**
 * Cabecera con menú principal y menú lateral móvil (drawer).
 * @var string $app_name @var string $home_url
 * @var array  $menu  Lista de ['url', 'label', 'active']
 * @var string $theme 'dark' | 'light'
 */
?>
<header class="site-header">
    <nav class="container" aria-label="Principal">
        <ul>
            <li><a class="brand" href="<?= $home_url ?>"><strong><?= $app_name ?></strong></a></li>
        </ul>

        <div class="nav-end">
            <!-- Escritorio (≥ 768 px) -->
            <ul class="nav-desktop">
                <?php foreach ($menu as $item): ?>
                <li><a href="<?= $item['url'] ?>"<?= $item['active'] ? ' aria-current="page"' : '' ?>><?= $item['label'] ?></a></li>
                <?php endforeach ?>
            </ul>

            <!-- Cambio de tema (solo escritorio; en móvil está dentro del menú lateral).
                 Oculto hasta que app.js lo habilita (sin JS no puede funcionar). -->
            <button type="button" class="theme-toggle theme-toggle--header" data-theme-toggle hidden
                    aria-label="<?= $theme === 'light' ? 'Activar modo oscuro' : 'Activar modo claro' ?>"
                    title="<?= $theme === 'light' ? 'Activar modo oscuro' : 'Activar modo claro' ?>">
                <?= icon('sun-dim') ?>
                <?= icon('moon') ?>
            </button>

            <!-- Móvil (< 768 px). Sin JS funciona con el ancla #menu-movil (:target);
                 con JS, app.js lo convierte en un diálogo accesible. -->
            <a class="nav-toggle" href="#menu-movil" data-drawer-open
               aria-controls="menu-movil" aria-expanded="false">
                <?= icon('menu-closer') ?>
                <span class="sr-only">Abrir menú</span>
            </a>
        </div>
    </nav>
</header>

<!-- Fuera de <header>: su backdrop-filter limitaría un position:fixed interno. -->
<div class="drawer" id="menu-movil" data-drawer>
    <a class="drawer-backdrop" href="#" data-drawer-close tabindex="-1" aria-hidden="true"></a>
    <div class="drawer-panel" data-drawer-panel aria-labelledby="drawer-title">
        <div class="drawer-head">
            <strong id="drawer-title">Menú</strong>
            <a class="drawer-close" href="#" data-drawer-close>
                <?= icon('times-line') ?>
                <span class="sr-only">Cerrar menú</span>
            </a>
        </div>
        <nav aria-label="Principal (móvil)">
            <ul class="drawer-links">
                <?php foreach ($menu as $item): ?>
                <li><a href="<?= $item['url'] ?>"<?= $item['active'] ? ' aria-current="page"' : '' ?>><?= $item['label'] ?></a></li>
                <?php endforeach ?>
            </ul>
        </nav>

        <!-- Cambio de tema en móvil (app.js lo habilita y actualiza el texto). -->
        <button type="button" class="drawer-theme" data-theme-toggle hidden>
            <span data-theme-text><?= $theme === 'light' ? 'Activar modo oscuro' : 'Activar modo claro' ?></span>
            <span class="drawer-theme-icons" aria-hidden="true">
                <?= icon('sun-dim') ?>
                <?= icon('moon') ?>
            </span>
        </button>
    </div>
</div>
