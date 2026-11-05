<?php
$purposes = require __DIR__ . '/partials/purposes.php';
$modes = [
    'seats' => ['Miejsce do pracy', 'fa-laptop'],
    'exclusive' => ['Cały lokal', 'fa-key'],
];
// Link filtra zmienia jeden parametr i zachowuje pozostałe (pusta fraza nie trafia do adresu).
$filterUrl = static fn (array $change): string => '/venues?' . http_build_query(array_filter(
    array_merge(['q' => $form->query, 'purpose' => $form->purpose, 'mode' => $form->mode], $change)
));
$pins = array_map(static fn (array $venue): array => [
    'id' => $venue['id'],
    'name' => $venue['name'],
    'lat' => $venue['lat'],
    'lng' => $venue['lng'],
    'price' => $form->mode === 'seats' ? $venue['seatPrice'] . ' zł/h · miejsce' : $venue['exclusivePrice'] . ' zł/h',
], $venues);
?>
<?php require __DIR__ . '/partials/header.html.php'; ?>

<main class="discover">
    <h1 class="visually-hidden">Odkrywaj lokale</h1>

    <?php require __DIR__ . '/partials/form-errors.html.php'; ?>

    <nav class="filters" aria-label="Filtry">
        <ul class="filters__purposes">
            <?php foreach ($purposes as $value => [$label, $icon]): ?>
                <li>
                    <a class="chip" href="<?= htmlspecialchars($filterUrl(['purpose' => $value])) ?>"<?= $form->purpose === $value ? ' aria-current="true"' : '' ?>>
                        <i class="fa-solid <?= $icon ?>" aria-hidden="true"></i>
                        <?= $label ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
        <ul class="segmented">
            <?php foreach ($modes as $value => [$label, $icon]): ?>
                <li>
                    <a href="<?= htmlspecialchars($filterUrl(['mode' => $value])) ?>"<?= $form->mode === $value ? ' aria-current="true"' : '' ?>>
                        <i class="fa-solid <?= $icon ?>" aria-hidden="true"></i>
                        <?= $label ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>

    <!-- Na telefonie widać rolki albo mapę – mapę pokazuje link do kotwicy #map (selektor :target w CSS). -->
    <nav class="view-switch" aria-label="Widok">
        <a class="view-switch__reels" href="#reels">Rolki</a>
        <a class="view-switch__map" href="#map">Mapa</a>
    </nav>

    <div class="discover__results">
        <section class="map" id="map" aria-label="Mapa lokali">
            <?php if ($mapbox['token'] !== ''): ?>
                <div class="map__canvas" data-token="<?= htmlspecialchars($mapbox['token']) ?>" data-style="<?= htmlspecialchars($mapbox['style']) ?>" data-venues="<?= htmlspecialchars(json_encode($pins, JSON_UNESCAPED_UNICODE)) ?>"></div>
            <?php else: ?>
                <p class="map__missing">Mapa pojawi się po ustawieniu MAPBOX_TOKEN w pliku .env.</p>
            <?php endif; ?>
        </section>

        <section class="reels" id="reels" aria-label="Lokale">
            <?php foreach ($venues as $venue): ?>
                <article class="venue-card" data-venue-id="<?= $venue['id'] ?>">
                    <img class="venue-card__image" src="/images/login-hero.svg" alt="">
                    <div class="venue-card__info">
                        <h2><?= htmlspecialchars($venue['name']) ?></h2>
                        <p>
                            <i class="fa-solid fa-location-dot" aria-hidden="true"></i>
                            <?= htmlspecialchars($venue['district']) ?>
                        </p>
                        <ul class="venue-card__offers">
                            <?php if ($venue['seatPrice'] !== null): ?>
                                <li class="badge badge--seats">Miejsca do pracy · <?= $venue['seatPrice'] ?> zł/h</li>
                            <?php endif; ?>
                            <?php if ($venue['exclusivePrice'] !== null): ?>
                                <li class="badge badge--exclusive">Na wyłączność · <?= $venue['exclusivePrice'] ?> zł/h</li>
                            <?php endif; ?>
                        </ul>
                        <a class="button" href="/venues/<?= $venue['id'] ?>">Zarezerwuj</a>
                    </div>
                </article>
            <?php endforeach; ?>

            <?php if ($venues === []): ?>
                <p class="reels__empty">Brak lokali dla wybranych filtrów.</p>
            <?php endif; ?>
        </section>
    </div>
</main>

<?php require __DIR__ . '/partials/footer.html.php'; ?>

<?php if ($mapbox['token'] !== ''): ?>
    <link rel="stylesheet" href="https://api.mapbox.com/mapbox-gl-js/v3.16.0/mapbox-gl.css">
    <script src="https://api.mapbox.com/mapbox-gl-js/v3.16.0/mapbox-gl.js" defer></script>
    <script src="/js/map.js" defer></script>
<?php endif; ?>
