<?php require __DIR__ . '/partials/header.html.php'; ?>

<main class="page page--narrow stack">
    <div>
        <h1>Zmień hasło</h1>
        <p class="muted">Po zmianie zalogujesz się nowym hasłem.</p>
    </div>

    <?php require __DIR__ . '/partials/status-message.html.php'; ?>

    <form class="stack app-form" action="/account/password" method="post">
        <?php require __DIR__ . '/partials/form-errors.html.php'; ?>

        <section class="card form-section" aria-label="Hasło">
            <div class="field">
                <label for="current-password">Aktualne hasło</label>
                <input type="password" id="current-password" name="currentPassword" autocomplete="current-password" required <?= $form->invalidAttributes('currentPassword') ?>>
            </div>
            <div class="field">
                <label for="new-password">Nowe hasło</label>
                <input type="password" id="new-password" name="newPassword" minlength="8" autocomplete="new-password" required <?= $form->invalidAttributes('newPassword', 'new-password-hint') ?>>
                <p class="field__hint" id="new-password-hint">Co najmniej 8 znaków.</p>
            </div>
            <div class="field">
                <label for="new-password-confirmation">Powtórz nowe hasło</label>
                <input type="password" id="new-password-confirmation" name="newPasswordConfirmation" autocomplete="new-password" required <?= $form->invalidAttributes('newPasswordConfirmation') ?>>
            </div>
        </section>

        <div class="actions actions--end">
            <a class="button button--secondary" href="/venues">Anuluj</a>
            <button class="button" type="submit"><i class="fa-solid fa-lock" aria-hidden="true"></i> Zmień hasło</button>
        </div>
    </form>
</main>

<?php require __DIR__ . '/partials/footer.html.php'; ?>
