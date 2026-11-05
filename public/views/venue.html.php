<?php
$purposes = require __DIR__ . '/partials/purposes.php';
$dayNames = [1 => 'pon', 'wt', 'śr', 'czw', 'pt', 'sob', 'nd'];
$monthNames = [1 => 'styczeń', 'luty', 'marzec', 'kwiecień', 'maj', 'czerwiec', 'lipiec', 'sierpień', 'wrzesień', 'październik', 'listopad', 'grudzień'];

// Teksty i ceny obu trybów; strona pokazuje tylko tryby, dla których gospodarz ma okno dostępności.
// TODO: Laboratorium 8 – wolne miejsca w godzinie liczone z zapisanych rezerwacji.
$modes = [
    'seats' => ['name' => 'Miejsca do pracy', 'choice' => 'Miejsce do pracy', 'icon' => 'fa-laptop',
        'price' => $venue['seatPrice'], 'unit' => 'za miejsce',
        'details' => $venue['seatCount'] . ' miejsc · rezerwujesz pojedyncze miejsca',
        'free' => $venue['seatCount'] . ' z ' . $venue['seatCount'] . ' wolnych'],
    'exclusive' => ['name' => 'Na wyłączność', 'choice' => 'Cały lokal', 'icon' => 'fa-key',
        'price' => $venue['exclusivePrice'], 'unit' => 'za cały lokal',
        'details' => 'do ' . $venue['capacity'] . ' osób · min. ' . $form::MIN_HOURS . ' h',
        'free' => 'wolne'],
];
$activeMode = isset($venue['windows'][$form->mode]) ? $form->mode : array_key_first($venue['windows']);

// Cele, na które gospodarz nie pozwala – do zdania pod listą dozwolonych celów.
$deniedPurposes = [];
foreach ($purposes as $value => [$label]) {
    if (!in_array($value, $venue['purposes'], true)) {
        $deniedPurposes[] = mb_strtolower($label);
    }
}

$initials = require __DIR__ . '/partials/initials.php';
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
            <!-- TODO: Laboratorium 13 – wideo rolki gospodarza zamiast ilustracji. -->
            <figure class="reel reel--host">
                <img src="/images/login-hero.svg" alt="">
                <figcaption><?= htmlspecialchars($venue['reelTitle']) ?></figcaption>
            </figure>

            <ul class="venue-photos" aria-label="Zdjęcia lokalu dodane przez gospodarza">
                <?php foreach ($venue['photos'] as $photo): ?>
                    <li>
                        <img src="<?= htmlspecialchars($photo) ?>" alt="Zdjęcie lokalu">
                    </li>
                <?php endforeach; ?>
            </ul>
        </section>

        <div class="venue__details">
            <section class="card host-card">
                <span class="host-card__avatar" aria-hidden="true"><?= htmlspecialchars($initials($venue['host'])) ?></span>
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
                        <li class="chip"><i class="fa-solid <?= $purposes[$purpose][1] ?>" aria-hidden="true"></i> <?= $purposes[$purpose][0] ?></li>
                    <?php endforeach; ?>
                </ul>
                <?php if ($deniedPurposes !== []): ?>
                    <p class="venue__muted">Gospodarz nie zezwala na: <?= implode(', ', $deniedPurposes) ?>.</p>
                <?php endif; ?>
            </section>

            <section>
                <h2>Oferta</h2>
                <?php foreach ($venue['windows'] as $mode => $window): ?>
                    <article class="card offer">
                        <div>
                            <span class="badge badge--<?= $mode ?>"><i class="fa-solid <?= $modes[$mode]['icon'] ?>" aria-hidden="true"></i> <?= $modes[$mode]['name'] ?></span>
                            <h3><?= htmlspecialchars($window['label']) ?> <?= $window['from'] ?>:00–<?= $window['to'] ?>:00</h3>
                            <p class="venue__muted"><?= $modes[$mode]['details'] ?></p>
                        </div>
                        <p class="offer__price"><strong><?= $modes[$mode]['price'] ?> zł/h</strong> <?= $modes[$mode]['unit'] ?></p>
                    </article>
                <?php endforeach; ?>
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

        <aside class="card reservation-panel" id="reservation" aria-labelledby="reservation-heading">
            <div class="reservation-panel__title">
                <h2 id="reservation-heading">Rezerwacja</h2>
                <p><strong data-price><?= $modes[$activeMode]['price'] ?> zł/h</strong> <span data-unit><?= $modes[$activeMode]['unit'] ?></span></p>
            </div>

            <form action="/venues/<?= $venue['id'] ?>/reservations#reservation" method="post">
                <?php require __DIR__ . '/partials/form-errors.html.php'; ?>

                <fieldset class="mode-switch">
                    <legend class="visually-hidden">Tryb rezerwacji</legend>
                    <?php foreach ($venue['windows'] as $mode => $window): ?>
                        <label>
                            <input type="radio" name="mode" value="<?= $mode ?>" data-price="<?= $modes[$mode]['price'] ?>" data-unit="<?= $modes[$mode]['unit'] ?>" <?= $mode === $activeMode ? 'checked' : '' ?>>
                            <i class="fa-solid <?= $modes[$mode]['icon'] ?>" aria-hidden="true"></i> <?= $modes[$mode]['choice'] ?>
                        </label>
                    <?php endforeach; ?>
                </fieldset>

                <div class="field">
                    <label for="purpose">Cel</label>
                    <div class="select">
                        <i class="fa-solid <?= $purposes[$form->purpose][1] ?? 'fa-laptop' ?>" aria-hidden="true" data-purpose-icon></i>
                        <select id="purpose" name="purpose" <?= $form->invalidAttributes('purpose') ?>>
                            <?php foreach ($venue['purposes'] as $purpose): ?>
                                <option value="<?= $purpose ?>" data-icon="<?= $purposes[$purpose][1] ?>" <?= $form->purpose === $purpose ? 'selected' : '' ?>><?= $purposes[$purpose][0] ?></option>
                            <?php endforeach; ?>
                        </select>
                        <i class="fa-solid fa-chevron-down" aria-hidden="true"></i>
                    </div>
                </div>

                <!-- Dni i godziny każdego trybu; pola niewybranego trybu są wyłączone (disabled), więc formularz ich nie wysyła. -->
                <?php foreach ($venue['windows'] as $mode => $window): ?>
                    <fieldset class="slots" data-mode="<?= $mode ?>" <?= $mode === $activeMode ? '' : 'disabled hidden' ?>>
                        <div class="slots__header">
                            <span id="date-<?= $mode ?>">Data</span>
                            <span class="venue__muted"><?= $monthNames[(int) $days[0]->format('n')] ?></span>
                        </div>
                        <div class="day-chips" role="group" aria-labelledby="date-<?= $mode ?>" <?= $form->invalidAttributes('date') ?>>
                            <?php foreach ($days as $day): ?>
                                <?php $isOpen = in_array((int) $day->format('N'), $window['days'], true); ?>
                                <label class="day-chip">
                                    <input type="radio" name="date" value="<?= $day->format('Y-m-d') ?>" data-label="<?= $dayNames[(int) $day->format('N')] . ' ' . $day->format('d.m') ?>" <?= $isOpen ? '' : 'disabled' ?> <?= $form->date === $day->format('Y-m-d') ? 'checked' : '' ?>>
                                    <?= $dayNames[(int) $day->format('N')] ?>
                                    <strong><?= $day->format('j') ?></strong>
                                    <span><?= $isOpen ? 'wolne' : '—' ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>

                        <div class="slots__header">
                            <span id="hours-<?= $mode ?>">Godziny</span>
                            <span class="venue__muted" data-selected></span>
                        </div>
                        <div class="hour-list" role="group" aria-labelledby="hours-<?= $mode ?>" <?= $form->invalidAttributes('hours') ?>>
                            <?php for ($hour = $window['from']; $hour < $window['to']; $hour++): ?>
                                <label class="hour-row">
                                    <input type="checkbox" name="hours[]" value="<?= $hour ?>" <?= in_array($hour, $form->hours, true) ? 'checked' : '' ?>>
                                    <strong><?= $hour ?>:00</strong>
                                    <span class="venue__muted">· <?= $modes[$mode]['free'] ?></span>
                                    <span class="hour-row__bar" aria-hidden="true"></span>
                                </label>
                            <?php endfor; ?>
                        </div>

                        <?php if ($mode === 'seats'): ?>
                            <div class="seats">
                                <label for="seats">
                                    Liczba miejsc: <span data-seats-label><?= htmlspecialchars($form->seats) ?></span>
                                    <span class="venue__muted" id="seats-hint">maks. <?= $form::MAX_SEATS ?> w tym zakresie</span>
                                </label>
                                <div class="stepper">
                                    <button type="button" data-step="-1" aria-label="Mniej miejsc"><i class="fa-solid fa-minus" aria-hidden="true"></i></button>
                                    <input type="number" id="seats" name="seats" min="1" max="<?= $form::MAX_SEATS ?>" value="<?= htmlspecialchars($form->seats) ?>" <?= $form->invalidAttributes('seats', 'seats-hint') ?>>
                                    <button type="button" data-step="1" aria-label="Więcej miejsc"><i class="fa-solid fa-plus" aria-hidden="true"></i></button>
                                </div>
                            </div>
                        <?php endif; ?>
                    </fieldset>
                <?php endforeach; ?>

                <div class="summary" hidden>
                    <p><i class="fa-regular fa-calendar" aria-hidden="true"></i> <span data-term></span></p>
                    <p><span data-calc></span> <span data-total></span></p>
                    <p class="summary__total">Razem <span data-total></span></p>
                </div>

                <p class="reservation-panel__note">
                    <i class="fa-solid fa-circle-info" aria-hidden="true"></i>
                    Gospodarz potwierdza w ciągu 24 h. Do tego czasu miejsce jest zarezerwowane dla Ciebie.
                </p>

                <button class="button" type="submit">Wyślij prośbę o rezerwację</button>
            </form>
        </aside>
    </div>
</main>

<?php require __DIR__ . '/partials/footer.html.php'; ?>

<script src="/js/reservation.js" defer></script>
