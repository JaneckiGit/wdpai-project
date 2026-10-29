<!doctype html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Newsreader:opsz,wght@6..72,400..700&display=swap">
    <link rel="stylesheet" href="/css/style.css">
</head>
<body class="auth-page">
    <header class="auth-hero">
        <a class="brand" href="/venues">
            <svg class="brand__mark" width="34" height="34" viewBox="0 0 32 32" aria-hidden="true">
                <circle cx="16" cy="16" r="15"></circle>
                <path d="M16 8v8l5 3"></path>
            </svg>
            PoGodzinach
        </a>
        <p class="auth-hero__badge">Bar Lampion, Stare Miasto · pon–czw 8:00–14:00</p>
        <p class="auth-hero__tagline">Puste godziny lokali. Pełne możliwości.</p>
        <p class="auth-hero__description">Bary rano, kawiarnie wieczorami, pracownie w dni wolne – rezerwujesz na godziny, a gospodarz potwierdza w ciągu 24 h.</p>
        <ul class="auth-hero__purposes" aria-label="Cele rezerwacji">
            <li>Praca</li>
            <li>Spotkania i szkolenia</li>
            <li>Sesje foto i wideo</li>
            <li>Kameralne wydarzenia</li>
        </ul>
    </header>

    <main class="auth-main">
        <h1>Witaj w PoGodzinach</h1>
        <p class="auth-main__lead">Przestrzeń na godziny – do pracy, spotkań, sesji i kameralnych wydarzeń</p>

        <form class="auth-form" action="/login" method="post">
            <fieldset class="choice-group">
                <legend>Jak chcesz korzystać z PoGodzinach?</legend>

                <div class="choice-group__options">
                    <label class="choice-card">
                        <input type="radio" name="accountType" value="renter" checked>
                        <span class="choice-card__icon" aria-hidden="true">
                            <svg class="icon" width="24" height="24" viewBox="0 0 24 24">
                                <rect x="4" y="5" width="16" height="11" rx="1.5"></rect>
                                <path d="M2 19h20"></path>
                            </svg>
                        </span>
                        <span class="choice-card__title">Szukam przestrzeni</span>
                        <span class="choice-card__text">Rezerwuj klimatyczne lokale na godziny: do pracy, na spotkania, sesje i wydarzenia.</span>
                    </label>

                    <label class="choice-card">
                        <input type="radio" name="accountType" value="host">
                        <span class="choice-card__icon" aria-hidden="true">
                            <svg class="icon" width="24" height="24" viewBox="0 0 24 24">
                                <path d="M3 9l1.5-5h15L21 9"></path>
                                <path d="M3 9a3 3 0 0 0 6 0 3 3 0 0 0 6 0 3 3 0 0 0 6 0"></path>
                                <path d="M5 11.5V20h14v-8.5M10 20v-5h4v5"></path>
                            </svg>
                        </span>
                        <span class="choice-card__title">Udostępniam lokal</span>
                        <span class="choice-card__text">Zarabiaj na godzinach, w których Twój lokal jest zamknięty lub pusty.</span>
                    </label>
                </div>
            </fieldset>

            <div class="field">
                <label for="email">E-mail</label>
                <input type="email" id="email" name="email" autocomplete="email" required>
            </div>

            <div class="field">
                <div class="field__row">
                    <label for="password">Hasło</label>
                    <a href="/password-reset">Nie pamiętasz hasła?</a>
                </div>
                <input type="password" id="password" name="password" autocomplete="current-password" required>
            </div>

            <button class="button" type="submit">Zaloguj się</button>
        </form>

        <p class="auth-divider">lub</p>
        <p class="auth-main__register">Nie masz konta? <a href="/register">Załóż konto</a></p>
    </main>

    <?php require __DIR__ . '/partials/footer.html.php'; ?>
</body>
</html>
