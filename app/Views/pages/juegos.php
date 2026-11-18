<?php
/**
 * Página Juegos: todos los juegos.
 * @var array $games @var string $contact_url
 */
?>
<?php if ($games): ?>
<div class="games-grid">
    <?php foreach ($games as $game): ?>
    <?= $this->render('partials/game-card.php', null, ['game' => $game] + get_defined_vars()) ?>
    <?php endforeach ?>
</div>
<?php else: ?>
<p class="lead">Pronto habrá juegos por aquí. ¡Vuelve en unos días!</p>
<?php endif ?>

<aside class="games-note">
    <p>¿Tienes un canal, un blog o escribes reseñas? <a href="<?= $contact_url ?>">Escríbeme y te envío una clave</a>.</p>
</aside>
