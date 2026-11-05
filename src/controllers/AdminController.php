<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\UserFilterForm;

// TODO: Laboratorium 9 – panel tylko dla zalogowanego administratora (rola z sesji).
final class AdminController extends AppController
{
    // TODO: Laboratorium 8 – wnioski, lokale do akceptacji i użytkownicy z bazy zamiast przykładowych danych.
    private const SAMPLE_APPLICATIONS = [
        ['name' => 'Anna Kowalska', 'email' => 'anna.kowalska@example.com', 'sent' => 'dziś 9:40', 'waitingHours' => 2,
            'phone' => '+48 601 234 567', 'company' => 'Witraż Anna Kowalska', 'nip' => '676-248-10-35',
            'venue' => 'Pracownia Szkła Witraż', 'venueDetails' => 'ul. Józefińska 12, Podgórze · Pracownia',
            'message' => 'Prowadzę pracownię witrażu w Podgórzu. Od poniedziałku do środy po 16:00 pracownia stoi pusta – chcę ją udostępniać na warsztaty, sesje zdjęciowe i spotkania do 10 osób.'],
        ['name' => 'Paweł Kruk', 'email' => 'pawel.kruk@example.com', 'sent' => 'wczoraj 18:15', 'waitingHours' => 17,
            'phone' => '+48 512 880 341', 'company' => 'Echo Studio sp. z o.o.', 'nip' => '945-221-77-60',
            'venue' => 'Studio Nagrań Echo', 'venueDetails' => 'ul. Grzegórzecka 21, Grzegórzki · Studio',
            'message' => 'Studio nagrań z salą 40 m². W weekendy do 14:00 stoi wolne – na podcasty, sesje i małe szkolenia.'],
        ['name' => 'Monika Sowa', 'email' => 'monika.sowa@example.com', 'sent' => 'wczoraj 11:02', 'waitingHours' => 31,
            'phone' => '+48 698 117 402', 'company' => 'Księgarnia Strona s.c.', 'nip' => '677-239-51-08',
            'venue' => 'Księgarnia Strona', 'venueDetails' => 'ul. Miodowa 9, Kazimierz · Księgarnia z kawiarnią',
            'message' => 'Po 19:00 księgarnia jest zamknięta. Chcemy wynajmować antresolę na spotkania autorskie i kluby książki.'],
    ];

    private const SAMPLE_PENDING_VENUES = [
        ['name' => 'Piwnica Lampion', 'host' => 'Piotr Nowak', 'address' => 'ul. Szewska 14, Stare Miasto', 'sent' => 'dziś 8:15',
            'purposes' => ['meetings', 'events'], 'seatPrice' => null, 'seatCount' => null, 'exclusivePrice' => 180, 'capacity' => 24,
            'hasReel' => false],
        ['name' => 'Atelier Len', 'host' => 'Joanna Wrona', 'address' => 'ul. Krakowska 30, Kazimierz', 'sent' => 'wczoraj 20:30',
            'purposes' => ['work', 'shoots'], 'seatPrice' => 16, 'seatCount' => 8, 'exclusivePrice' => 140, 'capacity' => 12,
            'hasReel' => true],
    ];

    private const SAMPLE_USERS = [
        ['name' => 'Anna Kowalska', 'email' => 'anna.kowalska@example.com', 'isHost' => false, 'status' => 'application', 'lastSeen' => 'dziś 9:40'],
        ['name' => 'Piotr Nowak', 'email' => 'piotr.nowak@example.com', 'isHost' => true, 'status' => 'active', 'lastSeen' => 'dziś 8:15'],
        ['name' => 'Tomasz Wrona', 'email' => 'tomasz.wrona@example.com', 'isHost' => true, 'status' => 'active', 'lastSeen' => 'wczoraj 21:40'],
        ['name' => 'Marta Zielińska', 'email' => 'marta.zielinska@example.com', 'isHost' => false, 'status' => 'active', 'lastSeen' => 'wczoraj 21:05'],
        ['name' => 'Tomasz Wójcik', 'email' => 'tomasz.wojcik@example.com', 'isHost' => false, 'status' => 'active', 'lastSeen' => 'wczoraj 16:30'],
        ['name' => 'Adam Nowicki', 'email' => 'adam.nowicki@example.com', 'isHost' => false, 'status' => 'blocked', 'lastSeen' => '12.11',
            'blockReason' => 'spam w prośbach o rezerwację'],
    ];

    public function dashboard(): void
    {
        $form = new UserFilterForm($_GET);
        $isValid = $form->isValid();

        if (!$isValid) {
            http_response_code(400);
        }

        $this->render('admin', [
            'pageTitle' => 'Panel administratora – PoGodzinach',
            'stylesheet' => 'admin',
            'activeNav' => '',
            'applications' => self::SAMPLE_APPLICATIONS,
            'pendingVenues' => self::SAMPLE_PENDING_VENUES,
            'users' => $isValid ? $this->findUsers($form) : [],
            'groups' => UserFilterForm::GROUPS,
            'form' => $form,
            'errors' => $form->errors(),
        ]);
    }

    /* Użytkownik pasuje, gdy należy do wybranej grupy i ma szukaną frazę w imieniu albo e-mailu. */
    private function findUsers(UserFilterForm $form): array
    {
        return array_values(array_filter(
            self::SAMPLE_USERS,
            static fn (array $user): bool => ($form->group !== 'hosts' || $user['isHost'])
                && ($form->group !== 'blocked' || $user['status'] === 'blocked')
                && ($form->query === '' || mb_stripos($user['name'] . ' ' . $user['email'], $form->query) !== false),
        ));
    }
}
