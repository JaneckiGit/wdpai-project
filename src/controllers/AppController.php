<?php

declare(strict_types=1);

namespace App\Controller;

use App\Database;
use App\Model\User;
use App\Repository\UserRepository;

abstract class AppController
{
    // TODO: Laboratorium 9 – zalogowany użytkownik z sesji zamiast konta demo gospodarza.
    private const DEMO_USER_EMAIL = 'piotr.nowak@example.com';

    private ?User $currentUser = null;

    protected function render(string $template, array $variables = []): void
    {
        $templatePath = dirname(__DIR__, 2) . '/public/views/' . $template . '.html.php';

        if (!is_file($templatePath)) {
            throw new \RuntimeException('View not found: ' . $template);
        }

        // Każdy widok dostaje zalogowanego użytkownika (nagłówek, menu konta, powitanie w panelu),
        // chyba że kontroler poda go sam – strona błędu serwera podaje null, bo nie może zależeć od bazy.
        if (!array_key_exists('currentUser', $variables)) {
            $variables['currentUser'] = $this->currentUser();
        }

        extract($variables, EXTR_SKIP);

        // Widok trafia do bufora, a wspólny layout wstawia go do <body> jako $content.
        ob_start();
        require $templatePath;
        $content = ob_get_clean();

        require dirname(__DIR__, 2) . '/public/views/layout.html.php';
    }

    /* Konto z bazy wczytane raz na żądanie; do Laboratorium 9 zawsze to samo konto demo. */
    protected function currentUser(): User
    {
        $this->currentUser ??= (new UserRepository((new Database())->connect()))->findByEmail(self::DEMO_USER_EMAIL);

        return $this->currentUser ?? throw new \RuntimeException('Demo user not found: ' . self::DEMO_USER_EMAIL);
    }

    /* Token i styl mapy z .env – docker-compose przekazuje je usłudze php. */
    protected function mapbox(): array
    {
        return [
            'token' => getenv('MAPBOX_TOKEN') ?: '',
            'style' => getenv('MAPBOX_STYLE') ?: 'mapbox://styles/mapbox/light-v11',
        ];
    }

    protected function renderError(int $status, string $title, string $message, array $variables = []): void
    {
        http_response_code($status);

        $this->render('error', [
            'pageTitle' => $title,
            'status' => $status,
            'message' => $message,
        ] + $variables);
    }

    /* Komunikat wybierany z listy dozwolonych, więc tekst z adresu nigdy nie trafia na stronę. */
    protected function statusMessage(array $messages): ?string
    {
        $status = $_GET['status'] ?? null;

        return is_string($status) ? ($messages[$status] ?? null) : null;
    }

    protected function isPost(): bool
    {
        return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
    }

    protected function redirect(string $path): never
    {
        header('Location: ' . $path, true, 303);
        exit;
    }
}
