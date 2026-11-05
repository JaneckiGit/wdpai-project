<?php
$purposes = require __DIR__ . '/partials/purposes.php';

// Cele, na które gospodarz nie pozwala – do zdania pod listą dozwolonych celów.
$deniedPurposes = [];
foreach ($purposes as $value => [$label]) {
    if (!in_array($value, $venue['purposes'], true)) {
        $deniedPurposes[] = mb_strtolower($label);
    }
}

// Inicjały gospodarza w kółku: pierwsza litera każdego słowa.
$hostInitials = implode('', array_map(static fn (string $word): string => mb_substr($word, 0, 1), explode(' ', $venue['host'])));
?>
<?php require __DIR__ . '/partials/header.html.php'; ?>

<main class="venue">
    <?php require __DIR__ . '/partials/status-message.html.php'; ?>

    <nav class="breadcrumbs" aria-label="Ścieżka">
        <ol>
            <li><a href="/venues">Odkrywaj</a></li>
            <li><a href="/venues?q=<?= urlencode($venue['district']) ?>"><?= htmlspecialchars($venue['district']) ?></a></li>
            <li aria-current="page"><?= htmlspecialchars($venue['name']) ?></li>
        </ol>
    </nav>

    <h1><?= htmlspecialchars($venue['name']) ?></h1>
    <p class="venue__address">
        <i class="fa-solid fa-location-dot" aria-hidden="true"></i>
        <?= htmlspecialchars($venue['district'] . ', ' . $venue['address']) ?>
    </p>

    <div class="venue__layout">
        <section class="venue__media" aria-label="Zdjęcia i rolki">
            <!-- TODO: Laboratorium 13 – wideo rolki gospodarza i rolek najemców zamiast samej ilustracji. -->
            <figure class="reel reel--host">
                <img src="/images/login-hero.svg" alt="">
                <figcaption><?= htmlspecialchars($venue['reelTitle']) ?></figcaption>
            </figure>

            <?php if ($venue['reels'] !== []): ?>
                <h2>Nagrane tutaj</h2>
                <p class="venue__muted">Rolki najemców po wizycie</p>
                <ul class="reels-grid">
                    <?php foreach ($venue['reels'] as [$purpose, $author]): ?>
                        <li class="reel">
                            <img src="/images/login-hero.svg" alt="">
                            <span><strong><?= htmlspecialchars($purpose) ?></strong><?= htmlspecialchars($author) ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </section>

        <div class="venue__details">
            <section class="host-card">
                <span class="host-card__avatar" aria-hidden="true"><?= htmlspecialchars($hostInitials) ?></span>
                <div>
                    <p class="venue__muted">Gospodarz</p>
                    <h2><?= htmlspecialchars($venue['host']) ?></h2>
                    <p class="venue__muted">potwierdza prośby w ciągu 24 h</p>
                </div>
            </section>

            <section>
                <h2>O lokalu</h2>
                <p><?= htmlspecialchars($venue['description']) ?></p>
            </section>

            <section>
                <h2>Dozwolone cele</h2>
                <ul class="purpose-list">
                    <?php foreach ($venue['purposes'] as $purpose): ?>
                        <li><i class="fa-solid <?= $purposes[$purpose][1] ?>" aria-hidden="true"></i> <?= $purposes[$purpose][0] ?></li>
                    <?php endforeach; ?>
                </ul>
                <?php if ($deniedPurposes !== []): ?>
                    <p class="venue__muted">Gospodarz nie zezwala na: <?= implode(', ', $deniedPurposes) ?>.</p>
                <?php endif; ?>
            </section>

            <section>
                <h2>Oferta</h2>
                <?php if ($venue['seatPrice'] !== null): ?>
                    <article class="offer">
                        <div>
                            <span class="offer__badge offer__badge--seats"><i class="fa-solid fa-laptop" aria-hidden="true"></i> Miejsca do pracy</span>
                            <h3><?= htmlspecialchars($venue['seatHours']) ?></h3>
                            <p class="venue__muted"><?= $venue['seatCount'] ?> miejsc · rezerwujesz pojedyncze miejsca</p>
                        </div>
                        <p class="offer__price"><strong><?= $venue['seatPrice'] ?> zł/h</strong> za miejsce</p>
                    </article>
                <?php endif; ?>
                <?php if ($venue['exclusivePrice'] !== null): ?>
                    <article class="offer">
                        <div>
                            <span class="offer__badge offer__badge--exclusive"><i class="fa-solid fa-key" aria-hidden="true"></i> Na wyłączność</span>
                            <h3><?= htmlspecialchars($venue['exclusiveHours']) ?></h3>
                            <p class="venue__muted">do <?= $venue['capacity'] ?> osób · min. 2 h</p>
                        </div>
                        <p class="offer__price"><strong><?= $venue['exclusivePrice'] ?> zł/h</strong> za cały lokal</p>
                    </article>
                <?php endif; ?>
            </section>

            <section>
                <h2>Udogodnienia</h2>
                <ul class="amenities">
                    <?php foreach ($venue['amenities'] as [$icon, $name, $note]): ?>
                        <li>
                            <span class="icon-tile"><i class="fa-solid <?= $icon ?>" aria-hidden="true"></i></span>
                            <span><strong><?= htmlspecialchars($name) ?></strong><?= htmlspecialchars($note) ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </section>

            <section class="entry">
                <span class="icon-tile"><i class="fa-solid fa-user-check" aria-hidden="true"></i></span>
                <p><strong>Wejście: <?= htmlspecialchars($venue['entry']) ?></strong> Szczegóły wejścia zobaczysz po akceptacji rezerwacji.</p>
            </section>
        </div>

        <aside class="reservation-panel" id="reservation" aria-labelledby="reservation-heading">
            <div class="reservation-panel__title">
                <h2 id="reservation-heading">Rezerwacja</h2>
                <?php if ($venue['seatPrice'] !== null): ?>
                    <p><strong><?= $venue['seatPrice'] ?> zł/h</strong> za miejsce</p>
                <?php endif; ?>
            </div>

            <form action="/venues/<?= $venue['id'] ?>/reservations#reservation" method="post">
                <?php require __DIR__ . '/partials/form-errors.html.php'; ?>

                <!-- Przełącznik trybu to dwa pola radio; wybrane pole wyróżnia selektor :has(:checked). -->
                <fieldset class="mode-switch">
                    <legend class="visually-hidden">Tryb rezerwacji</legend>
                    <?php if ($venue['seatPrice'] !== null): ?>
                        <label><input type="radio" name="mode" value="seats" <?= $form->mode === 'seats' ? 'checked' : '' ?>> <i class="fa-solid fa-laptop" aria-hidden="true"></i> Miejsce do pracy</label>
                    <?php endif; ?>
                    <?php if ($venue['exclusivePrice'] !== null): ?>
                        <label><input type="radio" name="mode" value="exclusive" <?= $form->mode === 'exclusive' ? 'checked' : '' ?>> <i class="fa-solid fa-key" aria-hidden="true"></i> Cały lokal</label>
                    <?php endif; ?>
                </fieldset>

                <div class="field">
                    <label for="purpose">Cel</label>
                    <select id="purpose" name="purpose" <?= $form->invalidAttributes('purpose') ?>>
                        <?php foreach ($venue['purposes'] as $purpose): ?>
                            <option value="<?= $purpose ?>" <?= $form->purpose === $purpose ? 'selected' : '' ?>><?= $purposes[$purpose][0] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="field">
                    <label for="date">Data</label>
                    <input type="date" id="date" name="date" value="<?= htmlspecialchars($form->date) ?>" min="<?= date('Y-m-d') ?>" required <?= $form->invalidAttributes('date') ?>>
                </div>

                <fieldset class="field">
                    <legend>Godziny</legend>
                    <div class="reservation-panel__hours">
                        <label class="visually-hidden" for="time-from">Od godziny</label>
                        <input type="time" id="time-from" name="from" value="<?= htmlspecialchars($form->from) ?>" step="3600" required <?= $form->invalidAttributes('from') ?>>
                        <span aria-hidden="true">–</span>
                        <label class="visually-hidden" for="time-to">Do godziny</label>
                        <input type="time" id="time-to" name="to" value="<?= htmlspecialchars($form->to) ?>" step="3600" required <?= $form->invalidAttributes('to') ?>>
                    </div>
                </fieldset>

                <div class="reservation-panel__seats">
                    <label for="seats">Liczba miejsc <span class="venue__muted" id="seats-hint">maks. 4, tylko przy miejscach do pracy</span></label>
                    <input type="number" id="seats" name="seats" min="1" max="4" value="<?= htmlspecialchars($form->seats) ?>" <?= $form->invalidAttributes('seats', 'seats-hint') ?>>
                </div>

                <p class="reservation-panel__note">
                    <i class="fa-solid fa-circle-info" aria-hidden="true"></i>
                    Gospodarz potwierdza w ciągu 24 h. Do tego czasu termin jest zarezerwowany dla Ciebie.
                </p>

                <button class="button" type="submit">Wyślij prośbę o rezerwację</button>
            </form>
        </aside>
    </div>
</main>

<?php require __DIR__ . '/partials/footer.html.php'; ?>
