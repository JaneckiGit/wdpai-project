<?php

declare(strict_types=1);

use App\Controller\DefaultController;
use App\Controller\HostController;
use App\Controller\ProjectController;
use App\Controller\SecurityController;
use App\Controller\VenueController;
use App\Routing;

require_once dirname(__DIR__) . '/vendor/autoload.php';

// Szczegóły błędów trafiają do logu kontenera (docker compose logs php), a nie do przeglądarki.
ini_set('display_errors', '0');

set_error_handler(static function (int $severity, string $message, string $file, int $line): bool {
    if ((error_reporting() & $severity) === 0) {
        return false;
    }

    throw new ErrorException($message, 0, $severity, $file, $line);
});

$routing = new Routing();

$routing->get('/projects', ProjectController::class, 'index');
$routing->get('/projects/{id}', ProjectController::class, 'show');
$routing->get('/', DefaultController::class, 'index');
$routing->get('/login', SecurityController::class, 'login');
$routing->get('/venues', VenueController::class, 'index');
$routing->get('/venues/{id}', VenueController::class, 'show');
$routing->get('/host', HostController::class, 'dashboard');
$routing->get('/host/venues/new', HostController::class, 'addVenue');

try {
    $routeFound = $routing->dispatch(
        $_SERVER['REQUEST_METHOD'],
        $_SERVER['REQUEST_URI'],
    );

    if (!$routeFound) {
        http_response_code(404);
        (new DefaultController())->notFound();
    }
} catch (Throwable $exception) {
    error_log((string) $exception);

    // Usuwa niedokończony widok z bufora, żeby na stronie błędu nie został jego fragment.
    while (ob_get_level() > 0) {
        ob_end_clean();
    }

    (new DefaultController())->serverError();
}
