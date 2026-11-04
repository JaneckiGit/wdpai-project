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
        <svg class="icon site-search__icon" width="18" height="18" viewBox="0 0 24 24" aria-hidden="true">
            <circle cx="11" cy="11" r="7"></circle>
            <path d="m20 20-3.5-3.5"></path>
        </svg>
        <input type="search" id="site-search" name="q" value="<?= htmlspecialchars($searchQuery ?? '') ?>" placeholder="Dzielnica, nazwa lokalu…">
    </form>

    <nav class="site-nav" aria-label="Główna nawigacja">
        <ul>
            <li>
                <a class="site-nav__link" href="/venues"<?= $activeNav === 'venues' ? ' aria-current="page"' : '' ?>>
                    <span class="site-nav__icon">
                        <svg class="icon" width="20" height="20" viewBox="0 0 24 24" aria-hidden="true">
                            <circle cx="12" cy="12" r="9"></circle>
                            <path d="m15.5 8.5-2 5-5 2 2-5z"></path>
                        </svg>
                    </span>
                    Odkrywaj
                </a>
            </li>
            <li>
                <a class="site-nav__link" href="/reservations"<?= $activeNav === 'reservations' ? ' aria-current="page"' : '' ?>>
                    <span class="site-nav__icon">
                        <svg class="icon" width="20" height="20" viewBox="0 0 24 24" aria-hidden="true">
                            <rect x="3.5" y="5" width="17" height="15.5" rx="2"></rect>
                            <path d="M3.5 10h17M8 3v4M16 3v4"></path>
                        </svg>
                    </span>
                    <span class="site-nav__label-long">Moje rezerwacje</span>
                    <span class="site-nav__label-short">Rezerwacje</span>
                </a>
            </li>
            <li>
                <a class="site-nav__link" href="/host"<?= $activeNav === 'host' ? ' aria-current="page"' : '' ?>>
                    <span class="site-nav__icon">
                        <svg class="icon" width="20" height="20" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M3 9l1.5-5h15L21 9"></path>
                            <path d="M3 9a3 3 0 0 0 6 0 3 3 0 0 0 6 0 3 3 0 0 0 6 0"></path>
                            <path d="M5 11.5V20h14v-8.5M10 20v-5h4v5"></path>
                        </svg>
                    </span>
                    Dla gospodarzy
                </a>
            </li>
            <li class="site-nav__profile">
                <a class="site-nav__link" href="/profile">
                    <span class="site-nav__icon">
                        <svg class="icon" width="20" height="20" viewBox="0 0 24 24" aria-hidden="true">
                            <circle cx="12" cy="8" r="4"></circle>
                            <path d="M4.5 20.5a7.5 7.5 0 0 1 15 0"></path>
                        </svg>
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
