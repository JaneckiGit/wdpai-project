<?php require __DIR__ . '/partials/header.html.php'; ?>

<!-- Wspólny widok stron, których treść jeszcze powstaje (Regulamin, Kontakt); tytuł przekazuje kontroler. -->
<main class="page page--narrow in-progress">
    <span class="icon-tile" aria-hidden="true"><i class="fa-solid fa-person-digging"></i></span>
    <h1><?= htmlspecialchars($heading) ?></h1>
    <p class="muted">Ta strona jest w trakcie przygotowania. Zajrzyj tu wkrótce.</p>
    <a class="button button--secondary" href="/venues">Wróć do Odkrywaj</a>
</main>

<?php require __DIR__ . '/partials/footer.html.php'; ?>
