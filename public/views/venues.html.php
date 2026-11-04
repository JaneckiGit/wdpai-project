<?php require __DIR__ . '/partials/header.html.php'; ?>

<main class="discover">
    <h1 class="visually-hidden">Odkrywaj lokale</h1>

    <!-- Na telefonie mapa jest osobną warstwą – pokazuje ją link do kotwicy #map (selektor :target w CSS). -->
    <nav class="view-switch view-switch--mobile" aria-label="Widok">
        <a class="view-switch__option view-switch__option--reels" href="#reels">
            <svg class="icon" width="16" height="16" aria-hidden="true"><use href="/images/icons.svg#video"></use></svg>
            Rolki
        </a>
        <a class="view-switch__option view-switch__option--map" href="#map">
            <svg class="icon" width="16" height="16" aria-hidden="true"><use href="/images/icons.svg#map"></use></svg>
            Mapa
        </a>
    </nav>

    <form class="filters" action="/venues" method="get" aria-label="Filtry">
        <?php require __DIR__ . '/partials/form-errors.html.php'; ?>

        <input type="hidden" name="q" value="<?= htmlspecialchars($form->query) ?>">

        <a class="filters__open" href="#filters-panel" aria-label="Więcej filtrów">
            <svg class="icon" width="17" height="17" aria-hidden="true"><use href="/images/icons.svg#sliders"></use></svg>
        </a>

        <fieldset class="filters__purpose">
            <legend>Cel</legend>
            <label class="chip">
                <input type="radio" name="purpose" value="work" <?= $form->purpose === 'work' ? 'checked' : '' ?>>
                <svg class="icon" width="16" height="16" aria-hidden="true"><use href="/images/icons.svg#laptop"></use></svg>
                Praca
            </label>
            <label class="chip">
                <input type="radio" name="purpose" value="meetings" <?= $form->purpose === 'meetings' ? 'checked' : '' ?>>
                <svg class="icon" width="16" height="16" aria-hidden="true"><use href="/images/icons.svg#people"></use></svg>
                Spotkania i szkolenia
            </label>
            <label class="chip">
                <input type="radio" name="purpose" value="shoots" <?= $form->purpose === 'shoots' ? 'checked' : '' ?>>
                <svg class="icon" width="16" height="16" aria-hidden="true"><use href="/images/icons.svg#camera"></use></svg>
                Sesje foto i wideo
            </label>
            <label class="chip">
                <input type="radio" name="purpose" value="events" <?= $form->purpose === 'events' ? 'checked' : '' ?>>
                <svg class="icon" width="16" height="16" aria-hidden="true"><use href="/images/icons.svg#glass"></use></svg>
                Kameralne wydarzenia
            </label>
        </fieldset>

        <!-- Na telefonie reszta filtrów jest w panelu otwieranym linkiem #filters-panel, na komputerze stoi w pasku. -->
        <div class="filters__panel" id="filters-panel">
            <div class="filters__panel-header">
                <h2>Filtry</h2>
                <a class="filters__close" href="#" aria-label="Zamknij filtry">
                    <svg class="icon" width="22" height="22" aria-hidden="true"><use href="/images/icons.svg#close"></use></svg>
                </a>
            </div>

            <fieldset class="filters__mode">
                <legend>Tryb</legend>
                <div class="segmented">
                    <label class="segmented__option">
                        <input type="radio" name="mode" value="seats" <?= $form->mode === 'seats' ? 'checked' : '' ?>>
                        <svg class="icon" width="16" height="16" aria-hidden="true"><use href="/images/icons.svg#laptop"></use></svg>
                        Miejsce do pracy
                    </label>
                    <label class="segmented__option">
                        <input type="radio" name="mode" value="exclusive" <?= $form->mode === 'exclusive' ? 'checked' : '' ?>>
                        <svg class="icon" width="16" height="16" aria-hidden="true"><use href="/images/icons.svg#key"></use></svg>
                        Cały lokal
                    </label>
                </div>
            </fieldset>

            <div class="filters__fields">
                <label class="pill-field">
                    <svg class="icon" width="16" height="16" aria-hidden="true"><use href="/images/icons.svg#calendar"></use></svg>
                    <span class="pill-field__name">Data</span>
                    <input type="date" id="date" name="date" value="<?= htmlspecialchars($form->date) ?>" <?= $form->invalidAttributes('date') ?>>
                </label>

                <div class="pill-field" role="group" aria-labelledby="hours-label">
                    <svg class="icon" width="16" height="16" aria-hidden="true"><use href="/images/icons.svg#clock"></use></svg>
                    <span class="pill-field__name" id="hours-label">Godziny</span>
                    <label class="visually-hidden" for="time-from">Od godziny</label>
                    <input type="time" id="time-from" name="from" value="<?= htmlspecialchars($form->from) ?>" step="3600" <?= $form->invalidAttributes('from') ?>>
                    <span aria-hidden="true">–</span>
                    <label class="visually-hidden" for="time-to">Do godziny</label>
                    <input type="time" id="time-to" name="to" value="<?= htmlspecialchars($form->to) ?>" step="3600" <?= $form->invalidAttributes('to') ?>>
                </div>

                <label class="pill-field">
                    <svg class="icon" width="16" height="16" aria-hidden="true"><use href="/images/icons.svg#people"></use></svg>
                    <span class="pill-field__name">Liczba osób</span>
                    <input class="pill-field__number" type="number" id="guests" name="guests" min="1" value="<?= htmlspecialchars($form->guests) ?>" <?= $form->invalidAttributes('guests') ?>>
                </label>

                <label class="pill-field">
                    <span class="pill-field__name">Cena do</span>
                    <input class="pill-field__number" type="number" id="max-price" name="maxPrice" value="<?= htmlspecialchars($form->maxPrice) ?>" min="0" <?= $form->invalidAttributes('maxPrice') ?>>
                    <span class="pill-field__unit">zł/h</span>
                </label>

                <!-- TODO: Filtry stosowane od razu po zmianie, bez przycisku (Laboratorium 10) -->
                <button class="filters__submit" type="submit">Pokaż lokale</button>
                <a class="filters__clear" href="/venues">Wyczyść filtry</a>

                <!-- TODO: Lokale z bazy filtrowane według wartości z formularza (Laboratorium 8) -->
                <p class="filters__count"><strong>8 lokali</strong> w tym obszarze</p>
            </div>
        </div>

        <nav class="view-switch view-switch--desktop" aria-label="Widok wyników">
            <a class="view-switch__option" href="/venues" aria-current="page">
                <svg class="icon" width="16" height="16" aria-hidden="true"><use href="/images/icons.svg#map"></use></svg>
                Mapa
            </a>
            <!-- TODO: Widok listy lokali – prototyp pokazuje tylko widok mapy -->
            <a class="view-switch__option" href="/venues?view=list">
                <svg class="icon" width="16" height="16" aria-hidden="true"><use href="/images/icons.svg#list"></use></svg>
                Lista
            </a>
        </nav>
    </form>

    <div class="discover__results">
        <section class="map" id="map" aria-labelledby="map-heading">
            <h2 class="visually-hidden" id="map-heading">Mapa lokali w Krakowie</h2>

            <!-- TODO: Interaktywna mapa z położeniem lokali z bazy (Laboratorium 13) -->
            <img class="map__image" src="/images/map-krakow.svg" alt="Mapa Krakowa: Stare Miasto, Kazimierz, Podgórze, Krowodrza i Wisła">

            <ul class="map__labels" aria-hidden="true">
                <li class="map__label map__label--stare-miasto">Stare Miasto</li>
                <li class="map__label map__label--kazimierz">Kazimierz</li>
                <li class="map__label map__label--podgorze">Podgórze</li>
                <li class="map__label map__label--krowodrza">Krowodrza</li>
                <li class="map__label map__label--grzegorzki">Grzegórzki</li>
                <li class="map__label map__label--debniki">Dębniki</li>
                <li class="map__place map__place--wawel">Wawel</li>
                <li class="map__place map__place--blonia">Błonia</li>
                <li class="map__place map__place--wisla">Wisła</li>
            </ul>

            <ul class="map__pins" aria-label="Lokale na mapie">
                <li><a class="map-pin map-pin--oliwka" href="/venues/3" aria-label="Bistro Oliwka, 18 zł za godzinę za miejsce">18 zł/h · miejsce</a></li>
                <li><a class="map-pin map-pin--pod-zegarem" href="/venues/4" aria-label="Kawiarnia Pod Zegarem, 20 zł za godzinę za miejsce">20 zł/h · miejsce</a></li>
                <li><a class="map-pin map-pin--polcien" href="/venues/5" aria-label="Galeria Półcień, 12 zł za godzinę za miejsce">12 zł/h · miejsce</a></li>
                <li><a class="map-pin map-pin--wiklina" href="/venues/6" aria-label="Studio Wiklina, 200 zł za godzinę za cały lokal">200 zł/h</a></li>
                <li><a class="map-pin map-pin--ziarno" href="/venues/2" aria-label="Kawiarnia Ziarno, 15 zł za godzinę za miejsce">15 zł/h · miejsce</a></li>
                <li><a class="map-pin map-pin--glina" href="/venues/7" aria-label="Pracownia Ceramiki Glina, 120 zł za godzinę za cały lokal">120 zł/h</a></li>
                <li><a class="map-pin map-pin--debnik" href="/venues/8" aria-label="Herbaciarnia Dębnik, 90 zł za godzinę za cały lokal">90 zł/h</a></li>
                <li>
                    <a class="map-pin map-pin--selected map-pin--lampion" href="/venues/1" aria-label="Bar Lampion, 15 zł za godzinę za miejsce – wybrany, odtwarzany w rolce">
                        <span class="map-pin__name">Bar Lampion</span>
                        15 zł/h · miejsce
                    </a>
                </li>
            </ul>

            <!-- TODO: Przyciski mapy zaczną działać razem z mapą (Laboratorium 13) -->
            <button class="map__search" type="button">
                <svg class="icon" width="16" height="16" aria-hidden="true"><use href="/images/icons.svg#search"></use></svg>
                Szukaj w tym obszarze
            </button>

            <div class="map__controls">
                <div class="map__zoom">
                    <button class="map__control" type="button" aria-label="Przybliż mapę">
                        <svg class="icon" width="18" height="18" aria-hidden="true"><use href="/images/icons.svg#plus"></use></svg>
                    </button>
                    <button class="map__control" type="button" aria-label="Oddal mapę">
                        <svg class="icon" width="18" height="18" aria-hidden="true"><use href="/images/icons.svg#minus"></use></svg>
                    </button>
                </div>
                <button class="map__control map__locate" type="button" aria-label="Pokaż moją lokalizację">
                    <svg class="icon" width="18" height="18" aria-hidden="true"><use href="/images/icons.svg#locate"></use></svg>
                </button>
            </div>

            <div class="map__legend">
                <p class="map__legend-title">Ceny za godzinę</p>
                <p><strong>15 zł/h · miejsce</strong> – jedno miejsce do pracy</p>
                <p><strong>150 zł/h</strong> – cały lokal na wyłączność</p>
            </div>
        </section>

        <section class="reels" id="reels" aria-labelledby="reels-heading">
            <h2 class="visually-hidden" id="reels-heading">Rolki lokali</h2>
            <p class="reels__hint">
                <svg class="icon" width="16" height="16" aria-hidden="true"><use href="/images/icons.svg#mouse"></use></svg>
                Przewijaj rolki – mapa podąża za Tobą
            </p>

            <!-- tabindex="0" pozwala przewijać rolki strzałkami po przejściu do nich klawiszem Tab. -->
            <div class="reels__feed" tabindex="0" role="group" aria-labelledby="reels-heading">
                <!-- TODO: Lokale z bazy wyświetlone w pętli, rolka jako wideo (Laboratorium 13) -->
                <article class="reel" aria-labelledby="reel-1-title">
                    <img class="reel__media" src="/images/venues/bar-lampion.svg" alt="Bar Lampion rano: długi stół, laptopy, światło z łukowego okna">

                    <div class="reel__status">
                        <progress class="reel__progress" max="42" value="15">0:15 z 0:42</progress>
                        <p class="reel__meta">
                            <span>
                                <svg class="icon" width="15" height="15" aria-hidden="true"><use href="/images/icons.svg#video"></use></svg>
                                Rolka gospodarza
                            </span>
                            <span>0:15 / 0:42</span>
                        </p>
                    </div>

                    <!-- TODO: Obsługa przycisków rolki (Laboratorium 10) -->
                    <div class="reel__actions">
                        <button class="reel__action" type="button">
                            <span class="reel__action-icon"><svg class="icon" width="20" height="20" aria-hidden="true"><use href="/images/icons.svg#bookmark"></use></svg></span>
                            Zapisz
                        </button>
                        <button class="reel__action" type="button">
                            <span class="reel__action-icon"><svg class="icon" width="20" height="20" aria-hidden="true"><use href="/images/icons.svg#share"></use></svg></span>
                            Udostępnij
                        </button>
                        <button class="reel__action" type="button" aria-pressed="false">
                            <span class="reel__action-icon"><svg class="icon" width="20" height="20" aria-hidden="true"><use href="/images/icons.svg#mute"></use></svg></span>
                            Wycisz
                        </button>
                        <button class="reel__action" type="button">
                            <span class="reel__action-icon"><svg class="icon" width="20" height="20" aria-hidden="true"><use href="/images/icons.svg#pause"></use></svg></span>
                            Pauza
                        </button>
                    </div>

                    <div class="reel__info">
                        <h3 id="reel-1-title">Bar Lampion</h3>
                        <p class="reel__place">
                            <svg class="icon" width="15" height="15" aria-hidden="true"><use href="/images/icons.svg#pin"></use></svg>
                            Stare Miasto · 0,8 km
                        </p>
                        <ul class="reel__offers">
                            <li class="badge badge--seats">
                                <svg class="icon" width="15" height="15" aria-hidden="true"><use href="/images/icons.svg#laptop"></use></svg>
                                Miejsca do pracy · 15 zł/h
                            </li>
                            <li class="badge badge--exclusive">
                                <svg class="icon" width="15" height="15" aria-hidden="true"><use href="/images/icons.svg#key"></use></svg>
                                Na wyłączność · 160 zł/h
                            </li>
                        </ul>
                        <p class="reel__availability">
                            <svg class="icon" width="16" height="16" aria-hidden="true"><use href="/images/icons.svg#clock"></use></svg>
                            Dziś 8:00–14:00 · wolne 5 z 12 miejsc
                        </p>
                        <a class="button reel__book" href="/venues/1">Zarezerwuj</a>
                    </div>
                </article>

                <article class="reel" aria-labelledby="reel-2-title">
                    <img class="reel__media reel__media--top" src="/images/venues/kawiarnia-ziarno.svg" alt="Kawiarnia Ziarno wieczorem: ceglana ściana, długi stół i lampki">

                    <div class="reel__status">
                        <progress class="reel__progress" max="36" value="0">0:00 z 0:36</progress>
                        <p class="reel__meta">
                            <span>
                                <svg class="icon" width="15" height="15" aria-hidden="true"><use href="/images/icons.svg#video"></use></svg>
                                Rolka gospodarza
                            </span>
                            <span>0:00 / 0:36</span>
                        </p>
                    </div>

                    <div class="reel__actions">
                        <button class="reel__action" type="button">
                            <span class="reel__action-icon"><svg class="icon" width="20" height="20" aria-hidden="true"><use href="/images/icons.svg#bookmark"></use></svg></span>
                            Zapisz
                        </button>
                        <button class="reel__action" type="button">
                            <span class="reel__action-icon"><svg class="icon" width="20" height="20" aria-hidden="true"><use href="/images/icons.svg#share"></use></svg></span>
                            Udostępnij
                        </button>
                        <button class="reel__action" type="button" aria-pressed="false">
                            <span class="reel__action-icon"><svg class="icon" width="20" height="20" aria-hidden="true"><use href="/images/icons.svg#mute"></use></svg></span>
                            Wycisz
                        </button>
                        <button class="reel__action" type="button">
                            <span class="reel__action-icon"><svg class="icon" width="20" height="20" aria-hidden="true"><use href="/images/icons.svg#pause"></use></svg></span>
                            Pauza
                        </button>
                    </div>

                    <div class="reel__info">
                        <h3 id="reel-2-title">Kawiarnia Ziarno</h3>
                        <p class="reel__place">
                            <svg class="icon" width="15" height="15" aria-hidden="true"><use href="/images/icons.svg#pin"></use></svg>
                            Kazimierz · 1,2 km
                        </p>
                        <ul class="reel__offers">
                            <li class="badge badge--seats">
                                <svg class="icon" width="15" height="15" aria-hidden="true"><use href="/images/icons.svg#laptop"></use></svg>
                                Miejsca do pracy · 15 zł/h
                            </li>
                            <li class="badge badge--exclusive">
                                <svg class="icon" width="15" height="15" aria-hidden="true"><use href="/images/icons.svg#key"></use></svg>
                                Na wyłączność · 150 zł/h
                            </li>
                        </ul>
                        <p class="reel__availability">
                            <svg class="icon" width="16" height="16" aria-hidden="true"><use href="/images/icons.svg#clock"></use></svg>
                            Najbliżej: wt 18:00–22:00 · miejsca do pracy
                        </p>
                        <a class="button reel__book" href="/venues/2">Zarezerwuj</a>
                    </div>
                </article>
            </div>
        </section>
    </div>
</main>

<?php require __DIR__ . '/partials/footer.html.php'; ?>
