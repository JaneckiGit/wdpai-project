<div class="auth-page">
    <?php require __DIR__ . '/partials/auth-hero.html.php'; ?>

    <main class="auth-main">
        <h1>Załóż konto</h1>
        <p class="auth-main__lead">Rezerwuj klimatyczne lokale na godziny – do pracy, spotkań, sesji i kameralnych wydarzeń.</p>

        <form class="auth-form" action="/register" method="post">
            <?php require __DIR__ . '/partials/form-errors.html.php'; ?>

            <div class="field">
                <label for="name">Imię i nazwisko</label>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($form->name) ?>" autocomplete="name" required <?= $form->invalidAttributes('name') ?>>
            </div>

            <div class="field">
                <label for="email">E-mail</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($form->email) ?>" autocomplete="email" required <?= $form->invalidAttributes('email') ?>>
            </div>

            <div class="field">
                <label for="password">Hasło</label>
                <input type="password" id="password" name="password" autocomplete="new-password" minlength="8" required <?= $form->invalidAttributes('password', 'password-hint') ?>>
                <p class="field__hint" id="password-hint">Co najmniej 8 znaków</p>
            </div>

            <div class="field">
                <label for="password-confirmation">Powtórz hasło</label>
                <input type="password" id="password-confirmation" name="passwordConfirmation" autocomplete="new-password" required <?= $form->invalidAttributes('passwordConfirmation') ?>>
            </div>

            <label class="checkbox">
                <input type="checkbox" name="terms" value="1" required <?= $form->termsAccepted ? 'checked' : '' ?> <?= $form->invalidAttributes('terms') ?>>
                <span>Akceptuję <a href="/terms">Regulamin</a></span>
            </label>

            <p class="auth-note">
                <strong>Konto startuje jako „Szukam przestrzeni”.</strong>
                Chcesz udostępniać swój lokal? Po założeniu konta złóż wniosek w zakładce Dla gospodarzy – administrator sprawdzi go zwykle w ciągu jednego dnia roboczego.
            </p>

            <button class="button" type="submit">Załóż konto</button>
        </form>

        <p class="auth-divider">lub</p>
        <p class="auth-main__register">Masz już konto? <a href="/login">Zaloguj się</a></p>
    </main>

    <?php require __DIR__ . '/partials/footer.html.php'; ?>
</div>
