<?php require __DIR__ . '/partials/header.html.php'; ?>

<main>
    <h1>Odkrywaj lokale</h1>

    <form action="/venues" method="get" aria-label="Filtry">
        <fieldset>
            <legend>Cel</legend>
            <label><input type="radio" name="purpose" value="work" checked> Praca</label>
            <label><input type="radio" name="purpose" value="meetings"> Spotkania i szkolenia</label>
            <label><input type="radio" name="purpose" value="shoots"> Sesje foto i wideo</label>
            <label><input type="radio" name="purpose" value="events"> Kameralne wydarzenia</label>
        </fieldset>

        <fieldset>
            <legend>Tryb</legend>
            <label><input type="radio" name="mode" value="seats" checked> Miejsce do pracy</label>
            <label><input type="radio" name="mode" value="exclusive"> Cały lokal</label>
        </fieldset>

        <label for="date">Data</label>
        <input type="date" id="date" name="date">

        <label for="time-from">Od godziny</label>
        <input type="time" id="time-from" name="from" step="3600">

        <label for="time-to">Do godziny</label>
        <input type="time" id="time-to" name="to" step="3600">

        <label for="guests">Liczba osób</label>
        <input type="number" id="guests" name="guests" min="1" value="1">

        <label for="max-price">Cena do (zł/h)</label>
        <input type="number" id="max-price" name="maxPrice" min="0">

        <button type="submit">Pokaż lokale</button>
        <a href="/venues">Wyczyść filtry</a>
    </form>

    <p>8 lokali w tym obszarze</p>

    <section aria-labelledby="map-heading">
        <h2 id="map-heading">Mapa</h2>
        <!-- TODO: Mapa z pinezkami cen lokali -->
        <p>Ceny za godzinę:</p>
        <ul>
            <li><strong>15 zł/h · miejsce</strong> – jedno miejsce do pracy</li>
            <li><strong>150 zł/h</strong> – cały lokal na wyłączność</li>
        </ul>
    </section>

    <section aria-labelledby="reels-heading">
        <h2 id="reels-heading">Rolki lokali</h2>
        <p>Przewijaj rolki – mapa podąża za Tobą</p>

        <!-- TODO: Lista lokali z bazy wyświetlona w pętli -->
        <article>
            <!-- TODO: Rolka gospodarza (wideo) -->
            <h3>Bar Lampion</h3>
            <p>Stare Miasto · 0,8 km</p>
            <ul>
                <li>Miejsca do pracy · 15 zł/h</li>
                <li>Na wyłączność · 160 zł/h</li>
            </ul>
            <p>Dziś 8:00–14:00 · wolne 5 z 12 miejsc</p>
            <a href="/venues/1">Zarezerwuj</a>
        </article>

        <article>
            <h3>Kawiarnia Ziarno</h3>
            <p>Kazimierz · 1,2 km</p>
            <ul>
                <li>Miejsca do pracy · 15 zł/h</li>
                <li>Na wyłączność · 150 zł/h</li>
            </ul>
            <p>Najbliżej: wt 18:00–22:00 · miejsca do pracy</p>
            <a href="/venues/2">Zarezerwuj</a>
        </article>
    </section>
</main>

<?php require __DIR__ . '/partials/footer.html.php'; ?>
