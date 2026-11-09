<?php
$purposes = require __DIR__ . '/partials/purposes.php';
$types = require __DIR__ . '/partials/venue-types.php';
$daySets = ['mon-fri' => 'pon–pt', 'mon-thu' => 'pon–czw', 'mon' => 'pon', 'tue' => 'wt', 'wed' => 'śr', 'thu' => 'czw', 'fri' => 'pt', 'sat' => 'sob', 'sun' => 'nd'];

// Kroki formularza: kotwica sekcji => [nazwa, pola, których błędy należą do sekcji].
$steps = [
    'basics' => ['Podstawowe', ['name', 'type', 'address', 'location', 'description']],
    'purposes' => ['Dozwolone cele', ['purposes']],
    'photos' => ['Zdjęcia', []],
    'reel' => ['Rolka', []],
    'windows' => ['Okna dostępności', ['windows']],
    'entry' => ['Wejście do lokalu', ['entryMode', 'entryInstructions']],
];
// Błąd „windows.0.from” należy do pola „windows”.
$errorFields = array_map(static fn (string $key): string => explode('.', $key)[0], array_keys($errors));
$failedSteps = array_filter($steps, static fn (array $step): bool => array_intersect($step[1], $errorFields) !== []);
?>
<?php require __DIR__ . '/partials/header.html.php'; ?>

<main class="page add-venue">
    <nav class="breadcrumbs" aria-label="Ścieżka">
        <ol>
            <li><a href="/host">Panel gospodarza</a></li>
            <li aria-current="page">Dodaj lokal</li>
        </ol>
    </nav>

    <h1>Dodaj lokal</h1>

    <div class="add-venue__layout">
        <aside class="add-venue__aside">
            <!-- Przed wysłaniem kroki mają numery, po wysłaniu z błędami: „!” przy sekcji z błędem, „✓” przy pozostałych. -->
            <nav aria-label="Sekcje formularza">
                <ol class="steps">
                    <?php foreach (array_keys($steps) as $number => $anchor): ?>
                        <?php $state = $errors === [] ? 'todo' : (isset($failedSteps[$anchor]) ? 'error' : 'done'); ?>
                        <li>
                            <a class="steps__link steps__link--<?= $state ?>" href="#<?= $anchor ?>">
                                <span class="initials steps__icon">
                                    <?php if ($state === 'todo'): ?>
                                        <?= $number + 1 ?>
                                    <?php elseif ($state === 'error'): ?>
                                        !<span class="visually-hidden">(błąd)</span>
                                    <?php else: ?>
                                        <i class="fa-solid fa-check" aria-hidden="true"></i>
                                    <?php endif; ?>
                                </span>
                                <?= $steps[$anchor][0] ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ol>
            </nav>

            <p class="add-venue__next">
                <strong>Co dalej?</strong>
                Po wysłaniu administrator sprawdzi lokal i rolkę. Do tego czasu lokal nie jest widoczny w Odkrywaj.
            </p>
        </aside>

        <form class="stack add-venue__form app-form" action="/host/venues/new" method="post" enctype="multipart/form-data">
            <?php require __DIR__ . '/partials/form-errors.html.php'; ?>

            <section class="card form-section" id="basics">
                <h2><span>1</span> Podstawowe</h2>
                <div class="form-grid">
                    <div class="field">
                        <label for="name">Nazwa lokalu</label>
                        <input type="text" id="name" name="name" value="<?= htmlspecialchars($form->name) ?>" maxlength="120" required <?= $form->invalidAttributes('name') ?>>
                    </div>

                    <div class="field">
                        <label for="type">Typ lokalu</label>
                        <select id="type" name="type" required <?= $form->invalidAttributes('type') ?>>
                            <option value="">Wybierz typ</option>
                            <?php foreach ($types as $value => $label): ?>
                                <option value="<?= $value ?>" <?= $form->type === $value ? 'selected' : '' ?>><?= $label ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="field form-grid__wide">
                        <label for="address">Adres</label>
                        <input type="text" id="address" name="address" value="<?= htmlspecialchars($form->address) ?>" autocomplete="street-address" required <?= $form->invalidAttributes('address', 'address-hint') ?>>
                        <?php if ($mapbox['token'] !== ''): ?>
                            <div class="location-map" data-token="<?= htmlspecialchars($mapbox['token']) ?>" data-style="<?= htmlspecialchars($mapbox['style']) ?>"></div>
                        <?php endif; ?>
                        <input type="hidden" name="latitude" value="<?= htmlspecialchars($form->latitude) ?>">
                        <input type="hidden" name="longitude" value="<?= htmlspecialchars($form->longitude) ?>">
                        <p class="field__hint" id="address-hint">Przeciągnij pinezkę, jeśli adres wskazuje niedokładne miejsce. Najemcy zobaczą przybliżoną lokalizację.</p>
                    </div>

                    <div class="field form-grid__wide">
                        <label for="description">Opis</label>
                        <textarea id="description" name="description" rows="4" maxlength="600" data-counter="description-length" required <?= $form->invalidAttributes('description', 'description-count') ?>><?= htmlspecialchars($form->description) ?></textarea>
                        <p class="field__hint field__count" id="description-count"><span id="description-length"><?= mb_strlen($form->description) ?></span> / 600</p>
                    </div>
                </div>
            </section>

            <section class="card form-section" id="purposes">
                <h2><span>2</span> Dozwolone cele</h2>
                <p class="field__hint">Najemca wybierze jeden z zaznaczonych celów przy rezerwacji.</p>
                <div class="form-grid">
                    <?php foreach ($purposes as $value => [$label, $icon, $hint]): ?>
                        <label class="option-card">
                            <input type="checkbox" name="purposes[]" value="<?= $value ?>" <?= in_array($value, $form->purposes, true) ? 'checked' : '' ?>>
                            <span class="icon-tile"><i class="fa-solid <?= $icon ?>" aria-hidden="true"></i></span>
                            <span><strong><?= $label ?></strong><?= $hint ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </section>

            <!-- TODO: Laboratorium 13 – zapis zdjęć i rolki na serwerze oraz ich podgląd. -->
            <section class="card form-section" id="photos">
                <h2><span>3</span> Zdjęcia</h2>
                <label class="dropzone">
                    <i class="fa-regular fa-image" aria-hidden="true"></i>
                    <strong>Dodaj zdjęcia</strong>
                    <span class="field__hint">JPG/PNG, maks. 5 MB · pierwsze zdjęcie jest okładką</span>
                    <input type="file" name="photos[]" accept="image/jpeg,image/png" multiple>
                </label>
            </section>

            <section class="card form-section" id="reel">
                <h2><span>4</span> Rolka <span class="badge add-venue__optional">opcjonalnie</span></h2>
                <p class="add-venue__tip"><i class="fa-solid fa-arrow-trend-up" aria-hidden="true"></i> Lokale z rolką są wyżej w wynikach</p>
                <label class="dropzone">
                    <span class="icon-tile"><i class="fa-solid fa-video" aria-hidden="true"></i></span>
                    <strong>Przeciągnij plik lub wybierz z dysku</strong>
                    <span class="field__hint">Pionowe wideo MP4, 9:16, maks. 60 s i 50 MB</span>
                    <input type="file" name="reel" accept="video/mp4">
                </label>
                <p class="field__hint">Każdą rolkę zatwierdza administrator przed publikacją. Zwykle trwa to jeden dzień roboczy.</p>
            </section>

            <section class="card form-section" id="windows">
                <h2><span>5</span> Okna dostępności</h2>
                <p class="field__hint">Ustal, kiedy i w jakim trybie lokal jest dostępny. Okna nie mogą się nakładać. Minimum godzin – domyślnie 2.</p>

                <div class="windows">
                    <div class="window-row window-row--header" aria-hidden="true">
                        <span>Dni</span><span>Godziny od–do</span><span>Tryb</span><span>Liczba miejsc / maks. osób</span><span>Cena za godzinę</span><span>Minimum godzin</span>
                    </div>

                    <!-- add-venue.js kopiuje pierwszy wiersz przy „Dodaj okno” i numeruje nazwy pól windows[0], windows[1]… -->
                    <?php foreach ($form->windows as $index => $window): ?>
                        <div class="window-row">
                            <label><span class="visually-hidden">Dni</span><select name="windows[<?= $index ?>][days]" <?= $window->invalidAttributes('days') ?>>
                                <?php foreach ($daySets as $value => $label): ?>
                                    <option value="<?= $value ?>" <?= $window->days === $value ? 'selected' : '' ?>><?= $label ?></option>
                                <?php endforeach; ?>
                            </select></label>
                            <div class="window-row__hours">
                                <label><span class="visually-hidden">Od</span><input type="time" name="windows[<?= $index ?>][from]" value="<?= htmlspecialchars($window->from) ?>" step="3600" required <?= $window->invalidAttributes('from') ?>></label>
                                <span aria-hidden="true">–</span>
                                <label><span class="visually-hidden">Do</span><input type="time" name="windows[<?= $index ?>][to]" value="<?= htmlspecialchars($window->to) ?>" step="3600" required <?= $window->invalidAttributes('to') ?>></label>
                            </div>
                            <label class="window-row__mode"><span class="visually-hidden">Tryb</span><select name="windows[<?= $index ?>][mode]" <?= $window->invalidAttributes('mode') ?>>
                                <option value="seats" <?= $window->mode === 'seats' ? 'selected' : '' ?>>Miejsca do pracy</option>
                                <option value="exclusive" <?= $window->mode === 'exclusive' ? 'selected' : '' ?>>Na wyłączność</option>
                            </select></label>
                            <label class="unit unit--capacity"><span class="visually-hidden">Liczba miejsc / maks. osób</span><input type="number" name="windows[<?= $index ?>][capacity]" value="<?= htmlspecialchars($window->capacity) ?>" min="1" required <?= $window->invalidAttributes('capacity') ?>></label>
                            <label class="unit" data-unit="zł/h"><span class="visually-hidden">Cena za godzinę</span><input type="number" name="windows[<?= $index ?>][price]" value="<?= htmlspecialchars($window->price) ?>" min="0" step="0.01" required <?= $window->invalidAttributes('price') ?>></label>
                            <label class="unit" data-unit="h"><span class="visually-hidden">Minimum godzin</span><input type="number" name="windows[<?= $index ?>][minHours]" value="<?= htmlspecialchars($window->minHours) ?>" min="1" required <?= $window->invalidAttributes('minHours') ?>></label>
                            <button class="window-row__remove" type="button" aria-label="Usuń okno"><i class="fa-regular fa-trash-can" aria-hidden="true"></i></button>
                        </div>
                    <?php endforeach; ?>
                </div>

                <button class="button button--secondary add-venue__add-window" type="button"><i class="fa-solid fa-plus" aria-hidden="true"></i> Dodaj okno</button>
            </section>

            <section class="card form-section" id="entry">
                <h2><span>6</span> Wejście do lokalu</h2>
                <div class="form-grid">
                    <label class="option-card">
                        <input type="radio" name="entryMode" value="staff" <?= $form->entryMode === 'staff' ? 'checked' : '' ?>>
                        <span><strong><i class="fa-solid fa-user-check" aria-hidden="true"></i> Pracownik na miejscu</strong>Ktoś z obsługi wpuści najemcę i będzie dostępny w lokalu.</span>
                    </label>
                    <label class="option-card">
                        <input type="radio" name="entryMode" value="key" <?= $form->entryMode === 'key' ? 'checked' : '' ?>>
                        <span><strong><i class="fa-solid fa-key" aria-hidden="true"></i> Przekazanie klucza</strong>Najemca odbiera klucz lub kod – instrukcję podajesz poniżej.</span>
                    </label>
                </div>
                <div class="field">
                    <label for="entry-instructions"><i class="fa-solid fa-lock" aria-hidden="true"></i> Instrukcja wejścia – widoczna dla najemcy dopiero po akceptacji</label>
                    <textarea id="entry-instructions" name="entryInstructions" rows="3" maxlength="1000" <?= $form->invalidAttributes('entryInstructions') ?>><?= htmlspecialchars($form->entryInstructions) ?></textarea>
                </div>
            </section>

            <div class="card form-actions">
                <?php if ($failedSteps !== []): ?>
                    <p class="form-actions__errors">
                        <i class="fa-solid fa-circle-exclamation" aria-hidden="true"></i>
                        Do poprawienia:
                        <?= implode(', ', array_map(static fn (string $anchor): string => '<a href="#' . $anchor . '">' . $steps[$anchor][0] . '</a>', array_keys($failedSteps))) ?>
                    </p>
                <?php endif; ?>
                <button class="button button--secondary" type="submit" name="action" value="draft" formnovalidate>Zapisz szkic</button>
                <button class="button" type="submit" name="action" value="submit">Wyślij do weryfikacji</button>
            </div>
        </form>
    </div>
</main>

<?php require __DIR__ . '/partials/footer.html.php'; ?>

<?php if ($mapbox['token'] !== ''): ?>
    <link rel="stylesheet" href="https://api.mapbox.com/mapbox-gl-js/v3.16.0/mapbox-gl.css">
    <script src="https://api.mapbox.com/mapbox-gl-js/v3.16.0/mapbox-gl.js" defer></script>
<?php endif; ?>
<script src="/js/char-count.js" defer></script>
<script src="/js/add-venue.js" defer></script>
