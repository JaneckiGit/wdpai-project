<?php
$purposes = require __DIR__ . '/partials/purposes.php';
$initials = require __DIR__ . '/partials/initials.php';
$statuses = [
    'application' => ['Wniosek w toku', 'warning', 'fa-regular fa-clock'],
    'active' => ['Aktywne', 'success', 'fa-regular fa-circle-check'],
    'blocked' => ['Zablokowane', 'danger', 'fa-solid fa-ban'],
];
// Link filtra zmienia grupę i zachowuje szukaną frazę; grupa „Wszyscy” nie trafia do adresu.
$filterUrl = static fn (string $group): string => '/admin?' . http_build_query(array_filter(['q' => $form->query, 'group' => $group === 'all' ? '' : $group])) . '#users';
?>
<?php require __DIR__ . '/partials/header.html.php'; ?>

<!-- TODO: Laboratorium 8 i 9 – przyciski decyzji zapiszą zmiany w bazie; do tego czasu mają tylko wygląd (type="button"). -->
<main class="page stack admin">
    <h1>Panel administratora</h1>

    <section class="card panel" aria-labelledby="applications-heading">
        <h2 id="applications-heading">Wnioski o rolę gospodarza <span class="count"><?= count($applications) ?></span></h2>
        <p class="muted">Po zatwierdzeniu użytkownik dostaje panel gospodarza.</p>

        <ul>
            <?php foreach ($applications as $application): ?>
                <li class="list-item">
                    <span class="initials" aria-hidden="true"><?= htmlspecialchars($initials($application['name'])) ?></span>
                    <div>
                        <h3><?= htmlspecialchars($application['name']) ?></h3>
                        <p class="muted"><?= htmlspecialchars($application['email']) ?> · konto „Szukam przestrzeni”</p>
                    </div>
                    <span class="badge <?= $application['waitingHours'] >= 24 ? 'badge--warning' : 'badge--muted' ?>">
                        <i class="fa-regular fa-clock" aria-hidden="true"></i>
                        <?= $application['waitingHours'] >= 24 ? 'czeka ' . $application['waitingHours'] . ' h · ' : 'wysłany ' ?><?= $application['sent'] ?>
                    </span>

                    <dl class="list-item__row application">
                        <div><dt>Telefon</dt><dd><?= htmlspecialchars($application['phone']) ?></dd></div>
                        <div><dt>Firma</dt><dd><?= htmlspecialchars($application['company']) ?> <span class="muted">NIP <?= htmlspecialchars($application['nip']) ?></span></dd></div>
                        <div><dt>Lokal</dt><dd><?= htmlspecialchars($application['venue']) ?> <span class="muted"><?= htmlspecialchars($application['venueDetails']) ?></span></dd></div>
                    </dl>
                    <p class="list-item__row">„<?= htmlspecialchars($application['message']) ?>”</p>

                    <div class="list-item__row actions admin__decision">
                        <button class="button button--outline" type="button">Odrzuć</button>
                        <button class="button" type="button"><i class="fa-solid fa-check" aria-hidden="true"></i> Zatwierdź</button>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>

    <section class="stack admin__section" aria-labelledby="venues-heading">
        <h2 class="section-title" id="venues-heading">Lokale do akceptacji <span class="count"><?= count($pendingVenues) ?></span></h2>

        <ul class="pending-venues">
            <?php foreach ($pendingVenues as $venue): ?>
                <li class="card pending-venue">
                    <img src="/images/login-hero.svg" alt="">
                    <div>
                        <h3><?= htmlspecialchars($venue['name']) ?></h3>
                        <span class="badge badge--muted"><?= $venue['sent'] ?></span>
                        <p class="muted"><?= htmlspecialchars($venue['host'] . ' · ' . $venue['address']) ?></p>
                        <p class="pending-venue__tags">
                            <?php foreach ($venue['purposes'] as $purpose): ?>
                                <span class="chip chip--small"><i class="fa-solid <?= $purposes[$purpose][1] ?>" aria-hidden="true"></i> <?= $purposes[$purpose][0] ?></span>
                            <?php endforeach; ?>
                        </p>
                        <p class="pending-venue__tags">
                            <?php if ($venue['seatPrice'] !== null): ?>
                                <span class="badge badge--seats"><i class="fa-solid fa-laptop" aria-hidden="true"></i> Miejsca do pracy · <?= $venue['seatCount'] ?> × <?= $venue['seatPrice'] ?> zł/h</span>
                            <?php endif; ?>
                            <?php if ($venue['exclusivePrice'] !== null): ?>
                                <span class="badge badge--exclusive"><i class="fa-solid fa-key" aria-hidden="true"></i> Na wyłączność · <?= $venue['exclusivePrice'] ?> zł/h</span>
                                <span class="muted">do <?= $venue['capacity'] ?> osób</span>
                            <?php endif; ?>
                            <span class="badge <?= $venue['hasReel'] ? 'badge--warning' : 'badge--muted' ?>">
                                <i class="fa-solid fa-video" aria-hidden="true"></i> <?= $venue['hasReel'] ? 'Rolka czeka na zatwierdzenie' : 'Bez rolki' ?>
                            </span>
                        </p>
                        <div class="actions">
                            <button class="button button--outline" type="button">Odrzuć</button>
                            <button class="button" type="button"><i class="fa-solid fa-check" aria-hidden="true"></i> Opublikuj</button>
                        </div>
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

        <div class="admin__table">
            <table>
                <thead>
                    <tr><th scope="col">Użytkownik</th><th scope="col">Role</th><th scope="col">Status</th><th scope="col">Ostatnio</th><th scope="col"><span class="visually-hidden">Akcje</span></th></tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td>
                                <span class="admin__user">
                                    <span class="initials" aria-hidden="true"><?= htmlspecialchars($initials($user['name'])) ?></span>
                                    <span><strong><?= htmlspecialchars($user['name']) ?></strong> <span class="muted"><?= htmlspecialchars($user['email']) ?></span></span>
                                </span>
                            </td>
                            <td>
                                <span class="badge badge--muted">Najemca</span>
                                <?php if ($user['isHost']): ?>
                                    <span class="badge badge--exclusive"><i class="fa-solid fa-key" aria-hidden="true"></i> Gospodarz</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge badge--<?= $statuses[$user['status']][1] ?>"><i class="<?= $statuses[$user['status']][2] ?>" aria-hidden="true"></i> <?= $statuses[$user['status']][0] ?></span>
                                <?php if ($user['status'] === 'blocked'): ?>
                                    <span class="muted">powód: <?= htmlspecialchars($user['blockReason']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td><?= $user['lastSeen'] ?></td>
                            <td>
                                <div class="actions admin__user-actions">
                                <?php if ($user['isHost']): ?>
                                    <button class="button button--outline" type="button">Odbierz rolę gospodarza</button>
                                <?php endif; ?>
                                <?php if ($user['status'] === 'blocked'): ?>
                                    <button class="button button--outline admin__unblock" type="button">Odblokuj</button>
                                <?php else: ?>
                                    <button class="button button--outline admin__block" type="button">Zablokuj</button>
                                <?php endif; ?>
                                </div>
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
