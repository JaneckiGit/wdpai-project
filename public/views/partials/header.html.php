<header class="site-header">
    <a class="brand site-header__brand" href="/venues">
        <svg class="brand__mark" width="32" height="32" viewBox="0 0 32 32" aria-hidden="true">
            <circle cx="16" cy="16" r="15"></circle>
            <path d="M16 8v8l5 3"></path>
        </svg>
        PoGodzinach
    </a>

    <form class="site-search search" action="/venues" method="get" role="search">
        <label class="visually-hidden" for="site-search">Szukaj lokalu</label>
        <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
        <input type="search" id="site-search" name="q" value="<?= htmlspecialchars($searchQuery ?? '') ?>" placeholder="Dzielnica, nazwa lokalu…">
    </form>

    <nav class="site-nav" aria-label="Główna nawigacja">
        <ul>
            <li>
                <a class="site-nav__link" href="/venues"<?= $activeNav === 'venues' ? ' aria-current="page"' : '' ?>>
                    <span class="site-nav__icon"><i class="fa-solid fa-compass" aria-hidden="true"></i></span>
                    Odkrywaj
                </a>
            </li>
            <li>
                <a class="site-nav__link" href="/reservations"<?= $activeNav === 'reservations' ? ' aria-current="page"' : '' ?>>
                    <span class="site-nav__icon"><i class="fa-solid fa-calendar" aria-hidden="true"></i></span>
                    <span class="site-nav__label-long">Moje rezerwacje</span>
                    <span class="site-nav__label-short">Rezerwacje</span>
                </a>
            </li>
            <li>
                <a class="site-nav__link" href="/host"<?= $activeNav === 'host' ? ' aria-current="page"' : '' ?>>
                    <span class="site-nav__icon"><i class="fa-solid fa-store" aria-hidden="true"></i></span>
                    Dla gospodarzy
                </a>
            </li>
        </ul>
    </nav>

    <!-- Strona błędu serwera nie ma konta (null), więc nie pokazuje menu konta. -->
    <?php if ($currentUser !== null): ?>
    <!-- Atrybut popover: przeglądarka sama otwiera menu po kliknięciu avatara i zamyka je klawiszem Esc albo kliknięciem obok. -->
    <button class="avatar" type="button" popovertarget="account-menu" aria-label="Konto: <?= htmlspecialchars($currentUser->getName()) ?>"></button>
    <div class="account-menu" id="account-menu" popover>
        <div class="account-menu__user">
            <span class="avatar" aria-hidden="true"></span>
            <div>
                <p class="account-menu__name"><?= htmlspecialchars($currentUser->getName()) ?></p>
                <p class="account-menu__email"><?= htmlspecialchars($currentUser->getEmail()) ?></p>
                <span class="account-menu__role"><?= $currentUser->isAdmin() ? 'Administrator' : ($currentUser->isHost() ? 'Gospodarz' : 'Najemca') ?></span>
            </div>
        </div>
        <a href="/account/password"><i class="fa-solid fa-lock" aria-hidden="true"></i> Zmień hasło</a>
        <a class="account-menu__danger" href="/account/delete"><i class="fa-solid fa-trash" aria-hidden="true"></i> Usuń konto</a>
        <hr>
        <!-- Wylogowanie zmienia stan, więc jest formularzem POST, a nie linkiem (jak w Laboratorium 9). -->
        <form action="/logout" method="post">
            <button type="submit"><i class="fa-solid fa-right-from-bracket" aria-hidden="true"></i> Wyloguj się</button>
        </form>
    </div>
    <?php endif; ?>
</header>
