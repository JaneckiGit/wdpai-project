<?php
// Inicjały do kółka z osobą: pierwsza litera każdego słowa, np. „Piotr Nowak” → „PN”.
return static fn (string $name): string => implode('', array_map(static fn (string $word): string => mb_substr($word, 0, 1), explode(' ', $name)));
