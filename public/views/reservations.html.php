<?php
$dayNames = require __DIR__ . '/partials/day-names.php';
// Status rezerwacji: etykieta i kolor plakietki ze wspólnych klas badge--….
$statusLabels = [
    'pending' => ['Oczekuje na akceptację', 'warning'],
    'accepted' => ['Potwierdzona', 'success'],
    'rejected' => ['Odrzucona', 'danger'],
    'cancelled' => ['Anulowana', 'muted'],
];
?>
<?php require __DIR__ . '/partials/header.html.php'; ?>

<main class="reservations">
    <h1>Moje rezerwacje</h1>

    <?php require __DIR__ . '/partials/status-message.html.php'; ?>

    <?php if ($reservations === []): ?>
        <p class="muted">Nie masz jeszcze rezerwacji. <a href="/venues">Znajdź lokal w Odkrywaj</a>.</p>
    <?php endif; ?>

    <ul class="reservation-list">
        <?php foreach ($reservations as $reservation): ?>
            <?php $date = $reservation->getDate(); ?>
            <li class="card reservation">
                <a href="/venues/<?= $reservation->getVenueId() ?>"><?= htmlspecialchars((string) $reservation->getVenueName()) ?></a>
                <span class="muted"><?= $dayNames[(int) $date->format('N')] . ' ' . $date->format('d.m') ?>, <?= $reservation->getStartHour() ?>:00–<?= $reservation->getEndHour() ?>:00</span>
                <span class="badge badge--<?= $statusLabels[$reservation->getStatus()][1] ?>"><?= $statusLabels[$reservation->getStatus()][0] ?></span>
                <?php if (in_array($reservation->getStatus(), $cancellableStatuses, true)): ?>
                    <form method="post" action="/reservations/<?= $reservation->getId() ?>/cancel">
                        <button type="submit">Anuluj rezerwację</button>
                    </form>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
</main>

<?php require __DIR__ . '/partials/footer.html.php'; ?>
