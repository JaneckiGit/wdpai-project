<?php
// Cena z bazy (NUMERIC, np. 15.00) bez zbędnych zer: 15 → „15”, 15.5 → „15,50”.
return static fn (float $price): string => fmod($price, 1.0) === 0.0 ? (string) (int) $price : number_format($price, 2, ',', ' ');
