<?php

declare(strict_types=1);

namespace App\Controller;

final class HostController extends AppController
{
    public function dashboard(): void
    {
        $this->render('host-dashboard', [
            'pageTitle' => 'Panel gospodarza – PoGodzinach',
            'activeNav' => 'host',
        ]);
    }

    public function addVenue(): void
    {
        $this->render('add-venue', [
            'pageTitle' => 'Dodaj lokal – PoGodzinach',
            'activeNav' => 'host',
        ]);
    }
}
