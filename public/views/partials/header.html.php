<?php $themeToggleInHeader = true; ?>
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
        <svg class="icon site-search__icon" width="18" height="18" aria-hidden="true"><use href="/images/icons.svg#search"></use></svg>
        <input type="search" id="site-search" name="q" value="<?= htmlspecialchars($searchQuery ?? '') ?>" placeholder="Dzielnica, nazwa lokalu…">
    </form>

    <nav class="site-nav" aria-label="Główna nawigacja">
        <ul>
            <li>
                <a class="site-nav__link" href="/venues"<?= $activeNav === 'venues' ? ' aria-current="page"' : '' ?>>
                    <span class="site-nav__icon">
                        <svg class="icon" width="20" height="20" aria-hidden="true"><use href="/images/icons.svg#compass"></use></svg>
                    </span>
                    Odkrywaj
                </a>
            </li>
            <li>
                <a class="site-nav__link" href="/reservations"<?= $activeNav === 'reservations' ? ' aria-current="page"' : '' ?>>
                    <span class="site-nav__icon">
                        <svg class="icon" width="20" height="20" aria-hidden="true"><use href="/images/icons.svg#calendar"></use></svg>
                    </span>
                    <span class="site-nav__label-long">Moje rezerwacje</span>
                    <span class="site-nav__label-short">Rezerwacje</span>
                </a>
            </li>
            <li>
                <a class="site-nav__link" href="/host"<?= $activeNav === 'host' ? ' aria-current="page"' : '' ?>>
                    <span class="site-nav__icon">
                        <svg class="icon" width="20" height="20" aria-hidden="true"><use href="/images/icons.svg#store"></use></svg>
                    </span>
                    Dla gospodarzy
                </a>
            </li>
            <li class="site-nav__profile">
                <a class="site-nav__link" href="/profile">
                    <span class="site-nav__icon">
                        <svg class="icon" width="20" height="20" aria-hidden="true"><use href="/images/icons.svg#user"></use></svg>
                    </span>
                    Profil
                </a>
            </li>
        </ul>
    </nav>

    <?php require __DIR__ . '/theme-toggle.html.php'; ?>

    <!-- TODO: Imię i inicjały zalogowanego użytkownika z sesji (Laboratorium 9) -->
    <a class="site-header__account" href="/profile" aria-label="Konto: Anna Kowalska">AK</a>
</header>
