<div class="auth-page">
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
            <?php require __DIR__ . '/partials/form-errors.html.php'; ?>

            <div class="field">
                <label for="email">E-mail</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($form->email) ?>" autocomplete="email" required <?= $form->invalidAttributes('email') ?>>
            </div>

            <div class="field">
                <div class="field__row">
                    <label for="password">Hasło</label>
                    <a href="/password-reset">Nie pamiętasz hasła?</a>
                </div>
                <input type="password" id="password" name="password" autocomplete="current-password" required <?= $form->invalidAttributes('password') ?>>
            </div>

            <button class="button" type="submit">Zaloguj się</button>
        </form>

        <p class="auth-divider">lub</p>
        <p class="auth-main__register">Nie masz konta? <a href="/register">Załóż konto</a></p>
    </main>

    <?php require __DIR__ . '/partials/footer.html.php'; ?>
</div>
