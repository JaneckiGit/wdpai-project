<!doctype html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <script>
        (function () {
            const storageKey = 'theme';
            let savedTheme = null;

            try {
                savedTheme = localStorage.getItem(storageKey);
            } catch (error) {
                savedTheme = null;
            }

            const hasSavedTheme = savedTheme === 'light' || savedTheme === 'dark';
            const systemPrefersDark = window.matchMedia(
                '(prefers-color-scheme: dark)'
            ).matches;

            document.documentElement.dataset.theme = hasSavedTheme
                ? savedTheme
                : (systemPrefersDark ? 'dark' : 'light');
        })();
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Newsreader:opsz,wght@6..72,400..700&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" referrerpolicy="no-referrer">
    <?php
    // Czas zmiany pliku w adresie (?v=…) – po każdej zmianie CSS przeglądarka pobiera nowy plik zamiast starego z pamięci podręcznej.
    $asset = static fn (string $path): string => $path . '?v=' . filemtime(dirname(__DIR__) . $path);
    ?>
    <link rel="stylesheet" href="<?= $asset('/css/style.css') ?>">
    <?php if (isset($stylesheet)): ?>
        <link rel="stylesheet" href="<?= htmlspecialchars($asset('/css/' . $stylesheet . '.css')) ?>">
    <?php endif; ?>
</head>
<body>
    <button
        class="theme-toggle"
        type="button"
        aria-label="Włącz ciemny motyw"
        aria-pressed="false"
        title="Zmień motyw"
    >
        <i class="fa-solid fa-moon" aria-hidden="true"></i>
    </button>

    <?= $content ?>

    <script src="<?= $asset('/js/theme.js') ?>" defer></script>
</body>
</html>
