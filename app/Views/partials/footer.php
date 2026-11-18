<?php
/**
 * Pie de página (incluido por layouts/main.php en todas las páginas).
 * @var string $app_name @var string $year
 * @var array  $social  Company::social()
 */
?>
<footer class="site-footer">
    <div class="container footer-inner">
        <div class="footer-brand">
            <strong><?= $app_name ?></strong>
            <small>Videojuegos independientes hechos a mano.</small>
        </div>

        <?php if ($social): ?>
        <nav class="social" aria-label="Redes sociales">
            <ul>
                <?php foreach ($social as $net): ?>
                <?php $svg = icon($net['icon']) ?>
                <li>
                    <a class="social-link <?= $net['class'] ?>" href="<?= $net['url'] ?>"
                       target="_blank" rel="noopener noreferrer">
                        <span class="social-icon" aria-hidden="true">
                            <?= $svg !== '' ? $svg : substr($net['name'], 0, 1) ?>
                        </span>
                        <span class="social-label"><?= $net['name'] ?></span>
                        <span class="sr-only">(se abre en una pestaña nueva)</span>
                    </a>
                </li>
                <?php endforeach ?>
            </ul>
        </nav>
        <?php endif ?>
    </div>

    <div class="container footer-bottom">
        <small>&copy; <?= $year ?> <?= $app_name ?>. Todos los derechos reservados.</small>
    </div>
</footer>
