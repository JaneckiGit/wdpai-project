<?php
$purposeIcons = require __DIR__ . '/partials/purposes.php';
$initials = require __DIR__ . '/partials/initials.php';
$relativeTime = require __DIR__ . '/partials/relative-time.php';
$price = require __DIR__ . '/partials/price.php';
$statuses = [
    'application' => ['Wniosek w toku', 'warning', 'fa-regular fa-clock'],
    'active' => ['Aktywne', 'success', 'fa-regular fa-circle-check'],
    'blocked' => ['Zablokowane', 'danger', 'fa-solid fa-ban'],
];
// Link filtra zmienia grupę i zachowuje szukaną frazę; grupa „Wszyscy” nie trafia do adresu.
$filterUrl = static fn (string $group): string => '/admin?' . http_build_query(array_filter(['q' => $form->query, 'group' => $group === 'all' ? '' : $group])) . '#users';
?>
<?php require __DIR__ . '/partials/header.html.php'; ?>

<main class="page stack admin">
    <h1>Panel administratora</h1>

    <?php require __DIR__ . '/partials/status-message.html.php'; ?>

    <section class="card panel" aria-labelledby="applications-heading">
        <h2 id="applications-heading">Wnioski o rolę gospodarza <span class="count"><?= count($applications) ?></span></h2>
        <p class="muted">Po zatwierdzeniu użytkownik dostaje panel gospodarza.</p>

        <ul>
            <?php foreach ($applications as $application): ?>
                <li class="list-item">
                    <?php $hoursWaiting = $application->getHoursWaiting(); ?>
                    <span class="initials" aria-hidden="true"><?= htmlspecialchars($initials($application->getApplicantName())) ?></span>
                    <div>
                        <h3><?= htmlspecialchars($application->getApplicantName()) ?></h3>
                        <p class="muted"><?= htmlspecialchars($application->getEmail()) ?> · konto „Szukam przestrzeni”</p>
                    </div>
                    <span class="badge <?= $hoursWaiting >= 24 ? 'badge--warning' : 'badge--muted' ?>">
                        <i class="fa-regular fa-clock" aria-hidden="true"></i>
                        <?= $hoursWaiting >= 24 ? 'czeka ' . $hoursWaiting . ' h · ' : 'wysłany ' ?><?= $relativeTime($application->getCreatedAt()) ?>
                    </span>

                    <dl class="list-item__row application">
                        <div><dt>Telefon</dt><dd><?= htmlspecialchars($application->getPhone()) ?></dd></div>
                        <div><dt>Firma</dt><dd><?= htmlspecialchars($application->getCompanyName()) ?> <span class="muted">NIP <?= $application->getFormattedNip() ?></span></dd></div>
                        <div><dt>Lokal</dt><dd><?= htmlspecialchars($application->getVenueName()) ?> <span class="muted"><?= htmlspecialchars($application->getAddress() . ' · ' . $venueTypes[$application->getVenueType()]) ?></span></dd></div>
                    </dl>
                    <p class="list-item__row">„<?= htmlspecialchars($application->getMessage()) ?>”</p>

                    <form class="list-item__row actions actions--end" action="/admin/applications/<?= $application->getId() ?>" method="post">
                        <button class="button button--outline" type="submit" name="decision" value="reject">Odrzuć</button>
                        <button class="button" type="submit" name="decision" value="approve"><i class="fa-solid fa-check" aria-hidden="true"></i> Zatwierdź</button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>

    <section class="stack admin__section" aria-labelledby="venues-heading">
        <h2 class="section-title" id="venues-heading">Lokale do akceptacji <span class="count"><?= count($pendingVenues) ?></span></h2>

        <ul class="pending-venues">
            <?php foreach ($pendingVenues as $venue): ?>
                <?php $windows = $venue->getWindowsByMode(); ?>
                <li class="card pending-venue">
                    <img src="/images/login-hero.svg" alt="">
                    <div>
                        <h3><?= htmlspecialchars($venue->getName()) ?></h3>
                        <span class="badge badge--muted"><?= $relativeTime($venue->getCreatedAt()) ?></span>
                        <p class="muted"><?= htmlspecialchars(implode(' · ', array_filter([$venue->getHostName(), $venue->getStreetAddress(), $venue->getDistrict()]))) ?></p>
                        <p class="pending-venue__tags">
                            <?php foreach ($venue->getPurposes() as $purpose): ?>
                                <span class="chip chip--small"><i class="fa-solid <?= $purposeIcons[$purpose][0] ?? 'fa-circle' ?>" aria-hidden="true"></i> <?= htmlspecialchars($purposeNames[$purpose]) ?></span>
                            <?php endforeach; ?>
                        </p>
                        <p class="pending-venue__tags">
                            <?php if (isset($windows['seats'])): ?>
                                <span class="badge badge--seats"><i class="fa-solid fa-laptop" aria-hidden="true"></i> Miejsca do pracy · <?= $windows['seats']->getCapacity() ?> × <?= $price($windows['seats']->getPrice()) ?> zł/h</span>
                            <?php endif; ?>
                            <?php if (isset($windows['exclusive'])): ?>
                                <span class="badge badge--exclusive"><i class="fa-solid fa-key" aria-hidden="true"></i> Na wyłączność · <?= $price($windows['exclusive']->getPrice()) ?> zł/h</span>
                                <span class="muted">do <?= $windows['exclusive']->getCapacity() ?> osób</span>
                            <?php endif; ?>
                        </p>
                        <form class="actions" action="/admin/venues/<?= $venue->getId() ?>" method="post">
                            <button class="button button--outline" type="submit" name="decision" value="reject">Odrzuć</button>
                            <button class="button" type="submit" name="decision" value="publish"><i class="fa-solid fa-check" aria-hidden="true"></i> Opublikuj</button>
                        </form>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>

    <section class="card panel" id="users" aria-labelledby="users-heading">
        <div class="admin__users-header">
            <h2 id="users-heading">Użytkownicy</h2>
            <form class="search" action="/admin#users" method="get" role="search">
                <label class="visually-hidden" for="user-search">Szukaj po imieniu lub e-mailu</label>
                <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                <input type="search" id="user-search" name="q" value="<?= htmlspecialchars($form->query) ?>" placeholder="Szukaj po imieniu lub e-mailu" <?= $form->invalidAttributes('q') ?>>
                <?php if ($form->group !== 'all'): ?>
                    <input type="hidden" name="group" value="<?= htmlspecialchars($form->group) ?>">
                <?php endif; ?>
            </form>
            <nav class="admin__filters" aria-label="Grupy użytkowników">
                <?php foreach ($groups as $group => $label): ?>
                    <a class="chip" href="<?= htmlspecialchars($filterUrl($group)) ?>"<?= $form->group === $group ? ' aria-current="true"' : '' ?>><?= $label ?></a>
                <?php endforeach; ?>
            </nav>
        </div>

        <?php require __DIR__ . '/partials/form-errors.html.php'; ?>
        <?php if (isset($userStatusMessage)): ?>
            <p class="status-message" role="status"><?= htmlspecialchars($userStatusMessage) ?></p>
        <?php endif; ?>

        <div class="admin__table">
            <table>
                <thead>
                    <tr><th scope="col">Użytkownik</th><th scope="col">Role</th><th scope="col">Status</th><th scope="col">Ostatnio</th><th scope="col"><span class="visually-hidden">Akcje</span></th></tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <?php $status = $user->isBlocked() ? 'blocked' : ($user->hasPendingApplication() ? 'application' : 'active'); ?>
                        <tr>
                            <td>
                                <span class="admin__user">
                                    <span class="initials" aria-hidden="true"><?= htmlspecialchars($initials($user->getFullName())) ?></span>
                                    <span><strong><?= htmlspecialchars($user->getFullName()) ?></strong> <span class="muted"><?= htmlspecialchars($user->getEmail()) ?></span></span>
                                </span>
                            </td>
                            <td>
                                <span class="badge badge--muted">Najemca</span>
                                <?php if ($user->isHost()): ?>
                                    <span class="badge badge--exclusive"><i class="fa-solid fa-key" aria-hidden="true"></i> Gospodarz</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge badge--<?= $statuses[$status][1] ?>"><i class="<?= $statuses[$status][2] ?>" aria-hidden="true"></i> <?= $statuses[$status][0] ?></span>
                            </td>
                            <td><?= $user->getLastSeenAt() === null ? '—' : $relativeTime($user->getLastSeenAt()) ?></td>
                            <td>
                                <form class="actions actions--end" action="/admin/users/<?= $user->getId() ?>" method="post">
                                    <?php if ($user->isHost()): ?>
                                        <button class="button button--outline" type="submit" name="action" value="revoke-host">Odbierz rolę gospodarza</button>
                                    <?php endif; ?>
                                    <?php if ($user->isBlocked()): ?>
                                        <button class="button button--outline admin__unblock" type="submit" name="action" value="unblock">Odblokuj</button>
                                    <?php else: ?>
                                        <button class="button button--outline admin__block" type="submit" name="action" value="block">Zablokuj</button>
                                    <?php endif; ?>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if ($users === []): ?>
                        <tr><td colspan="5" class="muted">Brak użytkowników dla wybranych filtrów.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>

<?php require __DIR__ . '/partials/footer.html.php'; ?>
