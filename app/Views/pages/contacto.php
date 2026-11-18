<?php
/**
 * @var array $company      Company::info()
 * @var array $availability Company::availability()
 */
?>
<h1>Contacto</h1>
<p class="lead">Escríbenos o llámanos; te responderemos lo antes posible.</p>

<div class="cards">
    <article>
        <h2 class="card-title with-icon"><?= icon('mail') ?> Correo</h2>
        <p><a href="mailto:<?= $company['email'] ?>"><?= $company['email'] ?></a></p>
    </article>
    <article>
        <h2 class="card-title with-icon"><?= icon('phone-log') ?> Teléfono</h2>
        <p><a href="tel:<?= $company['phone_href'] ?>"><?= $company['phone'] ?></a></p>
    </article>
    <article>
        <h2 class="card-title with-icon"><?= icon('home-lg') ?> Dirección</h2>
        <address><?= implode('<br>', $company['address']) ?></address>
    </article>
    <article class="availability <?= $availability['open'] ? 'is-open' : 'is-closed' ?>">
        <h2 class="card-title with-icon"><?= icon('calendar-days') ?> Horario</h2>
        <p class="availability-status">
            <span class="status-dot" aria-hidden="true"></span>
            <strong><?= $availability['status'] ?></strong>
        </p>
        <ul class="schedule">
            <?php foreach ($availability['schedule'] as $row): ?>
            <li<?= $row['today'] ? ' class="is-today"' : '' ?>>
                <span><?= $row['days'] ?></span>
                <span><?= $row['time'] ?></span>
            </li>
            <?php endforeach ?>
        </ul>
    </article>
</div>
