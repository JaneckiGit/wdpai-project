<?php

declare(strict_types=1);

use App\Controller\DefaultController;
use App\Controller\ProjectController;
use App\Controller\SecurityController;
use App\Controller\VenueController;
use App\Routing;

require_once dirname(__DIR__) . '/vendor/autoload.php';

$routing = new Routing();

$routing->get('/projects', ProjectController::class, 'index');
$routing->get('/projects/{id}', ProjectController::class, 'show');
$routing->get('/', DefaultController::class, 'index');
$routing->get('/login', SecurityController::class, 'login');
$routing->get('/venues', VenueController::class, 'index');
$routing->get('/venues/{id}', VenueController::class, 'show');

$routeFound = $routing->dispatch(
    $_SERVER['REQUEST_METHOD'],
    $_SERVER['REQUEST_URI'],
);

if (!$routeFound) {
    http_response_code(404);
    (new DefaultController())->notFound();
}
