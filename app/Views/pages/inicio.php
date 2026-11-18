<?php
/**
 * @var string $app_name @var array $slides @var array $games
 * @var array  $company      Company::info()
 * @var array  $availability Company::availability()
 * @var string $games_url @var string $about_url @var string $contact_url @var string $itch_url
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
<section class="slider" data-slider data-effect="<?= $slider_effect ?>" aria-roledescription="carrusel" aria-label="Destacados">
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

<!-- Apartado destacado: imagen con insignia flotante + titular, beneficios y botón. -->
<section class="highlight" aria-labelledby="highlight-title">
    <div class="highlight-media">
        <?php if ($highlight['image_url'] !== ''): ?>
        <img src="<?= $highlight['image_url'] ?>" alt="<?= $highlight['image_alt'] ?>"
             width="<?= (int) $highlight['image_w'] ?>" height="<?= (int) $highlight['image_h'] ?>"
             loading="lazy" decoding="async">
        <?php else: ?>
        <!-- Ilustración de respaldo mientras no haya foto (decorativa). -->
        <div class="highlight-placeholder" aria-hidden="true">
            <span class="ph-window">
                <span class="ph-dots"><i></i><i></i><i></i></span>
                <span class="ph-line ph-line--lg"></span>
                <span class="ph-line"></span>
                <span class="ph-line ph-line--sm"></span>
                <span class="ph-blocks"><i></i><i></i></span>
            </span>
        </div>
        <?php endif ?>

        <div class="floating-badge">
            <strong><?= $highlight['badge_value'] ?></strong>
            <span><?= $highlight['badge_text'] ?></span>
        </div>
    </div>

    <div class="highlight-content">
        <h2 id="highlight-title"><?= $highlight['title'] ?></h2>
        <p class="lead"><?= $highlight['subtitle'] ?></p>

        <ul class="features">
            <?php foreach ($highlight['features'] as $feature): ?>
            <?php $featureIcon = icon($feature['icon']) ?>
            <li class="feature-item">
                <span class="feature-icon" aria-hidden="true"><?= $featureIcon !== '' ? $featureIcon : '<span class="feature-dot"></span>' ?></span>
                <div>
                    <h3><?= $feature['title'] ?></h3>
                    <p><?= $feature['text'] ?></p>
                </div>
            </li>
            <?php endforeach ?>
        </ul>

        <a href="<?= $highlight['url'] ?>" role="button" class="highlight-btn"><?= $highlight['cta'] ?> <span aria-hidden="true">&rarr;</span></a>
    </div>
</section>

<section aria-labelledby="mis-juegos">
    <h2 id="mis-juegos">Mis juegos</h2>
    <div class="games-grid">
        <?php foreach ($games as $game): ?>
        <?= $this->render('partials/game-card.php', null, ['game' => $game] + get_defined_vars()) ?>
        <?php endforeach ?>
    </div>
    <p class="more"><a href="<?= $games_url ?>">Ver todos mis juegos &rarr;</a></p>
</section>

<!-- En números: el valor final viene del servidor; app.js lo anima desde 0 al verse. -->
<?php if ($stats): ?>
<section class="home-section" aria-labelledby="en-numeros">
    <h2 id="en-numeros">En números</h2>
    <dl class="stats">
        <?php foreach ($stats as $stat): ?>
        <div class="stat">
            <dt class="stat-label"><?= icon($stat['icon']) ?><?= $stat['label'] ?></dt>
            <dd class="stat-value">
                <span aria-hidden="true"><span data-count-to="<?= (int) $stat['value'] ?>"><?= number_format((int) $stat['value'], 0, ',', '.') ?></span><?= $stat['suffix'] ?></span>
                <span class="sr-only"><?= number_format((int) $stat['value'], 0, ',', '.') ?><?= $stat['suffix'] ?></span>
            </dd>
        </div>
        <?php endforeach ?>
    </dl>
</section>
<?php endif ?>

<!-- Sobre mí: por qué hago juegos, hacia dónde voy y cuándo respondo -->
<section class="home-section" aria-labelledby="sobre-mi">
    <h2 id="sobre-mi">Detrás de los juegos</h2>
    <div class="about-grid">
        <article class="info-card">
            <header class="info-card-head"><?= icon('target') ?><h3>Por qué hago juegos</h3></header>
            <p><?= $company['mission'] ?></p>
        </article>

        <article class="info-card">
            <header class="info-card-head"><?= icon('eye') ?><h3>Hacia dónde voy</h3></header>
            <p><?= $company['vision'] ?></p>
        </article>

        <!-- Widget de disponibilidad: estado calculado en el servidor según el horario. -->
        <article class="info-card availability <?= $availability['open'] ? 'is-open' : 'is-closed' ?>" aria-labelledby="disponibilidad">
            <header class="info-card-head"><?= icon('calendar-days') ?><h3 id="disponibilidad">¿Cuándo respondo?</h3></header>
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
    <h2 id="valores">Lo que guía mis juegos</h2>
    <ul class="values">
        <?php foreach ($company['values'] as $value): ?>
        <li class="value">
            <strong><?= $value['name'] ?></strong>
            <span><?= $value['text'] ?></span>
        </li>
        <?php endforeach ?>
    </ul>
    <p class="more"><a href="<?= $about_url ?>">Conoce más sobre mí &rarr;</a></p>
</section>

<!-- Call to action -->
<section class="cta" aria-labelledby="cta-title">
    <div class="cta-content">
        <h2 id="cta-title">¿Te gustan mis juegos?</h2>
        <p>Sígueme en itch.io para enterarte de cada lanzamiento, o escríbeme si quieres una clave, una entrevista o colaborar.</p>
        <div class="actions">
            <?php if ($itch_url !== ''): ?>
            <a href="<?= $itch_url ?>" role="button" class="with-icon" target="_blank" rel="noopener noreferrer"><?= icon('shop') ?> Seguir en itch.io<span class="sr-only"> (se abre en una pestaña nueva)</span></a>
            <?php endif ?>
            <a href="<?= $contact_url ?>" role="button" class="secondary outline with-icon"><?= icon('send-top-right') ?> Escríbeme</a>
        </div>
    </div>
</section>
