<!doctype html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
</head>
<body>
    <header>
        <a href="/venues">PoGodzinach</a>
    </header>

    <aside aria-label="O PoGodzinach">
        <p>Bar Lampion, Stare Miasto · pon–czw 8:00–14:00</p>
        <p>Puste godziny lokali. Pełne możliwości.</p>
        <p>Bary rano, kawiarnie wieczorami, pracownie w dni wolne – rezerwujesz na godziny, a gospodarz potwierdza w ciągu 24 h.</p>
        <ul aria-label="Cele rezerwacji">
            <li>Praca</li>
            <li>Spotkania i szkolenia</li>
            <li>Sesje foto i wideo</li>
            <li>Kameralne wydarzenia</li>
        </ul>
    </aside>

    <main>
        <h1>Witaj w PoGodzinach</h1>
        <p>Przestrzeń na godziny – do pracy, spotkań, sesji i kameralnych wydarzeń</p>

        <form action="/login" method="post">
            <fieldset>
                <legend>Jak chcesz korzystać z PoGodzinach?</legend>

                <label>
                    <input type="radio" name="accountType" value="renter" checked>
                    <strong>Szukam przestrzeni</strong>
                    Rezerwuj klimatyczne lokale na godziny: do pracy, na spotkania, sesje i wydarzenia.
                </label>

                <label>
                    <input type="radio" name="accountType" value="host">
                    <strong>Udostępniam lokal</strong>
                    Zarabiaj na godzinach, w których Twój lokal jest zamknięty lub pusty.
                </label>
            </fieldset>

            <label for="email">E-mail</label>
            <input type="email" id="email" name="email" autocomplete="email" required>

            <label for="password">Hasło</label>
            <input type="password" id="password" name="password" autocomplete="current-password" required>
            <a href="/password-reset">Nie pamiętasz hasła?</a>

            <button type="submit">Zaloguj się</button>
        </form>

        <p>Nie masz konta? <a href="/register">Załóż konto</a></p>
    </main>

    <?php require __DIR__ . '/partials/footer.html.php'; ?>
</body>
</html>
