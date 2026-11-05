<?php
$purposeLabels = [
    'work' => 'Praca',
    'meetings' => 'Spotkania i szkolenia',
    'shoots' => 'Sesje foto i wideo',
    'events' => 'Kameralne wydarzenia',
];
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

    <div class="venue__layout">
        <div class="venue__details">
            <header>
                <h1><?= htmlspecialchars($venue['name']) ?></h1>
                <p><?= htmlspecialchars($venue['district'] . ', ' . $venue['address']) ?></p>
            </header>

            <img class="venue__image" src="/images/login-hero.svg" alt="">

            <section>
                <h2>Gospodarz</h2>
                <p><strong><?= htmlspecialchars($venue['host']) ?></strong> · potwierdza prośby w ciągu 24 h</p>
            </section>

            <section>
                <h2>O lokalu</h2>
                <p><?= htmlspecialchars($venue['description']) ?></p>
            </section>

            <section>
                <h2>Dozwolone cele</h2>
                <ul>
                    <?php foreach ($venue['purposes'] as $purpose): ?>
                        <li><?= $purposeLabels[$purpose] ?></li>
                    <?php endforeach; ?>
                </ul>
            </section>

            <section>
                <h2>Oferta</h2>
                <div class="offers">
                    <?php if ($venue['seatPrice'] !== null): ?>
                        <article class="offer">
                            <h3>Miejsca do pracy</h3>
                            <p><?= htmlspecialchars($venue['seatHours']) ?></p>
                            <p><strong><?= $venue['seatPrice'] ?> zł/h</strong> za miejsce</p>
                        </article>
                    <?php endif; ?>
                    <?php if ($venue['exclusivePrice'] !== null): ?>
                        <article class="offer">
                            <h3>Na wyłączność</h3>
                            <p><?= htmlspecialchars($venue['exclusiveHours']) ?></p>
                            <p><strong><?= $venue['exclusivePrice'] ?> zł/h</strong> za cały lokal</p>
                        </article>
                    <?php endif; ?>
                </div>
            </section>

            <section>
                <h2>Udogodnienia</h2>
                <ul>
                    <?php foreach ($venue['amenities'] as $amenity): ?>
                        <li><?= htmlspecialchars($amenity) ?></li>
                    <?php endforeach; ?>
                </ul>
            </section>

            <section>
                <h2>Wejście do lokalu</h2>
                <p><?= htmlspecialchars($venue['entry']) ?></p>
            </section>

            <?php if ($venue['reels'] !== []): ?>
                <section>
                    <h2>Nagrane tutaj</h2>
                    <p>Rolki najemców po wizycie</p>
                    <!-- TODO: Laboratorium 13 – wideo rolek zamiast samych podpisów. -->
                    <ul>
                        <?php foreach ($venue['reels'] as $reel): ?>
                            <li><?= htmlspecialchars($reel) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </section>
            <?php endif; ?>
        </div>

        <aside class="reservation-panel" id="reservation" aria-labelledby="reservation-heading">
            <h2 id="reservation-heading">Rezerwacja</h2>

            <form action="/venues/<?= $venue['id'] ?>/reservations#reservation" method="post">
                <?php require __DIR__ . '/partials/form-errors.html.php'; ?>

                <fieldset>
                    <legend>Tryb</legend>
                    <?php if ($venue['seatPrice'] !== null): ?>
                        <label class="checkbox"><input type="radio" name="mode" value="seats" <?= $form->mode === 'seats' ? 'checked' : '' ?>> Miejsce do pracy · <?= $venue['seatPrice'] ?> zł/h</label>
                    <?php endif; ?>
                    <?php if ($venue['exclusivePrice'] !== null): ?>
                        <label class="checkbox"><input type="radio" name="mode" value="exclusive" <?= $form->mode === 'exclusive' ? 'checked' : '' ?>> Cały lokal · <?= $venue['exclusivePrice'] ?> zł/h</label>
                    <?php endif; ?>
                </fieldset>

                <div class="field">
                    <label for="purpose">Cel</label>
                    <select id="purpose" name="purpose" <?= $form->invalidAttributes('purpose') ?>>
                        <?php foreach ($venue['purposes'] as $purpose): ?>
                            <option value="<?= $purpose ?>" <?= $form->purpose === $purpose ? 'selected' : '' ?>><?= $purposeLabels[$purpose] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="field">
                    <label for="date">Data</label>
                    <input type="date" id="date" name="date" value="<?= htmlspecialchars($form->date) ?>" min="<?= date('Y-m-d') ?>" required <?= $form->invalidAttributes('date') ?>>
                </div>

                <div class="reservation-panel__hours">
                    <div class="field">
                        <label for="time-from">Od godziny</label>
                        <input type="time" id="time-from" name="from" value="<?= htmlspecialchars($form->from) ?>" step="3600" required <?= $form->invalidAttributes('from') ?>>
                    </div>
                    <div class="field">
                        <label for="time-to">Do godziny</label>
                        <input type="time" id="time-to" name="to" value="<?= htmlspecialchars($form->to) ?>" step="3600" required <?= $form->invalidAttributes('to') ?>>
                    </div>
                </div>

                <div class="field">
                    <label for="seats">Liczba miejsc</label>
                    <input type="number" id="seats" name="seats" min="1" max="4" value="<?= htmlspecialchars($form->seats) ?>" <?= $form->invalidAttributes('seats', 'seats-hint') ?>>
                    <p class="field__hint" id="seats-hint">Tylko przy miejscach do pracy, maks. 4</p>
                </div>

                <p class="field__hint">Gospodarz potwierdza w ciągu 24 h. Do tego czasu termin jest zarezerwowany dla Ciebie.</p>

                <button class="button" type="submit">Wyślij prośbę o rezerwację</button>
            </form>
        </aside>
    </div>
</main>

<?php require __DIR__ . '/partials/footer.html.php'; ?>
