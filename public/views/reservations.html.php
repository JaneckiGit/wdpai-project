<?php
$statusLabels = [
    'pending' => 'Oczekuje na akceptację',
    'accepted' => 'Potwierdzona',
    'rejected' => 'Odrzucona',
];
?>
<?php require __DIR__ . '/partials/header.html.php'; ?>

<main class="reservations">
    <h1>Moje rezerwacje</h1>

    <?php require __DIR__ . '/partials/status-message.html.php'; ?>

    <ul class="reservation-list">
        <?php foreach ($reservations as $reservation): ?>
            <li class="card reservation">
                <a href="/venues/<?= $reservation['venueId'] ?>"><?= htmlspecialchars($reservation['venueName']) ?></a>
                <span class="reservation__date"><?= $reservation['date'] ?>, <?= $reservation['hours'] ?></span>
                <span class="badge reservation__status--<?= $reservation['status'] ?>"><?= $statusLabels[$reservation['status']] ?></span>
                <?php if ($reservation['status'] !== 'rejected'): ?>
                    <form method="post" action="/reservations/<?= $reservation['id'] ?>/cancel">
                        <button type="submit">Anuluj rezerwację</button>
                    </form>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
</main>

<?php require __DIR__ . '/partials/footer.html.php'; ?>
