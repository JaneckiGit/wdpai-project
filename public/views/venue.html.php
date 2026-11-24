<?php
$purposeIcons = require __DIR__ . '/partials/purposes.php';
$amenityIcons = require __DIR__ . '/partials/amenities.php';
$dayNames = require __DIR__ . '/partials/day-names.php';
$dayRange = require __DIR__ . '/partials/day-range.php';
$price = require __DIR__ . '/partials/price.php';
$initials = require __DIR__ . '/partials/initials.php';
$monthNames = [1 => 'styczeń', 'luty', 'marzec', 'kwiecień', 'maj', 'czerwiec', 'lipiec', 'sierpień', 'wrzesień', 'październik', 'listopad', 'grudzień'];
$entryLabels = ['staff' => 'na miejscu będzie pracownik', 'key' => 'przekazanie klucza'];

// Teksty obu trybów; strona pokazuje tylko tryby, dla których gospodarz ma okno dostępności.
$modes = [
    'seats' => ['name' => 'Miejsca do pracy', 'choice' => 'Miejsce do pracy', 'icon' => 'fa-laptop', 'unit' => 'za miejsce'],
    'exclusive' => ['name' => 'Na wyłączność', 'choice' => 'Cały lokal', 'icon' => 'fa-key', 'unit' => 'za cały lokal'],
];
// Opis okna pod jego godzinami, np. „10 miejsc · rezerwujesz pojedyncze miejsca” albo „do 25 osób · min. 2 h”.
$windowDetails = static fn (App\Model\AvailabilityWindow $window): string => $window->getMode() === 'seats'
    ? $window->getCapacity() . ' miejsc · rezerwujesz pojedyncze miejsca'
    : 'do ' . $window->getCapacity() . ' osób · min. ' . $window->getMinHours() . ' h';

// Panel rezerwacji korzysta z pierwszego okna każdego trybu (dni, godziny, cena).
// TODO: Laboratorium 8 – wolne miejsca w godzinie liczone z zapisanych rezerwacji.
$windows = $venue->getWindowsByMode();
$activeMode = isset($windows[$form->mode]) ? $form->mode : array_key_first($windows);

// Cele, na które gospodarz nie pozwala – do zdania pod listą dozwolonych celów.
$deniedPurposes = [];
foreach ($purposeNames as $value => $label) {
    if (!in_array($value, $venue->getPurposes(), true)) {
        $deniedPurposes[] = mb_strtolower($label);
    }
}
?>
<?php require __DIR__ . '/partials/header.html.php'; ?>

<main class="page venue">
    <?php require __DIR__ . '/partials/status-message.html.php'; ?>

    <nav class="breadcrumbs" aria-label="Ścieżka">
        <ol>
            <li><a href="/venues">Odkrywaj</a></li>
            <?php if ($venue->getDistrict() !== null): ?>
                <li><a href="/venues?q=<?= urlencode($venue->getDistrict()) ?>"><?= htmlspecialchars($venue->getDistrict()) ?></a></li>
            <?php endif; ?>
            <li aria-current="page"><?= htmlspecialchars($venue->getName()) ?></li>
        </ol>
    </nav>

    <h1><?= htmlspecialchars($venue->getName()) ?></h1>
    <p class="venue__address">
        <i class="fa-solid fa-location-dot" aria-hidden="true"></i>
        <?= htmlspecialchars(implode(', ', array_filter([$venue->getDistrict(), $venue->getStreetAddress(), $venue->getCity()]))) ?>
    </p>

    <div class="venue__layout">
        <section class="venue__media" aria-label="Zdjęcia i rolki">
            <!-- TODO: Laboratorium 13 – wideo rolki i zdjęcia dodane przez gospodarza zamiast ilustracji. -->
            <figure class="reel reel--host">
                <img src="/images/login-hero.svg" alt="">
            </figure>
        </section>

        <div class="venue__details">
            <section class="card host-card">
                <span class="initials" aria-hidden="true"><?= htmlspecialchars($initials($venue->getHostName())) ?></span>
                <div>
                    <p class="muted">Gospodarz</p>
                    <h2><?= htmlspecialchars($venue->getHostName()) ?></h2>
                    <p class="muted">potwierdza prośby w ciągu 24 h</p>
                </div>
            </section>

            <section>
                <h2>O lokalu</h2>
                <p><?= htmlspecialchars((string) $venue->getDescription()) ?></p>
            </section>

            <section>
                <h2>Dozwolone cele</h2>
                <ul class="purpose-list">
                    <?php foreach ($venue->getPurposes() as $purpose): ?>
                        <li class="chip"><i class="fa-solid <?= $purposeIcons[$purpose][0] ?? 'fa-circle' ?>" aria-hidden="true"></i> <?= htmlspecialchars($purposeNames[$purpose]) ?></li>
                    <?php endforeach; ?>
                </ul>
                <?php if ($deniedPurposes !== []): ?>
                    <p class="muted">Gospodarz nie zezwala na: <?= htmlspecialchars(implode(', ', $deniedPurposes)) ?>.</p>
                <?php endif; ?>
            </section>

            <section>
                <h2>Oferta</h2>
                <?php foreach ($venue->getWindows() as $window): ?>
                    <?php $mode = $window->getMode(); ?>
                    <article class="card offer">
                        <div>
                            <span class="badge badge--<?= $mode ?>"><i class="fa-solid <?= $modes[$mode]['icon'] ?>" aria-hidden="true"></i> <?= $modes[$mode]['name'] ?></span>
                            <h3><?= $dayRange($window->getDays()) ?> <?= $window->getStartHour() ?>:00–<?= $window->getEndHour() ?>:00</h3>
                            <p class="muted"><?= $windowDetails($window) ?></p>
                        </div>
                        <p class="offer__price"><strong><?= $price($window->getPrice()) ?> zł/h</strong> <?= $modes[$mode]['unit'] ?></p>
                    </article>
                <?php endforeach; ?>
            </section>

            <section>
                <h2>Udogodnienia</h2>
                <ul class="amenities">
                    <?php foreach ($venue->getAmenities() as $amenity => $note): ?>
                        <li>
                            <span class="icon-tile"><i class="fa-solid <?= $amenityIcons[$amenity] ?? 'fa-circle-check' ?>" aria-hidden="true"></i></span>
                            <span><strong><?= htmlspecialchars($amenityNames[$amenity]) ?></strong><?= htmlspecialchars((string) $note) ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </section>

            <section class="entry">
                <span class="icon-tile"><i class="fa-solid fa-user-check" aria-hidden="true"></i></span>
                <p><strong>Wejście: <?= $entryLabels[$venue->getEntryMode()] ?></strong> Szczegóły wejścia zobaczysz po akceptacji rezerwacji.</p>
            </section>
        </div>

        <aside class="card reservation-panel" id="reservation" aria-labelledby="reservation-heading">
            <div class="reservation-panel__title">
                <h2 id="reservation-heading">Rezerwacja</h2>
                <p><strong data-price><?= $price($windows[$activeMode]->getPrice()) ?> zł/h</strong> <span data-unit><?= $modes[$activeMode]['unit'] ?></span></p>
            </div>

            <form class="app-form" action="/venues/<?= $venue->getId() ?>/reservations#reservation" method="post">
                <?php require __DIR__ . '/partials/form-errors.html.php'; ?>

                <fieldset class="mode-switch">
                    <legend class="visually-hidden">Tryb rezerwacji</legend>
                    <?php foreach ($windows as $mode => $window): ?>
                        <label>
                            <input type="radio" name="mode" value="<?= $mode ?>" data-price="<?= $window->getPrice() ?>" data-unit="<?= $modes[$mode]['unit'] ?>" <?= $mode === $activeMode ? 'checked' : '' ?>>
                            <i class="fa-solid <?= $modes[$mode]['icon'] ?>" aria-hidden="true"></i> <?= $modes[$mode]['choice'] ?>
                        </label>
                    <?php endforeach; ?>
                </fieldset>

                <div class="field">
                    <label for="purpose">Cel</label>
                    <div class="select">
                        <i class="fa-solid <?= $purposeIcons[$form->purpose][0] ?? 'fa-circle' ?>" aria-hidden="true" data-purpose-icon></i>
                        <select id="purpose" name="purpose" <?= $form->invalidAttributes('purpose') ?>>
                            <?php foreach ($venue->getPurposes() as $purpose): ?>
                                <option value="<?= $purpose ?>" data-icon="<?= $purposeIcons[$purpose][0] ?? 'fa-circle' ?>" <?= $form->purpose === $purpose ? 'selected' : '' ?>><?= htmlspecialchars($purposeNames[$purpose]) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <i class="fa-solid fa-chevron-down" aria-hidden="true"></i>
                    </div>
                </div>

                <!-- Dni i godziny każdego trybu; pola niewybranego trybu są wyłączone (disabled), więc formularz ich nie wysyła. -->
                <?php foreach ($windows as $mode => $window): ?>
                    <fieldset class="slots" data-mode="<?= $mode ?>" <?= $mode === $activeMode ? '' : 'disabled hidden' ?>>
                        <div class="slots__header">
                            <span id="date-<?= $mode ?>">Data</span>
                            <span class="muted"><?= $monthNames[(int) $days[0]->format('n')] ?></span>
                        </div>
                        <div class="day-chips" role="group" aria-labelledby="date-<?= $mode ?>" <?= $form->invalidAttributes('date') ?>>
                            <?php foreach ($days as $day): ?>
                                <?php $isOpen = $venue->isOpenOn($mode, (int) $day->format('N')); ?>
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
                            <span class="muted" data-selected></span>
                        </div>
                        <div class="hour-list" role="group" aria-labelledby="hours-<?= $mode ?>" <?= $form->invalidAttributes('hours') ?>>
                            <?php for ($hour = $window->getStartHour(); $hour < $window->getEndHour(); $hour++): ?>
                                <label class="hour-row">
                                    <input type="checkbox" name="hours[]" value="<?= $hour ?>" <?= in_array($hour, $form->hours, true) ? 'checked' : '' ?>>
                                    <strong><?= $hour ?>:00</strong>
                                    <span class="muted">· <?= $mode === 'seats' ? $window->getCapacity() . ' z ' . $window->getCapacity() . ' wolnych' : 'wolne' ?></span>
                                    <span class="hour-row__bar" aria-hidden="true"></span>
                                </label>
                            <?php endfor; ?>
                        </div>

                        <?php if ($mode === 'seats'): ?>
                            <div class="seats">
                                <label for="seats">
                                    Liczba miejsc: <span data-seats-label><?= htmlspecialchars($form->seats) ?></span>
                                    <span class="muted" id="seats-hint">maks. <?= $window->getCapacity() ?> w tym oknie</span>
                                </label>
                                <div class="stepper">
                                    <button type="button" data-step="-1" aria-label="Mniej miejsc"><i class="fa-solid fa-minus" aria-hidden="true"></i></button>
                                    <input type="number" id="seats" name="seats" min="1" max="<?= $window->getCapacity() ?>" value="<?= htmlspecialchars($form->seats) ?>" <?= $form->invalidAttributes('seats', 'seats-hint') ?>>
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
