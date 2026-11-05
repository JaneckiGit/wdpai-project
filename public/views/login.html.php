<div class="auth-page">
    <?php require __DIR__ . '/partials/auth-hero.html.php'; ?>

    <main class="auth-main">
        <h1>Witaj w PoGodzinach</h1>
        <p class="auth-main__lead">Przestrzeń na godziny – do pracy, spotkań, sesji i kameralnych wydarzeń</p>

        <form class="auth-form" action="/login" method="post">
            <?php require __DIR__ . '/partials/status-message.html.php'; ?>
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
