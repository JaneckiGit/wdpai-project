<?php

declare(strict_types=1);

namespace App\Controller;

use App\Database;
use App\Form\UserFilterForm;
use App\Repository\HostApplicationRepository;
use App\Repository\UserRepository;
use App\Repository\VenueRepository;

// TODO: Laboratorium 9 – panel tylko dla zalogowanego administratora (rola z sesji).
final class AdminController extends AppController
{
    private const APPLICATION_DECISIONS = ['approve', 'reject'];
    private const VENUE_DECISIONS = ['publish', 'reject'];
    private const USER_ACTIONS = ['block', 'unblock', 'revoke-host'];

    // TODO: Laboratorium 8 – zapis decyzji o wnioskach (status wniosku i rola gospodarza w jednej transakcji).
    private const STATUS_MESSAGES = [
        'application-approve' => 'Wniosek zatwierdzony. Użytkownik dostanie rolę gospodarza, gdy decyzje o wnioskach trafią do bazy danych.',
        'application-reject' => 'Wniosek odrzucony. Decyzja zapisze się, gdy decyzje o wnioskach trafią do bazy danych.',
        'venue-publish' => 'Lokal opublikowany. Decyzja zapisze się w kolejnym kroku.',
        'venue-reject' => 'Lokal odrzucony. Decyzja zapisze się w kolejnym kroku.',
    ];

    // Akcje na kontach przekierowują do tabeli użytkowników (#users), więc ich komunikat pokazuje się przy tabeli.
    private const USER_STATUS_MESSAGES = [
        'user-block' => 'Użytkownik zablokowany. Zmiana zapisze się w kolejnym kroku.',
        'user-unblock' => 'Użytkownik odblokowany. Zmiana zapisze się w kolejnym kroku.',
        'user-revoke-host' => 'Rola gospodarza odebrana. Zmiana zapisze się w kolejnym kroku.',
    ];

    private UserRepository $users;
    private VenueRepository $venues;
    private HostApplicationRepository $applications;

    public function __construct()
    {
        $database = (new Database())->connect();
        $this->users = new UserRepository($database);
        $this->venues = new VenueRepository($database);
        $this->applications = new HostApplicationRepository($database);
    }

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
            'applications' => $this->applications->findPending(),
            'pendingVenues' => $this->venues->findByStatus('pending'),
            'users' => $isValid ? $this->users->findForAdmin($form->group === 'hosts', $form->group === 'blocked', $form->query) : [],
            'groups' => UserFilterForm::GROUPS,
            'form' => $form,
            'errors' => $form->errors(),
            'statusMessage' => $this->statusMessage(self::STATUS_MESSAGES),
            'userStatusMessage' => $this->statusMessage(self::USER_STATUS_MESSAGES),
        ]);
    }

    public function decideApplication(int $id): void
    {
        $decision = $_POST['decision'] ?? null;

        if (!in_array($decision, self::APPLICATION_DECISIONS, true)) {
            $this->renderError(400, 'Nieprawidłowe żądanie', 'Nie rozpoznano decyzji dla tego wniosku.');
            return;
        }

        $application = $this->applications->find($id);

        if ($application === null || $application->getStatus() !== 'pending') {
            $this->renderError(404, 'Nie znaleziono wniosku', 'Wniosek o podanym numerze nie istnieje albo ma już decyzję.');
            return;
        }

        $this->redirect('/admin?status=application-' . $decision);
    }

    public function decideVenue(int $id): void
    {
        $decision = $_POST['decision'] ?? null;

        if (!in_array($decision, self::VENUE_DECISIONS, true)) {
            $this->renderError(400, 'Nieprawidłowe żądanie', 'Nie rozpoznano decyzji dla tego lokalu.');
            return;
        }

        $venue = $this->venues->find($id);

        // Decyzja tylko dla lokalu w weryfikacji – ponowne wysłanie formularza po decyzji daje 404.
        if ($venue === null || $venue->getStatus() !== 'pending') {
            $this->renderError(404, 'Nie znaleziono lokalu', 'Lokal o podanym numerze nie czeka na akceptację.');
            return;
        }

        $this->redirect('/admin?status=venue-' . $decision);
    }

    /* Blokada, odblokowanie albo odebranie roli gospodarza; akcja musi pasować do obecnego stanu konta. */
    public function updateUser(int $id): void
    {
        $action = $_POST['action'] ?? null;

        if (!in_array($action, self::USER_ACTIONS, true)) {
            $this->renderError(400, 'Nieprawidłowe żądanie', 'Nie rozpoznano akcji dla tego użytkownika.');
            return;
        }

        $user = $this->users->find($id);

        if ($user === null || $user->isAdmin()) {
            $this->renderError(404, 'Nie znaleziono użytkownika', 'Użytkownik o podanym numerze nie istnieje.');
            return;
        }

        $isAllowed = match ($action) {
            'block' => !$user->isBlocked(),
            'unblock' => $user->isBlocked(),
            'revoke-host' => $user->isHost(),
        };

        if (!$isAllowed) {
            $this->renderError(409, 'Nie można wykonać tej akcji', 'Konto jest już w tym stanie albo nie ma roli gospodarza.');
            return;
        }

        $this->redirect('/admin?status=user-' . $action . '#users');
    }
}
