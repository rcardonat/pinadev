<?php
/**
 * Tarjeta de juego (Inicio y página Juegos).
 * Se incluye con: $this->render('partials/game-card.php', null, ['game' => $game] + get_defined_vars())
 * @var array $game  Game::all() + datos de PageController::prepareGames()
 */
$soon = $game['status'] !== 'available';
?>
<article class="game-card">
    <div class="game-cover game-cover--<?= (int) $game['variant'] ?>">
        <?php if ($game['cover_url'] !== ''): ?>
        <img src="<?= $game['cover_url'] ?>" alt="Portada de <?= $game['title'] ?>"
             width="<?= (int) $game['cover_w'] ?>" height="<?= (int) $game['cover_h'] ?>" loading="lazy" decoding="async">
        <?php else: ?>
        <!-- Portada de respaldo mientras no haya imagen (assets/img/juegos/<?= $game['slug'] ?>.webp). -->
        <span class="game-cover-title" aria-hidden="true"><?= $game['title'] ?></span>
        <?php endif ?>
        <span class="game-status game-status--<?= $game['status'] ?>"><?= $game['status_label'] ?></span>
    </div>

    <div class="game-body">
        <p class="game-genre"><?= $game['genre'] ?></p>
        <h3 class="game-title"><?= $game['title'] ?></h3>
        <p class="game-summary"><?= $game['summary'] ?></p>

        <ul class="game-platforms" aria-label="Plataformas">
            <?php foreach ($game['platforms'] as $platform): ?>
            <li><?= $platform ?></li>
            <?php endforeach ?>
        </ul>

        <footer class="game-footer">
            <strong class="game-price"><?= $game['price'] !== '' ? $game['price'] : 'Precio por anunciar' ?></strong>
            <span class="game-links">
                <?php if ($game['steam'] !== ''): ?>
                <a href="<?= $game['steam'] ?>" class="game-link is-steam" target="_blank" rel="noopener noreferrer">
                    <?= icon('steam') ?><?= $soon ? 'Añadir a deseados' : 'Ver en Steam' ?>
                    <span class="sr-only">(<?= $game['title'] ?>, se abre en una pestaña nueva)</span>
                </a>
                <?php endif ?>
                <?php if ($game['itch'] !== ''): ?>
                <a href="<?= $game['itch'] ?>" class="game-link is-itchio" target="_blank" rel="noopener noreferrer">
                    <?= icon('shop') ?><?= $soon ? 'Seguir en itch.io' : 'Jugar en itch.io' ?>
                    <span class="sr-only">(<?= $game['title'] ?>, se abre en una pestaña nueva)</span>
                </a>
                <?php endif ?>
            </span>
        </footer>
    </div>
</article>
