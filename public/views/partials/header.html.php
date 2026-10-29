<header>
    <a href="/venues">PoGodzinach</a>

    <form action="/venues" method="get" role="search">
        <label for="site-search">Szukaj lokalu</label>
        <input type="search" id="site-search" name="q" placeholder="Dzielnica, nazwa lokalu...">
        <button type="submit">Szukaj</button>
    </form>

    <nav aria-label="Główna nawigacja">
        <ul>
            <li><a href="/venues"<?= $activeNav === 'venues' ? ' aria-current="page"' : '' ?>>Odkrywaj</a></li>
            <li><a href="/reservations"<?= $activeNav === 'reservations' ? ' aria-current="page"' : '' ?>>Moje rezerwacje</a></li>
            <li><a href="/host"<?= $activeNav === 'host' ? ' aria-current="page"' : '' ?>>Dla gospodarzy</a></li>
        </ul>
    </nav>
</header>
