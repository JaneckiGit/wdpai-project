<?php
// Plakietki trybów lokalu z ceną za godzinę (pierwsze okno każdego trybu) – Odkrywaj i panel gospodarza.
// Widok dołącza ten plik w pętli, więc $price wczytujemy tylko raz.
$price ??= require __DIR__ . '/price.php';
$windows = $venue->getWindowsByMode();
?>
<?php if (isset($windows['seats'])): ?>
    <li class="badge badge--seats">Miejsca do pracy · <?= $price($windows['seats']->getPrice()) ?> zł/h</li>
<?php endif; ?>
<?php if (isset($windows['exclusive'])): ?>
    <li class="badge badge--exclusive">Na wyłączność · <?= $price($windows['exclusive']->getPrice()) ?> zł/h</li>
<?php endif; ?>
