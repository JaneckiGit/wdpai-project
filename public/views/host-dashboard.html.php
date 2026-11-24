<?php
$purposeIcons = require __DIR__ . '/partials/purposes.php';
$initials = require __DIR__ . '/partials/initials.php';
$dayNames = require __DIR__ . '/partials/day-names.php';
$modes = ['seats' => ['Miejsca do pracy', 'fa-laptop'], 'exclusive' => ['Na wyłączność', 'fa-key']];
$money = static fn (float $amount): string => number_format($amount, 2, ',', ' ') . ' zł';
$price = require __DIR__ . '/partials/price.php';
// Status lokalu: etykieta i kolor plakietki; tylko opublikowany lokal ma stronę, do której prowadzi kafelek.
$venueStatuses = [
    'draft' => ['Szkic', 'muted'],
    'pending' => ['Czeka na weryfikację', 'warning'],
    'published' => ['Opublikowany', 'success'],
    'rejected' => ['Odrzucony', 'danger'],
];
?>
<?php require __DIR__ . '/partials/header.html.php'; ?>

<main class="page host">
    <?php require __DIR__ . '/partials/status-message.html.php'; ?>

    <div class="host__header">
        <h1>Dzień dobry, <?= htmlspecialchars($currentUser->getFirstName()) ?></h1>
        <a class="button" href="/host/venues/new"><i class="fa-solid fa-plus" aria-hidden="true"></i> Dodaj lokal</a>
    </div>

    <div class="host__columns">
        <section class="card panel" aria-labelledby="requests-heading">
            <h2 id="requests-heading">Prośby o rezerwację <span class="count"><?= count($requests) ?></span></h2>

            <?php if ($requests === []): ?>
                <p class="muted">Brak próśb do decyzji.</p>
            <?php endif; ?>

            <ul class="request-list">
                <?php foreach ($requests as $request): ?>
                    <li class="list-item">
                        <span class="initials" aria-hidden="true"><?= htmlspecialchars($initials($request['guest'])) ?></span>
                        <div>
                            <h3><?= htmlspecialchars($request['guest']) ?></h3>
                            <p class="muted"><?= htmlspecialchars($request['venueName']) ?> · <?= $dayNames[(int) $request['date']->format('N')] . ' ' . $request['date']->format('d.m') ?>, <?= $request['from'] ?>:00–<?= $request['to'] ?>:00</p>
                        </div>
                        <span class="badge <?= $request['expiresInHours'] <= 6 ? 'badge--warning' : 'badge--muted' ?>">
                            <i class="fa-regular fa-clock" aria-hidden="true"></i> wygasa za <?= $request['expiresInHours'] ?> h
                        </span>

                        <p class="list-item__row">
                            <span class="chip chip--small"><i class="fa-solid <?= $purposeIcons[$request['purpose']][0] ?? 'fa-circle' ?>" aria-hidden="true"></i> <?= htmlspecialchars($purposeNames[$request['purpose']]) ?></span>
                            <span class="badge badge--<?= $request['mode'] ?>"><i class="fa-solid <?= $modes[$request['mode']][1] ?>" aria-hidden="true"></i> <?= $modes[$request['mode']][0] ?></span>
                            <?php if ($request['mode'] === 'seats'): ?>
                                <span class="muted">· <?= $request['count'] ?> <?= $request['count'] === 1 ? 'miejsce' : 'miejsca' ?></span>
                            <?php endif; ?>
                        </p>

                        <div class="list-item__row request__footer">
                            <p>
                                <strong><?= $money($request['total']) ?></strong>
                                <span class="muted"><?= $request['mode'] === 'seats' ? $request['count'] . ' × ' : '' ?><?= $request['hours'] ?> h × <?= $price($request['price']) ?> zł</span>
                            </p>
                            <form class="actions" action="/host/requests/<?= $request['id'] ?>" method="post">
                                <button class="button button--outline" type="submit" name="decision" value="reject">Odrzuć</button>
                                <button class="button" type="submit" name="decision" value="accept"><i class="fa-solid fa-check" aria-hidden="true"></i> Akceptuj</button>
                            </form>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>

            <p class="muted host__note">Prośby bez decyzji wygasają po 24 h, a zarezerwowane miejsca wracają do puli.</p>
        </section>

        <section class="card panel" aria-labelledby="venues-heading">
            <h2 id="venues-heading">Twoje lokale</h2>

            <ul class="venue-tiles">
                <?php foreach ($venues as $venue): ?>
                    <?php [$statusLabel, $statusColor] = $venueStatuses[$venue->getStatus()]; ?>
                    <li>
                        <?= $venue->isPublished() ? '<a class="card venue-tile" href="/venues/' . $venue->getId() . '">' : '<div class="card venue-tile">' ?>
                            <img src="/images/login-hero.svg" alt="">
                            <div>
                                <h3><?= htmlspecialchars($venue->getName()) ?></h3>
                                <span class="badge badge--<?= $statusColor ?>"><?= $statusLabel ?></span>
                                <p class="muted"><?= htmlspecialchars(implode(' · ', array_filter([$venue->getDistrict(), $venue->getStreetAddress()]))) ?></p>
                                <ul class="offer-badges">
                                    <?php require __DIR__ . '/partials/offer-badges.html.php'; ?>
                                </ul>
                            </div>
                        <?= $venue->isPublished() ? '</a>' : '</div>' ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </section>
    </div>
</main>

<?php require __DIR__ . '/partials/footer.html.php'; ?>
