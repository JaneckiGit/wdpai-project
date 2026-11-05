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
        ['id' => 1, 'name' => 'Bar Lampion', 'district' => 'Stare Miasto', 'address' => 'ul. Szpitalna 9',
            'lat' => 50.0623, 'lng' => 19.9343, 'seatPrice' => 15, 'exclusivePrice' => 160,
            'seatHours' => 'pn–pt 9:00–15:00', 'exclusiveHours' => 'niedziele 10:00–16:00',
            'purposes' => ['work', 'meetings', 'shoots'], 'host' => 'Ewa Mazur',
            'description' => 'Bar z lampami nad długim stołem i ceglaną ścianą. Do popołudnia, zanim zaczyna się wieczorna obsługa, sala jest cicha i jasna, a w niedziele można ją wynająć na wyłączność, np. na sesję zdjęciową.',
            'amenities' => ['Wi-Fi', 'Gniazdka przy stołach', 'Duże okno od północy'],
            'entry' => 'Klucz odbierzesz w sklepie obok – szczegóły zobaczysz po akceptacji rezerwacji.',
            'reels' => ['Sesja foto · Marta Z. · 0:24']],
        ['id' => 2, 'name' => 'Kawiarnia Ziarno', 'district' => 'Kazimierz', 'address' => 'ul. Estery 8',
            'lat' => 50.0512, 'lng' => 19.9466, 'seatPrice' => 15, 'exclusivePrice' => 150,
            'seatHours' => 'wt–pt 18:00–22:00', 'exclusiveHours' => 'poniedziałki 9:00–21:00',
            'purposes' => ['work', 'meetings', 'events'], 'host' => 'Piotr Nowak',
            'description' => 'Kameralna kawiarnia na Kazimierzu z długim dębowym stołem, ceglaną ścianą i oknem na podwórko. Wieczorami, gdy kończymy obsługę gości, oddajemy salę do cichej pracy, a w poniedziałki – gdy kawiarnia jest nieczynna – cały lokal na wyłączność.',
            'amenities' => ['Wi-Fi', 'Gniazdka przy każdym miejscu', 'Rzutnik', 'Ekspres do kawy'],
            'entry' => 'Na miejscu będzie pracownik. Szczegóły wejścia zobaczysz po akceptacji rezerwacji.',
            'reels' => ['Praca · Kuba W. · 0:18', 'Spotkanie · Ola M. · 0:31']],
        ['id' => 3, 'name' => 'Kawiarnia Pod Zegarem', 'district' => 'Kleparz', 'address' => 'ul. Długa 15',
            'lat' => 50.0668, 'lng' => 19.9420, 'seatPrice' => 20, 'exclusivePrice' => null,
            'seatHours' => 'pn–sob 8:00–16:00', 'exclusiveHours' => null,
            'purposes' => ['work', 'meetings'], 'host' => 'Tomasz Wrona',
            'description' => 'Jasna kawiarnia z dużym zegarem nad barem i stołami przy oknach. Od rana do popołudnia jest tu miejsce do cichej pracy i krótkich spotkań.',
            'amenities' => ['Wi-Fi', 'Gniazdka przy oknach'],
            'entry' => 'Na miejscu będzie pracownik.',
            'reels' => []],
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
        $venue = $this->findVenue($id);

        if ($venue === null) {
            $this->renderVenueNotFound();
            return;
        }

        $this->renderVenue($venue, new ReservationForm());
    }

    public function requestReservation(int $id): void
    {
        $venue = $this->findVenue($id);

        if ($venue === null) {
            $this->renderVenueNotFound();
            return;
        }

        $form = new ReservationForm($_POST);

        if ($form->isValid()) {
            // TODO: Laboratorium 8 – zapis prośby w bazie i blokada terminu na 24 h.
            $this->redirect('/venues/' . $id . '?status=request-sent');
        }

        http_response_code(422);
        $this->renderVenue($venue, $form);
    }

    /* array_column(…, null, 'id') zamienia listę lokali na tablicę, w której kluczem jest id. */
    private function findVenue(int $id): ?array
    {
        return array_column(self::SAMPLE_VENUES, null, 'id')[$id] ?? null;
    }

    private function renderVenue(array $venue, ReservationForm $form): void
    {
        $this->render('venue', [
            'pageTitle' => $venue['name'] . ' – PoGodzinach',
            'stylesheet' => 'venue',
            'activeNav' => 'venues',
            'venue' => $venue,
            'form' => $form,
            'errors' => $form->errors(),
            'statusMessage' => $this->statusMessage(self::VENUE_STATUS_MESSAGES),
        ]);
    }

    private function renderVenueNotFound(): void
    {
        $this->renderError(404, 'Nie znaleziono lokalu', 'Taki lokal nie istnieje.');
    }
}
