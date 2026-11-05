<?php

declare(strict_types=1);

namespace App\Controller;

abstract class AppController
{
    // TODO: Laboratorium 9 – zalogowany użytkownik z sesji zamiast przykładowego gospodarza.
    protected const CURRENT_USER = ['name' => 'Piotr Nowak', 'email' => 'piotr.nowak@example.com', 'role' => 'Gospodarz'];

    protected function render(string $template, array $variables = []): void
    {
        $templatePath = dirname(__DIR__, 2) . '/public/views/' . $template . '.html.php';

        if (!is_file($templatePath)) {
            throw new \RuntimeException('View not found: ' . $template);
        }

        // Każdy widok dostaje zalogowanego użytkownika (nagłówek, menu konta, powitanie w panelu).
        extract($variables + ['currentUser' => self::CURRENT_USER], EXTR_SKIP);

        // Widok trafia do bufora, a wspólny layout wstawia go do <body> jako $content.
        ob_start();
        require $templatePath;
        $content = ob_get_clean();

        require dirname(__DIR__, 2) . '/public/views/layout.html.php';
    }

    /* Token i styl mapy z .env – docker-compose przekazuje je usłudze php. */
    protected function mapbox(): array
    {
        return [
            'token' => getenv('MAPBOX_TOKEN') ?: '',
            'style' => getenv('MAPBOX_STYLE') ?: 'mapbox://styles/mapbox/light-v11',
        ];
    }

    protected function renderError(int $status, string $title, string $message): void
    {
        http_response_code($status);

        $this->render('error', [
            'pageTitle' => $title,
            'status' => $status,
            'message' => $message,
        ]);
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
