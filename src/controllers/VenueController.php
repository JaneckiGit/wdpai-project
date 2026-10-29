<?php

declare(strict_types=1);

namespace App\Controller;

final class VenueController extends AppController
{
    public function index(): void
    {
        $this->render('venues', [
            'pageTitle' => 'Odkrywaj – PoGodzinach',
            'activeNav' => 'venues',
        ]);
    }

    public function show(int $id): void
    {
        $this->render('venue', [
            'pageTitle' => 'Lokal i rezerwacja – PoGodzinach',
            'activeNav' => 'venues',
            'venueId' => $id,
        ]);
    }
}
