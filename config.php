<?php

declare(strict_types=1);

// Dane logowania do bazy pochodzą z .env – docker-compose przekazuje je usłudze php, więc hasło nie trafia do repozytorium.
// define() zamiast const, bo const przyjmuje tylko wartość znaną przed uruchomieniem, a getenv() działa w trakcie.
define('DB_USERNAME', (string) getenv('POSTGRES_USER'));
define('DB_PASSWORD', (string) getenv('POSTGRES_PASSWORD'));
const DB_HOST = 'db';
const DB_PORT = 5432;
define('DB_NAME', (string) getenv('POSTGRES_DB'));
