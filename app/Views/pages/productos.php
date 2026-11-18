<?php /** @var array $products */ ?>
<h1>Productos</h1>
<p class="lead">Conoce nuestro catálogo.</p>

<?php if ($products): ?>
<div class="cards">
    <?php foreach ($products as $product): ?>
    <article>
        <h2 class="card-title"><?= $product['name'] ?></h2>
        <p><?= $product['summary'] ?></p>
        <footer><strong>$<?= number_format((float) $product['price'], 2) ?></strong></footer>
    </article>
    <?php endforeach ?>
</div>
<?php else: ?>
<p>Aún no hay productos disponibles.</p>
<?php endif ?>
