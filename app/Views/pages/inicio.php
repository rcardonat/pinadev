<?php
/**
 * @var string $app_name @var array $slides @var array $featured
 * @var array  $company      Company::info()
 * @var array  $availability Company::availability()
 * @var string $products_url @var string $about_url @var string $contact_url
 */
$total = count($slides);
?>
<!-- Fondo animado: estrellas y luciérnagas (oscuro) / polvo (claro). Lo dibuja app.js. -->
<canvas class="bg-particles" data-particles aria-hidden="true"></canvas>

<h1 class="sr-only">Bienvenido a <?= $app_name ?></h1>

<?php if ($total): ?>
<!--
  Slider: sin JavaScript es un carrusel deslizable (scroll-snap).
  app.js muestra los controles, añade autoplay con pausa y sincroniza los puntos.
-->
<section class="slider" data-slider aria-roledescription="carrusel" aria-label="Destacados">
    <div class="slider-track" data-slider-track tabindex="0" aria-live="polite">
        <?php foreach ($slides as $i => $slide): ?>
        <div class="slide slide--<?= (int) $slide['theme'] ?>" role="group"
             aria-roledescription="diapositiva" aria-label="<?= $i + 1 ?> de <?= $total ?>">
            <div class="slide-content">
                <h2><?= $slide['title'] ?></h2>
                <p><?= $slide['text'] ?></p>
                <a href="<?= $slide['url'] ?>" role="button"><?= $slide['cta'] ?></a>
            </div>
        </div>
        <?php endforeach ?>
    </div>

    <div class="slider-controls" data-slider-controls hidden>
        <button type="button" class="slider-btn" data-slider-prev aria-label="Diapositiva anterior"><?= icon('chevron-left') ?></button>
        <div class="slider-dots">
            <?php for ($i = 0; $i < $total; $i++): ?>
            <button type="button" class="slider-dot" data-slider-dot="<?= $i ?>" aria-label="Ir a la diapositiva <?= $i + 1 ?>"></button>
            <?php endfor ?>
        </div>
        <button type="button" class="slider-btn" data-slider-next aria-label="Diapositiva siguiente"><?= icon('chevron-right') ?></button>
        <button type="button" class="slider-btn slider-pause" data-slider-pause data-state="playing" aria-label="Pausar carrusel">
            <?= icon('stop-circle', '', 'when-playing') ?>
            <?= icon('play-circle', '', 'when-paused') ?>
        </button>
    </div>
</section>
<?php endif ?>

<section aria-labelledby="destacados">
    <h2 id="destacados">Productos destacados</h2>
    <div class="cards">
        <?php foreach ($featured as $product): ?>
        <article class="card">
            <h3><?= $product['name'] ?></h3>
            <p><?= $product['summary'] ?></p>
            <footer><strong>$<?= number_format((float) $product['price'], 2) ?></strong></footer>
        </article>
        <?php endforeach ?>
    </div>
    <p class="more"><a href="<?= $products_url ?>">Ver todos los productos &rarr;</a></p>
</section>

<!-- Quiénes somos: misión, visión y disponibilidad -->
<section class="home-section" aria-labelledby="quienes-somos">
    <h2 id="quienes-somos">Quiénes somos</h2>
    <div class="about-grid">
        <article class="info-card">
            <header class="info-card-head"><?= icon('target') ?><h3>Misión</h3></header>
            <p><?= $company['mission'] ?></p>
        </article>

        <article class="info-card">
            <header class="info-card-head"><?= icon('eye') ?><h3>Visión</h3></header>
            <p><?= $company['vision'] ?></p>
        </article>

        <!-- Widget de disponibilidad: estado calculado en el servidor según el horario. -->
        <article class="info-card availability <?= $availability['open'] ? 'is-open' : 'is-closed' ?>" aria-labelledby="disponibilidad">
            <header class="info-card-head"><?= icon('calendar-days') ?><h3 id="disponibilidad">Disponibilidad</h3></header>
            <p class="availability-status">
                <span class="status-dot" aria-hidden="true"></span>
                <strong><?= $availability['status'] ?></strong>
            </p>
            <p class="availability-detail"><?= $availability['detail'] ?></p>
            <ul class="schedule">
                <?php foreach ($availability['schedule'] as $row): ?>
                <li<?= $row['today'] ? ' class="is-today"' : '' ?>>
                    <span><?= $row['days'] ?><?= $row['today'] ? ' <small>(hoy)</small>' : '' ?></span>
                    <span><?= $row['time'] ?></span>
                </li>
                <?php endforeach ?>
            </ul>
            <p class="availability-note"><?= $company['response_time'] ?></p>
        </article>
    </div>
</section>

<!-- Valores -->
<section class="home-section" aria-labelledby="valores">
    <h2 id="valores">Nuestros valores</h2>
    <ul class="values">
        <?php foreach ($company['values'] as $value): ?>
        <li class="value">
            <strong><?= $value['name'] ?></strong>
            <span><?= $value['text'] ?></span>
        </li>
        <?php endforeach ?>
    </ul>
    <p class="more"><a href="<?= $about_url ?>">Conoce más sobre nosotros &rarr;</a></p>
</section>

<!-- Call to action -->
<section class="cta" aria-labelledby="cta-title">
    <div class="cta-content">
        <h2 id="cta-title">¿Listo para empezar tu proyecto?</h2>
        <p>Cuéntanos qué necesitas y te ayudamos a convertir tu idea en una solución real.</p>
        <div class="actions">
            <a href="<?= $contact_url ?>" role="button" class="with-icon"><?= icon('send-top-right') ?> Contáctanos</a>
            <a href="mailto:<?= $company['email'] ?>" role="button" class="secondary outline with-icon"><?= icon('mail') ?> <?= $company['email'] ?></a>
        </div>
    </div>
</section>
