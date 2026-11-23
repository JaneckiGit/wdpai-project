<?php require __DIR__ . '/partials/header.html.php'; ?>

<!-- TODO: Laboratorium 9 – obsługa formularza (POST, walidacja, zapis nowego hasła); do tego czasu przycisk ma tylko wygląd (type="button"). -->
<main class="page page--narrow stack">
    <div>
        <h1>Zmień hasło</h1>
        <p class="muted">Po zmianie zalogujesz się nowym hasłem.</p>
    </div>

    <form class="stack app-form" action="/account/password" method="post">
        <section class="card form-section" aria-label="Hasło">
            <div class="field">
                <label for="current-password">Aktualne hasło</label>
                <input type="password" id="current-password" name="currentPassword" autocomplete="current-password" required>
            </div>
            <div class="field">
                <label for="new-password">Nowe hasło</label>
                <input type="password" id="new-password" name="newPassword" minlength="8" autocomplete="new-password" required aria-describedby="new-password-hint">
                <p class="field__hint" id="new-password-hint">Co najmniej 8 znaków.</p>
            </div>
            <div class="field">
                <label for="new-password-confirmation">Powtórz nowe hasło</label>
                <input type="password" id="new-password-confirmation" name="newPasswordConfirmation" autocomplete="new-password" required>
            </div>
        </section>

        <div class="actions actions--end">
            <a class="button button--secondary" href="/venues">Anuluj</a>
            <button class="button" type="button"><i class="fa-solid fa-lock" aria-hidden="true"></i> Zmień hasło</button>
        </div>
    </form>
</main>

<?php require __DIR__ . '/partials/footer.html.php'; ?>
