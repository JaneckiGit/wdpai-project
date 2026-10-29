<!doctype html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Newsreader:opsz,wght@6..72,400..700&display=swap">
    <link rel="stylesheet" href="/css/style.css">
    <?php if (isset($stylesheet)): ?>
        <link rel="stylesheet" href="/css/<?= htmlspecialchars($stylesheet) ?>.css">
    <?php endif; ?>
</head>
<body>
    <?= $content ?>
</body>
</html>
