<?php /** @var string $app_name @var array $company Company::info() */ ?>
<h1>Nosotros</h1>
<p class="lead">Breve historia de <?= $app_name ?>: cómo nació, qué hacemos y qué nos diferencia.</p>

<div class="about-grid about-grid--two">
    <article class="info-card">
        <header class="info-card-head"><?= icon('target') ?><h2 class="card-title">Misión</h2></header>
        <p><?= $company['mission'] ?></p>
    </article>
    <article class="info-card">
        <header class="info-card-head"><?= icon('eye') ?><h2 class="card-title">Visión</h2></header>
        <p><?= $company['vision'] ?></p>
    </article>
</div>

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
</section>
