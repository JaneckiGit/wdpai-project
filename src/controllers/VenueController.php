<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\ReservationForm;
use App\Form\VenueFilterForm;

final class VenueController extends AppController
{
    private const VENUE_STATUS_MESSAGES = [
        'request-sent' => 'Formularz jest poprawny. Prośba o rezerwację zostanie zapisana, gdy aplikacja będzie miała bazę danych.',
    ];

    // TODO: Laboratorium 8 – lokale z bazy (repozytorium) zamiast przykładowych danych.
    private const SAMPLE_VENUES = [
        ['id' => 1, 'name' => 'Bar Lampion', 'district' => 'Stare Miasto', 'image' => 'bar-lampion.svg',
            'lat' => 50.0623, 'lng' => 19.9343, 'seatPrice' => 15, 'exclusivePrice' => 160,
            'purposes' => ['work', 'meetings', 'shoots']],
        ['id' => 2, 'name' => 'Kawiarnia Ziarno', 'district' => 'Kazimierz', 'image' => 'kawiarnia-ziarno.svg',
            'lat' => 50.0512, 'lng' => 19.9466, 'seatPrice' => 15, 'exclusivePrice' => 150,
            'purposes' => ['work', 'meetings', 'events']],
        ['id' => 3, 'name' => 'Kawiarnia Pod Zegarem', 'district' => 'Kleparz', 'image' => 'pod-zegarem.svg',
            'lat' => 50.0668, 'lng' => 19.9420, 'seatPrice' => 20, 'exclusivePrice' => null,
            'purposes' => ['work', 'meetings']],
    ];

    public function index(): void
    {
        $form = new VenueFilterForm($_GET);
        $isValid = $form->isValid();

        if (!$isValid) {
            http_response_code(400);
        }

        $this->render('venues', [
            'pageTitle' => 'Odkrywaj – PoGodzinach',
            'stylesheet' => 'venues',
            'activeNav' => 'venues',
            'form' => $form,
            'errors' => $form->errors(),
            'searchQuery' => $form->query,
            'venues' => $isValid ? $this->findVenues($form) : [],
            'mapbox' => [
                'token' => getenv('MAPBOX_TOKEN') ?: '',
                'style' => getenv('MAPBOX_STYLE') ?: 'mapbox://styles/mapbox/light-v11',
            ],
        ]);
    }

    /* Lokal pasuje, gdy pozwala na wybrany cel, ma cenę w wybranym trybie i zawiera szukaną frazę. */
    private function findVenues(VenueFilterForm $form): array
    {
        $priceKey = $form->mode === 'seats' ? 'seatPrice' : 'exclusivePrice';

        return array_values(array_filter(
            self::SAMPLE_VENUES,
            static fn (array $venue): bool => in_array($form->purpose, $venue['purposes'], true)
                && $venue[$priceKey] !== null
                && ($form->query === '' || mb_stripos($venue['name'] . ' ' . $venue['district'], $form->query) !== false),
        ));
    }

    public function show(int $id): void
    {
        if ($id < 1) {
            $this->renderVenueNotFound();
            return;
        }

        $this->renderVenue($id, new ReservationForm());
    }

    public function requestReservation(int $id): void
    {
        if ($id < 1) {
            $this->renderVenueNotFound();
            return;
        }

        $form = new ReservationForm($_POST);

        if ($form->isValid()) {
            // TODO: Laboratorium 8 – zapis prośby w bazie i blokada terminu na 24 h.
            $this->redirect('/venues/' . $id . '?status=request-sent');
        }

        http_response_code(422);
        $this->renderVenue($id, $form);
    }

    private function renderVenue(int $id, ReservationForm $form): void
    {
        $this->render('venue', [
            'pageTitle' => 'Lokal i rezerwacja – PoGodzinach',
            'activeNav' => 'venues',
            'venueId' => $id,
            'form' => $form,
            'errors' => $form->errors(),
            'statusMessage' => $this->statusMessage(self::VENUE_STATUS_MESSAGES),
        ]);
    }

    // TODO: Laboratorium 8 – 404 także dla identyfikatora, którego nie ma w bazie.
    private function renderVenueNotFound(): void
    {
        $this->renderError(404, 'Nie znaleziono lokalu', 'Taki lokal nie istnieje.');
    }
}
