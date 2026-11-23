<?php require __DIR__ . '/partials/header.html.php'; ?>

<!-- TODO: Laboratorium 9 – obsługa formularza (POST, sprawdzenie hasła, usunięcie konta); do tego czasu przycisk ma tylko wygląd (type="button"). -->
<main class="page page--narrow stack">
    <div>
        <h1>Usuń konto</h1>
        <p class="muted">Tego nie da się cofnąć. Znikną Twoje rezerwacje, wnioski i lokale, a oczekujące prośby zostaną anulowane.</p>
    </div>

    <form class="stack app-form" action="/account/delete" method="post">
        <section class="card form-section" aria-label="Potwierdzenie">
            <div class="field">
                <label for="delete-password">Aktualne hasło</label>
                <input type="password" id="delete-password" name="password" autocomplete="current-password" required>
            </div>
            <label class="checkbox">
                <input type="checkbox" name="confirmed" value="1" required>
                <span>Rozumiem, że usunięcia konta nie da się cofnąć</span>
            </label>
        </section>

        <div class="actions actions--end">
            <a class="button button--secondary" href="/venues">Anuluj</a>
            <button class="button button--danger" type="button"><i class="fa-solid fa-trash" aria-hidden="true"></i> Usuń konto</button>
        </div>
    </form>
</main>

<?php require __DIR__ . '/partials/footer.html.php'; ?>
