<?php require __DIR__ . '/partials/header.html.php'; ?>

<main class="page page--narrow message-page">
    <span class="icon-tile" aria-hidden="true"><i class="fa-solid fa-triangle-exclamation"></i></span>
    <h1><?= $status ?></h1>
    <p class="muted"><?= htmlspecialchars($message) ?></p>
    <a class="button button--secondary" href="/">Wróć na stronę główną</a>
</main>

<?php require __DIR__ . '/partials/footer.html.php'; ?>
