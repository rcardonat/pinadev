<?php /** @var int $code @var string $message @var string $trace @var string $home_url */ ?>
<section class="error-page">
    <p class="error-code"><?= $code ?></p>
    <h1><?= $message ?></h1>
    <p><a href="<?= $home_url ?>" role="button" class="with-icon"><?= icon('home-lg') ?> Volver al inicio</a></p>
    <?php if ($trace !== ''): ?>
    <details>
        <summary>Detalle (solo visible en modo debug)</summary>
        <pre><?= $trace ?></pre>
    </details>
    <?php endif ?>
</section>
