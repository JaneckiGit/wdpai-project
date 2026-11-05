<header class="site-header">
    <a class="brand site-header__brand" href="/venues">
        <svg class="brand__mark" width="32" height="32" viewBox="0 0 32 32" aria-hidden="true">
            <circle cx="16" cy="16" r="15"></circle>
            <path d="M16 8v8l5 3"></path>
        </svg>
        PoGodzinach
    </a>

    <form class="site-search" action="/venues" method="get" role="search">
        <label class="visually-hidden" for="site-search">Szukaj lokalu</label>
        <i class="fa-solid fa-magnifying-glass site-search__icon" aria-hidden="true"></i>
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

    <!-- Atrybut popover: przeglądarka sama otwiera menu po kliknięciu avatara i zamyka je klawiszem Esc albo kliknięciem obok. -->
    <!-- TODO: Laboratorium 9 – działające „Zmień hasło” i „Usuń konto”. -->
    <button class="avatar" type="button" popovertarget="account-menu" aria-label="Konto: <?= htmlspecialchars($currentUser['name']) ?>"></button>
    <div class="account-menu" id="account-menu" popover>
        <div class="account-menu__user">
            <span class="avatar" aria-hidden="true"></span>
            <div>
                <p class="account-menu__name"><?= htmlspecialchars($currentUser['name']) ?></p>
                <p class="account-menu__email"><?= htmlspecialchars($currentUser['email']) ?></p>
                <span class="account-menu__role"><?= htmlspecialchars($currentUser['role']) ?></span>
            </div>
        </div>
        <a href="/account/password"><i class="fa-solid fa-lock" aria-hidden="true"></i> Zmień hasło</a>
        <a class="account-menu__danger" href="/account/delete"><i class="fa-solid fa-trash" aria-hidden="true"></i> Usuń konto</a>
        <hr>
        <a href="/login"><i class="fa-solid fa-right-from-bracket" aria-hidden="true"></i> Wyloguj się</a>
    </div>
</header>
