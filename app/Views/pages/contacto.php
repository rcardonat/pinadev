<?php
/**
 * Contacto: formulario por preguntas + datos de contacto.
 *
 * Preguntas: 1 nombre · 2 necesidad · 3 teléfono (opcional, «Paso») ·
 *            4 correo · 5 comentario (opcional; se envía con «Enviar», aunque esté vacío).
 *
 * Sin JS: todas las preguntas se ven como un formulario normal.
 * Con JS (app.js, [data-qform]): una pregunta a la vez, con progreso.
 *
 * @var array  $company @var array $availability
 * @var string $contact_url @var string $csrf
 * @var array  $needs                          ContactRequest::NEEDS
 * @var int    $comment_max
 * @var array  $old @var array $errors         valores y errores por campo
 * @var string $alert                          mensaje general de error
 * @var array|null $flash                      resultado del último envío
 */
$e = static fn (string $field): string => $errors[$field] ?? '';
$invalid = static fn (string $field): string => ($errors[$field] ?? '') !== '' ? ' aria-invalid="true"' : '';
?>
<div class="contact-layout">
    <section class="qform-wrap" aria-labelledby="qform-title">
        <?php if ($flash && !empty($flash['ok'])): ?>
        <!-- Envío correcto -->
        <div class="qform-success" role="status" tabindex="-1" data-qform-success>
            <span class="qform-success-check" aria-hidden="true"></span>
            <h2 id="qform-title">¡Mensaje enviado!</h2>
            <p>
                <?php if ($flash['name'] !== ''): ?>
                Gracias, <strong><?= $flash['name'] ?></strong>. Te responderé lo antes posible.
                <?php else: ?>
                Gracias. Te responderé lo antes posible.
                <?php endif ?>
            </p>
            <p class="qform-note"><?= $company['response_time'] ?></p>
            <a href="<?= $contact_url ?>" role="button" class="secondary outline">Enviar otro mensaje</a>
        </div>
        <?php else: ?>

        <h2 id="qform-title" class="qform-title">Escríbeme</h2>

        <?php if ($alert !== ''): ?>
        <p class="qform-alert" role="alert"><?= $alert ?></p>
        <?php endif ?>

        <form class="qform" method="post" action="<?= $contact_url ?>" novalidate data-qform>
            <input type="hidden" name="csrf" value="<?= $csrf ?>">

            <!-- Campo trampa para bots: invisible para personas y lectores de pantalla. -->
            <div class="qform-hp" aria-hidden="true">
                <label>No completar este campo <input type="text" name="website" tabindex="-1" autocomplete="off"></label>
            </div>

            <div class="qform-progress" data-qform-progress hidden>
                <span class="qform-bar"><i data-qform-bar></i></span>
                <span class="qform-count" data-qform-count></span>
            </div>

            <!-- 1. Nombre (obligatorio) -->
            <fieldset class="qstep" data-step>
                <legend><span class="qnum">1</span> ¿Cómo te llamas?</legend>
                <input type="text" id="f-name" name="name" value="<?= $old['name'] ?>"
                       autocomplete="name" maxlength="80" required placeholder="Escribe tu nombre"
                       aria-label="Tu nombre" aria-describedby="err-name"<?= $invalid('name') ?>>
                <p class="qerror" id="err-name"<?= $e('name') === '' ? ' hidden' : '' ?>><?= $e('name') ?></p>
            </fieldset>

            <!-- 2. Necesidad (obligatoria) -->
            <fieldset class="qstep" data-step aria-describedby="err-need">
                <legend><span class="qnum">2</span> ¿De qué quieres hablar?</legend>
                <div class="qoptions">
                    <?php foreach ($needs as $value => $opt): ?>
                    <label class="qoption">
                        <input type="radio" name="need" value="<?= $value ?>" required<?= $old['need'] === $value ? ' checked' : '' ?>>
                        <span class="qoption-body">
                            <strong><?= $opt['label'] ?></strong>
                            <small><?= $opt['text'] ?></small>
                        </span>
                    </label>
                    <?php endforeach ?>
                </div>
                <p class="qerror" id="err-need"<?= $e('need') === '' ? ' hidden' : '' ?>><?= $e('need') ?></p>
            </fieldset>

            <!-- 3. Teléfono (opcional: «Paso» lo salta) -->
            <fieldset class="qstep" data-step data-optional>
                <legend>
                    <span class="qnum">3</span> ¿A qué número te llamo?
                    <small class="qoptional">Opcional</small>
                </legend>
                <div class="qinput-icon">
                    <span aria-hidden="true"><?= icon('phone-log') ?></span>
                    <input type="tel" id="f-phone" name="phone" value="<?= $old['phone'] ?>"
                           autocomplete="tel" inputmode="tel" maxlength="20" placeholder="+593 99 123 4567"
                           aria-label="Tu número de teléfono (opcional)" aria-describedby="err-phone"<?= $invalid('phone') ?>>
                </div>
                <p class="qerror" id="err-phone"<?= $e('phone') === '' ? ' hidden' : '' ?>><?= $e('phone') ?></p>
            </fieldset>

            <!-- 4. Correo (obligatorio) -->
            <fieldset class="qstep" data-step>
                <legend><span class="qnum">4</span> ¿A dónde te escribo?</legend>
                <div class="qinput-icon">
                    <span aria-hidden="true"><?= icon('mail') ?></span>
                    <input type="email" id="f-email" name="email" value="<?= $old['email'] ?>"
                           autocomplete="email" maxlength="190" required placeholder="nombre@dominio.com"
                           aria-label="Tu correo electrónico" aria-describedby="err-email"<?= $invalid('email') ?>>
                </div>
                <p class="qerror" id="err-email"<?= $e('email') === '' ? ' hidden' : '' ?>><?= $e('email') ?></p>
            </fieldset>

            <!-- 5. Comentario (opcional: «Enviar» funciona aunque esté vacío) -->
            <fieldset class="qstep" data-step data-optional data-final>
                <legend>
                    <span class="qnum">5</span> Cuéntame
                    <small class="qoptional">Opcional</small>
                </legend>
                <textarea id="f-comment" name="comment" rows="5" maxlength="<?= (int) $comment_max ?>"
                          placeholder="Cuéntame los detalles: qué juego, qué pasó, qué tienes en mente…"
                          aria-label="Tu comentario (opcional)" aria-describedby="err-comment comment-count"<?= $invalid('comment') ?>><?= $old['comment'] ?></textarea>
                <p class="qcounter" id="comment-count" data-qform-counter hidden></p>
                <p class="qerror" id="err-comment"<?= $e('comment') === '' ? ' hidden' : '' ?>><?= $e('comment') ?></p>
            </fieldset>

            <!-- Botones. Sin JS solo se ve «Enviar» (envía todo el formulario). -->
            <div class="qform-nav">
                <button type="button" class="secondary outline" data-qform-back hidden>&larr; Atrás</button>
                <span class="qform-nav-end">
                    <button type="button" class="secondary outline" data-qform-skip hidden>Paso</button>
                    <button type="button" data-qform-next hidden>Siguiente &rarr;</button>
                    <button type="submit" data-qform-submit>Enviar</button>
                </span>
            </div>
        </form>
        <?php endif ?>
    </section>

    <aside class="contact-aside" aria-label="Otros medios de contacto">
        <article>
            <h2 class="card-title with-icon"><?= icon('mail') ?> Correo</h2>
            <p><a href="mailto:<?= $company['email'] ?>"><?= $company['email'] ?></a></p>
        </article>
        <article>
            <h2 class="card-title with-icon"><?= icon('phone-log') ?> Teléfono</h2>
            <p><a href="tel:<?= $company['phone_href'] ?>"><?= $company['phone'] ?></a></p>
        </article>
        <article>
            <h2 class="card-title with-icon"><?= icon('home-lg') ?> Ubicación</h2>
            <p><?= implode('<br>', $company['location']) ?></p>
        </article>
        <article class="availability <?= $availability['open'] ? 'is-open' : 'is-closed' ?>">
            <h2 class="card-title with-icon"><?= icon('calendar-days') ?> ¿Cuándo respondo?</h2>
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
    </aside>
</div>
