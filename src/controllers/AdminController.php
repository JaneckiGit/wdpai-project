<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\UserFilterForm;
use App\Repository\DictionaryRepository;
use App\Repository\HostApplicationRepository;
use App\Repository\UserRepository;
use App\Repository\VenueRepository;

// TODO: Laboratorium 9 – panel tylko dla zalogowanego administratora (rola z sesji).
final class AdminController extends AppController
{
    private const APPLICATION_DECISIONS = ['approve', 'reject'];
    private const VENUE_DECISIONS = ['publish', 'reject'];
    private const USER_ACTIONS = ['block', 'unblock', 'revoke-host'];

    // TODO: Laboratorium 8 – decyzje zapisane w bazie zamiast samego komunikatu.
    private const STATUS_MESSAGES = [
        'application-approve' => 'Wniosek zatwierdzony. Użytkownik dostanie rolę gospodarza, gdy wnioski trafią do bazy danych.',
        'application-reject' => 'Wniosek odrzucony. Decyzja zapisze się, gdy wnioski trafią do bazy danych.',
        'venue-publish' => 'Lokal opublikowany. Pojawi się w Odkrywaj, gdy lokale trafią do bazy danych.',
        'venue-reject' => 'Lokal odrzucony. Decyzja zapisze się, gdy lokale trafią do bazy danych.',
    ];

    // Akcje na kontach przekierowują do tabeli użytkowników (#users), więc ich komunikat pokazuje się przy tabeli.
    private const USER_STATUS_MESSAGES = [
        'user-block' => 'Użytkownik zablokowany. Blokada zapisze się, gdy konta trafią do bazy danych.',
        'user-unblock' => 'Użytkownik odblokowany. Zmiana zapisze się, gdy konta trafią do bazy danych.',
        'user-revoke-host' => 'Rola gospodarza odebrana. Zmiana zapisze się, gdy konta trafią do bazy danych.',
    ];

    private UserRepository $users;
    private HostApplicationRepository $applications;
    private VenueRepository $venues;
    private DictionaryRepository $dictionaries;

    public function __construct()
    {
        $this->users = new UserRepository($this->database());
        $this->applications = new HostApplicationRepository($this->database());
        $this->venues = new VenueRepository($this->database());
        $this->dictionaries = new DictionaryRepository($this->database());
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
            'purposeNames' => $this->dictionaries->purposes(),
            'venueTypes' => $this->dictionaries->venueTypes(),
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

        if ($application === null || !$application->isPending()) {
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

        // Konta administratorów nie ma w tabeli panelu, więc akcja na nim wygląda jak na nieistniejącym.
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
