<?php
/**
 * Página Sobre mí.
 * @var string $app_name @var array $company Company::info() @var string $games_url
 */
?>
<p class="about-bio"><?= $company['bio'] ?></p>

<div class="about-grid about-grid--two">
    <article class="info-card">
        <header class="info-card-head"><?= icon('target') ?><h2 class="card-title">Por qué hago juegos</h2></header>
        <p><?= $company['mission'] ?></p>
    </article>
    <article class="info-card">
        <header class="info-card-head"><?= icon('eye') ?><h2 class="card-title">Hacia dónde voy</h2></header>
        <p><?= $company['vision'] ?></p>
    </article>
</div>

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
    <p class="more"><a href="<?= $games_url ?>">Ver mis juegos &rarr;</a></p>
</section>
