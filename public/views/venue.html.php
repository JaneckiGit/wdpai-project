<?php require __DIR__ . '/partials/header.html.php'; ?>

<main>
    <?php require __DIR__ . '/partials/status-message.html.php'; ?>

    <!-- TODO: Dane lokalu o id z adresu pobrane z bazy -->
    <nav aria-label="Ścieżka">
        <ol>
            <li><a href="/venues">Odkrywaj</a></li>
            <li><a href="/venues?q=Kazimierz">Kazimierz</a></li>
            <li aria-current="page">Kawiarnia Ziarno</li>
        </ol>
    </nav>

    <header>
        <h1>Kawiarnia Ziarno</h1>
        <p>Ocena 4,9 · <a href="#reviews">31 opinii</a> · Kazimierz, ul. Estery 8 · 1,2 km od Ciebie</p>
    </header>

    <section aria-labelledby="media-heading">
        <h2 id="media-heading">Rolka i zdjęcia</h2>
        <!-- TODO: Rolka gospodarza „Wieczór przy długim stole” i galeria zdjęć -->
    </section>

    <section aria-labelledby="recorded-heading">
        <h2 id="recorded-heading">Nagrane tutaj</h2>
        <p>Rolki najemców po wizycie</p>
        <ul>
            <li>Sesja foto · Marta Z. · 0:24</li>
            <li>Praca · Kuba W. · 0:18</li>
            <li>Spotkanie · Ola M. · 0:31</li>
        </ul>
    </section>

    <section aria-labelledby="host-heading">
        <h2 id="host-heading">Gospodarz</h2>
        <p><strong>Piotr Nowak</strong> · ocena 4,9 · 31 opinii · potwierdza prośby w ciągu 24 h</p>
    </section>

    <section aria-labelledby="about-heading">
        <h2 id="about-heading">O lokalu</h2>
        <p>Kameralna kawiarnia na Kazimierzu z długim dębowym stołem, ceglaną ścianą i oknem na podwórko. Wieczorami, gdy kończymy obsługę gości, oddajemy salę do cichej pracy, a w poniedziałki – gdy kawiarnia jest nieczynna – cały lokal na wyłączność.</p>
    </section>

    <section aria-labelledby="purposes-heading">
        <h2 id="purposes-heading">Dozwolone cele</h2>
        <ul>
            <li>Praca</li>
            <li>Spotkania i szkolenia</li>
            <li>Kameralne wydarzenia</li>
        </ul>
        <p>Gospodarz nie zezwala na sesje foto i wideo.</p>
    </section>

    <section aria-labelledby="offer-heading">
        <h2 id="offer-heading">Oferta</h2>

        <article>
            <h3>Miejsca do pracy</h3>
            <p>wt–pt 18:00–22:00</p>
            <p>12 miejsc · rezerwujesz pojedyncze miejsca</p>
            <p><strong>15 zł/h</strong> za miejsce</p>
        </article>

        <article>
            <h3>Na wyłączność</h3>
            <p>poniedziałki 9:00–21:00</p>
            <p>do 20 osób · min. 2 h</p>
            <p><strong>150 zł/h</strong> za cały lokal</p>
        </article>
    </section>

    <section aria-labelledby="amenities-heading">
        <h2 id="amenities-heading">Udogodnienia</h2>
        <dl>
            <dt>Wi-Fi</dt>
            <dd>hasło w instrukcji po akceptacji</dd>
            <dt>Gniazdka</dt>
            <dd>przy każdym miejscu</dd>
            <dt>Rzutnik</dt>
            <dd>na spotkania i szkolenia</dd>
            <dt>Ekspres do kawy</dt>
            <dd>do dyspozycji najemców</dd>
        </dl>
    </section>

    <section aria-labelledby="entry-heading">
        <h2 id="entry-heading">Wejście do lokalu</h2>
        <p>Na miejscu będzie pracownik. Szczegóły wejścia zobaczysz po akceptacji rezerwacji.</p>
    </section>

    <section id="reviews" aria-labelledby="reviews-heading">
        <h2 id="reviews-heading">Opinie</h2>
        <p>Średnia ocena 4,9 · 31 opinii</p>

        <!-- TODO: Opinie z bazy wyświetlone w pętli -->
        <article>
            <h3>Kasia Lis</h3>
            <p>Praca · październik</p>
            <p>Cicho, ciepło i świetne światło nad stołem. Cztery godziny pracy minęły szybciej niż w domu.</p>
        </article>

        <article>
            <h3>Michał Bąk</h3>
            <p>Spotkania i szkolenia · wrzesień</p>
            <p>Cała sala na poniedziałkowe warsztaty dla 14 osób. Rzutnik działał od razu, a kawa czekała na stole.</p>
        </article>
    </section>

    <aside id="reservation" aria-labelledby="reservation-heading">
        <h2 id="reservation-heading">Rezerwacja</h2>
        <p><strong>15 zł/h</strong> za miejsce</p>

        <form action="/venues/<?= $venueId ?>/reservations#reservation" method="post">
            <?php require __DIR__ . '/partials/form-errors.html.php'; ?>

            <fieldset>
                <legend>Tryb</legend>
                <label><input type="radio" name="mode" value="seats" <?= $form->mode === 'seats' ? 'checked' : '' ?>> Miejsce do pracy</label>
                <label><input type="radio" name="mode" value="exclusive" <?= $form->mode === 'exclusive' ? 'checked' : '' ?>> Cały lokal</label>
            </fieldset>

            <label for="purpose">Cel</label>
            <select id="purpose" name="purpose" <?= $form->invalidAttributes('purpose') ?>>
                <option value="work" <?= $form->purpose === 'work' ? 'selected' : '' ?>>Praca</option>
                <option value="meetings" <?= $form->purpose === 'meetings' ? 'selected' : '' ?>>Spotkania i szkolenia</option>
                <option value="events" <?= $form->purpose === 'events' ? 'selected' : '' ?>>Kameralne wydarzenia</option>
            </select>

            <label for="date">Data</label>
            <input type="date" id="date" name="date" value="<?= htmlspecialchars($form->date) ?>" required <?= $form->invalidAttributes('date') ?>>

            <label for="time-from">Od godziny</label>
            <input type="time" id="time-from" name="from" value="<?= htmlspecialchars($form->from) ?>" step="3600" required <?= $form->invalidAttributes('from') ?>>

            <label for="time-to">Do godziny</label>
            <input type="time" id="time-to" name="to" value="<?= htmlspecialchars($form->to) ?>" step="3600" required <?= $form->invalidAttributes('to') ?>>

            <label for="seats">Liczba miejsc</label>
            <input type="number" id="seats" name="seats" min="1" max="4" value="<?= htmlspecialchars($form->seats) ?>" <?= $form->invalidAttributes('seats', 'seats-hint') ?>>
            <p id="seats-hint">maks. 4 w tym zakresie</p>

            <!-- TODO: Podsumowanie liczone z wybranych wartości (Laboratorium 10) -->
            <dl>
                <dt>Termin</dt>
                <dd>czw 20.11, 18:00–22:00 (4 h)</dd>
                <dt>1 × 4 h × 15 zł</dt>
                <dd>60,00 zł</dd>
                <dt>Razem</dt>
                <dd><output name="total" for="seats time-from time-to">60,00 zł</output></dd>
            </dl>

            <p>Gospodarz potwierdza w ciągu 24 h. Do tego czasu miejsce jest zarezerwowane dla Ciebie.</p>

            <button type="submit">Wyślij prośbę o rezerwację</button>
        </form>
    </aside>
</main>

<?php require __DIR__ . '/partials/footer.html.php'; ?>
