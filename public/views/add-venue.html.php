<?php require __DIR__ . '/partials/header.html.php'; ?>

<main>
    <nav aria-label="Ścieżka">
        <ol>
            <li><a href="/host">Panel gospodarza</a></li>
            <li><a href="/host/venues">Moje lokale</a></li>
            <li aria-current="page">Dodaj lokal</li>
        </ol>
    </nav>

    <h1>Dodaj lokal</h1>

    <aside aria-label="Kroki formularza">
        <nav aria-label="Kroki">
            <ol>
                <li><a href="#basics">Podstawowe</a></li>
                <li><a href="#purposes">Dozwolone cele</a></li>
                <li><a href="#photos">Zdjęcia</a></li>
                <li><a href="#reel">Rolka (opcjonalnie)</a></li>
                <li><a href="#windows">Okna dostępności</a></li>
                <li><a href="#entry">Wejście do lokalu</a></li>
            </ol>
        </nav>

        <section aria-labelledby="next-heading">
            <h2 id="next-heading">Co dalej?</h2>
            <p>Po wysłaniu administrator sprawdzi lokal i rolkę. Do tego czasu lokal nie jest widoczny w Odkrywaj.</p>
        </section>
    </aside>

    <form action="/host/venues/new" method="post" enctype="multipart/form-data">
        <?php require __DIR__ . '/partials/form-errors.html.php'; ?>

        <fieldset id="basics">
            <legend>Podstawowe</legend>

            <label for="name">Nazwa lokalu</label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($form->name) ?>" maxlength="120" required <?= $form->invalidAttributes('name') ?>>

            <label for="type">Typ lokalu</label>
            <select id="type" name="type" required <?= $form->invalidAttributes('type') ?>>
                <option value="">Wybierz typ</option>
                <?php foreach (['cafe' => 'Kawiarnia', 'bar' => 'Bar koktajlowy', 'restaurant' => 'Restauracja', 'studio' => 'Pracownia'] as $value => $label): ?>
                    <option value="<?= $value ?>" <?= $form->type === $value ? 'selected' : '' ?>><?= $label ?></option>
                <?php endforeach; ?>
            </select>

            <label for="address">Adres</label>
            <input type="text" id="address" name="address" value="<?= htmlspecialchars($form->address) ?>" autocomplete="street-address" required <?= $form->invalidAttributes('address', 'address-hint') ?>>
            <!-- TODO: Mapa z pinezką do poprawienia lokalizacji -->
            <p id="address-hint">Przeciągnij pinezkę, jeśli adres wskazuje niedokładne miejsce. Najemcy zobaczą przybliżoną lokalizację.</p>

            <label for="description">Opis</label>
            <textarea id="description" name="description" rows="4" maxlength="600" required <?= $form->invalidAttributes('description', 'description-hint') ?>><?= htmlspecialchars($form->description) ?></textarea>
            <p id="description-hint">Maksymalnie 600 znaków.</p>
        </fieldset>

        <fieldset id="purposes">
            <legend>Dozwolone cele</legend>
            <p>Najemca wybierze jeden z zaznaczonych celów przy rezerwacji.</p>

            <?php foreach ([
                'work' => ['Praca', 'praca zdalna, coworking'],
                'meetings' => ['Spotkania i szkolenia', 'zespoły, warsztaty, prezentacje'],
                'shoots' => ['Sesje foto i wideo', 'zdjęcia, nagrania, reklamy'],
                'events' => ['Kameralne wydarzenia', 'kluby, degustacje, małe przyjęcia'],
            ] as $value => [$label, $hint]): ?>
                <label><input type="checkbox" name="purposes[]" value="<?= $value ?>" <?= in_array($value, $form->purposes, true) ? 'checked' : '' ?>> <strong><?= $label ?></strong> <?= $hint ?></label>
            <?php endforeach; ?>
        </fieldset>

        <fieldset id="photos">
            <legend>Zdjęcia</legend>

            <label for="photo-files">Dodaj zdjęcia</label>
            <input type="file" id="photo-files" name="photos[]" accept="image/jpeg,image/png" multiple aria-describedby="photos-hint">
            <p id="photos-hint">JPG/PNG, maks. 5 MB · pierwsze zdjęcie jest okładką</p>
        </fieldset>

        <fieldset id="reel">
            <legend>Rolka (opcjonalnie)</legend>
            <p>Lokale z rolką są wyżej w wynikach.</p>

            <label for="reel-file">Wybierz plik wideo</label>
            <input type="file" id="reel-file" name="reel" accept="video/mp4" aria-describedby="reel-hint">
            <p id="reel-hint">Pionowe wideo MP4, 9:16, maks. 60 s i 50 MB. Każdą rolkę zatwierdza administrator przed publikacją, zwykle w ciągu jednego dnia roboczego.</p>
        </fieldset>

        <fieldset id="windows">
            <legend>Okna dostępności</legend>
            <p>Ustal, kiedy i w jakim trybie lokal jest dostępny. Okna tego samego lokalu nie mogą się nakładać.</p>

            <?php foreach ($form->windows as $index => $window): ?>
                <fieldset>
                    <legend>Okno <?= $index + 1 ?></legend>

                    <label for="window-days-<?= $index ?>">Dni</label>
                    <select id="window-days-<?= $index ?>" name="windows[<?= $index ?>][days]" <?= $window->invalidAttributes('days') ?>>
                        <?php foreach (['mon-fri' => 'pon–pt', 'mon-thu' => 'pon–czw', 'mon' => 'pon', 'tue' => 'wt', 'wed' => 'śr', 'thu' => 'czw', 'fri' => 'pt', 'sat' => 'sob', 'sun' => 'nd'] as $value => $label): ?>
                            <option value="<?= $value ?>" <?= $window->days === $value ? 'selected' : '' ?>><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>

                    <label for="window-from-<?= $index ?>">Od godziny</label>
                    <input type="time" id="window-from-<?= $index ?>" name="windows[<?= $index ?>][from]" value="<?= htmlspecialchars($window->from) ?>" step="3600" required <?= $window->invalidAttributes('from') ?>>

                    <label for="window-to-<?= $index ?>">Do godziny</label>
                    <input type="time" id="window-to-<?= $index ?>" name="windows[<?= $index ?>][to]" value="<?= htmlspecialchars($window->to) ?>" step="3600" required <?= $window->invalidAttributes('to') ?>>

                    <label for="window-mode-<?= $index ?>">Tryb</label>
                    <select id="window-mode-<?= $index ?>" name="windows[<?= $index ?>][mode]" <?= $window->invalidAttributes('mode') ?>>
                        <option value="seats" <?= $window->mode === 'seats' ? 'selected' : '' ?>>Miejsca do pracy</option>
                        <option value="exclusive" <?= $window->mode === 'exclusive' ? 'selected' : '' ?>>Na wyłączność</option>
                    </select>

                    <label for="window-capacity-<?= $index ?>">Liczba miejsc / maks. osób</label>
                    <input type="number" id="window-capacity-<?= $index ?>" name="windows[<?= $index ?>][capacity]" value="<?= htmlspecialchars($window->capacity) ?>" min="1" required <?= $window->invalidAttributes('capacity') ?>>

                    <label for="window-price-<?= $index ?>">Cena za godzinę (zł)</label>
                    <input type="number" id="window-price-<?= $index ?>" name="windows[<?= $index ?>][price]" value="<?= htmlspecialchars($window->price) ?>" min="0" step="0.01" required <?= $window->invalidAttributes('price') ?>>

                    <label for="window-min-hours-<?= $index ?>">Minimum godzin</label>
                    <input type="number" id="window-min-hours-<?= $index ?>" name="windows[<?= $index ?>][minHours]" value="<?= htmlspecialchars($window->minHours) ?>" min="1" required <?= $window->invalidAttributes('minHours') ?>>
                </fieldset>
            <?php endforeach; ?>

            <!-- TODO: Kolejne okna dodawane przyciskiem „Dodaj okno” (Laboratorium 10) -->
            <button type="button">Dodaj okno</button>
        </fieldset>

        <fieldset id="entry">
            <legend>Wejście do lokalu</legend>

            <label><input type="radio" name="entryMode" value="staff" <?= $form->entryMode === 'staff' ? 'checked' : '' ?>> <strong>Pracownik na miejscu</strong> Ktoś z obsługi wpuści najemcę i będzie dostępny w lokalu.</label>
            <label><input type="radio" name="entryMode" value="key" <?= $form->entryMode === 'key' ? 'checked' : '' ?>> <strong>Przekazanie klucza</strong> Najemca odbiera klucz lub kod – instrukcję podajesz poniżej.</label>

            <label for="entry-instructions">Instrukcja wejścia – widoczna dla najemcy dopiero po akceptacji</label>
            <textarea id="entry-instructions" name="entryInstructions" rows="3" maxlength="1000" <?= $form->invalidAttributes('entryInstructions') ?>><?= htmlspecialchars($form->entryInstructions) ?></textarea>
        </fieldset>

        <button type="submit" name="action" value="draft" formnovalidate>Zapisz szkic</button>
        <button type="submit" name="action" value="submit">Wyślij do weryfikacji</button>
    </form>
</main>

<?php require __DIR__ . '/partials/footer.html.php'; ?>
