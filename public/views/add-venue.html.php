<!doctype html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
</head>
<body>
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

        <form action="/host/venues" method="post" enctype="multipart/form-data">
            <fieldset id="basics">
                <legend>Podstawowe</legend>

                <label for="name">Nazwa lokalu</label>
                <input type="text" id="name" name="name" maxlength="120" required>

                <label for="type">Typ lokalu</label>
                <select id="type" name="type" required>
                    <option value="">Wybierz typ</option>
                    <option value="cafe">Kawiarnia</option>
                    <option value="bar">Bar koktajlowy</option>
                    <option value="restaurant">Restauracja</option>
                    <option value="studio">Pracownia</option>
                </select>

                <label for="address">Adres</label>
                <input type="text" id="address" name="address" autocomplete="street-address" required aria-describedby="address-hint">
                <!-- TODO: Mapa z pinezką do poprawienia lokalizacji -->
                <p id="address-hint">Przeciągnij pinezkę, jeśli adres wskazuje niedokładne miejsce. Najemcy zobaczą przybliżoną lokalizację.</p>

                <label for="description">Opis</label>
                <textarea id="description" name="description" rows="4" maxlength="600" aria-describedby="description-hint"></textarea>
                <p id="description-hint">Maksymalnie 600 znaków.</p>
            </fieldset>

            <fieldset id="purposes">
                <legend>Dozwolone cele</legend>
                <p>Najemca wybierze jeden z zaznaczonych celów przy rezerwacji.</p>

                <label><input type="checkbox" name="purposes[]" value="work"> <strong>Praca</strong> praca zdalna, coworking</label>
                <label><input type="checkbox" name="purposes[]" value="meetings"> <strong>Spotkania i szkolenia</strong> zespoły, warsztaty, prezentacje</label>
                <label><input type="checkbox" name="purposes[]" value="shoots"> <strong>Sesje foto i wideo</strong> zdjęcia, nagrania, reklamy</label>
                <label><input type="checkbox" name="purposes[]" value="events"> <strong>Kameralne wydarzenia</strong> kluby, degustacje, małe przyjęcia</label>
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

                <!-- TODO: Kolejne okna dodawane przyciskiem „Dodaj okno” -->
                <fieldset>
                    <legend>Okno 1</legend>

                    <label for="window-days">Dni</label>
                    <select id="window-days" name="windows[0][days]">
                        <option value="mon-fri">pon–pt</option>
                        <option value="mon-thu">pon–czw</option>
                        <option value="mon">pon</option>
                        <option value="tue">wt</option>
                        <option value="wed">śr</option>
                        <option value="thu">czw</option>
                        <option value="fri">pt</option>
                        <option value="sat">sob</option>
                        <option value="sun">nd</option>
                    </select>

                    <label for="window-from">Od godziny</label>
                    <input type="time" id="window-from" name="windows[0][from]" step="3600" required>

                    <label for="window-to">Do godziny</label>
                    <input type="time" id="window-to" name="windows[0][to]" step="3600" required>

                    <label for="window-mode">Tryb</label>
                    <select id="window-mode" name="windows[0][mode]">
                        <option value="seats">Miejsca do pracy</option>
                        <option value="exclusive">Na wyłączność</option>
                    </select>

                    <label for="window-capacity">Liczba miejsc / maks. osób</label>
                    <input type="number" id="window-capacity" name="windows[0][capacity]" min="1" required>

                    <label for="window-price">Cena za godzinę (zł)</label>
                    <input type="number" id="window-price" name="windows[0][price]" min="0" step="0.01" required>

                    <label for="window-min-hours">Minimum godzin</label>
                    <input type="number" id="window-min-hours" name="windows[0][minHours]" min="1" value="2" required>
                </fieldset>

                <button type="button">Dodaj okno</button>
            </fieldset>

            <fieldset id="entry">
                <legend>Wejście do lokalu</legend>

                <label><input type="radio" name="entryMode" value="staff" checked> <strong>Pracownik na miejscu</strong> Ktoś z obsługi wpuści najemcę i będzie dostępny w lokalu.</label>
                <label><input type="radio" name="entryMode" value="key"> <strong>Przekazanie klucza</strong> Najemca odbiera klucz lub kod – instrukcję podajesz poniżej.</label>

                <label for="entry-instructions">Instrukcja wejścia – widoczna dla najemcy dopiero po akceptacji</label>
                <textarea id="entry-instructions" name="entryInstructions" rows="3"></textarea>
            </fieldset>

            <button type="submit" name="action" value="draft">Zapisz szkic</button>
            <button type="submit" name="action" value="submit">Wyślij do weryfikacji</button>
        </form>
    </main>

    <?php require __DIR__ . '/partials/footer.html.php'; ?>
</body>
</html>
