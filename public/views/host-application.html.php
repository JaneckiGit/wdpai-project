<?php
$types = require __DIR__ . '/partials/venue-types.php';
?>
<?php require __DIR__ . '/partials/header.html.php'; ?>

<main class="page host-application">
    <nav class="breadcrumbs" aria-label="Ścieżka">
        <ol>
            <li><a href="/venues">Odkrywaj</a></li>
            <li aria-current="page">Dla gospodarzy</li>
        </ol>
    </nav>

    <h1>Zostań gospodarzem</h1>
    <p class="host-application__lead">Udostępniaj swój lokal w godzinach, gdy jest zamknięty lub pusty. Najpierw sprawdzimy Twoje dane – po akceptacji dodasz lokal i zaczniesz przyjmować rezerwacje.</p>

    <?php require __DIR__ . '/partials/status-message.html.php'; ?>

    <div class="host-application__layout">
        <form class="stack app-form host-application__form" action="/host/apply" method="post">
            <?php require __DIR__ . '/partials/form-errors.html.php'; ?>

            <section class="card form-section">
                <h2><span>1</span> Dane kontaktowe</h2>
                <p class="field__hint">Administrator może zadzwonić, gdyby coś wymagało wyjaśnienia.</p>
                <div class="form-grid">
                    <div class="field">
                        <label for="first-name">Imię</label>
                        <input type="text" id="first-name" name="firstName" value="<?= htmlspecialchars($form->firstName) ?>" maxlength="60" autocomplete="given-name" required <?= $form->invalidAttributes('firstName') ?>>
                    </div>
                    <div class="field">
                        <label for="last-name">Nazwisko</label>
                        <input type="text" id="last-name" name="lastName" value="<?= htmlspecialchars($form->lastName) ?>" maxlength="60" autocomplete="family-name" required <?= $form->invalidAttributes('lastName') ?>>
                    </div>
                    <div class="field">
                        <label for="phone">Telefon</label>
                        <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($form->phone) ?>" autocomplete="tel" required <?= $form->invalidAttributes('phone', 'phone-hint') ?>>
                        <p class="field__hint" id="phone-hint">Najemcy zobaczą go dopiero po akceptacji rezerwacji.</p>
                    </div>
                    <div class="field">
                        <label for="account-email">E-mail z Twojego konta</label>
                        <input type="email" id="account-email" value="<?= htmlspecialchars($currentUser['email']) ?>" readonly>
                    </div>
                </div>
            </section>

            <section class="card form-section">
                <h2><span>2</span> Firma</h2>
                <p class="field__hint">Dane firmy widzi tylko administrator – nie pokazujemy ich najemcom.</p>
                <div class="form-grid">
                    <div class="field">
                        <label for="company-name">Nazwa firmy</label>
                        <input type="text" id="company-name" name="companyName" value="<?= htmlspecialchars($form->companyName) ?>" maxlength="120" autocomplete="organization" required <?= $form->invalidAttributes('companyName') ?>>
                    </div>
                    <div class="field">
                        <label for="nip">NIP</label>
                        <input type="text" id="nip" name="nip" value="<?= htmlspecialchars($form->nip) ?>" inputmode="numeric" required <?= $form->invalidAttributes('nip', 'nip-hint') ?>>
                        <p class="field__hint" id="nip-hint">10 cyfr, np. 676-248-10-35.</p>
                    </div>
                </div>
            </section>

            <section class="card form-section">
                <h2><span>3</span> Lokal</h2>
                <p class="field__hint">Pierwszy lokal, który chcesz udostępniać – kolejne dodasz w panelu gospodarza.</p>
                <div class="form-grid">
                    <div class="field">
                        <label for="venue-name">Nazwa lokalu</label>
                        <input type="text" id="venue-name" name="venueName" value="<?= htmlspecialchars($form->venueName) ?>" maxlength="120" required <?= $form->invalidAttributes('venueName') ?>>
                    </div>
                    <div class="field">
                        <label for="venue-type">Typ lokalu</label>
                        <select id="venue-type" name="venueType" required <?= $form->invalidAttributes('venueType') ?>>
                            <option value="">Wybierz typ</option>
                            <?php foreach ($types as $value => $label): ?>
                                <option value="<?= $value ?>" <?= $form->venueType === $value ? 'selected' : '' ?>><?= $label ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="field form-grid__wide">
                        <label for="address">Adres</label>
                        <input type="text" id="address" name="address" value="<?= htmlspecialchars($form->address) ?>" maxlength="255" autocomplete="street-address" required <?= $form->invalidAttributes('address', 'address-hint') ?>>
                        <p class="field__hint" id="address-hint">Zdjęcia, rolkę i okna dostępności dodasz po akceptacji w formularzu „Dodaj lokal”.</p>
                    </div>
                </div>
            </section>

            <section class="card form-section">
                <h2><span>4</span> Opis</h2>
                <p class="field__hint">Kilka zdań dla administratora. Opis dla najemców napiszesz przy dodawaniu lokalu.</p>
                <div class="field">
                    <label for="message">Czym się zajmujesz i jak chcesz korzystać z PoGodzinach?</label>
                    <textarea id="message" name="message" rows="4" maxlength="600" data-counter="message-length" required <?= $form->invalidAttributes('message', 'message-count') ?>><?= htmlspecialchars($form->message) ?></textarea>
                    <p class="field__hint field__count" id="message-count"><span id="message-length"><?= mb_strlen($form->message) ?></span> / 600</p>
                </div>
            </section>

            <label class="checkbox">
                <input type="checkbox" name="terms" value="1" required <?= $form->termsAccepted ? 'checked' : '' ?> <?= $form->invalidAttributes('terms') ?>>
                <span>Oświadczam, że mogę udostępniać ten lokal, i akceptuję <a href="/terms">Regulamin dla gospodarzy</a></span>
            </label>

            <div class="actions host-application__actions">
                <p class="field__hint">Wniosek sprawdzi administrator – decyzję dostaniesz e-mailem.</p>
                <a class="button button--secondary" href="/venues">Anuluj</a>
                <button class="button" type="submit">Wyślij wniosek</button>
            </div>
        </form>

        <aside class="card host-application__steps" aria-labelledby="steps-heading">
            <h2 id="steps-heading">Jak to działa</h2>
            <ol>
                <li>
                    <span class="initials">1</span>
                    <span><strong>Wypełnij wniosek</strong> <span class="badge badge--warning">Jesteś tutaj</span></span>
                </li>
                <li>
                    <span class="initials">2</span>
                    <span><strong>Administrator sprawdza dane</strong> <span class="muted">zwykle 1 dzień roboczy</span></span>
                </li>
                <li>
                    <span class="initials">3</span>
                    <span><strong>Panel gospodarza</strong> <span class="muted">dodajesz lokal, okna dostępności i przyjmujesz rezerwacje</span></span>
                </li>
            </ol>
        </aside>
    </div>
</main>

<?php require __DIR__ . '/partials/footer.html.php'; ?>

<script src="/js/char-count.js" defer></script>
