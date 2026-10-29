<!doctype html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
</head>
<body>
    <?php require __DIR__ . '/partials/header.html.php'; ?>

    <aside aria-label="Panel gospodarza">
        <nav aria-label="Sekcje panelu">
            <ul>
                <li><a href="/host" aria-current="page">Pulpit</a></li>
                <li><a href="/host/venues">Moje lokale</a></li>
                <li><a href="/host/requests">Prośby (5)</a></li>
                <li><a href="/host/calendar">Kalendarz</a></li>
                <li><a href="/host/stats">Statystyki</a></li>
            </ul>
        </nav>

        <section aria-labelledby="my-venues-heading">
            <h2 id="my-venues-heading">Twoje lokale</h2>
            <ul>
                <li><a href="/venues/1">Bar Lampion</a> · Stare Miasto</li>
                <li><a href="/venues/2">Kawiarnia Ziarno</a> · Kazimierz</li>
            </ul>
        </section>
    </aside>

    <main>
        <header>
            <p>Gospodarz · poniedziałek, 17.11</p>
            <h1>Dzień dobry, Piotr</h1>
            <a href="/host/venues/new">Dodaj lokal</a>
        </header>

        <section aria-labelledby="summary-heading">
            <h2 id="summary-heading">Podsumowanie</h2>
            <dl>
                <dt>Prośby do decyzji</dt>
                <dd>5 · najstarsza wygasa za 6 h</dd>
                <dt>Obłożenie w tym tygodniu</dt>
                <dd><meter min="0" max="100" value="41">41%</meter> 41%</dd>
                <dt>Przychód – październik</dt>
                <dd>3 920,00 zł · Bar Lampion i Kawiarnia Ziarno</dd>
                <dt>Ocena</dt>
                <dd>4,9 · 31 opinii</dd>
            </dl>
        </section>

        <section aria-labelledby="requests-heading">
            <h2 id="requests-heading">Prośby o rezerwację (5)</h2>
            <a href="/host/requests">Zobacz wszystkie</a>

            <!-- TODO: Prośby z bazy wyświetlone w pętli -->
            <article>
                <h3>Marta Zielińska · Studio Kadr</h3>
                <p>Bar Lampion · pt 21.11, 8:00–12:00 · wygasa za 6 h</p>
                <ul>
                    <li>Sesje foto i wideo</li>
                    <li>Na wyłączność</li>
                    <li>do 6 osób</li>
                </ul>
                <p><strong>640,00 zł</strong> · 4 h × 160 zł</p>
                <form action="/host/requests/1" method="post">
                    <button type="submit" name="decision" value="reject">Odrzuć</button>
                    <button type="submit" name="decision" value="accept">Akceptuj</button>
                </form>
            </article>

            <article>
                <h3>Anna Kowalska</h3>
                <p>Kawiarnia Ziarno · czw 20.11, 18:00–22:00 · wygasa za 23 h</p>
                <ul>
                    <li>Praca</li>
                    <li>Miejsca do pracy</li>
                    <li>1 miejsce</li>
                </ul>
                <p><strong>60,00 zł</strong> · 1 × 4 h × 15 zł</p>
                <form action="/host/requests/2" method="post">
                    <button type="submit" name="decision" value="reject">Odrzuć</button>
                    <button type="submit" name="decision" value="accept">Akceptuj</button>
                </form>
            </article>

            <p>+3 prośby więcej. Prośby bez decyzji wygasają po 24 h, a zarezerwowane miejsca wracają do puli.</p>
        </section>

        <section aria-labelledby="calendar-heading">
            <h2 id="calendar-heading">Kalendarz tygodnia</h2>

            <!-- TODO: Obłożenie wybranego lokalu z bazy -->
            <table>
                <caption>Bar Lampion, 17–23 listopada · miejsca do pracy: zajęte / wszystkie</caption>
                <thead>
                    <tr>
                        <th scope="col">Godziny</th>
                        <th scope="col">pon 17 (dziś)</th>
                        <th scope="col">wt 18</th>
                        <th scope="col">śr 19</th>
                        <th scope="col">czw 20</th>
                        <th scope="col">pt 21</th>
                        <th scope="col">sob 22</th>
                        <th scope="col">nd 23</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th scope="row">8:00–10:00</th>
                        <td>7/12</td>
                        <td>6/12</td>
                        <td>8/12</td>
                        <td>5/12</td>
                        <td rowspan="2">Studio Kadr, 8–12 · czeka na decyzję</td>
                        <td rowspan="3">poza oknami</td>
                        <td rowspan="3">poza oknami</td>
                    </tr>
                    <tr>
                        <th scope="row">10:00–12:00</th>
                        <td>9/12</td>
                        <td>4/12</td>
                        <td>10/12</td>
                        <td>7/12</td>
                    </tr>
                    <tr>
                        <th scope="row">12:00–14:00</th>
                        <td>5/12</td>
                        <td>3/12</td>
                        <td>6/12</td>
                        <td>4/12</td>
                        <td>Fundacja Kod, 12–14 · potwierdzona</td>
                    </tr>
                </tbody>
            </table>

            <p>Statusy rezerwacji:</p>
            <ul>
                <li>Oczekuje na akceptację</li>
                <li>Potwierdzona</li>
                <li>Odrzucona</li>
                <li>Wygasła</li>
                <li>Zakończona</li>
            </ul>
        </section>
    </main>

    <?php require __DIR__ . '/partials/footer.html.php'; ?>
</body>
</html>
